<?php
include_once 'CuentaBancaria.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (isset($_POST['nombre'], $_POST['apellido'], $_POST['tipoDocumento'], $_POST['nroDocumento'], $_POST['email'], $_POST['tipoCuenta'], $_POST['moneda']))
        {
            $email = $_POST['email'];
            $moneda = $_POST['moneda'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $tipoCuenta = $_POST['tipoCuenta'];
            $nroDocumento = $_POST['nroDocumento'];
            $tipoDocumento = $_POST['tipoDocumento'];
            $saldoInicial = isset($_POST['saldoInicial']) ? $_POST['saldoInicial'] : 0;

            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK)
            {
                $imagen = $_FILES['imagen'];

                // Generar un número de cuenta autoincremental de 6 dígitos
                $numeroCuenta = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

                // Combinar número de cuenta y tipo de cuenta para la identificación de la imagen
                $imagenIdentificacion = $numeroCuenta . $tipoCuenta;      

                // Crear una instancia de la clase CuentaBancaria
                $cuenta = new CuentaBancaria($numeroCuenta, $nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoCuenta, $moneda, $saldoInicial);

                // Guardar la cuenta en el archivo "banco.json"
                $message = CuentaBancaria::ActualizarArray($cuenta, 'add');

                // Guardar la imagen en la carpeta correspondiente
                $rutaImagen = 'ImagenesDeCuentas/2023/' . $imagenIdentificacion . '.jpg';
                move_uploaded_file($imagen['tmp_name'], $rutaImagen);

                // Puedes mostrar un mensaje de éxito o redirigir a una página de confirmación
                echo "Cuenta creada exitosamente. $message";
            }
        }
    }
    else {
        echo "Método de solicitud incorrecto. Debe ser una solicitud POST.";
    }


?>