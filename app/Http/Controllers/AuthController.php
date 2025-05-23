<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
public function register(Request $request)
{

    $data = $request->validate([
        'HoTen' => 'required|string|max:255',
        'Email' => 'required|string|email|max:255|unique:users',
        'SDT' => 'required|string|max:15|unique:users',
        'Password' => 'required|string|min:8',
        'MaQuyen' => 'required|integer|exists:roles,MaQuyen',
    ]);


    $data['Password'] = bcrypt($data['Password']);

    $user = User::create([
        'HoTen' => $data['HoTen'],
        'Email' => $data['Email'],
        'SDT' => $data['SDT'],
        'Password' => $data['Password'],
    ]);

    $user->roles()->attach($data['MaQuyen']);

    // try {
    //     $user->roles()->attach($data['MaQuyen']);
    // } catch (Exception $e) {
    //     return response()->json([
    //         'message' => 'Lỗi khi gán quyền: ' . $e->getMessage(),
    //     ], 500);
    // }

    return response()->json(
        [
            'message' => 'Đăng ký thành công',
            'user' => $user->only(['MaNguoiDung', 'HoTen', 'Email', 'SDT']),
        ],
        201,
    );
}

    public function login(Request $request)
    {
        $request->validate([
            'SDT' => 'required|string',
            'Password' => 'required|string',
        ]);

        $user = User::where('SDT', $request->SDT)->first();

        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Thông tin xác thực không hợp lệ'], 401);
        }

        $roles = $user->roles()->pluck('TenQuyen')->toArray();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Xóa tất cả token

        return response()->json([
            'message' => 'Đăng xuất thành công.',
        ]);
    }
}
