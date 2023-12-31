<?php
include_once 'Deposito.php';
include_once 'CuentaBancaria.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['tipoCuenta']) && isset($_GET['moneda']))
    {
        // Obtiene los valores de los parámetros desde la URL
        $tipoCuenta = $_GET['tipoCuenta'];
        $moneda = $_GET['moneda'];

        // Verifica si se proporcionó la fecha en los parámetros de la URL
        if (isset($_GET['fecha'])) {
            $fecha = $_GET['fecha'];
        } else {
            $fecha = null; // Si no se proporciona una fecha, se calculará para el día anterior
        }
        $totalDepositado = Deposito::calcularTotalPorTipoCuentaYMoneda($tipoCuenta, $moneda,$fecha);
        

        // Muestra el resultado
        echo "Total depositado por tipo de cuenta $tipoCuenta y moneda $moneda en la fecha $fecha: $totalDepositado";
    }else if (isset($_GET["idCuenta"]))
    {
        $idCuenta = $_GET['idCuenta'];

        $listadoDepositado = Deposito::listarDepositosPorUsuario($idCuenta);
        $segundoListado = CuentaBancaria::tieneDepositoyRetiro($idCuenta);

        echo "Los depositos y retiros de la cuenta: $idCuenta son:";
        var_dump($segundoListado);
    }
    else if(isset($_GET["fechaInicio"]) && isset($_GET["fechaFin"]))
    {
        $fechaInicio = $_GET["fechaInicio"];
        $fechaFin = $_GET["fechaFin"];

        $listadoDepositos = Deposito::listarDepositosEntreFechasORdenadasPorNombre($fechaInicio,$fechaFin);

        var_dump($listadoDepositos);
    }
    else if(isset($_GET["tipoCuenta"]))
    {
        $tipoCuenta = $_GET["tipoCuenta"];

        $listadoTipoCuenta = Deposito::obtenerDepositosPorTipoCuenta($tipoCuenta);

        var_dump($listadoTipoCuenta);
    }
    else if(isset($_GET["moneda"]))
    {
        $moneda = $_GET["moneda"];

        $listadoPorMoneda = Deposito::obtenerDepositosPorMoneda( $moneda );

        var_dump($listadoPorMoneda);
    }
    
    
} else {
    echo "Método de solicitud incorrecto. Debe ser una solicitud GET.";
}
