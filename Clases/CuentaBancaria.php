<?php

require_once 'Retiro.php';


class CuentaBancaria
{
    #region ATRIBUTOS
    public $id;
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $nroDocumento;
    public $email;
    public $tipoCuenta;
    public $moneda;
    public $saldoInicial;

    public $_dadoDeBaja;


        // Agregar una propiedad para rastrear si la cuenta ha sido eliminada
        private $eliminada = false;
    
    #endregion

        // Método para establecer el estado de eliminación
        public function setEliminada($eliminada)
        {
            $this->eliminada = $eliminada;
        }

            // Método para obtener el estado de eliminación
    public function isEliminada()
    {
        return $this->eliminada;
    }

    public function getDadoDeBaja()
    {
        return $this->_dadoDeBaja;
    }

    public function setDadoDeBaja($valor)
    {
        $this->_dadoDeBaja = $valor;
    }

    #region CONSTRUCT
    public function __construct($id,$nombre, $apellido, $tipoDocumento, $nroDocumento, $email, $tipoCuenta, $moneda, $saldoInicial = 0, $dadoDeBaja = false)
    {
        $this->setNombre($nombre);
        $this->setId($id);
        $this->setApellido($apellido);
        $this->setTipoDocumento($tipoDocumento);
        $this->setNroDocumento($nroDocumento);
        $this->setEmail($email);
        $this->setTipoCuenta($tipoCuenta);
        $this->setMoneda($moneda);
        $this->setSaldoInicial($saldoInicial);
        $this->_dadoDeBaja = $dadoDeBaja;
    }
    #endregion

    #region SETTERS

    public function setId($id)
    {
        if(is_numeric($id) && $id >0)
        {
            $this->id = $id;
        }
        else
        {
            http_response_code(400);
            echo 'Error! Id no válido.';
            exit();
        }
    }

    public function setNombre($nombre)
    {
        if(is_string($nombre) && !empty($nombre))
        {
            $this->nombre = $nombre;
        }
        else{
            http_response_code(400);
            echo 'Error! Nombre no válido.';
            exit();
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
            http_response_code(400);
            echo 'Error! Apellido no válido.';
            exit();
        }
    }

    public function setTipoDocumento($tipoDocumento) {
        // Validación del tipo de documento
        if ($tipoDocumento === 'DNI' || $tipoDocumento === 'CI' || $tipoDocumento === 'Pasaporte')
        {
            $this->tipoDocumento = $tipoDocumento;
        } else {
            http_response_code(400);
            echo 'Error! Documento no válido.';
            exit();
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
            http_response_code(400);
            echo 'Error! numero de documento no válido.';
            exit();
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
            http_response_code(400);
            echo 'Error! Email no válido.';
            exit();
        }
    }

    public function setTipoCuenta($tipoCuenta)
    {
        if($tipoCuenta === 'CA' || $tipoCuenta === 'CC' || $tipoCuenta === 'CA$')
        {
            $this->tipoCuenta = $tipoCuenta;
        }
        else
        {
            http_response_code(400);
            echo 'Error! Tipo de cuenta no válido.';
            exit();
        }      
    }

    public function setMoneda($moneda)
    {
        if($moneda === '$' || $moneda === 'U$S')
        {
            $this->moneda = $moneda;
        }
        else{
            http_response_code(400);
            echo 'Error! Moneda no válida.';
            exit();
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
            http_response_code(400);
            echo 'Error! Saldo inicial no válido.';
            exit();
        }
    }


    #endregion

    #region GETTERS

    public function getId()
    {
        return $this->id;
    }
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
    
    #region MÉTODOS

    /**
     * The function "obtenerCuentaPorId" retrieves a specific account object from a JSON file based on its
     * ID.
     * 
     * @param //idCuenta The parameter `idCuenta` is the ID of the account that you want to retrieve.
     * 
     * @return //an object of type "Cuenta" if a matching account ID is found in the array of accounts. If no
     * matching account is found, it returns null.
     */
    public static function obtenerCuentaPorId($idCuenta)
    {
        $cuentas = self::LeerJSON();
        foreach($cuentas as $cuenta)
        {
            if($cuenta->getId() === $idCuenta)
            {
                return $cuenta;
            }
        }

        return null;
    }

