<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DepositHistory;
use App\Models\House;
class PaymentsController extends Controller
{
    const STATUS_PENDING_PAYMENT = 'Đang chờ thanh toán';
    const STATUS_PROCESSING = 'Đang xử lý';
    const STATUS_APPROVED = 'Đã duyệt';
    const STATUS_REJECTED = 'Đã từ chối';
    const STATUS_RENTED = 'Đã cho thuê';
    const STATUS_HIDDEN = 'Đã ẩn';
    const STATUS_EXPIRED = 'Tin hết hạn';
    /**
     * Display a listing of the resource.
     */
    public function getUserPayments()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }

        $payments = Payments::where('MaNguoiDung', $user->MaNguoiDung)->with('house')->orderBy('created_at', 'desc')->get();

        return response()->json($payments);
    }
    public function index()
    {
        //
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
        //
        $validated = $request->validate([
            'ma_giao_dich' => 'required|string',
            'houseId' => 'required|exists:houses,MaNha',
            'type' => 'required|in:normal,vip',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|in:day,week,month',
        ]);

        $deposit = DepositHistory::where('ma_giao_dich', $validated['ma_giao_dich'])->where('trang_thai', 'Hoàn tất')->firstOrFail();

        $user = $deposit->user;
        $house = House::where('MaNha', $validated['houseId'])->where('MaNguoiDung', $user->MaNguoiDung)->firstOrFail();

        // Tránh xử lý trùng
        if (Payments::where('MaGiaoDich', $validated['ma_giao_dich'])->exists()) {
            return response()->json(['message' => 'Giao dịch đã được xử lý.'], 200);
        }

        $unitMap = ['day' => 1, 'week' => 7, 'month' => 30];
        $days = $validated['quantity'] * $unitMap[$validated['unit']];
        $expiryDate = now()->addDays($days);

        $house->TrangThai = $validated['type'] === 'vip' ? House::STATUS_APPROVED : House::STATUS_PROCESSING;
        $house->NoiBat = $validated['type'] === 'vip' ? 1 : 0;
        $house->NgayHetHan = $expiryDate;
        $house->save();

        Payments::create([
            'MaGiaoDich' => $validated['ma_giao_dich'],
            'MaNha' => $house->MaNha,
            'MaNguoiDung' => $user->MaNguoiDung,
            'Voucher' => 0,
            'PhiGiaoDich' => $deposit->so_tien,
            'TongTien' => $deposit->thuc_nhan,
        ]);

        return response()->json([
            'message' => 'Đã xác nhận thanh toán bài đăng qua QR thành công!',
            'house' => $house,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payments $payments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payments $payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payments $payments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payments $payments)
    {
        //
    }
}
