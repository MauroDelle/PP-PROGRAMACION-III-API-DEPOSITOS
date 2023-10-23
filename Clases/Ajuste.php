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




}
?>