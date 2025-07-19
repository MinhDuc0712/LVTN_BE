<?php

namespace App\Http\Controllers;

use App\Models\Hopdong;
use App\Models\Khach;
use App\Models\Phong;
use Illuminate\Support\Facades\Validator;
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
            'cmnd' => 'required|string|max:20',
            'MaNguoiDung' => 'nullable|exists:users,MaNguoiDung',
            'ho_ten' => 'required|string|max:255',
            'sdt' => 'required|string|max:15|regex:/^0[35789][0-9]{8}$/',
            'email' => 'nullable|email|max:255',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
            'tien_coc' => 'required|numeric|min:0',
            'tien_thue' => 'required|numeric|min:0',
            'chi_phi_tien_ich' => 'nullable|numeric|min:0',
            'ghi_chu' => 'nullable|string|max:255',
        ]);

        // Check if the room is available
        $phong = Phong::find($validatedData['phong_id']);
        if (!$phong || $phong->trang_thai !== 'trong') {
            return response()->json(['message' => 'Phòng không khả dụng'], 400);
        }

        // Check for existing customer by cmnd or MaNguoiDung
        $query = Khach::where('cmnd', $validatedData['cmnd']);
        if ($validatedData['MaNguoiDung']) {
            $query->orWhere('MaNguoiDung', $validatedData['MaNguoiDung']);
        }
        $khach = $query->first();

        if ($khach) {
            // Verify ho_ten and sdt match to prevent mismatches
            if ($khach->ho_ten !== $validatedData['ho_ten'] || $khach->sdt !== $validatedData['sdt']) {
                return response()->json([
                    'message' => 'Thông tin khách hàng không khớp với CMND/CCCD hiện có',
                    'errors' => [
                        'ho_ten' => ['Họ tên không khớp với khách hàng hiện có'],
                        'sdt' => ['Số điện thoại không khớp với khách hàng hiện có'],
                    ],
                ], 422);
            }
        } else {
            // Create new customer if none exists
            $khachData = [
                'cmnd' => $validatedData['cmnd'],
                'ho_ten' => $validatedData['ho_ten'],
                'sdt' => $validatedData['sdt'],
                'email' => $validatedData['email'] ?? null,
                'dia_chi' => $validatedData['dia_chi'] ?? '',
                'MaNguoiDung' => $validatedData['MaNguoiDung'] ?? null,
            ];

            // Validate uniqueness for new customer
            $khachValidator = Validator::make($khachData, [
                'cmnd' => 'unique:khach,cmnd',
                'sdt' => 'unique:khach,sdt',
                'email' => 'nullable|unique:khach,email',
            ]);

            if ($khachValidator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu khách hàng không hợp lệ',
                    'errors' => $khachValidator->errors(),
                ], 422);
            }

            $khach = Khach::create($khachData);
        }

        // Check for overlapping contracts
        $existingContract = Hopdong::where('phong_id', $validatedData['phong_id'])
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('ngay_bat_dau', [$validatedData['ngay_bat_dau'], $validatedData['ngay_ket_thuc']])
                      ->orWhereBetween('ngay_ket_thuc', [$validatedData['ngay_bat_dau'], $validatedData['ngay_ket_thuc']])
                      ->orWhere(function ($q) use ($validatedData) {
                          $q->where('ngay_bat_dau', '<=', $validatedData['ngay_bat_dau'])
                            ->where('ngay_ket_thuc', '>=', $validatedData['ngay_ket_thuc']);
                      });
            })->exists();

        if ($existingContract) {
            return response()->json(['message' => 'Phòng đã có hợp đồng trong khoảng thời gian này'], 400);
        }

        // Create contract
        $hopdongData = [
            'phong_id' => $validatedData['phong_id'],
            'khach_id' => $khach->id,
            'ngay_bat_dau' => $validatedData['ngay_bat_dau'],
            'ngay_ket_thuc' => $validatedData['ngay_ket_thuc'],
            'tien_coc' => $validatedData['tien_coc'],
            'tien_thue' => $validatedData['tien_thue'],
            'chi_phi_tien_ich' => $validatedData['chi_phi_tien_ich'] ?? 0,
            'ghi_chu' => $validatedData['ghi_chu'] ?? '',
        ];

        $hopdong = Hopdong::create($hopdongData);

        // Update room status
        $phong->update(['trang_thai' => 'da_thue']);

        return response()->json([
            'message' => 'Hợp đồng đã được tạo thành công',
            'contract' => $hopdong,
        ], 201);
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