    /**
     * The function checks if two objects of the class "CuentaBancaria" have the same values for the
     * "nombre", "apellido", and "tipoCuenta" properties.
     * 
     * @param //obj The parameter `` is an object of the class `CuentaBancaria`.
     * 
     * @return //bool a boolean value. If the conditions inside the if statement are met, it will return
     * true. Otherwise, it will return false.
     */
    public function Equals($obj): bool 
    {
        if (get_class($obj) == "CuentaBancaria" &&
            //$obj->nombre === $this->nombre &&
            $obj->id === $this->id &&
            $obj->tipoCuenta === $this->tipoCuenta) {
            return true;
        }
        return false;
    }

    /**
     * The function "Buscar" searches for a specific account in an array based on the given criteria (name,
     * last name, and account type) and returns a message indicating if a matching account was found or
     * not.
     * 
     * @param //array An array of objects representing accounts.
     * @param //nombre The parameter "nombre" is a string that represents the name of the account you want to
     * search for.
     * @param //apellido The parameter "apellido" represents the last name of the account holder.
     * @param //tipoCuenta The parameter "tipoCuenta" represents the type of account.
     * 
     * @return //a message indicating whether a matching account was found based on the specified criteria
     * (name, last name, and account type) in the given array. The message will vary depending on which
     * criteria were matched.
     */
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

    /**
     * The function "BuscarEnArray" checks if a given account exists in an array of accounts.
     * 
     * @param //arrayCuentas The parameter  is an array that contains bank account objects.
     * 
     * @return //bool a boolean value. It returns true if the account being searched for is found in the
     * array, and false if it is not found or if the array is empty.
     */
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

    /**
     * The function "ActualizarArray" updates an array of bank accounts based on the provided account,
     * action, and image.
     * 
     * @param //cuenta The parameter "cuenta" is an object of a class that represents a bank account. It
     * contains information such as the account number, account holder name, and initial balance.
     * @param //action The "action" parameter is a string that specifies the action to be performed on the
     * account. It can have two possible values: "add" or "sub".
     * @param //imagen The parameter "imagen" is a variable that represents an image. It is used in the
     * function to save the image associated with the account.
     * 
     * @return string a string message.
     */
    public static function ActualizarArray($cuenta,$action,$imagen):string
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
                $cuenta->guardarImagen($imagen);
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

    /**
     * The function "guardarImagen" saves an image file to a specific directory with a unique name based on
     * the account ID and account type.
     * 
     * @param //imagen The parameter "imagen" is a file that represents an image.
     */
    public function guardarImagen($imagen) {

        if($imagen != NULL)
        {
            // Combinar número de cuenta y tipo de cuenta para la identificación de la imagen
            $imagenIdentificacion = $this->getId() . $this->getTipoCuenta();
            $rutaImagen = 'ImagenesDeCuentas/2023/' . $imagenIdentificacion . '.jpg';
            move_uploaded_file($imagen['tmp_name'], $rutaImagen);
        }
        else{
            http_response_code(400);
            echo 'Error! Imagen no válida.';
            exit();
        }
    }

    #endregion

    #region MÉTODO-PUNTO-2

    /**
     * The function "consultarCuenta" in PHP checks if a given account type and number combination exists
     * in a JSON file and returns the currency and balance if it exists, or an error message if it doesn't.
     * 
     * @param //_tipoCuenta The type of account (e.g. "savings", "checking", "credit").
     * @param //_nroCuenta The account number that you want to search for.
     * 
     * @return //either the account balance and currency if the account type and number match, or an error
     * message indicating either an incorrect account type or a non-existent combination of account type
     * and number.
     */
    public static function consultarCuenta($_tipoCuenta,$_nroCuenta)
    {
        //primero obtengo las cuentas bancarias desde el json
        $cuentas = self::LeerJSON();
        $monedaSaldo = '';
        $tipoCuentaCorrecto = false;

        //Busco la cuenta que coincida con el tipo y número de cuenta
        foreach($cuentas as $cuenta)
        {
            if($cuenta->tipoCuenta === $_tipoCuenta && $cuenta->getId() === $_nroCuenta)
            {
                $monedaSaldo .= "Moneda: " . $cuenta->moneda . ", Saldo: " . $cuenta->saldoInicial;
                $tipoCuentaCorrecto = true;
                break; // Se encontró la cuenta, se puede salir del bucle
            }
        }

        if($tipoCuentaCorrecto)
        {
            return $monedaSaldo;
        }
        else
        {
            $existeNumeroCuenta = false;
            foreach ($cuentas as $cuenta) {
                if ($cuenta->id === $_nroCuenta) {
                    $existeNumeroCuenta = true;
                    break;
                }
        }

        if($existeNumeroCuenta)
        {
            return "Tipo de cuenta Incorrecto";
        }
        else{
            return "No existe la combinación de número y tipo de cuenta";

        }
        }
    }

