<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;
    protected $fillable = [
        'drug_name',
        'drug_class',
        'ndc',
        'mfg',
        'form', 
        'strength'// Add this line
    ];
    
    public $timestamps=false;
    public function setDrugNameAttribute($value)
    {
        $this->attributes['drug_name'] = trim($value);
    }

    public function getDrugNameAttribute($value)
    {
        return trim($value);
    }


}
