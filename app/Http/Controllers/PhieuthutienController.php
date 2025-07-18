<?php

namespace App\Http\Controllers;

use App\Models\Phieuthutien;
use Illuminate\Http\Request;

class PhieuthutienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
        $phieuthutien = Phieuthutien::with(['hopdong.phong', 'hopdong.khach'])
            ->orderBy('ngay_thu', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $phieuthutien
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
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
        {
        $validated = $request->validate([
            'hopdong_id' => 'required|exists:hopdong,id',
            'thang' => 'required|string|size:7', 
            'so_tien' => 'required|numeric|min:0',
            'da_thanh_toan' => 'required|numeric|min:0',
            'no' => 'required|numeric',
            'ngay_thu' => 'required|date',
            'trang_thai' => 'required|string',
            'noi_dung' => 'nullable|string',
        ]);

        $phieu = PhieuThuTien::create($validated);

        return response()->json([
            'message' => 'Tạo phiếu thu tiền thành công',
            'data' => $phieu
        ], 201);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Phieuthutien $phieuthutien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phieuthutien $phieuthutien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phieuthutien $phieuthutien)
    {
        $validated = $request->validate([
        'da_thanh_toan' => 'required|numeric|min:0',
        'no' => 'required|numeric',
        'trang_thai' => 'required|string',
        'ngay_thu' => 'required|date', 
    ]);

    $phieuthutien->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Cập nhật phiếu thu tiền thành công',
        'data' => $phieuthutien
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phieuthutien $phieuthutien)
    {
        try {
        $phieuthutien->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa phiếu thu tiền thành công'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi khi xóa phiếu thu tiền: ' . $e->getMessage()
        ], 500);
    }
    }
}
