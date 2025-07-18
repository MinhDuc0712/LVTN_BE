<?php

namespace App\Http\Controllers;

use App\Models\Phieudien;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class PhieudienController extends Controller
{
    const STATUS_UNPAID = 'Chưa thanh toán';
    const STATUS_PAID = 'Đã thanh toán';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $bills = Phieudien::with('hopdong.phong')->orderBy('ngay_tao', 'desc')->get();

    return response()->json([
        'success' => true,
        'data' => $bills
    ]);
    }
public function toggleStatus($id)
{
    try {
        $bill = Phieudien::findOrFail($id);
        $bill->trang_thai = $bill->trang_thai === self::STATUS_PAID
            ? self::STATUS_UNPAID
            : self::STATUS_PAID;
        $bill->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => [
        'id' => $bill->id,
        'trang_thai' => $bill->trang_thai,
    ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        '*.hopdong_id' => 'required|exists:hopdong,id',
        '*.chi_so_dau' => 'required|numeric|min:0',
        '*.chi_so_cuoi' => 'required|numeric|min:0',
        '*.don_gia' => 'required|numeric|min:0',
        '*.ngay_tao' => 'required|date_format:Y-m-d',
    ]);

    try {
        DB::beginTransaction();
        
        $createdBills = [];
        foreach ($validated as $billData) {
            $billData['thang'] = Carbon::parse($billData['ngay_tao'])->format('Y-m');
            $billData['trang_thai'] = self::STATUS_UNPAID;
            $createdBills[] = Phieudien::create($billData);
        }

        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Hóa đơn điện đã được lưu thành công',
            'data' => $createdBills
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi lưu hóa đơn điện: ' . $e->getMessage()
        ], 500);
    }
}

public function getLastReading($hopdong_id, Request $request)
{
    $request->validate([
        'thang' => 'required|date_format:Y-m-d', 
    ]);

    $currentDate = $request->query('thang');
    $prevMonthDate = Carbon::parse($currentDate)->subMonth()->format('Y-m-d');

    $lastReading = Phieudien::where('hopdong_id', $hopdong_id)
        ->whereDate('ngay_tao', '<=', $prevMonthDate)
        ->orderBy('ngay_tao', 'desc')
        ->first();

    return response()->json([
        'success' => true,
        'data' => [
            'hopdong_id' => $hopdong_id,
            'chi_so_cuoi' => $lastReading ? $lastReading->chi_so_cuoi : 0,
            'thang_truoc' => $prevMonthDate
        ]
    ]);
}


    /**
     * Display the specified resource.
     */
    public function show(Phieudien $phieudien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phieudien $phieudien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phieudien $phieudien)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phieudien $phieudien)
    {
        //
    }
}
