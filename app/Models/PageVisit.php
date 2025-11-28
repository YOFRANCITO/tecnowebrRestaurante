<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = ['page_name', 'visit_count'];

    /**
     * Incrementar el contador de visitas para una página
     */
    public static function incrementVisit(string $pageName): int
    {
        $visit = self::firstOrCreate(
            ['page_name' => $pageName],
            ['visit_count' => 0]
        );
        
        $visit->increment('visit_count');
        
        return $visit->visit_count;
    }

    /**
     * Obtener el contador de visitas para una página
     */
    public static function getVisitCount(string $pageName): int
    {
        $visit = self::where('page_name', $pageName)->first();
        return $visit ? $visit->visit_count : 0;
    }
}
