<?php

require_once 'Retiro.php';


if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['tipoCuenta']) && isset($_POST['nroCuenta']) && isset($_POST['moneda']) && isset($_POST['importe']))
    {
        $tipoCuenta = $_POST['tipoCuenta'];
        $nroCuenta = $_POST['nroCuenta'];
        $moneda = $_POST['moneda'];
        $importe = $_POST['importe'];

        $resultado = Retiro::RealizarRetiro($tipoCuenta, $nroCuenta, $moneda, $importe);

        echo $resultado;
    }
    else {
        echo 'Faltan datos necesarios para el retiro.';
    }
}
else {
    echo "Método de solicitud incorrecto. Debe ser una solicitud POST.";
}



?>