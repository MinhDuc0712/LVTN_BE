<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Khach extends Model
{
    use HasFactory;
    protected $table = 'khach';
    protected $fillable = ['ho_ten', 'cmnd', 'sdt', 'email', 'dia_chi'];
    public $timestamps = false;

    public function hopdongs()
    {
        return $this->hasMany(Hopdong::class);
    }
}
