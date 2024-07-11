<?php
// Variables de configuración de la base de datos
$host = "localhost";
$username = "cacphp";
$password = "2023php";
$dbname = "cac_php_proyect";
$port = "3306";

// Crea una nueva conexión a la base de datos MySQL
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verifica si la conexión falló
if ($conn->connect_error) {
    // Si hay un error de conexión, devolver código de respuesta 500 (Error interno del servidor)
    http_response_code(500);
    die(json_encode(array("message" => "Error interno del servidor: " . $conn->connect_error)));
}