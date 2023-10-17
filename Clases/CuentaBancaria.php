<?php

class CuentaBancaria
{

    #region ATRIBUTOS
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $nroDocumento;
    public $email;
    public $tipoCuenta;
    public $moneda;
    public $saldoInicial;
    
    #endregion

    #region CONSTRUCT

    public function __construct($nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoCuenta, $moneda, $saldoInicial = 0)
    {
        $this->setNombre($nombre);
        $this->setApellido($apellido);
        $this->setTipoDocumento($tipoDocumento);
        $this->setNroDocumento($nroDocumento);
        $this->setEmail($email);
        $this->setTipoCuenta($tipoCuenta);
        $this->setMoneda($moneda);
        $this->setSaldoInicial($saldoInicial);
    }


    #endregion

    #region SETTERS

    public function setNombre($nombre)
    {
        if(is_string($nombre) && !empty($nombre))
        {
            $this->nombre = $nombre;
        }
        else{
            echo "Error! Nombre no válido";
        }
    }

    public function setApellido($apellido)
    {
        if(is_string($apellido) && !empty($apellido))
        {
            $this->apellido = $apellido;
        }
        else
        {
            echo "Error! Apellido no válido";
        }
    }

    public function setTipoDocumento($tipoDocumento)
    {
        //Valido el tipo de documento
        if($tipoDocumento === 'DNI' || $tipoDocumento === 'LC' ||  $tipoDocumento === 'LE') //LC = LIBRETA CIVICA. LE = LIBRETA ENRROLAMIENTO
        {
            $this->tipoDocumento = $tipoDocumento;
        }
        else
        {
            echo "Error! Tipo de docuemento no válido";
        }
    }

    public function setNroDocumento($nroDocumento)
    {
        if(is_numeric($nroDocumento) && $nroDocumento > 0 )
        {
            $this->nroDocumento = $nroDocumento;
        }
        else
        {
            echo "Error! Número de documento no válido";
        }
    }

    public function setEmail($email)
    {
        if(filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $this->email = $email;
        }
        else
        {
            echo "Error! Mail no válido";
        }
    }

    public function setTipoCuenta($tipoCuenta)
    {
        if($tipoCuenta === 'CA' || $tipoCuenta === 'CC')
        {
            $this->tipoCuenta = $tipoCuenta;
        }
        else
        {
            echo "Error! Tipo de cuenta no válida";
        }      
    }

    public function setMoneda($moneda)
    {
        if($moneda === '$' || $moneda === 'U$S')
        {
            $this->moneda = $moneda;
        }
        else{
            echo "Error! Moneda no válida";
        }
    }

    public function setSaldoInicial($saldoInicial)
    {
        if(is_numeric($saldoInicial) && $saldoInicial >= 0)
        {
            $this->saldoInicial = $saldoInicial;
        }
        else
        {
            echo "Error: Saldo inicial no válido";
        }
    }




    #endregion

    #region GETTERS

    public function getNombre()
    {
        return $this->nombre;
    }
    public function getApellido()
    {
        return $this->apellido;
    }

    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    public function getNroDocumento()
    {
        return $this->nroDocumento;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTipoCuenta()
    {
        return $this->tipoCuenta;
    }

    public function getMoneda()
    {
        return $this->moneda;
    }

    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }


    #endregion

    #region MÉTODOS-BUSCAR
    public function Equals($obj): bool {
        if (get_class($obj) == "CuentaBancaria" &&
            $obj->nombre === $this->nombre &&
            $obj->apellido === $this->apellido &&
            $obj->tipoCuenta === $this->tipoCuenta) {
            return true;
        }
        return false;
    }
    public static function Buscar($array,$nombre,$apellido,$tipoCuenta)
    {
        $message = "";
        $sNombre = false;
        $sApellido = false;
        $sTipoCuenta = false;

        foreach ($array as $cuenta) {
            if ($cuenta->nombre === $nombre) {
                $sNombre = true;
            }
            if ($cuenta->apellido === $apellido) {
                $sApellido = true;
            }
            if ($cuenta->tipoCuenta === $tipoCuenta) {
                $sTipoCuenta = true;
            }
        }

        if ($sNombre && $sApellido && $sTipoCuenta) {
            $message = 'Se encontró una cuenta con nombre ' . $nombre . ', apellido ' . $apellido . ' y tipo ' . $tipoCuenta;
        } else if ($sNombre && $sApellido) {
            $message = 'Se encontró una cuenta con nombre ' . $nombre . ' y apellido ' . $apellido;
        } else if ($sNombre && $sTipoCuenta) {
            $message = 'Se encontró una cuenta con nombre ' . $nombre . ' y tipo ' . $tipoCuenta;
        } else if ($sApellido && $sTipoCuenta) {
            $message = 'Se encontró una cuenta con apellido ' . $apellido . ' y tipo ' . $tipoCuenta;
        } else if ($sNombre) {
            $message = 'Se encontró una cuenta con nombre ' . $nombre;
        } else if ($sApellido) {
            $message = 'Se encontró una cuenta con apellido ' . $apellido;
        } else if ($sTipoCuenta) {
            $message = 'Se encontró una cuenta con tipo ' . $tipoCuenta;
        } else {
            $message = 'No se encontraron cuentas con los criterios especificados';
        }

        return $message;
    }
    public function BuscarEnArray($arrayCuentas): bool {
        // Primero verifico si el array no está vacío;
        if (!empty($arrayCuentas)) {
            echo "El array no está vacío<br>";
            // Itero a través de cada cuenta bancaria en el array;
            foreach ($arrayCuentas as $cuenta) {
                // Compruebo si la cuenta bancaria actual es igual a la cuenta en cuestión;
                if ($this->Equals($cuenta)) {
                    return true;
                }
            }
        } else {
            echo 'El Array está vacío <br>';
        }

        return false;
    }
    #endregion


