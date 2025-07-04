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
                $deposit = DepositHistory::create([
                    'ma_nguoi_dung' => $user->MaNguoiDung,
                    'so_tien' => $request->amount,
                    'khuyen_mai' => $request->khuyen_mai ?? 0,
                    'thuc_nhan' => $request->amount + ($request->amount * ($request->khuyen_mai / 100 ?? 0)),
                    'phuong_thuc' => 'ZaloPay',
                    'trang_thai' => 'Hoàn tất',
                    'ghi_chu' => 'Nạp tiền ZaloPay',
                    'ma_giao_dich' => $data['app_trans_id'],
                    'ngay_nap' => now(),
                ]);
                $user->so_du += $deposit->thuc_nhan;
                $user->save();

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

        $dataToSign = $data['data'] . '|' . $key2;
        $mac = hash_hmac('sha256', $dataToSign, $key2);

        // \Log::info('ZaloPay Callback:', $data);

        if ($mac === $data['mac']) {
            $callbackData = json_decode($data['data'], true);

            $maGiaoDich = $callbackData['app_trans_id'];
            $amount = $callbackData['amount'];

            $transaction = DepositHistory::where('ma_giao_dich', $maGiaoDich)->first();
            if ($transaction && $transaction->trang_thai !== 'Hoàn tất') {
                $transaction->update([
                    'trang_thai' => 'Hoàn tất',
                ]);

                $user = $transaction->user;
                $user->so_du += $transaction->thuc_nhan;
                $user->save();
            }

            return response()->json(['return_code' => 1, 'return_message' => 'Success']);
        }

        return response()->json(['return_code' => -1, 'return_message' => 'Invalid MAC'], 400);
    }
}
