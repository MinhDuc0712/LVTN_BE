<?php

namespace App\Http\Controllers;

use App\Models\DepositHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepositHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DepositHistory::with('user');

        if ($userId = $request->input('ma_nguoi_dung')) {
            $query->where('ma_nguoi_dung', $userId);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('SDT', 'like', "%$search%")->orWhere('HoTen', 'like', "%$search%"))->orWhere('ma_giao_dich', 'like', "%$search%");
        }

        if ($status = $request->input('status')) {
            $query->where('trang_thai', $status);
        }

        $perPage = $request->input('per_page', 1000);
        return $query->orderBy('id', 'desc')->paginate($perPage);
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

        $thuc_nhan = $request->so_tien + $request->so_tien * ($request->khuyen_mai / 100 ?? 0);

        $user = User::where('MaNguoiDung', $request['ma_nguoi_dung'])->orWhere('SDT', $request['ma_nguoi_dung'])->firstOrFail();

        $request['ma_nguoi_dung'] = $user->MaNguoiDung;

        $transaction = DepositHistory::create([
            'ma_nguoi_dung' => $request->ma_nguoi_dung,
            'so_tien' => $request->so_tien,
            'khuyen_mai' => $request->khuyen_mai ?? 0,
            'thuc_nhan' => $thuc_nhan,
            'phuong_thuc' => $request->phuong_thuc,
            'trang_thai' => $request->trang_thai,
            'ghi_chu' => $request->ghi_chu ?? '',
            'ma_giao_dich' => 'TXN-' . Str::uuid(),
            'ngay_nap' => now()->toDateTimeString(),
        ]);
        if ($request->trang_thai === 'Hoàn tất') {
            // $user->so_du += $thuc_nhan;
            $user->save();
        }
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

        $transaction = str_starts_with($id, 'TXN-') ? DepositHistory::where('ma_giao_dich', $id)->firstOrFail() : DepositHistory::findOrFail($id);

        $thucNhan = $validated['so_tien'] + $validated['so_tien'] * ($validated['khuyen_mai'] / 100 ?? 0);

        if ($transaction->trang_thai !== 'Hoàn tất' && $validated['trang_thai'] === 'Hoàn tất' && stripos($transaction->ghi_chu, 'Thanh toán tin đăng') === false) {
            $user = $transaction->user;
            $user->so_du += $thucNhan;
            $user->save();
        }

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
        $transaction = DepositHistory::findOrFail($id);

        if ($transaction->trang_thai === 'Hoàn tất') {
            $user = $transaction->user;
            $user->so_du -= $transaction->thuc_nhan;
            $user->save();
        }

        $transaction->delete();

        return response()->json(['message' => 'Đã xoá thành công'], 200);
    }
    public function checkTransaction($ma_giao_dich)
    {
        $transaction = DepositHistory::with('user')->where('ma_giao_dich', $ma_giao_dich)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Không tìm thấy giao dịch'], 404);
        }

        return response()->json($transaction);
    }
}
