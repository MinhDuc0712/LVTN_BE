<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\House;
use App\Models\DepositHistory;
use Illuminate\Support\Str;

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
    public function index()
    {
        //
                $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }

        $payments = Payments::where('MaNguoiDung', $user->MaNguoiDung)->with('house')->orderBy('created_at', 'desc')->get();

        return response()->json($payments);
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
            'ma_giao_dich' => 'nullable|string',
            'houseId' => 'required|exists:houses,MaNha',
            'type' => 'required|in:normal,vip',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|in:day,week,month',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }

        $house = House::where('MaNha', $validated['houseId'])->where('MaNguoiDung', $user->MaNguoiDung)->firstOrFail();

        // Tính toán chi phí
        $unitMap = ['day' => 1, 'week' => 7, 'month' => 30];
        $days = $validated['quantity'] * $unitMap[$validated['unit']];
        $planPrice = $validated['type'] === 'vip' ? 30000 : 5000;
        $minDays = $validated['type'] === 'vip' ? 1 : 3;
        $validDays = max($days, $minDays);
        $cost = $validDays * $planPrice;
        $expiryDate = now()->addDays($validDays);
        $maGiaoDichRequest = $validated['ma_giao_dich'] ?? null;

        // Phân biệt loại thanh toán
        if ($maGiaoDichRequest) {
            // Xử lý thanh toán bằng chuyển khoản
            $deposit = DepositHistory::where('ma_giao_dich', $maGiaoDichRequest)->where('trang_thai', 'Hoàn tất')->firstOrFail();

            if (Payments::where('MaGiaoDich', $maGiaoDichRequest)->exists()) {
                return response()->json(['message' => 'Giao dịch đã được xử lý.'], 200);
            }

            $maGiaoDich = $maGiaoDichRequest;

        } else {
            // Xử lý thanh toán bằng ví
            if ($user->so_du < $cost) {
                return response()->json(['message' => 'Số dư không đủ để thanh toán.'], 422);
            }

            $user->so_du -= $cost;
            $user->save();

            $maGiaoDich = 'WALLET-' . Str::uuid();
        }


        // Cập nhật bài đăng
        $house->TrangThai = $validated['type'] === 'vip' ? House::STATUS_APPROVED : House::STATUS_PROCESSING;
        $house->NoiBat = $validated['type'] === 'vip' ? 1 : 0;
        $house->NgayHetHan = $expiryDate;
        $house->save();

        // Lưu payment
        Payments::create([
            'MaGiaoDich' => $maGiaoDich,
            'MaNha' => $house->MaNha,
            'MaNguoiDung' => $user->MaNguoiDung,
            'Voucher' => 0,
            'PhiGiaoDich' => $cost,
            'TongTien' => $cost,
        ]);

        return response()->json([
            'message' => 'Đã xác nhận và thanh toán thành công!',
            'house' => $house,
            'ma_giao_dich' => $maGiaoDich,
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
