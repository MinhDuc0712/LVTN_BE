<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DepositHistory;
use App\Models\User;

class ZaloPayController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'ma_nguoi_dung' => 'required',
            'khuyen_mai' => 'nullable|numeric|min:0',
        ]);

        $user = User::where('MaNguoiDung', $request->ma_nguoi_dung)->orWhere('SDT', $request->ma_nguoi_dung)->firstOrFail();

        $app_id = config('zalopay.app_id');
        $key1 = config('zalopay.key1');
        $app_user = config('zalopay.app_user');
        $endpoint = config('zalopay.endpoint');
        $redirect_url = config('zalopay.redirect_url');

        $trans_id = now()->format('ymd') . '_' . rand(1000000, 9999999);
        $time = round(microtime(true) * 1000);
        $embed_data = json_encode(['redirecturl' => $redirect_url]);

        $data = [
            'app_id' => $app_id,
            'app_trans_id' => $trans_id,
            'app_user' => $app_user,
            'amount' => $request->amount,
            'app_time' => $time,
            'item' => '[]',
            'description' => 'Nạp tiền #' . $trans_id,
            'bank_code' => 'zalopayapp',
            'embed_data' => $embed_data,
            'callback_url' => route('zalopay.callback'),
        ];

        $data_string = implode('|', [$data['app_id'], $data['app_trans_id'], $data['app_user'], $data['amount'], $data['app_time'], $data['embed_data'], $data['item']]);
        $data['mac'] = hash_hmac('sha256', $data_string, $key1);

        // Log::info('ZaloPay Order Request:', $data);

        $response = Http::asForm()->post($endpoint, $data);

        if ($response->successful()) {
            $res = $response->json();
            if ($res['return_code'] === 1) {
                DepositHistory::create([
                    'ma_nguoi_dung' => $user->MaNguoiDung,
                    'so_tien' => $request->amount,
                    'khuyen_mai' => $request->khuyen_mai ?? 0,
                    'thuc_nhan' => $request->amount + $request->amount * ($request->khuyen_mai / 100 ?? 0),
                    'phuong_thuc' => 'ZaloPay',
                    'trang_thai' => 'Đang xử lý',
                    'ghi_chu' => 'Nạp tiền ZaloPay',
                    'ma_giao_dich' => $data['app_trans_id'],
                    'ngay_nap' => now(),
                ]);

                return response()->json($res);
            }
        }

        return response()->json(
            [
                'message' => 'ZaloPay tạo đơn hàng thất bại.',
                'details' => $response->body(),
            ],
            500,
        );
    }

    public function handleCallback(Request $request)
    {
        $key2 = config('zalopay.key2');
        $data = $request->all();

        // \Log::info('ZaloPay Callback Received:', $data);

        if (!isset($data['data'], $data['mac'])) {
            return response()->json(['return_code' => -1, 'return_message' => 'Invalid data'], 400);
        }

        $dataToSign = $data['data'] . '|' . $key2;
        $mac = hash_hmac('sha256', $dataToSign, $key2);

        if ($mac !== $data['mac']) {
            // \Log::warning('ZaloPay MAC mismatch', [
            //     'expected' => $mac,
            //     'received' => $data['mac'],
            // ]);
            return response()->json(['return_code' => -1, 'return_message' => 'Invalid MAC'], 400);
        }

        $callbackData = json_decode($data['data'], true);
        $maGiaoDich = $callbackData['app_trans_id'] ?? null;
        $amount = $callbackData['amount'] ?? 0;
        $status = $callbackData['status'] ?? 0;

        if (!$maGiaoDich) {
            return response()->json(['return_code' => -1, 'return_message' => 'Missing transaction ID'], 400);
        }

        $transaction = DepositHistory::where('ma_giao_dich', $maGiaoDich)->first();

        if (!$transaction) {
            return response()->json(['return_code' => -1, 'return_message' => 'Transaction not found'], 404);
        }

        if ($transaction->trang_thai === 'Hoàn tất' || $transaction->trang_thai === 'Hủy bỏ') {
            return response()->json(['return_code' => 1, 'return_message' => 'Already processed']);
        }

        if ($status == 1) {
            $transaction->update(['trang_thai' => 'Hoàn tất']);

            $user = $transaction->user;
            $user->so_du += $transaction->thuc_nhan;
            $user->save();

            // \Log::info("Nạp tiền thành công qua ZaloPay: $maGiaoDich");
        } else {
            $transaction->update(['trang_thai' => 'Hủy bỏ']);
            // \Log::warning("Giao dịch ZaloPay thất bại hoặc bị hủy: $maGiaoDich");
        }

        return response()->json(['return_code' => 1, 'return_message' => 'Success']);
    }
    public function checkZaloTransaction($ma_giao_dich)
    {
        $app_id = config('zalopay.app_id');
        $key1 = config('zalopay.key1');

        $mac_input = $app_id . '|' . $ma_giao_dich . '|' . $key1;
        $mac = hash_hmac('sha256', $mac_input, $key1);

        $response = Http::asForm()->post('https://sb-openapi.zalopay.vn/v2/query', [
            'app_id' => $app_id,
            'app_trans_id' => $ma_giao_dich,
            'mac' => $mac,
        ]);

        if (!$response->successful()) {
            return response()->json(['status' => 'error', 'message' => 'ZaloPay query failed'], 500);
        }

        $data = $response->json();

        $transaction = DepositHistory::where('ma_giao_dich', $ma_giao_dich)->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        // Nếu giao dịch đã hoàn tất từ trước thì không cập nhật lại
        if ($transaction->trang_thai === 'Hoàn tất') {
            return response()->json(['status' => 'success', 'transaction' => $transaction]);
        }

        if ($data['return_code'] == 1) {
            // Thành công → cập nhật trạng thái & cộng tiền
            $transaction->update(['trang_thai' => 'Hoàn tất']);

            $user = $transaction->user;
            $user->so_du += $transaction->thuc_nhan;
            $user->save();

            return response()->json(['status' => 'success', 'transaction' => $transaction]);
        } elseif ($data['return_code'] == 2) {
            // Thất bại → cập nhật trạng thái "Hủy bỏ"
            $transaction->update(['trang_thai' => 'Hủy bỏ']);

            return response()->json(['status' => 'failed', 'transaction' => $transaction]);
        } elseif ($data['return_code'] == 3) {
            // Giao dịch chưa hoàn tất, đang xử lý
            return response()->json(['status' => 'processing', 'transaction' => $transaction]);
        }

        return response()->json(['status' => 'unknown', 'data' => $data, 'transaction' => $transaction]);
    }
}
