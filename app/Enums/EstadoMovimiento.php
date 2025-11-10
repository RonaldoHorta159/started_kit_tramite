<?php

namespace App\Enums;

enum EstadoMovimiento: string
{
    case DERIVADO = 'Derivado';
    case ATENDIDO = 'Atendido';
    case RECHAZADO = 'Rechazado';
}
