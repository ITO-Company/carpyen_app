<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;

    protected $table = 'paginas_visitadas';

    protected $fillable = [
        'nombre_pagina',
        'pagina_url',
        'contador_vistas',
    ];

    public static function incrementVisit($pageName, $pageUrl)
    {
        $visit = self::firstOrCreate(
            ['pagina_url' => $pageUrl],
            ['nombre_pagina' => $pageName, 'contador_vistas' => 0]
        );
        
        $visit->increment('contador_vistas');
        
        return $visit;
    }
}
