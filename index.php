<?php

$method = $_SERVER['REQUEST_METHOD']; //Obtengo el metodo http de la solicitud (get, post, put, delete);
$action = key($_GET); //Obtengo el primer parametro de la url, que generalmente indica la accion a realizar;

//Ahora cargo el archivo de rutas y controladores;
$routes = include 'routes.php';

//Verifico si el metodo y la accion estan definidos en las rutas.
if(isset($routes[$method]) && isset($routes[$method][$action]))
{
    $controller = $routes[$method][$action];

    //Incluyo el controlador correspondiente
    include $controller;
}
else{
    http_response_code(404);
    echo "Página no encontrada";
}


?>