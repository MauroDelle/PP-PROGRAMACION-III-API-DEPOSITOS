<?php
require_once 'CuentaBancaria.php';

class Deposito
{
    #region ATRIBUTOS
    public $_id;
    public $_fecha;
    public $_monto;
    public $_moneda;
    public $_idCuenta;
    #endregion

    #region CONSTRUCT
    public function __construct($id, $fecha, $monto, $moneda, $idCuenta)
    {
        $this->_id = $id;
        $this->_fecha = $fecha;
        $this->_monto = $monto;
        $this->_moneda = $moneda;
        $this->_idCuenta = $idCuenta;
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

    public function getMoneda()
    {
        return $this->_moneda;
    }

    public function getIdCuenta()
    {
        return $this->_idCuenta;
    }


    #endregion

    #region SETTERS

    public function setFecha($fecha)
    {
        $this->_fecha = $fecha;
    }

    public function setMonto($monto)
    {
        $this->_monto = $monto;
    }

    public function setMoneda($moneda)
    {
        $this->_moneda = $moneda;
    }

    public function setIdCuenta($idCuenta)
    {
        $this->_idCuenta = $idCuenta;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    #endregion

    #region MÉTODOS


    public static function obtenerNuevoIdDeposito()
    {
        $file = 'deposito.json';

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

    public static function obtenerFechaActual()
    {
        return date('Y-m-d H:i:s');
    }

    public static function guardarDepositoEnJSON($deposito, $filename = "deposito.json"): bool
    {
        // Inicializo una variable para el éxito/fallo de la operación de guardado;
        $success = false;
    
        try {
            // Abro el archivo en modo append (agregar), y si no existe lo creo
            $file = fopen($filename, "w");
    
            if ($file) {
                // Convierte el array de depósitos en formato JSON con formato legible
                $json = json_encode($deposito, JSON_PRETTY_PRINT);
                // Agrego un salto de línea para separar los depósitos existentes del nuevo
                $json .= "\n";
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

    public static function LeerDepositoJSON($filename = "deposito.json"): array
    {
        // Crea un array vacío para almacenar los depósitos
        $depositos = array();

        try {
            // Verifica si el archivo especificado ($filename) existe en el sistema de archivos
            if (file_exists($filename)) {
                // Abre el archivo en modo lectura
                $file = fopen($filename, "r");

                if ($file) {
                    // Lee el contenido completo del archivo en una variable como una cadena JSON
                    $json = fread($file, filesize($filename));

                    // Decodifica el JSON en un array de objetos Deposito
                    $depositosFromJson = json_decode($json, true);

                    // Itera por cada depósito en el array decodificado
                    foreach ($depositosFromJson as $deposito) {
                        // Crea una nueva instancia de la clase Deposito y agrega el depósito al array $depositos
                        array_push($depositos, new Deposito(
                            $deposito["_id"],
                            $deposito["_fecha"],
                            $deposito["_monto"],
                            $deposito["_moneda"],
                            $deposito["_idCuenta"]
                        ));
                    }
                }

                // Cierra el archivo después de leerlo
                fclose($file);
            }
        } catch (\Throwable $th) {
            // En caso de que ocurra una excepción, imprime un mensaje de error
            echo "Error al leer el archivo de depósitos";
        } finally {
            // Devuelve el array de depósitos, ya sea vacío o con los depósitos leídos del archivo
            return $depositos;
        }
    }



    #endregion

    

    public static function Depositar($tipoCuenta, $nroCuenta, $moneda, $importe, $imagen)
    {
        // Primero obtengo las cuentas bancarias desde el JSON
        $cuentas = CuentaBancaria::LeerJSON();  
        
        // Ahora busco la cuenta que coincida con el tipo y número de cuenta;
        foreach ($cuentas as $cuenta) {
            if ($cuenta->tipoCuenta === $tipoCuenta && $cuenta->getId() === $nroCuenta && $cuenta->moneda === $moneda) {
                // Verificar que el importe del depósito sea válido
                if ($importe > 0) {
                    // Realizar el depósito en la cuenta
                    if ($moneda === '$' || $moneda === 'U$S') {
                        $cuenta->saldoInicial += $importe; 
                    }
                    
                    // Guardar el estado actual de la cuenta en banco.json
                    CuentaBancaria::GuardarEnJSON($cuentas);
                    // Crear un registro de depósito
                    
                    $nuevoIdDeposito = self::obtenerNuevoIdDeposito();
                    $fechaActual = self::obtenerFechaActual();
                    $monto = $importe;
                    $nuevaMoneda = $moneda;
                    $idCuenta = $cuenta->getID();
                    
                    $registroDeposito = new Deposito($nuevoIdDeposito, $fechaActual, $monto, $nuevaMoneda, $idCuenta);
                    
                    $depositos = Deposito::LeerDepositoJSON();

                    $nombreImagen = "{$tipoCuenta}_{$nroCuenta}_{$nuevoIdDeposito}" . ".jpg";
                    move_uploaded_file($imagen["tmp_name"],'./ImagenesDeDepositos2023/'.$nombreImagen);
                    
                    $depositos[] = $registroDeposito;
                  
                    // Guardar el registro de depósito en depósitos.json
                    self::guardarDepositoEnJSON($depositos);
                    return 'Depósito realizado con éxito';
                } else {
                    return 'El importe del depósito debe ser positivo';
                }
            }
        }
    }



}
