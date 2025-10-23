<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Datos reales (mu) — múltiples filas por DNI si tiene varias oficinas
        $rows = [
            ["dni" => "72014598", "nombres" => "ANDERSON", "ap_paterno" => "GIBAJA", "ap_materno" => "HERRERA", "abreviatura" => "CONT"],
            ["dni" => "46799610", "nombres" => "DENIS MARCELINO", "ap_paterno" => "HUANCA", "ap_materno" => "QUISPE", "abreviatura" => "TD"],
            ["dni" => "46799610", "nombres" => "DENIS MARCELINO", "ap_paterno" => "HUANCA", "ap_materno" => "QUISPE", "abreviatura" => "ABA"],
            ["dni" => "45795743", "nombres" => "JOSE LUIS", "ap_paterno" => "USCAMAYTA", "ap_materno" => "HUAMAN", "abreviatura" => "TI"],
            ["dni" => "45795743", "nombres" => "JOSE LUIS", "ap_paterno" => "USCAMAYTA", "ap_materno" => "HUAMAN", "abreviatura" => "GG"],
            ["dni" => "45795743", "nombres" => "JOSE LUIS", "ap_paterno" => "USCAMAYTA", "ap_materno" => "HUAMAN", "abreviatura" => "TD"],
            ["dni" => "45795743", "nombres" => "JOSE LUIS", "ap_paterno" => "USCAMAYTA", "ap_materno" => "HUAMAN", "abreviatura" => "CONT"],
            ["dni" => "72519718", "nombres" => "BRYAN", "ap_paterno" => "ZAMBRANO", "ap_materno" => "RAMOS", "abreviatura" => "TD"],
            ["dni" => "77696559", "nombres" => "EDISON", "ap_paterno" => "HUAMAN", "ap_materno" => "", "abreviatura" => "TD"],
            ["dni" => "47996942", "nombres" => "KAREN", "ap_paterno" => "CHACON", "ap_materno" => "SANCHEZ", "abreviatura" => "SST"],
            ["dni" => "72743723", "nombres" => "ZARANTA", "ap_paterno" => "MENDOZA", "ap_materno" => "CABRERA", "abreviatura" => "GG"],
            ["dni" => "23842358", "nombres" => "ASENCION", "ap_paterno" => "HUAMANRICRA", "ap_materno" => "LAURA", "abreviatura" => "ABA"],
            ["dni" => "23842358", "nombres" => "ASENCION", "ap_paterno" => "HUAMANRICRA", "ap_materno" => "LAURA", "abreviatura" => "CONT. PATR."],
            ["dni" => "23836817", "nombres" => "ADOLFO", "ap_paterno" => "QUISPE", "ap_materno" => "CUSI", "abreviatura" => "ST"],
            ["dni" => "23836817", "nombres" => "ADOLFO", "ap_paterno" => "QUISPE", "ap_materno" => "CUSI", "abreviatura" => "CSST"],
            ["dni" => "72373050", "nombres" => "YEFREY ALFREDO", "ap_paterno" => "LUCANA", "ap_materno" => "GARCIA", "abreviatura" => "GG"],
            ["dni" => "43412746", "nombres" => "NILDA", "ap_paterno" => "VARGAS", "ap_materno" => "HUALLPARIMACHI", "abreviatura" => "CONT"],
            ["dni" => "72963017", "nombres" => "CLAUDIA VANESSA", "ap_paterno" => "QUISPE", "ap_materno" => "CASTRO", "abreviatura" => "RH"],
            ["dni" => "24001823", "nombres" => "YOMPIAN PASCUAL", "ap_paterno" => "BERRIO", "ap_materno" => "FERNANDEZ", "abreviatura" => "ASE"],
            ["dni" => "40803582", "nombres" => "YURISSIE KARLA", "ap_paterno" => "SOLIS", "ap_materno" => "CHALLCO", "abreviatura" => "TES"],
            ["dni" => "45789315", "nombres" => "JULIO MANUEL", "ap_paterno" => "OCHOA", "ap_materno" => "GARCIA", "abreviatura" => "CULDEP"],
            ["dni" => "47372011", "nombres" => "DANITZA SOLEDAD", "ap_paterno" => "FARFAN", "ap_materno" => "GIBAJA", "abreviatura" => "ARCH"],
            ["dni" => "23858687", "nombres" => "ANTONIO", "ap_paterno" => "QUISPE", "ap_materno" => "ALTAMIRANO", "abreviatura" => "GD"],
            ["dni" => "23990145", "nombres" => "DENNIS", "ap_paterno" => "ESTRADA", "ap_materno" => "CHAVEZ", "abreviatura" => "PP"],
            ["dni" => "72317767", "nombres" => "JOSE ANGEL", "ap_paterno" => "CALLUCHI", "ap_materno" => "PIEDRA", "abreviatura" => "GG"],
            ["dni" => "12345678", "nombres" => "SECRETARIA GENERAL", "ap_paterno" => "SG", "ap_materno" => "SG", "abreviatura" => "SG"],
            ["dni" => "40078420", "nombres" => "CLORINDA", "ap_paterno" => "MATTO", "ap_materno" => "HUAMAN", "abreviatura" => "PROY"],
            ["dni" => "25311179", "nombres" => "BENEDICTO", "ap_paterno" => "TAPIA", "ap_materno" => "MENDOZA", "abreviatura" => "OM"],
            ["dni" => "73793455", "nombres" => "MARILUZ", "ap_paterno" => "HERRERA", "ap_materno" => "HUAMAN", "abreviatura" => "CONT"],
            ["dni" => "73081085", "nombres" => "DERLY", "ap_paterno" => "INCAROCA", "ap_materno" => "GAMARRA", "abreviatura" => "TES"],
            ["dni" => "60341003", "nombres" => "ANALI", "ap_paterno" => "GUZMAN", "ap_materno" => "QUISPE", "abreviatura" => "ABA"],
            ["dni" => "44576705", "nombres" => "RICARDO LORENZO", "ap_paterno" => "CORIA", "ap_materno" => "LOPEZ", "abreviatura" => "EI"],
            ["dni" => "46924768", "nombres" => "CHARLES", "ap_paterno" => "OQUENDO", "ap_materno" => "SULLA", "abreviatura" => "RRPP"],
            ["dni" => "76675437", "nombres" => "ANTONY JUAN", "ap_paterno" => "LEYVA", "ap_materno" => "SAVEDRA", "abreviatura" => "ALMCEN"],
            ["dni" => "76675437", "nombres" => "ANTONY JUAN", "ap_paterno" => "LEYVA", "ap_materno" => "SAVEDRA", "abreviatura" => "ABA"],
            ["dni" => "45099842", "nombres" => "CESAR AUGUSTO", "ap_paterno" => "SANCHEZ", "ap_materno" => "URQUIZO", "abreviatura" => "MNT"],
            ["dni" => "47254491", "nombres" => "EDGAR", "ap_paterno" => "MONTES", "ap_materno" => "FERNANDEZ", "abreviatura" => "TD"],
            ["dni" => "76162191", "nombres" => "RUTH", "ap_paterno" => "HUAMAN", "ap_materno" => "SOLIS", "abreviatura" => "TD"],
            ["dni" => "43793059", "nombres" => "LUIS", "ap_paterno" => "ARAGON", "ap_materno" => "ESPINOZA", "abreviatura" => "MNT"],
            ["dni" => "73232970", "nombres" => "ANTHUANNE", "ap_paterno" => "FARFAN", "ap_materno" => "RICALDE", "abreviatura" => "PROY"],
            ["dni" => "72429153", "nombres" => "JOSELUIS", "ap_paterno" => "CUTIMBO", "ap_materno" => "ALVAREZ", "abreviatura" => "CULDEP"],
            ["dni" => "42260206", "nombres" => "HEDI", "ap_paterno" => "LUNA", "ap_materno" => "MEZA", "abreviatura" => "DIR"],
            ["dni" => "73801664", "nombres" => "MARK SCOTT", "ap_paterno" => "ZANES", "ap_materno" => "CANDIA", "abreviatura" => "TI"],
            ["dni" => "72214663", "nombres" => "MARIA MILAGROS", "ap_paterno" => "CABALLERO", "ap_materno" => "CHACON", "abreviatura" => "SG"],
            ["dni" => "72383718", "nombres" => "JHANICE ARHANZAZU", "ap_paterno" => "OLAYA", "ap_materno" => "MEDINA", "abreviatura" => "TD"],
            ["dni" => "45919890", "nombres" => "TANIA", "ap_paterno" => "MILGROS", "ap_materno" => "LOPEZ", "abreviatura" => "CONT. PATR."],
            ["dni" => "70750320", "nombres" => "LEIDY YESENIA", "ap_paterno" => "SAAVEDRA", "ap_materno" => "SANCHEZ", "abreviatura" => "PSICO"],
            ["dni" => "75730455", "nombres" => "JHENNIFER", "ap_paterno" => "PACO", "ap_materno" => "GUILLEN", "abreviatura" => "TES"],
            ["dni" => "46964513", "nombres" => "CARLOS ALBERTO", "ap_paterno" => "SARAVIA", "ap_materno" => "GIBAJA", "abreviatura" => "PAD"],
            ["dni" => "71443333", "nombres" => "RADOMIRO NHOLAN", "ap_paterno" => "BUSTAMANTE", "ap_materno" => "CORDOVA", "abreviatura" => "RH"],
        ];

        // Agrupa por DNI
        $porDni = [];
        foreach ($rows as $r) {
            $dni = trim($r['dni'] ?? '');
            if ($dni === '')
                continue;
            $porDni[$dni][] = $r;
        }

        foreach ($porDni as $dni => $entradas) {
            $first = $entradas[0];

            $nombres = trim($first['nombres'] ?? '');
            $apPat = trim($first['ap_paterno'] ?? '');
            $apMat = trim($first['ap_materno'] ?? '');

            // Área principal = primera abreviatura
            $primaryAbr = trim($first['abreviatura'] ?? '');
            $primary = $primaryAbr !== '' ? Area::where('codigo', $primaryAbr)->first() : null;

            // ¿Es admin? => si alguna de sus oficinas es TI
            $isAdmin = false;
            foreach ($entradas as $e) {
                if (trim(($e['abreviatura'] ?? '')) === 'TI') {
                    $isAdmin = true;
                    break;
                }
            }
            $rol = $isAdmin ? 'Admin' : 'Usuario';

            $user = User::updateOrCreate(
                ['dni' => $dni],
                [
                    'nombres' => $nombres,
                    'apellido_paterno' => $apPat,
                    'apellido_materno' => $apMat,
                    'email' => strtolower($dni) . '@demo.local',
                    'celular' => null,
                    'password' => Hash::make($dni), // 👈 contraseña = DNI
                    'foto_path' => null,
                    'primary_area_id' => $primary?->id,
                    'rol' => $rol,
                    'estado' => 'Activo',
                ]
            );

            // Todas las áreas (pivot)
            $abrs = [];
            foreach ($entradas as $e) {
                $abr = trim($e['abreviatura'] ?? '');
                if ($abr !== '')
                    $abrs[$abr] = true;
            }
            $areaIds = Area::whereIn('codigo', array_keys($abrs))->pluck('id')->all();
            $user->areas()->sync($areaIds);
        }
    }
}
