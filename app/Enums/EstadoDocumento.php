<?php

namespace App\Enums;

enum EstadoDocumento: string
{
    case RECIBIDO = 'Recibido';
    case EN_TRAMITE = 'En Trámite';
    case ARCHIVADO = 'Archivado';
    case ATENDIDO = 'Atendido';
    case RECHAZADO = 'Rechazado';
}
