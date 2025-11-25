<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diseno;
use App\Models\Proyecto;

class DisenosSeeder extends Seeder
{
    public function run(): void
    {
        $imagenesDiseno = [
            'https://planner5d.com/blog/content/images/2023/02/r-architecture-rOk4VSMS3Ck-unsplash.jpg',
            'https://media1.amarilo.com.co/website/s3fs-public/2023-12/disen%CC%83o-de-interiores.webp',
            'https://i.pinimg.com/474x/63/4e/d6/634ed6c0b74885239969c26478654bda.jpg',
            'https://cei.es/wp-content/uploads/estilos-en-el-disen%CC%83o-de-interiores-scaled.jpg',
            'https://aimaestudio.es/wp-content/uploads/2024/01/diseno-interiores-sostenible.jpg',
            'https://blog.urbansa.co/hs-fs/hubfs/shutterstock_2290526749-2.jpg?width=620&height=413&name=shutterstock_2290526749-2.jpg'
        ];
        
        $descripcionesDiseno = [
            'Diseño moderno con acabados en madera natural y tonos cálidos. Incluye iluminación LED integrada.',
            'Estilo contemporáneo con líneas minimalistas. Combinación de materiales nobles y funcionalidad.',
            'Diseño elegante con detalles personalizados. Aprovechamiento máximo del espacio disponible.',
            'Propuesta moderna con enfoque en ergonomía. Acabados premium y atención al detalle.',
            'Diseño innovador con soluciones inteligentes de almacenamiento y distribución del espacio.',
            'Estilo clásico renovado con toques modernos. Materiales de alta calidad y durabilidad.'
        ];

        // Obtener todos los diseños existentes
        $disenos = Diseno::with('proyecto')->get();

        if ($disenos->isEmpty()) {
            echo "❌ No hay diseños en la base de datos. Ejecuta primero: php artisan migrate:fresh --seed\n";
            return;
        }

        foreach ($disenos as $index => $diseno) {
            $indexImagen = $index % count($imagenesDiseno);
            $indexDescripcion = $index % count($descripcionesDiseno);
            
            $diseno->update([
                'url_render' => $imagenesDiseno[$indexImagen],
                'descripcion' => $descripcionesDiseno[$indexDescripcion],
                'estado' => $diseno->proyecto->estado === 'completado' ? 'completado' : 
                           ($diseno->proyecto->estado === 'en_proceso' ? 'en_proceso' : 'pendiente'),
                'fecha_inicio' => $diseno->proyecto->estado !== 'pendiente' ? now()->subDays(20) : null,
                'fecha_fin' => $diseno->proyecto->estado === 'completado' ? now()->subDays(5) : null,
            ]);
        }

        echo "✅ Se actualizaron " . $disenos->count() . " diseños con imágenes reales\n";
    }
}
