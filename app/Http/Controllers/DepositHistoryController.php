<?php

namespace App\Http\Controllers;

use App\Models\DepositHistory;
use App\Models\User;
use Illuminate\Http\Request;

class DepositHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DepositHistory::with('user');

        if ($search = $request->input('search')) {
            $query->whereHas(
                'user',
                fn($q) =>
                $q->where('SDT', 'like', "%$search%")
                    ->orWhere('HoTen', 'like', "%$search%")
            )->orWhere('ma_giao_dich', 'like', "%$search%");
        }

        if ($status = $request->input('status')) {
            $query->where('trang_thai', $status);
        }

        return $query->orderBy('id', 'desc')->paginate(5);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_nguoi_dung' => 'required',
            'so_tien' => 'required|numeric|min:0',
            'khuyen_mai' => 'nullable|numeric|min:0',
            'phuong_thuc' => 'required|string',
            'trang_thai' => 'required|string',
            'ghi_chu' => 'nullable|string',
        ]);
        // $request->khuyen_mai = $request->so_tien * ($request->khuyen_mai/100);
        $thuc_nhan = $request->so_tien + $request->so_tien * ($request->khuyen_mai / 100 ?? 0);

        $user = User::where('MaNguoiDung', $request['ma_nguoi_dung'])
            ->orWhere('SDT', $request['ma_nguoi_dung'])
            ->firstOrFail();

        // Thêm user_id thực vào dữ liệu
        $request['ma_nguoi_dung'] = $user->MaNguoiDung;
        $transaction = DepositHistory::create([
            'ma_nguoi_dung' => $request->ma_nguoi_dung,
            'so_tien' => $request->so_tien,
            'khuyen_mai' => $request->khuyen_mai ?? 0,
            'thuc_nhan' => $thuc_nhan,
            'phuong_thuc' => $request->phuong_thuc,
            'trang_thai' => $request->trang_thai,
            'ghi_chu' => $request->ghi_chu ?? '',
            'ma_giao_dich' => 'TXN' . now()->timestamp,
            'ngay_nap' => now()->toDateTimeString(),
        ]);

        return response()->json($transaction->load('user'), 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'so_tien' => 'required|numeric|min:0',
            'khuyen_mai' => 'nullable|numeric|min:0',
            'phuong_thuc' => 'required|string',
            'trang_thai' => 'required|string',
            'ghi_chu' => 'nullable|string',
        ]);

        $transaction = DepositHistory::findOrFail($id);
        $thucNhan = $validated['so_tien'] + $validated['so_tien'] * ($validated['khuyen_mai'] / 100 ?? 0);

        $transaction->update([
            'so_tien' => $validated['so_tien'],
            'khuyen_mai' => $validated['khuyen_mai'] ?? 0,
            'thuc_nhan' => $thucNhan,
            'phuong_thuc' => $validated['phuong_thuc'],
            'trang_thai' => $validated['trang_thai'],
            'ghi_chu' => $validated['ghi_chu'] ?? '',
        ]);

        return response()->json($transaction->load('user'));
    }

    public function destroy($id)
    {
        DepositHistory::findOrFail($id)->delete();
        return response()->json(['message' => 'Đã xoá thành công'], 200);
    }


}