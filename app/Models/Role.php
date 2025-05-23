<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'MaQuyen';
    protected $fillable = ['TenQuyen', 'MoTaQuyen'];
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'MaQuyen', 'MaNguoiDung');
    }
}
