<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    //
    protected $table = 'payments';
    protected $fillable = [
        'MaGiaoDich',
        'MaNha',
        'MaNguoiDung',
        'Voucher',
        'PhiGiaoDich',
        'TongTien'
    ];
}
