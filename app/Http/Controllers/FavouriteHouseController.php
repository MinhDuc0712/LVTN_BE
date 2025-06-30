<?php

namespace App\Http\Controllers;

use App\Models\Favourite_House;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $favoriteHouses = Favourite_House::where('MaNguoiDung', $user->MaNguoiDung)
            ->with('house', 'user', 'house.images')
            ->orderBy('MaYeuThich', 'asc')
            ->get()
            ->map(function ($favorite) {
                return [
                    'favorite_id' => $favorite->MaYeuThich,
                    'id' => $favorite->house->MaNha,
                    'title' => $favorite->house->TieuDe,
                    'price' => $favorite->house->Gia,
                    'area' => $favorite->house->DienTich,
                    'district' => $favorite->house->Quan_Huyen,
                    'city' => $favorite->house->Tinh_TP,
                    'address' => $favorite->house->DiaChi,
                    'description' => $favorite->house->MoTa,
                    'posted_at' => $favorite->house->NgayDang,
                    'saved_at' => $favorite->created_at->format('d-m-Y H:i:s'),
                    'contact' => $favorite->house->user->SDT,
                    'poster_name' => $favorite->house->user->HoTen ?? '',
                    'type' => $favorite->house->category->name ?? '',
                    'image' => $favorite->house->images->firstWhere('LaAnhDaiDien', true)?->DuongDanHinh ?? '',
                ];
            });
        return response()->json(['data' => $favoriteHouses], 200);
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
            return response()->json(['error' => 'User not found'], 404);
        }
        $maNha = request('MaNha');

        $houseExists = House::where('MaNha', $maNha)->exists();
        if (!$houseExists) {
            return response()->json(['error' => 'Mã nhà không tồn tại'], 400);
        }

        $favorite = Favourite_House::create([
            'MaNha' => $maNha,
            'MaNguoiDung' => $user->MaNguoiDung,
        ]);
        return response()->json(['data' => $favorite, 'message' => 'Đã thêm vào danh sách yêu thích'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($favoriteId)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favourite_House $favourite_House)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $favoriteId)
    {
        $userId = Auth::user();
        if (!$userId) {
            return response()->json(['error' => 'User không tồn tại'], 404);
        }
        $action = $request->input('action');

        $favorite = Favourite_House::where('MaYeuThich', $favoriteId)->where('MaNguoiDung', $userId->MaNguoiDung)->first();

        if (!$favorite) {
            return response()->json(['message' => 'Không có quyền thao tác'], 403);
        }

        if ($action === 'like') {
            return response()->json(['message' => 'Đã tồn tại trong danh sách yêu thích']);
        }

        if ($action === 'unlike') {
            $favorite->delete();
            return response()->json(['message' => 'Đã xóa khỏi danh sách yêu thích']);
        }

        return response()->json(['message' => 'Hành động không hợp lệ'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($favoriteId)
    {
        $userId = Auth::user();
        $favorite = Favourite_House::where('MaYeuThich', $favoriteId)->where('MaNguoiDung', $userId->MaNguoiDung)->first();

        if (!$favorite) {
            return response()->json(
                [
                    'error' => 'Không tìm thấy bài đăng yêu thích',
                ],
                404,
            );
        }

        $favorite->delete();

        return response()->json(
            [
                'message' => 'Đã xóa khỏi danh sách yêu thích',
            ],
            204,
        );
    }
}
