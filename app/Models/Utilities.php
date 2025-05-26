<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Utilities extends Model
{
    //
protected $table = 'utilities';
    protected $primaryKey = 'MaTienIch'; 
    public $timestamps = true; 
    
    protected $fillable = [
        'TenTienIch', 
    ];

    
    public function houses(): BelongsToMany
    {
        return $this->belongsToMany(
            House::class,     
            'house_utility',   
            'MaTienIch',        
            'MaNha'          
        );
    }
}
