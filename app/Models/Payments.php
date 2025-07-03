<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    //
    protected $table = 'payments';
    protected $primaryKey = 'MaGiaoDich';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['MaGiaoDich', 'MaNha', 'MaNguoiDung', 'Voucher', 'PhiGiaoDich', 'TongTien'];
    public function house()
    {
        return $this->belongsTo(House::class, 'MaNha', 'MaNha');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'MaNguoiDung', 'MaNguoiDung');
    }

}
