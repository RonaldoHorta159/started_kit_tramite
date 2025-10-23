<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        // Datos reales (tipo_documento)
        $rows = [
            'ANEXO',
            'ACTA',
            'CARTA',
            'CARTA CIRCULAR',
            'CARTA MÚLTIPLE',
            'CARTA NOTARIAL',
            'CIRCULAR',
            'CONTRATO',
            'CONVENIO',
            'DECLARACIÓN JURADA',
            'DICTAMEN',
            'EXPEDIENTE',
            'INFORME',
            'INFORME CIRCULAR',
            'INFORME MULTIPLE',
            'INVITACION',
            'MEMO MÚLTIPLE',
            'MEMORANDUM',
            'MEMORANDUM CIRCULAR',
            'MEMORIAL',
            'NOTIFICACIÓN',
            'OFICIO',
            'OFICIO CIRCULAR',
            'OFICIO MÚLTIPLE',
            'REQUERIMIENTO',
        ];

        foreach ($rows as $nombre) {
            $nombre = trim($nombre);
            if ($nombre === '')
                continue;

            TipoDocumento::updateOrCreate(
                ['nombre' => $nombre],
                ['estado' => 'Activo']
            );
        }
    }
}
