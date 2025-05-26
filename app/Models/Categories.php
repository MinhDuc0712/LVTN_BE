<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categories extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $primaryKey = 'MaDanhMuc';
    protected $fillable = [
        'name',
        'mo_ta',
    ];
    public $timestamps = false;
}
