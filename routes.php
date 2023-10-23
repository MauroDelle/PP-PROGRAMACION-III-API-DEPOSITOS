<?php

return [
    'GET'=>[
        'Movimientos' => 'Clases/ConsultaMovimientos.php',
    ],
    'POST' => [
        'Alta' => 'Clases/CuentaAlta.php',
        'Consulta' => 'Clases/ConsultarCuenta.php',
        'Deposito' => 'Clases/DepositoCuenta.php',
        'Extraccion'=> 'Clases/RetiroCuenta.php',
        'Ajuste'=> 'Clases/AjusteCuenta.php'
    ],
    'PUT' =>
    [
        'Modificar' => 'Clases/ModificarCuenta.php',
    ],
    'DELETE' =>
    [
        'BorrarVenta' => 'BorrarVenta.php',
    ],
];

?>