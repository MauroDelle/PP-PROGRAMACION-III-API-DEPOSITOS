<?php
include_once 'CuentaBancaria.php';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['tipoCuenta']) && isset($_POST['nroCuenta']))
    {
        $tipoCuenta = $_POST['tipoCuenta'];
        $nroCuenta = $_POST['nroCuenta'];

        //realizo la consulta
        $resultado = CuentaBancaria::consultarCuenta($tipoCuenta,$nroCuenta);

        echo "$resultado";
     
    }
    
} else {
    echo "Método de solicitud incorrecto. Debe ser una solicitud POST.";
}





?>