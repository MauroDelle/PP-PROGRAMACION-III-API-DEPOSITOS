<?php
include_once 'Deposito.php';


if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['tipoCuenta'], $_POST['nroCuenta'], $_POST['moneda'], $_POST['importe']) && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK)
    {
        // Obtener los datos del formulario
        $imagen = $_FILES['imagen'];
        $tipoCuenta = $_POST['tipoCuenta'];
        $nroCuenta = $_POST['nroCuenta'];
        $moneda = $_POST['moneda'];
        $importe = floatval($_POST['importe']); // Convertir el importe a tipo float

        $resultado = Deposito::Depositar($tipoCuenta,$nroCuenta,$moneda,$importe,$imagen);
        
    }
    else {
        echo 'Faltan datos en la solicitud.';
    }
}
else {
    echo "Método de solicitud incorrecto. Debe ser una solicitud POST.";
}


?>