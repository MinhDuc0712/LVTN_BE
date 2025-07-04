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
        $totalRevenue = Payments::sum('TongTien');

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
        ]);
    }
    public function charts()
    {
        $monthlyRevenue = Payments::selectRaw('MONTH(created_at) as month, SUM(TongTien) as revenue, COUNT(*) as transactions')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => 'T' . $item->month,
                    'revenue' => (int) $item->revenue,
                    'transactions' => $item->transactions,
                ];
            });

          $categoryStats = Categories::withCount('houses')
            ->get()
            ->map(function ($item, $index) {
                $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'];
                return [
                    'name' => $item->name,
                    'value' => $item->houses_count,
                    'color' => $colors[$index % count($colors)]
                ];
            });

        $postTrend = House::where('TrangThai', House::STATUS_APPROVED)
            ->selectRaw('MONTH(NgayDang) as month, COUNT(*) as posts')
            ->groupBy(DB::raw('MONTH(NgayDang)'))
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => 'T' . $item->month,
                    'posts' => $item->posts,
                ];
            });
        return response()->json([
            'monthlyRevenue' => $monthlyRevenue,
            'CategoriesStats' => $categoryStats,
            'postTrend' => $postTrend,
        ]);
    }
}
