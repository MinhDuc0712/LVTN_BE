<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'users';
    protected $primaryKey = 'MaNguoiDung';
    public $timestamps = false;

    protected $fillable = ['HoTen', 'Email', 'SDT', 'Password', 'HinhDaiDien', 'DiaChi'];

    protected $hidden = ['Password', 'remember_token'];

    public function getAuthIdentifierName()
    {
        return 'SDT';
    }
}
