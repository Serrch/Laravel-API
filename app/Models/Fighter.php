<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fighter extends Model
{
    use HasFactory;
    protected $table = 'fighter';

    protected $fillable = [
        'nombre',
        'historia',
        'estilo',
        'icono',
        'img'
    ];
}
