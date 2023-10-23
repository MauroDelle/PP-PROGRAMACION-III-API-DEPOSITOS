<?php
require_once 'CuentaBancaria.php';

class Deposito
{
    #region ATRIBUTOS
    public $_id;
    public $_fecha;
    public $_monto;
    public $_moneda;
    public $_tipoCuenta;
    public $_idCuenta;
    #endregion

    #region CONSTRUCT
    public function __construct($id, $fecha, $monto, $moneda, $idCuenta,$tipoCuenta = null)
    {
        $this->_id = $id;
        $this->_fecha = $fecha;
        $this->_monto = $monto;
        $this->_moneda = $moneda;
        $this->_idCuenta = $idCuenta;
        $this->_tipoCuenta = $tipoCuenta;
    }

    #endregion

    #region GETTERS

    public function getTipoCuenta()
    {
        return $this->_tipoCuenta;
    }

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

    public function setTipoCuenta($tipoCuenta)
    {
        if($tipoCuenta === 'CA' || $tipoCuenta === 'CC')
        {
            $this->_tipoCuenta = $tipoCuenta;
        }
        else
        {
            http_response_code(400);
            echo 'Error! Tipo de cuenta no válido.';
            exit();
        }      
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
        return date('Y-m-d');
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
                            $deposito["_idCuenta"],
                            $deposito["_tipoCuenta"]
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
                    $tipoCuenta = $cuenta->getTipoCuenta();
                    
                    $registroDeposito = new Deposito($nuevoIdDeposito, $fechaActual, $monto, $nuevaMoneda, $idCuenta,$tipoCuenta);
                    
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

 
    #endregion
    
    #region METODOS-CONSULTA-GET

    /*a- El total depositado (monto) por tipo de cuenta y moneda en un día en
        particular (se envía por parámetro), si no se pasa fecha, se muestran las del día
        anterior.
    */

    public static function calcularTotalPorTipoCuentaYMoneda($tipoCuenta,$moneda,$fecha = null)
    {
        // si no tengo fecha, calculo la del dia anterior
        if($fecha === null)
        {
            $fecha = date('Y-m-d', strtotime('yesterday'));
        }

        $depositos = self::LeerDepositoJSON();

        //inicializo el total;
        $total = 0;


        foreach($depositos as $deposito)
        {
            $fechaDepositoJson = $deposito->_fecha;
            $fechaDepositoJson = DateTime::createFromFormat('Y-m-d H:i:s', $fechaDepositoJson);
            $fechaDepositoJson = $fechaDepositoJson->format('Y-m-d');

            if($fechaDepositoJson === $fecha && $deposito->_tipoCuenta === $tipoCuenta && $deposito->_moneda === $moneda)
            {
                $total += $deposito->_monto;
            }
        }

         // Devolver el total
         return $total;
    }

    public static function listarDepositosPorUsuario($idCuenta)
    {
        $depositos = self::LeerDepositoJSON();
        $resultados = array();

        foreach($depositos as $deposito)
        {
            if($deposito->getIdCuenta() === $idCuenta)
            {
                $resultados[] = $deposito;
            }
        }
        return $resultados;
    }


    public static function listarDepositosEntreFechasORdenadasPorNombre($fechaInicio, $fechaFin)
    {
        $depositos =self::LeerDepositoJSON();
        $resultados =  array();

        foreach($depositos as $deposito)
        {
            if($deposito->getFecha() >= $fechaInicio && $deposito->getFecha() <= $fechaFin)
            {
                $cuentaBancaria = CuentaBancaria::obtenerCuentaPorId($deposito->getIdCuenta());

                if($cuentaBancaria)
                {
                    $nombreCuenta = $cuentaBancaria->nombre;
                    $deposito->_nombre = $nombreCuenta;
                    $resultados[] = $deposito;
                }
            }
        }
        usort($resultados, function ($a, $b) {
            return strcmp($a->_nombre, $b->_nombre);
        });

        return $resultados;
    }

    public static function obtenerDepositosPorTipoCuenta($tipoCuenta)
    {
        $depositos = self::LeerDepositoJSON();

        $depList = array();

        foreach($depositos as $deposito)
        {
            if($deposito !== null && $deposito->_tipoCuenta === $tipoCuenta)
            {
                $depList[] = $deposito;
            }
        }
        return $depList;
    }

    public static function obtenerDepositosPorMoneda($moneda)
    {
        $depositos = self::LeerDepositoJSON();

        $depList = array();

        foreach($depositos as $deposito)
        {
            if($deposito->_moneda === $moneda)
            {
                $depList[] = $deposito;
            }
        }

        return $depList;
    }


    #endregion

}