    #endregion

    #region METODO-PUNTO-5

    public static function ModificarCuenta($tipoCuenta, $nroCuenta, $nuevosDatos)
    {
        $cuentas = self::LeerJSON();

        $cuentaEncontrada = false;

        foreach($cuentas as $cuenta)
        {
            if($cuenta->getTipoCuenta() === $tipoCuenta && $cuenta->getId() === $nroCuenta)
            {
                //Ahora modifico todos los datos de la cuenta con los nuevos datos;
                foreach($nuevosDatos as $key => $value)
                {
                    if(property_exists($cuenta, $key) && $key !== '_saldo')
                    {
                        $cuenta->$key = $value;
                    }
                }
                $cuentaEncontrada = true;
                break;
            }
        }

        if($cuentaEncontrada)
        {
            self::GuardarEnJSON($cuentas);
            return 'Cuenta modificada con éxito';
        }
        else
        {
            return 'No existe una cuenta con el tipo y número de cuenta proporcionados';
        }
    }


    // Método para buscar depósitos por ID en deposito.json
    public function tieneDepositoPorId($id) {
        $depositos = Deposito::LeerDepositoJSON(); 
        foreach ($depositos as $deposito) {
            if ($deposito->getId() == $id) {
                return true;
            }
        }

        return false;
    }

        // Método para buscar retiros por ID en retiro.json
        public function tieneRetiroPorId($id) {
            $retiros = Retiro::LeerExtraccionesJSON(); 
    
            foreach ($retiros as $retiro) {
                if ($retiro->getId() == $id) {
                    return true;
                }
            }
    
            return false;
        }


        public static function tieneDepositoyRetiro($id)
        {
            $depositos = Deposito::LeerDepositoJSON();
            $retiros = Retiro::LeerExtraccionesJSON();

            $depositosUsuario = [];
            $retirosUsuario = [];
    
            foreach ($depositos as $deposito) {
                if ($deposito->getIdCuenta() == $id) {
                    $depositosUsuario[] = $deposito;
                }
            }
    
            foreach ($retiros as $retiro) {
                if ($retiro->getId() == $id) {
                    $retirosUsuario[] = $retiro;
                }
            }
    
            return ['depositos' => $depositosUsuario, 'retiros' => $retirosUsuario];
        }



    public static function eliminarUsuarioYMoverFoto($tipoCuenta, $nroCuenta)
    {   
        $cuentas = self::LeerJSON();

        //ahora que tengo las cuentas busco por tipo y numero
        foreach($cuentas as $key => $cuenta)
        {
            if ($cuenta->tipoCuenta === $tipoCuenta && $cuenta->id === $nroCuenta)
            {
                $nombreFoto = $cuenta->getId() . $tipoCuenta . '.jpg'; // Nombre de la foto

                $rutaFotoOriginal = 'ImagenesDeCuentas/2023/' . $nombreFoto;
                $rutaCarpetaRespaldo = 'ImagenesBackupCuentas2023/';


                if (!is_dir($rutaCarpetaRespaldo)) {
                    mkdir($rutaCarpetaRespaldo, 0777, true);
                }             

                 // Mover la foto a la carpeta de respaldo
                 if (rename($rutaFotoOriginal, $rutaCarpetaRespaldo . $nombreFoto))
                 {

                $cuentas[$key]->setDadoDeBaja(true); // Configurar _dadoDeBaja en true

                // Guardar los cambios en el archivo JSON
                self::GuardarEnJSON($cuentas);
                    return 'Cuenta eliminada y foto respaldada con éxito.';
                 }
                 else {
                    return 'Error al mover la foto a la carpeta de respaldo.';
                }
            }


        }

        return 'Cuenta no encontrada.';
    }






    #endregion

    #region JSON


    /**
     * The function "LeerJSON" reads a JSON file containing bank account information and returns an array
     * of bank account objects.
     * 
     * @param //filename The filename parameter is optional and specifies the name of the JSON file to read.
     * If no filename is provided, it defaults to "banco.json".
     * 
     * @return array an array of CuentaBancaria objects.
     */
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
                            $cuenta["id"],
                            $cuenta["nombre"],
                            $cuenta["apellido"],
                            $cuenta["tipoDocumento"],
                            $cuenta["nroDocumento"],
                            $cuenta["email"],
                            $cuenta["tipoCuenta"],
                            $cuenta["moneda"],
                            $cuenta["saldoInicial"],
                            $cuenta["_dadoDeBaja"]
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