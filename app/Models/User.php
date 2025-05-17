<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'MaNguoiDung';
    public $timestamps = false;

    protected $fillable = [
        'HoTen',
        'Email',
        'SDT',
        'MatKhau',
        'HinhDaiDien',
        'DiaChi'
    ];

}
