<?php
require_once 'CuentaBancaria.php';
require_once 'Deposito.php';
require_once 'Retiro.php';

class Ajuste
{
    public $_idAjuste;
    public $_tipoTransaccion;
    public $_idDepositoRetiro;
    public $_motivo;
    public $_monto;

    /** 
     * The function is a constructor that initializes the properties of an object with the provided values.
     * 
     * @param //idAjuste The idAjuste parameter is used to store the unique identifier for the adjustment. It
     * could be an integer or a string, depending on how you want to identify the adjustment.
     * @param //tipoTransaccion The tipoTransaccion parameter represents the type of transaction being
     * performed. It could be a deposit or a withdrawal.
     * @param //idDepositoRetiro This parameter represents the ID of the deposit or withdrawal transaction.
     * It is used to identify the specific transaction being adjusted.
     * @param //motivo The "motivo" parameter is used to specify the reason or purpose of the adjustment. It
     * could be a description or explanation of why the adjustment is being made.
     * @param //monto The "monto" parameter represents the amount of money involved in the transaction. It
     * could be the amount being deposited or withdrawn, depending on the type of transaction.
     */
    public function __construct($idAjuste,$tipoTransaccion, $idDepositoRetiro, $motivo, $monto)
    {
        $this->_idAjuste = $idAjuste;
        $this->_tipoTransaccion = $tipoTransaccion;
        $this->_idDepositoRetiro = $idDepositoRetiro;
        $this->_motivo = $motivo;
        $this->_monto = $monto;
    }

    #region SETTERS y GETTERS

    

    public function getTipoTransaccion()
    {
        return $this->_tipoTransaccion;
    }

    
    public function setTipoTransaccion($tipoTransaccion)
    {
        $this->_tipoTransaccion = $tipoTransaccion;
    }

    public function getIdAjuste()
    {
        return $this->_idAjuste;
    }

    public function setIdAjuste($idAjuste)
    {
        $this->_idAjuste = $idAjuste;
    }

    public function getIdDepositoRetiro()
    {
        return $this->_idDepositoRetiro;
    }

    public function setIdDepositoRetiro($idDepositoRetiro)
    {
        $this->_idDepositoRetiro = $idDepositoRetiro;
    }

    public function getMotivo()
    {
        return $this->_motivo;
    }

    public function setMotivo($motivo)
    {
        $this->_motivo = $motivo;
    }

    public function getMonto()
    {
        return $this->_monto;
    }

    public function setMonto($monto)
    {
        $this->_monto = $monto;
    }


    #endregion


    #region METODOS

