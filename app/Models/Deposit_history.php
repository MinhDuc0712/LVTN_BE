<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit_history extends Model
{
    //
    protected $table = 'deposit_history';
    protected $fillable = [
        'so_tien',
        'khuyen_mai',
        'thuc_nhan',
        'phuong_thuc',
        'ma_giao_dich',
        'trang_thai',
        'ghi_chu',
        'ngay_nap'
    ];
    // public $timestamps = true;

}
