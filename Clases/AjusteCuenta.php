<?php
require_once 'Ajuste.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST['tipoTransaccion'], $_POST['idTransaccion'], $_POST['ajusteMonto'], $_POST['motivo']))
        {
            $tipoTransaccion = $_POST['tipoTransaccion'];
            $idTransaccion = $_POST['idTransaccion'];
            $ajusteMonto = $_POST['ajusteMonto'];
            $motivo = $_POST['motivo'];

            //realizo la consulta
            $resultado = Ajuste::realizarAjuste($tipoTransaccion,$idTransaccion,$ajusteMonto,$motivo);
            echo "$resultado";
        }
    }
    else {
        echo "Método de solicitud incorrecto. Debe ser una solicitud POST.";
    }




?>