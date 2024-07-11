<?php
// Establecer el encabezado para indicar que el contenido es JSON
header('Content-Type: application/json');
// Evitar error de CORS (acceso desde cualquier origen)
header("Access-Control-Allow-Origin: *");
// Establecer los métodos y encabezados permitidos
header("Access-Control-Allow-Methods: *");//GET, POST, PUT, DELETE
header("Access-Control-Allow-Headers: Content-Type");//json

// Incluir el archivo de conexión a la BD
include_once 'db_connection.php';

// Manejar las solicitudes según la petición (GET, POST)

// Manejar la petición GET para obtener todas las series
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM tv_series ORDER BY id_series DESC";
    $result = $conn->query($sql);

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        //retornar código de respuesta 200 (OK) con las series en formato JSON
        $series = array();
        while($row = $result->fetch_assoc()) {
            $series[] = $row;
        }
        //tv_series (id_series, title, release_date, runtime, episodes, genres, synopsis, image)
        http_response_code(200);
        // transforma en un json
        echo json_encode($series);
    } else {
        // Sin resultados, devolver código de respuesta 404 (Not found) con mensaje de error
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron series"));
    }
}

// petición POST para insertar una nueva película
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /*real_escape_string toma una cadena de texto como entrada y devuelve la misma cadena, pero con caracteres especiales (como comillas simples, dobles, barras invertidas, etc.) escapados para prevenir inyecciones SQL  $genero = isset($_POST['genero']) ? $conn->real_escape_string($_POST['genero']) : '';*/
    $postBody = file_get_contents("php://input");
    // lo transformo en array el json--> wn array asociativo de php key=>value
    $data = json_decode($postBody, true);
    var_dump($data);
    $titulo = $data['titulo'] ;
    $lanzamiento = $data['lanzamiento'];
    $duracion = $data['duracion'];
    $episodios = $data['episodios'];
    $generos = $data['genero'];
    $sinopsis = $data['sinopsis'];
    $imagen_name = $data['imagen']; //lo unico que se recibe desde el front es el nombre del archivo, no se guarda la imagen.

    // Verificar si todos los campos necesarios están completos
    if ($titulo && $lanzamiento && $duracion && $episodios && $generos && $sinopsis && $imagen_name) {
    // Consulta SQL para insertar una nueva película en la base de datos
    $query = "INSERT INTO tv_series (id_series, title, release_date, runtime, episodes, genres, synopsis, image) VALUES (NULL, '$titulo', '$lanzamiento', '$duracion', '$episodios', '$generos', '$sinopsis', '$imagen_name')";
        
        // Ejecutar la consulta SQL y verificar si se realizó correctamente
        if ($conn->query($query) === TRUE) {
            // Obtener el ID de la serie recién insertada
            $last_insert_id = $conn->insert_id;
            // Devolver código de respuesta 201 (Creado) e ID de la serie creada
            http_response_code(201);
            echo json_encode(array("message" => $last_insert_id));
        } else {
            // Si hubo un error al ejecutar la consulta SQL, devolver código de respuesta 500 (Error interno del servidor) con mensaje de error
            http_response_code(500);
            echo json_encode(array("message" => "Error al crear la serie: " . $conn->error));
        }
    } else {
        // Si no se completaron todos los campos necesarios, devolver código de respuesta 400 (Solicitud incorrecta) con mensaje de error
        http_response_code(400);
        echo json_encode(array("message" => "Debe completar todos los campos"));
    }

}


?>