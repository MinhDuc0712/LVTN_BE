<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        // \Log::info('Dữ liệu nhận được từ client:', $request->all());

        // Validate dữ liệu
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
            'MaNguoiDung' => 'required|exists:users,MaNguoiDung',
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

        $house = House::create(
            $request->only([
            'TieuDe',
            'Tinh_TP',
            'Quan_Huyen',
            'Phuong_Xa',
            'Duong',
            'DiaChi',
            'SoPhongNgu',
            'SoPhongTam',
            'SoTang',
            'DienTich',
            'Gia',
            'MoTaChiTiet',
            'MaNguoiDung',
            'MaDanhMuc',
            'HinhAnh' => $firstImage,
        ]));

        foreach ($request->images as $index => $base64Image) {
            Images::create([
                'MaNha' => $house->MaNha,
                'DuongDanHinh' => $base64Image,
                'LaAnhDaiDien' => $index === 0 // Ảnh đầu tiên là ảnh đại diện
            ]);
        }

        return response()->json([
            'message' => 'Nhà và ảnh đã được đăng thành công',
            'house' => $house->load('images')
        ], 201);

        // try {

        //     $house = House::create([
        //         'TieuDe' => trim($request->TieuDe),
        //         'Tinh_TP' => trim($request->Tinh_TP),
        //         'Quan_Huyen' => trim($request->Quan_Huyen),
        //         'Phuong_Xa' => trim($request->Phuong_Xa),
        //         'Duong' => $request->Duong ? trim($request->Duong) : null,
        //         'DiaChi' => trim($request->DiaChi),
        //         'SoPhongNgu' => intval($request->SoPhongNgu),
        //         'SoPhongTam' => intval($request->SoPhongTam),
        //         'SoTang' => $request->SoTang ? intval($request->SoTang) : null,
        //         'DienTich' => floatval($request->DienTich),
        //         'Gia' => floatval($request->Gia),
        //         'MoTaChiTiet' => trim($request->MoTaChiTiet),
        //         'MaDanhMuc' => intval($request->MaDanhMuc),
        //         'MaNguoiDung' => intval($request->MaNguoiDung),
        //         'MaTienIch' => intval($request->MaTienIch),
        //         // 'TrangThai' => 'Đang chờ thanh toán',
        //         'NgayDang' => now(),
        //     ]);

        //     // Xử lý utilities
        //     if ($request->has('utilities')) {
        //         $house->utilities()->sync(array_map('intval', $request->utilities));
        //     }

        //     if ($request->hasFile('thumbnail')) {
        //         $fileName = Str::random(20) . '.' . $request->file('thumbnail')->getClientOriginalExtension();
        //         $path = $request->file('thumbnail')->storeAs('public/houses/thumbnails', $fileName);

        //         // Cập nhật thumbnail cho house
        //         $house->update([
        //             'HinhAnh' => Storage::url($path),
        //         ]);
        //     }

        //     DB::commit();

        //     return response()->json([
        //         'success' => true,
        //         'house' => $house,
        //         'redirect_url' => route('houses.payment.process', ['id' => $house->MaNha]),
        //     ]);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     \Log::error('House creation error: ' . $e->getMessage());
        //     return response()->json(
        //         [
        //             'success' => false,
        //             'message' => 'Lỗi hệ thống khi tạo bài đăng',
        //             'error' => $e->getMessage(),
        //         ],
        //         500,
        //     );
        // }
    }

    // public function uploadImages(Request $request, $id)
    // {
    //     $request->validate([
    //         'images' => 'required|array|max:20',
    //         'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240', // max 10MB mỗi ảnh
    //     ]);

    //     $house = House::findOrFail($id);

    //     foreach ($request->file('images') as $file) {
    //         $path = $file->store('houses', 'public');

    //         HouseImage::create([
    //             'house_id' => $house->id,
    //             'image_path' => $path,
    //         ]);
    //     }

    //     return response()->json(['message' => 'Upload thành công'], 200);
    // }
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

    // quan ly bài dang
    // public function userHouses()
    // {
    //     $houses = House::where('MaNguoiDung', Auth::id())
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return response()->json($houses);
    // }

    // public function updateStatus(Request $request, $id)
    // {
    //     $house = House::where('MaNguoiDung', Auth::id())->findOrFail($id);

    //     $validated = $request->validate([
    //         'TrangThai' => 'required|in:Đang chờ thanh toán,Đang xử lý,Đã duyệt,Đã từ chối,Đã cho thuê,Đã ẩn'
    //     ]);

    //     $house->update($validated);

    //     return response()->json(['success' => true]);
    // }
    // // admin duyet bai
    // public function pendingHouses()
    // {
    //     // Chỉ admin mới được truy cập
    //     if (!Auth::user()->isAdmin()) {
    //         abort(403);
    //     }

    //     $houses = House::where('TrangThai', 'Đang xử lý')->paginate(10);
    //     return response()->json($houses);
    // }

    // public function approveHouse($id)
    // {
    //     $house = House::findOrFail($id);
    //     $house->TrangThai = 'Đã duyệt';
    //     $house->save();

    //     return response()->json(['success' => true]);
    // }

    // public function rejectHouse($id)
    // {
    //     $house = House::findOrFail($id);
    //     $house->TrangThai = 'Đã từ chối';
    //     $house->save();

    //     return response()->json(['success' => true]);
    // }
    // // loc bai
    // public function searchHouses(Request $request)
    // {
    //     $query = House::query()->where('TrangThai', 'Đã duyệt');

    //     if ($request->has('Tinh_TP')) {
    //         $query->where('Tinh_TP', $request->Tinh_TP);
    //     }

    //     if ($request->has('Quan_Huyen')) {
    //         $query->where('Quan_Huyen', $request->Quan_Huyen);
    //     }

    //     if ($request->has('price_min')) {
    //         $query->where('Gia', '>=', $request->price_min);
    //     }

    //     if ($request->has('price_max')) {
    //         $query->where('Gia', '<=', $request->price_max);
    //     }

    //     $houses = $query->paginate(10);
    //     return response()->json($houses);
    // }
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
