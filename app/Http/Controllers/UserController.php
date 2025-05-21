<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::all();
        return response()->json($users);
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
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|string|email|max:255|unique:users',
            'Password' => 'required|string|min:8|confirmed',
            'SDT' => 'required|string|max:15|unique:users',
            'HinhDaiDien' => 'nullable|string|max:255',
            'DiaChi' => 'nullable|string|max:255',
        ]);

        $data['Password'] = bcrypt($data['Password']);
        $user = User::create($data);
        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $user = User::findOrFail($id);
    return response()->json([
        'HoTen' => $user->HoTen,
        'SDT' => $user->SDT
        // hoặc field nào đúng trong bảng user
    ]);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
    public function findUser($identifier)
{
    // Tìm kiếm theo số điện thoại hoặc mã người dùng
    $user = User::where('SDT', $identifier)
               ->orWhere('MaNguoiDung', $identifier)
               ->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy người dùng'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'ma_nguoi_dung' => $user->MaNguoiDung,
            'ho_ten' => $user->HoTen,
            'sdt' => $user->SDT
        ]
    ]);
}
}
