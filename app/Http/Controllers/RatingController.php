<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $maNha = $request->query('MaNha');

        if (!$maNha) {
            return response()->json(['message' => 'Thiếu mã nhà'], 400);
        }

        $ratings = Rating::with('user')->where('MaNha', $maNha)->orderByDesc('ThoiGian')->get();

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        //
    }
}
