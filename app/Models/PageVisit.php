<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'page_url',
        'visit_count',
    ];

    public static function incrementVisit($pageName, $pageUrl)
    {
        $visit = self::firstOrCreate(
            ['page_url' => $pageUrl],
            ['page_name' => $pageName, 'visit_count' => 0]
        );
        
        $visit->increment('visit_count');
        
        return $visit;
    }
}
