<?php 
include_once 'CuentaBancaria.php';
include_once 'Deposito.php';
class Retiro
{
    public $_id;
    public $_fecha;
    public $_monto;
    public $_tipoCuenta;
    public $_nroCuenta;
    public $_moneda;

    public function __construct($id, $fecha, $monto, $tipoCuenta, $nroCuenta, $moneda)
    {
        $this->_id = $id;
        $this->_fecha = $fecha;
        $this->_monto = $monto;
        $this->_tipoCuenta = $tipoCuenta;
        $this->_nroCuenta = $nroCuenta;
        $this->_moneda = $moneda;
    }

    #region SETTERS

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function setFecha($fecha)
    {
        $this->_fecha = $fecha;
    }

    public function setMonto($monto)
    {
        $this->_monto = $monto;
    }

    public function setTipoCuenta($tipoCuenta)
    {
        $this->_tipoCuenta = $tipoCuenta;
    }

    public function setNroCuenta($nroCuenta)
    {
        $this->_nroCuenta = $nroCuenta;
    }

    public function setMoneda($moneda)
    {
        $this->_moneda = $moneda;
    }
    #endregion
    
    #region GETTERS

    public function getId()
    {
        return $this->_id;
    }

    public function getFecha()
    {
        return $this->_fecha;
    }

    public function getMonto()
    {
        return $this->_monto;
    }

    public function getTipoCuenta()
    {
        return $this->_tipoCuenta;
    }

    public function getNroCuenta()
    {
        return $this->_nroCuenta;
    }

    public function getMoneda()
    {
        return $this->_moneda;
    }

    #endregion


    public static function obtenerNuevoIdExtraccion()
    {
        $file = 'retiro.json';

        if (!file_exists($file)) {
            return 1;
        }
    
        $data = json_decode(file_get_contents($file), true);
    
        if (!$data) {
            return 1;
        }
    
        $ultimoDeposito = end($data);
        $ultimoId = intval($ultimoDeposito["_id"]) + 1;
        return $ultimoId;
    }



    public static function RealizarRetiro($tipoCuenta,$nroCuenta,$moneda,$importe)
    {
        //primero obtengo las cuentas bancarias desde el json.
        $cuentas = CuentaBancaria::LeerJSON();

        foreach($cuentas as $cuenta)
        {
            if($cuenta->tipoCuenta == $tipoCuenta && $cuenta->getId() === $nroCuenta && $cuenta->moneda == $moneda)
            {
                //primero me fijo que tenga suficiente saldo para retirar;
                if($cuenta->saldoInicial >= $importe)
                {
                    //realizo el retiro;
                    $cuenta->saldoInicial -= $importe;

                    //actualizo banco.json;
                    CuentaBancaria::GuardarEnJSON($cuentas);

                    //ahora creo el registro del retiro;
                    $nuevoIdRetiro = self::obtenerNuevoIdExtraccion();
                    $fechaActual = Deposito::obtenerFechaActual();

                    //ahora instancio el objeto de retiro;
                    $registroRetiro = new Retiro($nuevoIdRetiro,$fechaActual,$importe,$tipoCuenta,$nroCuenta,$moneda);

                    //ahora leo los registro de retiros existentes
                    $registrosRetiro = self::LeerExtraccionesJSON();

                    //Agrego el nuevo registro al array;
                    $registrosRetiro[] = $registroRetiro;

                    self::GuardarExtraccionEnJSON($registrosRetiro);

                    return 'Retiro realizado con exito';
                }
                else
                {
                    return 'Saldo insuficiente para el retiro';
                }

            }

        }
        return 'La cuenta no existe';
    }


    public static function GuardarExtraccionEnJSON($cuentasArray, $filename = "retiro.json"): bool
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
    

    public static function LeerExtraccionesJSON($filename = "retiro.json"): array
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
                        array_push($cuentas, new Retiro(
                            $cuenta["_id"],
                            $cuenta["_fecha"],
                            $cuenta["_monto"],
                            $cuenta["_tipoCuenta"],
                            $cuenta["_nroCuenta"],
                            $cuenta["_moneda"]
                        ));
                    }
                }

                // Cierro el archivo después de leerlo
                fclose($file);
            }
        } catch (\Throwable $th) {
            // En caso de que ocurra una excepción, imprime un mensaje de error
            echo "Cuenta nueva";
        } finally {
            // Devuelvo el array de cuentas bancarias, ya sea vacío o con las cuentas leídas del archivo
            return  $cuentas;
        }
    }



}




?>