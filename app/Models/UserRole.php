<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;


class UserRole extends Model
{
    protected $table = 'user_roles';
    protected $primaryKey = ['MaNguoiDung', 'MaQuyen'];
    public $timestamps = false;

    protected $fillable = [
        'MaNguoiDung',
        'MaQuyen'
    ];
}
