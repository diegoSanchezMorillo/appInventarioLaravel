<?php

return [
    // Días a partir de los cuales un préstamo temporal se considera "largo"
    'dias_alerta_prestamo' => (int) env('INVENTARIO_DIAS_ALERTA', 30),
];