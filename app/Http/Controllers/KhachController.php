<?php

namespace App\Http\Controllers;

use App\Models\Khach;
use App\Models\Hopdong;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KhachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $khachs = Khach::all();

        return response()->json([
            'data' => $khachs,
            'success' => true,
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
            'ho_ten' => 'required|string|max:255',
            'cmnd' => 'required|string|max:20',
            'sdt' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            // 'dia_chi' => 'nullable|string|max:255',
            'MaNguoiDung' => 'required|exists:users,id',
        ]);

        $validatedData['MaNguoiDung'] = $request->user()->MaNguoiDung;

        // Check for existing customer by cmnd
        $khach = Khach::where('cmnd', $validatedData['cmnd'])->first();

        if ($khach) {
            return response()->json(
                [
                    'message' => 'Khách hàng đã tồn tại',
                    'khach' => $khach,
                ],
                200,
            );
        }

        // Validate uniqueness for new customer
        $khachValidator = Validator::make($validatedData, [
            'cmnd' => 'unique:khach,cmnd',
            'sdt' => 'unique:khach,sdt',
            'email' => 'nullable|unique:khach,email',
        ]);

        if ($khachValidator->fails()) {
            return response()->json(
                [
                    'message' => 'Dữ liệu khách hàng không hợp lệ',
                    'errors' => $khachValidator->errors(),
                ],
                422,
            );
        }

        $khach = Khach::create($validatedData);

        return response()->json(
            [
                'message' => 'Khách hàng đã được tạo thành công',
                'khach' => $khach,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $khach = Khach::where('MaNguoiDung', $id)->first();

        if (!$khach) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => 'Không tìm thấy khách hàng',
                ],
                404,
            );
        }

        $hopdongs = Hopdong::where('khach_id', $khach->id)
            ->with('phong')
            ->get()
            ->map(function ($hopdong) {
                return [
                    'id' => $hopdong->id,
                    'propertyName' => $hopdong->phong->ten_phong ?? 'Phòng không xác định',
                    'roomNumber' => $hopdong->phong->ten_phong ?? 'N/A',
                    'monthlyRent' => (float) $hopdong->tien_thue,
                    'deposit' => (float) $hopdong->tien_coc,
                    'startDate' => $hopdong->ngay_bat_dau,
                    'endDate' => $hopdong->ngay_ket_thuc ?? null,
                    'status' => $hopdong->ngay_ket_thuc && now()->lt($hopdong->ngay_ket_thuc) ? 'active' : 'expired',
                    'area' => $hopdong->phong->dien_tich ? $hopdong->phong->dien_tich . 'm²' : 'N/A',
                    'bills' => [],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'khach' => [
                    'id' => $khach->id,
                    'ho_ten' => $khach->ho_ten,
                    'cmnd' => $khach->cmnd,
                    'sdt' => $khach->sdt,
                    'email' => $khach->email,
                    'dia_chi' => $khach->dia_chi,
                    'MaNguoiDung' => $khach->MaNguoiDung,
                ],
                'hopdongs' => $hopdongs,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Khach $khach)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Khach $khach)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Khach $khach)
    {
        //
    }
}
