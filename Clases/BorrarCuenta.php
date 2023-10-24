<?php
require_once 'CuentaBancaria.php';


// Verificar si la solicitud es DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
    if (isset($_GET['tipoCuenta']) && isset($_GET['nroCuenta'])) {
        $tipoCuenta = $_GET['tipoCuenta'];
        $nroCuenta = $_GET['nroCuenta'];
    
        $result = CuentaBancaria::eliminarUsuarioYMoverFoto($tipoCuenta, $nroCuenta);
        echo $result;
    } else {
        echo 'Faltan parámetros en la solicitud.';
    }
} else {
    $response = ['error' => 'Método no permitido'];
    http_response_code(405);
    echo json_encode($response);
}


?>