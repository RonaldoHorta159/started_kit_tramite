<?php

namespace App\Enums;

enum Rol: string
{
    case ADMIN = 'Admin';
    case USUARIO = 'Usuario';
    case MESA_DE_PARTES = 'Mesa de Partes';
}
