<?php

namespace App\Http\Controllers;

use App\Models\Hopdong;
use Illuminate\Http\Request;

class HopdongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $hopdongs = Hopdong::with(['phong', 'khach'])->get();
        return response()->json([
            'success' => true,
            'data' => $hopdongs
        ]);

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
        $validatedData = $request->validate([
            'phong_id' => 'required|exists:phong,id',
            'khach_id' => 'required|exists:khach,id',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
            'tien_coc' => 'required|numeric|min:0',
            'tien_thue' => 'required|numeric|min:0',
            'chi_phi_tien_ich' => 'nullable|numeric|min:0',
            'ghi_chu' => 'nullable|string|max:255',
        ]);
        $hopdong = Hopdong::create($validatedData);
        return response()->json(
            [
                'message' => 'Hợp đồng đã được tạo thành công',
                'contract' => $hopdong,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Hopdong $hopdong)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hopdong $hopdong)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hopdong $hopdong)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hopdong $hopdong)
    {
        //
    }
}
