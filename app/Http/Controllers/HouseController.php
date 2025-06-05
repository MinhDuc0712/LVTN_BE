<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $validated = $request->validate([
            'TieuDe' => 'required|min:30|max:100',
            'Tinh_TP' => 'required',
            'Quan_Huyen' => 'required',
            'Phuong_Xa' => 'required',
            'Duong' => 'required',
            'SoPhongNgu' => 'required|integer|min:0',
            'SoPhongTam' => 'required|integer|min:0',
            'SoTang' => 'nullable|integer|min:0',
            'DienTich' => 'required|numeric|min:0',
            'Gia' => 'required|numeric|min:0',
            'MoTaChiTiet' => 'required|min:50|max:5000',
            'MaDanhMuc' => 'required|exists:categories,id',
        ]);

        $house = new House();
        $house->fill($validated);
        $house->DiaChi = implode(', ', [
            $request->Duong,
            $request->Phuong_Xa,
            $request->Quan_Huyen,
            $request->Tinh_TP
        ]);
        $house->NgayDang = now();
        $house->TrangThai = 'Đang chờ thanh toán'; // Mặc định khi tạo mới
        $house->MaNguoiDung = Auth::id();
        
        $house->save();

        return response()->json([
            'success' => true,
            'house_id' => $house->MaNha,
            'redirect_url' => route('payment.page', ['id' => $house->MaNha])
        ]);
    }

    public function processPayment(Request $request, $id)
    {
        $house = House::findOrFail($id);
        
        // Kiểm tra quyền sở hữu
        if ($house->MaNguoiDung != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Giả lập quá trình thanh toán
        $paymentSuccess = $request->input('payment_success', false);

        if ($paymentSuccess) {
            $house->TrangThai = 'Đang xử lý';
            $house->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công! Tin đăng của bạn đang được xử lý.',
                'redirect_url' => route('user.dashboard') // Trang dashboard người dùng
            ]);
        } else {
            // Giữ nguyên trạng thái "Đang chờ thanh toán"
            return response()->json([
                'success' => false,
                'message' => 'Bạn có thể thanh toán sau trong mục quản lý tin đăng.',
                'redirect_url' => route('user.dashboard')
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(House $house)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(House $house)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, House $house)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(House $house)
    {
        //
    }
}