    public static function ActualizarArray($cuenta,$action):string
    {

        // Ruta del archivo JSON
        $filePath = 'banco.json';
        // El mensaje a devolver
        $message = '';
        // Ahora leo el contenido del archivo JSON y obtengo el array de cuentas bancarias
        $arrayDeCuentas = self::LeerJSON($filePath);

        // Si la cuenta no existe en el array, la agrego
        if (!$cuenta->BuscarEnArray($arrayDeCuentas)) {
            if ($action == "add") {
                array_push($arrayDeCuentas, $cuenta);
                $message = "La cuenta no existe, la añado.<br>";
            }
        }else
        {
            // Si existe, la actualizo.
            foreach ($arrayDeCuentas as $unaCuenta) {
                if ($unaCuenta->Equals($cuenta)) {
                    if ($action == "add") {
                        // Si la acción es "add", actualizo el saldo inicial
                        $unaCuenta->saldoInicial += $cuenta->saldoInicial;
                        $message = "La cuenta se actualizó";
                    } else if ($action == "sub") {
                        // Si la acción es "sub", resta el saldo inicial si hay suficiente saldo
                        if ($unaCuenta->saldoInicial >= $cuenta->saldoInicial) {
                            $unaCuenta->saldoInicial -= $cuenta->saldoInicial;
                            $message = "Saldo retirado de la cuenta";
                        } else {
                            $message = "Saldo insuficiente en la cuenta";
                        }
                    }
                    break;
                }
            }
        }
        
        // Guardo el array actualizado de cuentas bancarias en el archivo JSON
        self::GuardarEnJSON($arrayDeCuentas, $filePath);
        // Devuelvo el mensaje
        return $message;
    }



    #region JSON
    public static function LeerJSON($filename = "banco.json"): array
    {
        // Creo un array vacío para almacenar las cuentas bancarias
        $cuentas = array();

        try {
            // Primero compruebo si el archivo especificado ($filename) existe en el sistema de archivos
            if (file_exists($filename)) {
                // Abro el archivo en modo lectura
                $file = fopen($filename, "r");

                if ($file) {
                    // Leo el contenido completo del archivo en una variable como una cadena JSON
                    $json = fread($file, filesize($filename));

                    // Decodifico el JSON en un array asociativo
                    $cuentasFromJson = json_decode($json, true);

                    // Itero por cada cuenta bancaria en el array decodificado;
                    foreach ($cuentasFromJson as $cuenta) {
                        // Creo una nueva instancia de la clase CuentaBancaria y agrego la cuenta al array $cuentas
                        array_push($cuentas, new CuentaBancaria(
                            $cuenta["nombre"],
                            $cuenta["apellido"],
                            $cuenta["tipoDocumento"],
                            $cuenta["nroDocumento"],
                            $cuenta["email"],
                            $cuenta["tipoCuenta"],
                            $cuenta["moneda"],
                            $cuenta["saldoInicial"]
                        ));
                    }
                }

                // Cierro el archivo después de leerlo
                fclose($file);
            }
        } catch (\Throwable $th) {
            // En caso de que ocurra una excepción, imprime un mensaje de error
            echo "Hubo un Error al leer el archivo";
        } finally {
            // Devuelvo el array de cuentas bancarias, ya sea vacío o con las cuentas leídas del archivo
            return  $cuentas;
        }
    }

    public static function GuardarEnJSON($cuentasArray, $filename = "banco.json"): bool
    {
        // Inicializo una variable para el éxito/fallo de la operación de guardado;
        $success = false;

        try {
            // Abro el archivo en modo escritura, y si no existe lo creo
            $file = fopen($filename, "w");

            if ($file) {
                // Convierte el array de cuentas bancarias en formato JSON con formato legible
                $json = json_encode($cuentasArray, JSON_PRETTY_PRINT);
                // Escribe la cadena JSON en el archivo
                fwrite($file, $json);
                // Indica que la operación de guardado fue exitosa
                $success = true;
            }
        } catch (\Throwable $th) {
            // En caso de que ocurra una excepción, imprime un mensaje de error
            echo "Error al guardar el archivo";
        } finally {
            // Cierro el archivo después de escribir en él;
            fclose($file);
            // Devuelve true si el guardado fue exitoso, de lo contrario, false;
            return $success;
        }
    }
    #endregion





}



?>