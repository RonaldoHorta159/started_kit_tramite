<?php

namespace App\Enums;

enum EstadoMovimiento: string
{
    case RECIBIDO = 'Recibido';
    case DERIVADO = 'Derivado';
    case ATENDIDO = 'Atendido';
    case RECHAZADO = 'Rechazado';
}
