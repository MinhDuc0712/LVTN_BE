<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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
                'user' => $user->only(['MaNguoiDung', 'HoTen', 'Email', 'SDT', 'HinhDaiDien', 'so_du']),
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

        if (!$user || !\Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Thông tin xác thực không hợp lệ'], 401);
        }

        if ($user->TrangThai === 'Bị cấm') {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }
        $user->tokens()->delete();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $user->only(['MaNguoiDung', 'HoTen', 'Email', 'SDT', 'HinhDaiDien', 'so_du']),
            'roles' => $user->roles()->pluck('TenQuyen'),
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
                    'message' => 'Người dùng không được xác thực',
                ],
                401,
            );
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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Người dùng không được xác thực'], 401);
        }

        if ($user->TrangThai === 'Bị cấm') {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        $data = $request->validate(
            [
                'HoTen' => 'sometimes|string|max:255',
                'Email' => 'sometimes|string|email|max:255|unique:users,Email,' . $user->MaNguoiDung . ',MaNguoiDung',
                'SDT' => 'sometimes|string|max:15|unique:users,SDT,' . $user->MaNguoiDung . ',MaNguoiDung',
                'DiaChi' => 'sometimes|string|max:255',
                'HinhDaiDien' => 'nullable|url|max:255',
            ],
            [
                'Email.unique' => 'Email đã được sử dụng bởi người khác.',
                'SDT.unique' => 'Số điện thoại đã được sử dụng bởi người khác.',
            ],
        );

        $user->update($data);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'user' => $user,
            'roles' => $user->roles()->pluck('TenQuyen')->toArray(),
        ]);
    }
    public function sendOtp(Request $request)
    {
        // \Log::info('Gửi OTP cho email: ' . $request->Email);
        $request->validate(
            [
                'Email' => 'required|string|email|max:255|exists:users,Email',
            ],
            [
                'Email.exists' => 'Email không tồn tại trong hệ thống.',
            ],
        );

        $user = User::where('Email', $request->Email)->first();

        if ($user->TrangThai === 'Bị cấm') {
            // \Log::warning('Tài khoản bị cấm: ' . $user->Email);
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        // Tạo OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        // Lưu vào cache thay vì session
        \Cache::put(
            'otp_' . $request->Email,
            [
                'otp' => $otp,
                'expires_at' => $expiresAt,
            ],
            $expiresAt,
        );

        try {
            Mail::raw("Mã OTP của bạn là: $otp. Mã này có hiệu lực trong 10 phút.", function ($message) use ($user) {
                $message->to($user->Email)->subject('Mã OTP để đặt lại mật khẩu');
            });
            // \Log::info('Đã gửi OTP đến: ' . $user->Email);
        } catch (\Exception $e) {
            // \Log::error('Lỗi gửi email: ' . $e->getMessage());
            return response()->json(['message' => 'Không thể gửi email OTP'], 500);
        }

        return response()->json([
            'message' => 'Mã OTP đã được gửi đến email của bạn',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        // \Log::info('Xác minh OTP cho email: ' . $request->Email . ', OTP: ' . ($request->otp ?? 'undefined'));
        $request->validate(
            [
                'Email' => 'required|string|email|max:255|exists:users,Email',
                'otp' => 'required|string|size:6',
            ],
            [
                'Email.exists' => 'Email không tồn tại trong hệ thống.',
                'otp.required' => 'Vui lòng nhập mã OTP',
                'otp.size' => 'Mã OTP phải có 6 ký tự',
            ],
        );

        $cacheKey = 'otp_' . $request->Email;
        $otpData = \Cache::get($cacheKey);

        // \Log::info('OTP data from cache: ', [
        //     'stored_otp' => $otpData['otp'] ?? null,
        //     'stored_email' => $request->Email,
        //     'expires_at' => $otpData['expires_at'] ?? null,
        //     'current_time' => now()->toDateTimeString(),
        // ]);

        if (!$otpData || $request->otp !== $otpData['otp'] || now()->greaterThan($otpData['expires_at'])) {
            // \Log::error('OTP verification failed', [
            //     'otp_match' => $request->otp === ($otpData['otp'] ?? null),
            //     'expired' => now()->greaterThan($otpData['expires_at'] ?? now()),
            // ]);
            return response()->json(['message' => 'Mã OTP không hợp lệ hoặc đã hết hạn'], 400);
        }

        // Tạo token tạm thời cho bước reset password
        $resetToken = Str::random(60);
        \Cache::put('reset_token_' . $request->Email, $resetToken, now()->addMinutes(10));

        \Cache::forget($cacheKey);
        return response()->json([
            'message' => 'Xác minh OTP thành công',
            'reset_token' => $resetToken,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'Email' => 'required|string|email|max:255|exists:users,Email',
                'reset_token' => 'required|string',
                'new_password' => 'required|string|min:6',
            ],
            [
                'Email.exists' => 'Email không tồn tại trong hệ thống.',
            ],
        );

        $storedToken = \Cache::get('reset_token_' . $request->Email);

        if (!$storedToken || $request->reset_token !== $storedToken) {
            return response()->json(['message' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn'], 400);
        }
        // \Log::info('Received reset token:', ['token_request' => $request->reset_token]);
        // \Log::info('Stored reset token:', ['token_cache' => $storedToken]);

        $user = User::where('Email', $request->Email)->first();
        $user->Password = bcrypt($request->new_password);
        $user->save();

        \Cache::forget('reset_token_' . $request->Email);
        return response()->json([
            'message' => 'Đặt lại mật khẩu thành công',
        ]);
    }
}
