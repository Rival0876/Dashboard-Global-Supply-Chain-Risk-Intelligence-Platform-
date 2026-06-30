<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    // TAMBAHKAN KODE INI BRO 👇
    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'region'
    ];
}