    /**
     * The function "obtenerNuevoIdAjuste" returns the next available ID for an adjustment based on the
     * data stored in a JSON file.
     * 
     * @return //the value of the variable , which is the last adjustment ID incremented by 1.
     */
    public static function obtenerNuevoIdAjuste()
    {
        $file = 'ajustes.json';
        
        if (!file_exists($file)) {
            return 1;
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        if (!$data) {
            return 1;
        }
        
        $ultimoDeposito = end($data);
        $ultimoId = intval($ultimoDeposito["_idAjuste"]) + 1;
        return $ultimoId;
    }


    /**
     * This PHP function performs an adjustment on a transaction based on its type, ID, adjustment amount,
     * and reason.
     * 
     * @param //tipoTransaccion The tipoTransaccion parameter is a string that represents the type of
     * transaction. It can be either "deposito" (deposit) or "retiro" (withdrawal).
     * @param //idTransaccion The idTransaccion parameter is the unique identifier of the transaction that
     * needs to be adjusted. It is used to find the specific transaction in the deposits or withdrawals
     * list.
     * @param //ajusteMonto The parameter "ajusteMonto" represents the amount of the adjustment to be made.
     * It is a numeric value that can be positive or negative, depending on whether it is a deposit or a
     * withdrawal.
     * @param string $motivo The "motivo" parameter is a string that represents the reason or explanation for the
     * adjustment being made. It could be a description of the issue or any relevant information related to
     * the adjustment.
     * 
     * @return //a string. The possible return values are:
     * - "Tipo de Transacción no válida" if the type of transaction is not 'deposito' or 'retiro'.
     * - "No se encontró la transacción con el ID especificado" if the transaction with the specified ID is
     * not found.
     * - "Ajuste realizado con éxito" if the adjustment is successfully made
     */
    public static function realizarAjuste($tipoTransaccion, $idTransaccion, $ajusteMonto, $motivo)
    {
        // Valida el tipo de transacción
        if ($tipoTransaccion !== 'deposito' && $tipoTransaccion !== 'retiro') {
            return "Tipo de Transacción no válida";
        }
        
        // Lee los depósitos y retiros
        $depositos = Deposito::LeerDepositoJSON();
        $retiros = Retiro::LeerExtraccionesJSON();
        
        // Encuentra la transacción específica
        $transaccion = null;
        
        if ($tipoTransaccion === 'deposito') {
            $depositos = Deposito::LeerDepositoJSON();
            $filteredDepositos = array_filter($depositos, function ($deposito) use ($idTransaccion) {
                return $deposito->getId() == $idTransaccion;
            });
            
            if (!empty($filteredDepositos)) {
                $transaccion = reset($filteredDepositos);
            }
        } elseif ($tipoTransaccion === 'retiro') {
            $retiros = Retiro::LeerExtraccionesJSON();
            $filteredRetiros = array_filter($retiros, function ($retiro) use ($idTransaccion) {
                return $retiro->getId() == $idTransaccion;
            });
            
            if (!empty($filteredRetiros)) {
                $transaccion = reset($filteredRetiros);
            }
        }
        
        if ($transaccion) {
            // Realizar el ajuste
            $cuentas = CuentaBancaria::LeerJSON();
            $cuentaActualizada = null;
            
            var_dump($tipoTransaccion);
            
            if($tipoTransaccion === 'retiro')
            {
                foreach ($cuentas as $cuenta) {
                    if ($cuenta->id == $transaccion->_nroCuenta) {
                        $cuentaActualizada = $cuenta;
                        break;
                    }
                }
            }
            else
            {
                foreach ($cuentas as $cuenta) {
                    if ($cuenta->id == $transaccion->_idCuenta) {
                        $cuentaActualizada = $cuenta;
                        break;
                    }
                }
            }

            
            if ($cuentaActualizada) {
                if ($tipoTransaccion === 'deposito') {
                    $cuentaActualizada->setSaldoInicial($cuentaActualizada->getSaldoInicial() - $ajusteMonto);
                } elseif ($tipoTransaccion === 'retiro') {
                    $cuentaActualizada->setSaldoInicial($cuentaActualizada->getSaldoInicial() + $ajusteMonto);
                }
                // Cargar los ajustes existentes desde ajustes.json
                $ajustes = self::LeerAjusteJSON();

                $nuevoIdRetiro = self::obtenerNuevoIdAjuste();
                // Generar un nuevo objeto Ajuste
                $ajuste = new Ajuste($nuevoIdRetiro, $tipoTransaccion, $idTransaccion, $motivo, $ajusteMonto);
                
                // Agregar el nuevo ajuste al array de ajustes
                $ajustes[] = $ajuste;
                
                // Guardar todos los ajustes en el archivo ajustes.json
                self::guardarAjusteEnJSON($ajustes);
                
                
                // Actualizar el saldo en banco.json
            CuentaBancaria::GuardarEnJSON($cuentas);

                   return 'Ajuste realizado con éxito';
            }
        }
        
        return 'No se encontró la transacción con el ID especificado';
    }


    /**
     * The function "LeerAjusteJSON" reads the content of a JSON file and returns an array of objects
     * representing the settings.
     * 
     * @param //filename The filename parameter is optional and specifies the name of the JSON file to read.
     * If no filename is provided, it defaults to "ajustes.json".
     * 
     * @return array an array of Ajuste objects.
     */
    private static function LeerAjusteJSON($filename = "ajustes.json"): array
    {
        // Crea un array vacío para almacenar los ajustes
        $ajustes = array();
        
        try {
            // Verifica si el archivo especificado ($filename) existe en el sistema de archivos
            if (file_exists($filename)) {
                // Abre el archivo en modo lectura
                $file = fopen($filename, "r");
                
                if ($file) {
                    // Lee el contenido completo del archivo en una variable como una cadena JSON
                    $json = fread($file, filesize($filename));
                    
                    // Decodifica el JSON en un array de objetos Ajuste
                    $ajustesFromJson = json_decode($json, true);
                    
                    // Itera por cada ajuste en el array decodificado
                    foreach ($ajustesFromJson as $ajuste) {
                        // Crea una nueva instancia de la clase Ajuste y agrega el ajuste al array $ajustes
                        array_push($ajustes, new Ajuste(
                            $ajuste["_idAjuste"],
                            $ajuste["_tipoTransaccion"],
                            $ajuste["_idDepositoRetiro"],
                            $ajuste["_motivo"],
                            $ajuste["_monto"]
                        ));
                    }
                }
    
                // Cierra el archivo después de leerlo
                fclose($file);
            }
        } catch (\Throwable $th) {
            // En caso de que ocurra una excepción, imprime un mensaje de error
            echo "Error al leer el archivo";
        } finally {
            // Devuelve el array de ajustes, ya sea vacío o con los ajustes leídos del archivo
            return $ajustes;
        }
    }



    /**
     * The function `guardarAjusteEnJSON` saves a deposit array as a JSON string in a file.
     * 
     * @param //deposito An array containing the deposits to be saved in JSON format.
     * @param //filename The filename parameter is optional and it specifies the name of the file where the
     * adjustments will be saved. If no filename is provided, the default value is "ajustes.json".
     * 
     * @return //bool a boolean value. It returns true if the saving operation was successful, and false
     * otherwise.
     */
    public static function guardarAjusteEnJSON($deposito, $filename = "ajustes.json"): bool
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
    
    #endregion
        
    
}
?>