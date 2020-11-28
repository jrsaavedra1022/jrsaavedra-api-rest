<?php
// CLASE 06 - Exponer datos a traves de HTTP GET

### autenticacion HTTP
// $user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
// $pwd = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : '';

// if ($user !=='jose' || $pwd !== '1234'){
//     die;
// }

#### autenticacion HMAC por hash

// if(
//     !array_key_exists('HTTP_X_HASH', $_SERVER) ||
//     !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
//     !array_key_exists('HTTP_X_UID', $_SERVER) 
// ){
//     die;
// }

// list( $hash, $uid, $timestamp) = [
//     $_SERVER['HTTP_X_HASH'],
//     $_SERVER['HTTP_X_UID'],
//     $_SERVER['HTTP_X_TIMESTAMP'],
// ];

// $secret = "Sh!! No se lo cuentes a nadie!";
// $newHash = sha1($uid.$timestamp.$secret);

// if ($newHash !== $hash){
//     die;
// }


### authentication token

// if (!array_key_exists('HTTP_X_TOKEN', $_SERVER)){
//     die;
// }

// $url = 'http://localhost:8001';

// # validar que el token es valido
// $ch = curl_init($url);
//     //envian los headers -H
// curl_setopt(
//     $ch, 
//     CURLOPT_HTTPHEADER, 
//     [
//         "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
//     ]
// );
    
//     // opcion para obtener el resultado
// curl_setopt(
//     $ch, 
//     CURLOPT_RETURNTRANSFER, 
//     true
// );

//     //call curl
// $ret = curl_exec( $ch );
//     // verificar el resultado
// if( $ret !== 'true'){
//     echo "here 2 ----";
//     die;
// }


// Definimos los recursos disponibles
$allowedResourceType = [
    'books',
    'authors',
    'genres',
];

// Validamos que el recurso este disponible
$resourceType = $_GET['resource_type'];

if ( !in_array($resourceType, $allowedResourceType)) {

    http_response_code( 400 );
    die;
}

// Defino los recursos
$books = [
    1 => [
        'titulo' => 'Lo que el viento se llevo',
        'id_autor' => 2,
        'id_genero' => 2,
    ],
    2 => [
        'titulo' => 'La Iliada',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
    3 => [
        'titulo' => 'La Odisea',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
];

// Se indica al cliente que lo que recibirá es un json
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');


// Levantamos el id del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';
// Generamos la respuesta asumiendo que el pedido es correcto
switch( strtoupper($_SERVER['REQUEST_METHOD'])) {
    case'GET':
        if( empty($resourceId)){
            echo json_encode($books);
        }else{
            if(array_key_exists($resourceId, $books)){
                echo json_encode($books[$resourceId]);
            }
        }
        break;
    case'POST':
        //obtener el json que nos envian
        $json = file_get_contents('php://input');
        $books[] = json_decode($json, true);
        // Devolver la nueva posición
        // echo "</br>";
        // echo array_keys($books)[count($books) - 1 ];
        echo json_encode($books);
        break;
    case'PUT':
        if( !empty($resourceId) && array_key_exists($resourceId, $books)){
            $json = file_get_contents('php://input');

            $books[$resourceId] = json_decode($json, true);
            // Retornamos el id del recurso
            echo json_encode($books);
        }
        break;
    case'DELETE':
        // validamos si exite
        if( !empty($resourceId) && array_key_exists($resourceId, $books)){
           unset($books[$resourceId]);
        }
        echo json_encode($books);
        break;
}


// Inicio el servidor en la terminal 1
// php -S localhost:8000 server.php

// Terminal 2 ejecutar 
// curl http://localhost:8000 -v
// curl http://localhost:8000?resource_type=books
// curl http://localhost:8000?resource_type=books | jq

// curl "http://localhost:8000?resource_type=books&resource_id=1"

#WITH router
// curl http://localhost:8000/books/1
# POST
// curl -X 'POST' http://localhost:8000/books -d '{"titulo": "Nuevo libro POST", "id_autor": 2, "id_genero": 1}'
# PUT
// curl -X 'PUT' http://localhost:8000/books/2 -d '{"titulo": "la chimba PUT", "id_autor": 2, "id_genero": 1}'
# DELETE
// curl -X 'DELETE' http://localhost:8000/books/2

# autenticacion http

// curl http://jose:1234@localhost:8000/books/

# authenticacion hmac
// php generate_hash.php 1
// Time: 1595469651
// Hash: c86e34b581f6af2d26e1f48edc904116d40325a4

// curl http://localhost:8000/books -H 'X-HASH: 9371f9b44d19caea6f45f8fc8b66eb53f08a33d0' -H 'X-UID: 2' -H 'X_TIMESTAMP: 1595470102'

# authentication token
// curl http://localhost:8001 -X 'POST' -H 'X_CLIENT_ID: 1' -H 'X_SECRET: SuperSecreto!'

// 70510f791a1caa82f6895e77aeb5c10e31e98ae2


// curl http://localhost:8000/books -H 'X-Token: 70510f791a1caa82f6895e77aeb5c10e31e98ae2'