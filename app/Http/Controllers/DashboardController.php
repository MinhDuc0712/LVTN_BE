<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\House;
use App\Models\DepositHistory;
use App\Models\Payments;
use App\Models\Categories;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function stats()
    {
        // Tổng số người dùng
        $totalUsers = User::count();

        // Tổng số bài đăng
        $totalPosts = House::count();

        // Tổng số giao dịch nạp tiền hoàn tất
        $totalTransactions = DepositHistory::where('trang_thai', 'Hoàn tất')->count();

        // Tổng doanh thu từ các giao dịch Payments hoàn tất
        $totalRevenue = DepositHistory::where('trang_thai', 'Hoàn tất')->sum('so_tien') + Payments::sum('TongTien');

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
        ]);
    }
    public function charts()
    {
        $depositQuery = DB::table('deposit_history')->selectRaw('MONTH(ngay_nap) as month, SUM(so_tien) as revenue, COUNT(*) as transactions')->where('trang_thai', 'Hoàn tất')->groupBy(DB::raw('MONTH(ngay_nap)'));

        $paymentQuery = DB::table('payments')->selectRaw('MONTH(created_at) as month, SUM(TongTien) as revenue, COUNT(*) as transactions')->groupBy(DB::raw('MONTH(created_at)'));

        $combined = $depositQuery->unionAll($paymentQuery);

        $monthlyRevenue = DB::table(DB::raw("({$combined->toSql()}) as combined"))
            ->mergeBindings($combined)
            ->selectRaw('month, SUM(revenue) as revenue, SUM(transactions) as transactions')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => 'Tháng ' . $item->month,
                    'revenue' => (int) $item->revenue,
                    'Số giao dịch' => $item->transactions,
                ];
            });

        $categoryStats = Categories::withCount('houses')
            ->get()
            ->map(function ($item, $index) {
                $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'];
                return [
                    'name' => $item->name,
                    'value' => $item->houses_count,
                    'color' => $colors[$index % count($colors)],
                ];
            });

        $postTrend = House::where('TrangThai', House::STATUS_APPROVED)
            ->selectRaw('MONTH(NgayDang) as month, COUNT(*) as posts')
            ->groupBy(DB::raw('MONTH(NgayDang)'))
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => 'Tháng ' . $item->month,
                    'Bài đăng' => $item->posts,
                ];
            });
        return response()->json([
            'monthlyRevenue' => $monthlyRevenue,
            'CategoriesStats' => $categoryStats,
            'postTrend' => $postTrend,
        ]);
    }
}
