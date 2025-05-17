<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilities extends Model
{
    //
    protected $table = 'utilities';
    protected $fillable = [
        'MaTienIch',
        'TenTienIch',
        'MaNha',
        'MoTa',
    ];
    public $timestamps = false;
}
