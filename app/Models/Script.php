<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    use HasFactory;
    protected $fillable = [
        'Drug_Name',
        'Class',
        'NDC',
        'Date',
        'Script',
        'Ins' // Add this line
    ];
    
    public $timestamps=false;
}
