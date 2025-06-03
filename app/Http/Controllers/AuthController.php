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

    public function profile(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles()->pluck('TenQuyen')->toArray();

        return response()->json([
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(
                [
                'success' => false,
                'message' => 'Người dùng không được xác thực'
            ], 401);
        }

        if (!Hash::check($request->current_password, $user->Password)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng',
                ],
                400,
            );
        }

        $user->Password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }
}
