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
   public function handlePayment(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'houseId' => 'required|exists:houses,MaNha',
        'planType' => 'required|in:normal,vip',
        'duration' => 'required|integer|min:1',
        'unit' => 'required|in:day,week,month',
        'total' => 'required|numeric|min:0',
    ]);

    $house = House::where('MaNha', $validated['houseId'])
                  ->where('MaNguoiDung', $user->MaNguoiDung)
                  ->first();

    if (!$house) {
        return response()->json(['message' => 'Không tìm thấy bài đăng hoặc không có quyền'], 403);
    }

    
    if ($user->so_du < $validated['total']) {
        return response()->json(['message' => 'Số dư không đủ để thanh toán'], 400);
    }

    
    $user->so_du -= $validated['total'];
    $user->save();

    
    $unitMap = [
        'day' => 1,
        'week' => 7,
        'month' => 30,
    ];

    $days = $validated['duration'] * $unitMap[$validated['unit']];
    $expiryDate = now()->addDays($days);

   
    $house->TrangThai = $validated['planType'] === 'vip'
        ? House::STATUS_APPROVED
        : House::STATUS_PROCESSING;

    $house->NoiBat = $validated['planType'] === 'vip' ? 1 : 0;
    $house->NgayHetHan = $expiryDate;
    $house->save();

    return response()->json([
        'message' => 'Thanh toán thành công',
        'so_du_moi' => $user->so_du,
        'TrangThai' => $house->TrangThai,
        'NoiBat' => $house->NoiBat,
        'NgayHetHan' => $expiryDate,
    ]);
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
