<?php

return [
    'GET'=>[
        'Movimientos' => 'Clases/ConsultaMovimientos.php',
    ],
    'POST' => [
        'Alta' => 'Clases/CuentaAlta.php',
        'Consulta' => 'Clases/ConsultarCuenta.php',
        'Deposito' => 'Clases/DepositoCuenta.php'
    ],
    'PUT' =>
    [
        'ModificarVenta' => 'ModificarVenta.php',
    ],
    'DELETE' =>
    [
        'BorrarVenta' => 'BorrarVenta.php',
    ],
];

?>