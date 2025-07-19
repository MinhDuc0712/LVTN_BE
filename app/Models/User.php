<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'users';
    protected $primaryKey = 'MaNguoiDung';
    public $timestamps = false;

    protected $fillable = ['HoTen', 'Email', 'SDT', 'Password', 'HinhDaiDien', 'DiaChi', 'so_du', 'TrangThai', 'LyDoCam'];

    protected $hidden = ['Password'];

    public function getAuthIdentifierName()
    {
        return 'SDT';
    }
    public function depositHistories()
    {
        return $this->hasMany(DepositHistory::class, 'ma_nguoi_dung', 'MaNguoiDung');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'MaNguoiDung', 'MaQuyen');
    }

    public function getRoleAttribute()
    {
        return $this->roles->first()->TenQuyen ?? 'guest';
    }
    public function houses()
    {
        return $this->hasMany(House::class, 'MaNguoiDung', 'MaNguoiDung');
    }
    public function khach()
    {
        return $this->hasOne(Khach::class, 'MaNguoiDung');
    }
}
