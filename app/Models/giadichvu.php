<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Giadichvu extends Model
{
    use HasFactory;
public $timestamps = false;
    protected $table = 'giadichvu'; 
    
    protected $fillable = [
        'ten',
        'gia_tri',
        'ngay_ap_dung',
    ];
}
