<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'MaThongBao',
        'MaNguoiDung',
        'NoiDung',
        'TrangThai',
        'LoaiThongBao',
        'created_at',
        'updated_at'
    ];
    public $timestamps = true;
}
