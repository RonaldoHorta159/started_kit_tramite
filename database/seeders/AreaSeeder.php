<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        // Datos reales (man_oficina)
        $oficinas = [
            ['id' => 1, 'nombre' => 'TRAMITE DOCUMENTARIO', 'abreviatura' => 'TD'],
            ['id' => 2, 'nombre' => 'GERENCIA GENERAL', 'abreviatura' => 'GG'],
            ['id' => 3, 'nombre' => 'GESTION DEL TALENTO HUMANO', 'abreviatura' => 'RH'],
            ['id' => 4, 'nombre' => 'LOGISTICA Y ABASTECIMIENTO', 'abreviatura' => 'ABA'],
            ['id' => 5, 'nombre' => 'CONTABILIDAD Y COSTOS', 'abreviatura' => 'CONT'],
            ['id' => 6, 'nombre' => 'ASESORIA JURIDICA', 'abreviatura' => 'ASE'],
            ['id' => 7, 'nombre' => 'TESORERIA Y CONTROL FINANCIERO', 'abreviatura' => 'TES'],
            ['id' => 8, 'nombre' => 'PLANEAMIENTO Y PRESUPUESTO', 'abreviatura' => 'PP'],
            ['id' => 9, 'nombre' => 'ARCHIVO', 'abreviatura' => 'ARCH'],
            ['id' => 10, 'nombre' => 'ASISTENCIA SOCIAL', 'abreviatura' => 'PROY'],
            ['id' => 11, 'nombre' => 'SERVICIOS TURISTICOS', 'abreviatura' => 'ST'],
            ['id' => 13, 'nombre' => 'SECRETARIA GENERAL', 'abreviatura' => 'SG'],
            ['id' => 14, 'nombre' => 'TECNOLOGIAS DE INFORMACION', 'abreviatura' => 'TI'],
            ['id' => 15, 'nombre' => 'DEPARTAMENTO DE ALMACEN', 'abreviatura' => 'ALMCEN'],
            ['id' => 16, 'nombre' => 'CULTURA Y DEPORTE', 'abreviatura' => 'CULDEP'],
            ['id' => 17, 'nombre' => 'OFICINA MACHUPICCHU', 'abreviatura' => 'OM'],
            ['id' => 18, 'nombre' => 'GUARDIANIA', 'abreviatura' => 'GD'],
            ['id' => 19, 'nombre' => 'DEPARTAMENTO DE PATRIMONIO', 'abreviatura' => 'CONT. PATR.'],
            ['id' => 20, 'nombre' => 'RELACIONES PUBLICAS Y MARKETING', 'abreviatura' => 'RRPP'],
            ['id' => 21, 'nombre' => 'ENLACE INTERINSTITUCIONAL', 'abreviatura' => 'EI'],
            ['id' => 22, 'nombre' => 'MANTENIMIENTO', 'abreviatura' => 'MNT'],
            ['id' => 23, 'nombre' => 'DIRECTORIO', 'abreviatura' => 'DIR'],
            ['id' => 24, 'nombre' => 'PROCEDIMIENTO ADMINISTRATIVO DISCIPLINARIO', 'abreviatura' => 'PAD'],
            ['id' => 25, 'nombre' => 'DEPARTAMENTO DE PSICOLOGIA', 'abreviatura' => 'PSICO'],
            ['id' => 26, 'nombre' => 'COMITE DE SEGURIDAD Y SALUD', 'abreviatura' => 'CSST'],
            ['id' => 27, 'nombre' => 'SEGURIDAD Y SALUD EN EL TRABAJO', 'abreviatura' => 'SST'],
        ];

        foreach ($oficinas as $o) {
            Area::updateOrCreate(
                ['codigo' => trim($o['abreviatura'])],
                [
                    'nombre' => trim($o['nombre']),
                    'estado' => 'Activo',
                ]
            );
        }
    }
}
