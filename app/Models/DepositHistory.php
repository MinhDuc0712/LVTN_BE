<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositHistory extends Model
{
    //
    protected $table = 'deposit_history';
    protected $fillable = [
        'ma_nguoi_dung',
        'so_tien',
        'khuyen_mai',
        'thuc_nhan',
        'phuong_thuc',
        'ma_giao_dich',
        'trang_thai',
        'ghi_chu',
        'ngay_nap'
    ];
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class, 'ma_nguoi_dung', 'MaNguoiDung');
    }

}
