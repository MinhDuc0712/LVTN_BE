<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::with('roles')->get();

        return response()->json(
            $users->map(function ($user) {
                return [
                    'MaNguoiDung' => $user->MaNguoiDung,
                    'HoTen' => $user->HoTen,
                    'Email' => $user->Email,
                    'SDT' => $user->SDT,
                    'HinhDaiDien' => $user->HinhDaiDien,
                    'TrangThai' => $user->TrangThai,
                    'LyDoCam' => $user->LyDoCam,
                    'Role' => $user->roles->pluck('TenQuyen')->first(), // Lấy tên quyền đầu tiên
                ];
            }), 
        );
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
            'Password' => 'required|string|min:8',
            'SDT' => 'required|string|max:15|unique:users',
            'HinhDaiDien' => 'nullable|string|max:255',
            'DiaChi' => 'nullable|string|max:255',
        ]);

        $data['Password'] = bcrypt($data['Password']);
        $user = User::create($data);

        $role = Role::where('TenQuyen', 'user')->first();
        if ($role) {
            UserRole::create([
                'MaNguoiDung' => $user->MaNguoiDung,
                'MaQuyen' => $role->MaQuyen,
            ]);
        }

        return response()->json(['message' => 'Người dùng được tạo thành công', 'data' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('roles')->where('MaNguoiDung', $id)->first();

        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'MaNguoiDung' => $user->MaNguoiDung,
                'HoTen' => $user->HoTen,
                'Email' => $user->Email,
                'SDT' => $user->SDT,
                'HinhDaiDien' => $user->HinhDaiDien,
                'TrangThai' => $user->TrangThai,
                'LyDoCam' => $user->LyDoCam,
                'Role' => $user->role, // Sử dụng accessor getRoleAttribute
            ],
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
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate dữ liệu
        $data = $request->validate([
            'Role' => 'required|in:user,guest,owner,admin',
            'TrangThai' => 'required|in:Đang hoạt động,Tạm khóa,Bị cấm',
        ]);

        // Cập nhật TrangThai trong bảng users
        $user->update([
            'TrangThai' => $data['TrangThai'],
        ]);

        // Tìm role tương ứng với TenQuyen
        $role = Role::where('TenQuyen', $data['Role'])->firstOrFail();

        // Xóa role cũ (nếu có) và gán role mới
        $user->roles()->sync([$role->MaQuyen]);

        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function ban(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'LyDo' => 'nullable|string|max:255',
        ]);

        $user->TrangThai = 'Bị cấm';
        $user->LyDoCam = $data['LyDo'] ?? null;
        $user->save();

        return response()->json(
            [
                'message' => 'Cấm người dùng thành công',
                'data' => $user,
            ],
            200,
        );
    }

    public function unban($id)
    {
        $user = User::findOrFail($id);

        $user->TrangThai = 'Đang hoạt động';
        $user->LyDoCam = null;
        $user->save();

        return response()->json(
            [
                'message' => 'Bỏ cấm người dùng thành công',
                'data' => $user,
            ],
            200,
        );
    }

    public function findUser($identifier)
    {
        // Tìm kiếm theo số điện thoại hoặc mã người dùng
        $user = User::where('SDT', $identifier)->orWhere('MaNguoiDung', $identifier)->first();

        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ma_nguoi_dung' => $user->MaNguoiDung,
                'ho_ten' => $user->HoTen,
                'sdt' => $user->SDT,
            ],
        ]);
    }
}
