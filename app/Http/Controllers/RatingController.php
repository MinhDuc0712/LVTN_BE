<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $maNha = $request->query('MaNha');
        $user = Auth::user();

        $ratings = Rating::with('user')
            ->where('MaNha', $maNha)
            ->orderByDesc('ThoiGian')
            ->get()
            ->map(function ($rating) use ($user) {
                $rating->liked = false;

                if ($user) {
                    $exists = DB::table('like_comment')->where('MaDanhGia', $rating->MaDanhGia)->where('MaNguoiDung', $user->MaNguoiDung)->exists();
                    $rating->liked = $exists;
                }

                return $rating;
            });

        return response()->json($ratings);
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

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Không xác thực được người dùng'], 401);
        }

        $data = $request->validate([
            'MaNha' => 'required|exists:houses,MaNha',
            'MaNguoiDung' => 'required|exists:users,MaNguoiDung',
            'SoSao' => 'required|integer|min:1|max:5',
            'NoiDung' => 'nullable|string|max:1000',
            'LuotThich' => 'nullable|integer|min:0',
        ]);

        $data['Thoigian'] = now()->toDateTimeString();
        $rating = Rating::create($data);

        return response()->json(
            [
                'message' => 'Đánh giá đã được tạo thành công',
                'rating' => $rating,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Rating $rating)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rating $rating)
    {
        //
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        $action = $request->input('action');

        if ($action === 'like') {
            // Thêm like
            DB::table('like_comment')->updateOrInsert(['MaDanhGia' => $rating->MaDanhGia, 'MaNguoiDung' => $user->MaNguoiDung], ['created_at' => now(), 'updated_at' => now()]);
            $likeCount = DB::table('like_comment')->where('MaDanhGia', $rating->MaDanhGia)->count();
            $rating->LuotThich = $likeCount;
            $rating->save();
            return response()->json(['message' => 'Đã thích']);
        }

        if ($action === 'unlike') {
            // Huỷ like
            DB::table('like_comment')->where('MaDanhGia', $rating->MaDanhGia)->where('MaNguoiDung', $user->MaNguoiDung)->delete();
            $likeCount = DB::table('like_comment')->where('MaDanhGia', $rating->MaDanhGia)->count();
            $rating->LuotThich = $likeCount;
            $rating->save();
            return response()->json(['message' => 'Đã huỷ thích']);
        }

        return response()->json(['message' => 'Hành động không hợp lệ'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        //
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }
        if ($rating->MaNguoiDung !== $user->MaNguoiDung) {
            return response()->json(['message' => 'Bạn không có quyền xoá đánh giá này'], 403);
        }
        $rating->delete();
        return response()->json(
            [
                'message' => 'Đánh giá đã được xoá thành công',
            ],
            200,
        );
    }
}
