<?php
require_once 'CuentaBancaria.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Obtener los datos del cuerpo de la solicitud PUT
    $putData = file_get_contents("php://input");
    
    // Convertir los datos del cuerpo a un array asociativo
    parse_str($putData, $putParams);
    
    if (isset($putParams['tipoCuenta']) && isset($putParams['nroCuenta']) &&
        isset($putParams['nombre']) && isset($putParams['apellido']) &&
        isset($putParams['tipoDocumento']) && isset($putParams['nroDocumento']) &&
        isset($putParams['email']) && isset($putParams['moneda'])) {
        
        $tipoCuenta = $putParams['tipoCuenta'];
        $nroCuenta = $putParams['nroCuenta'];
        
        // Crear un array con los datos necesarios
        $nuevosDatos = [
            'nombre' => $putParams['nombre'],
            'apellido' => $putParams['apellido'],
            'tipoDocumento' => $putParams['tipoDocumento'],
            'nroDocumento' => $putParams['nroDocumento'],
            'email' => $putParams['email'],
            'moneda' => $putParams['moneda']
        ];
        
        $resultado = CuentaBancaria::ModificarCuenta($tipoCuenta, $nroCuenta, $nuevosDatos);
        
        // $resultado contendrá el mensaje de éxito o error de la operación
        echo $resultado;
    } else {
        echo 'Error';
    }
} else {
    echo "Método de solicitud incorrecto. Debe ser una solicitud PUT.";
}





?>