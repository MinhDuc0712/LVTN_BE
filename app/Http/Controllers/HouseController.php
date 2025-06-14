<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }
        $validator = Validator::make($request->all(), [
            'TieuDe' => 'required|min:30|max:100',
            'Tinh_TP' => 'required|string|max:255',
            'Quan_Huyen' => 'required|string|max:255',
            'Phuong_Xa' => 'required|string|max:255',
            'Duong' => 'nullable|string|max:255',
            'DiaChi' => 'required|string|max:255',
            'SoPhongNgu' => 'required|integer|min:0',
            'SoPhongTam' => 'required|integer|min:0',
            'SoTang' => 'nullable|integer|min:0',
            'DienTich' => 'required|numeric|min:0',
            'Gia' => 'required|numeric|min:0',
            'MoTaChiTiet' => 'required|min:50|max:5000',
            'MaDanhMuc' => 'required|exists:categories,MaDanhMuc',
            'images' => 'required|array',
            'images.*' => 'string',
            'utilities' => 'nullable|array',
            'utilities.*' => 'exists:utilities,MaTienIch',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }
        $firstImage = $request->images[0] ?? null;

        $house = House::create([
            'TieuDe' => $request->TieuDe,
            'Tinh_TP' => $request->Tinh_TP,
            'Quan_Huyen' => $request->Quan_Huyen,
            'Phuong_Xa' => $request->Phuong_Xa,
            'Duong' => $request->Duong,
            'DiaChi' => $request->DiaChi,
            'SoPhongNgu' => $request->SoPhongNgu,
            'SoPhongTam' => $request->SoPhongTam,
            'SoTang' => $request->SoTang,
            'DienTich' => $request->DienTich,
            'Gia' => $request->Gia,
            'MoTaChiTiet' => $request->MoTaChiTiet,
            'MaNguoiDung' => $user->MaNguoiDung,
            'MaDanhMuc' => $request->MaDanhMuc,
            'HinhAnh' => $firstImage,
        ]);
        if ($request->has('utilities')) {
            $house->utilities()->sync(array_map('intval', $request->utilities));
        }
        foreach ($request->images as $index => $base64Image) {
            Images::create([
                'MaNha' => $house->MaNha,
                'DuongDanHinh' => $base64Image,
                'LaAnhDaiDien' => $index === 0
            ]);
        }

        return response()->json([
            'message' => 'Nhà và ảnh đã được đăng thành công',
            'house' => $house->load('images')
        ], 201);

    }
    public function processPayment(Request $request, $id)
    {
        $house = House::findOrFail($id);

        if ($house->MaNguoiDung != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $paymentSuccess = $request->input('payment_success', false);

        if ($paymentSuccess) {
            $house->TrangThai = 'Đang xử lý';
            $house->save();

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công! Tin đăng của bạn đang được xử lý.',
                'redirect_url' => route('user.dashboard'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Bạn có thể thanh toán sau trong mục quản lý tin đăng.',
                'redirect_url' => route('user.dashboard'),
            ]);
        }
    }

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
