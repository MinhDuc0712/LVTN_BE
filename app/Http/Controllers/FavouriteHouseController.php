<?php

namespace App\Http\Controllers;

use App\Models\Favourite_House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = Auth::id();
            $favoriteHouses = Favourite_House::where('MaNguoiDung', $userId)
                ->with(['house' => fn($query) => $query->with(['images', 'user', 'category'])])
                ->get()
                ->map(
                    fn($favorite) => [
                        'favorite_id' => $favorite->MaYeuThich,
                        'id' => $favorite->house->MaNha,
                        'title' => $favorite->house->TieuDe,
                        'price' => $favorite->house->Gia,
                        'area' => $favorite->house->DienTich,
                        'district' => $favorite->house->Quan_Huyen,
                        'city' => $favorite->house->Tinh_TP,
                        'address' => $favorite->house->DiaChi,
                        'description' => $favorite->house->MoTa,
                        'posted_at' => date('d/m/Y H:i', strtotime($favorite->house->NgayDang)),
                        'saved_at' => date('d/m/Y H:i', strtotime($favorite->created_at)),
                        'contact' => $favorite->house->user->SDT ?? '',
                        'poster_name' => $favorite->house->user->HoTen ?? '',
                        'type' => $favorite->house->category->name ?? '',
                        'image' => $favorite->house->images->first()->DuongDanHinh ?? '',
                    ],
                );

            return response()->json(
                [
                    'data' => $favoriteHouses,
                    'meta' => ['count' => $favoriteHouses->count()],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Không thể lấy danh sách yêu thích',
                    'message' => $e->getMessage(),
                ],
                500,
            );
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
        try {
            $request->validate([
                'MaNha' => 'required|exists:houses,MaNha',
            ]);

            $userId = Auth::id();
            $houseId = $request->MaNha;

            if (Favourite_House::where('MaNguoiDung', $userId)->where('MaNha', $houseId)->exists()) {
                $favorite = Favourite_House::where('MaNguoiDung', $userId)->where('MaNha', $houseId)->first();
                return response()->json(
                    [
                        'data' => [
                            'favorite_id' => $favorite->MaYeuThich,
                            'id' => $houseId,
                            'saved_at' => date('d/m/Y H:i', strtotime($favorite->created_at)),
                        ],
                        'message' => 'Nhà đã có trong danh sách yêu thích',
                    ],
                    200,
                );
            }

            $favorite = Favourite_House::create([
                'MaNha' => $houseId,
                'MaNguoiDung' => $userId,
            ]);

            return response()->json(
                [
                    'data' => [
                        'favorite_id' => $favorite->MaYeuThich,
                        'id' => $houseId,
                        'saved_at' => date('d/m/Y H:i', strtotime($favorite->created_at)),
                    ],
                    'message' => 'Đã thêm vào danh sách yêu thích',
                ],
                201,
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(
                [
                    'error' => 'Dữ liệu không hợp lệ',
                    'message' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Không thể thêm yêu thích',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($favoriteId)
    {
        try {
            $userId = Auth::user();
            $favorite = Favourite_House::where('MaYeuThich', $favoriteId)
                ->where('MaNguoiDung', $userId)
                ->with(['house' => fn($query) => $query->with(['images', 'user', 'category'])])
                ->first();

            if (!$favorite) {
                return response()->json(
                    [
                        'error' => 'Không tìm thấy yêu thích',
                    ],
                    404,
                );
            }

            return response()->json(
                [
                    'data' => [
                        'favorite_id' => $favorite->MaYeuThich,
                        'id' => $favorite->house->MaNha,
                        'title' => $favorite->house->TieuDe,
                        'price' => $favorite->house->Gia,
                        'area' => $favorite->house->DienTich,
                        'district' => $favorite->house->Quan_Huyen,
                        'city' => $favorite->house->Tinh_TP,
                        'address' => $favorite->house->DiaChi,
                        'description' => $favorite->house->MoTa,
                        'posted_at' => date('d/m/Y H:i', strtotime($favorite->house->NgayDang)),
                        'saved_at' => date('d/m/Y H:i', strtotime($favorite->created_at)),
                        'contact' => $favorite->house->user->SDT ?? '',
                        'poster_name' => $favorite->house->user->HoTen ?? '',
                        'type' => $favorite->house->category->name ?? '',
                        'image' => $favorite->house->images->first()->DuongDanHinh ?? '',
                    ],
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Không thể lấy thông tin yêu thích',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
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
        try {
            $userId = Auth::id();
            $action = $request->input('action');

            $favorite = Favourite_House::where('MaYeuThich', $favoriteId)->where('MaNguoiDung', $userId)->first();

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
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Lỗi khi xử lý yêu thích',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($favoriteId)
    {
        try {
            $userId = Auth::id();
            $favorite = Favourite_House::where('MaYeuThich', $favoriteId)->where('MaNguoiDung', $userId)->first();

            if (!$favorite) {
                return response()->json(
                    [
                        'error' => 'Không tìm thấy yêu thích',
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
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Không thể xóa yêu thích',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
