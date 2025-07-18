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

    public function charts(Request $request)
    {
        $filter = $request->query('filter', 'month');

        $isWeek = $filter === 'week';
        $groupFormat = match ($filter) {
            'week' => 'YEARWEEK(%s, 3)',
            'year' => "DATE_FORMAT(%s, '%%Y')",
            default => "DATE_FORMAT(%s, '%%m-%%Y')",
        };
        $labelPrefix = match ($filter) {
            'week' => 'Tuần ',
            'year' => 'Năm ',
            default => 'Tháng ',
        };

        $depositQuery = DB::table('deposit_history')
            ->selectRaw(sprintf($groupFormat, 'ngay_nap') . ' as label, SUM(so_tien) as revenue')
            ->where('trang_thai', 'Hoàn tất')
            ->groupBy('label');

        $paymentQuery = DB::table('payments')
            ->selectRaw(sprintf($groupFormat, 'created_at') . ' as label, SUM(TongTien) as revenue')
            ->groupBy('label');

        $combined = $depositQuery->unionAll($paymentQuery);

        $results = DB::table(DB::raw("({$combined->toSql()}) as combined"))
            ->mergeBindings($combined)
            ->selectRaw('label, SUM(revenue) as revenue')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $mapped = $results->map(function ($item) use ($filter, $labelPrefix, $isWeek) {
            if ($isWeek) {
                $year = substr($item->label, 0, 4);
                $week = substr($item->label, 4);
                $start = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
                $end = $start->copy()->endOfWeek();
                $label = "{$labelPrefix}{$week} ({$start->format('d/m')} - {$end->format('d/m')})";
            } else {
                $label = $labelPrefix . $item->label;
            }
            return ['label' => $label, 'revenue' => (int) $item->revenue];
        });

        return response()->json(['revenueChart' => $mapped]);
    }
}
