<?php

// Configuración para mostrar errores en pantalla
ini_set("display_errors", 1); // Muestra errores
ini_set("display_startup_errors", 1); // Muestra errores de inicio
error_reporting(E_ALL); // Reporta todos los errores

// Conexión a la base de datos MySQL
($enlace = mysqli_connect("localhost", "accesoadatos", "accesoadatos", "accesoadatos")) or die("error");
// Si la conexión falla, se detiene la ejecución y se muestra "error"

// Lectura del archivo JSON que contiene la definición de las tablas
$json = file_get_contents("004-modelodedatos.json"); // Lee el contenido del archivo JSON
$datos = json_decode($json, true); // Decodifica el JSON en un array asociativo

// Iteración sobre cada tabla definida en el JSON
foreach ($datos as $dato) {
	$nombredetabla = $dato["nombre"]; // Obtiene el nombre de la tabla
	// Inicia la cadena SQL para crear la tabla
	$cadena = "CREATE TABLE " . $nombredetabla . " ( Identificador INT NOT NULL AUTO_INCREMENT , ";

	// Iteración sobre las columnas definidas para la tabla
	foreach ($dato["columnas"] as $columna) {
		// Agrega la definición de la columna a la cadena SQL
		$cadena .= $columna["nombre"] . " " . $columna["tipo"] . " ";
		// Si el tipo de columna no es TEXT, se agrega la longitud
		if ($columna["tipo"] != "TEXT") {
			$cadena .= " (" . $columna["longitud"] . ") ";
		}
		$cadena .= ","; // Agrega una coma al final de la definición de la columna
	}

	// Finaliza la cadena SQL con la clave primaria
	$cadena .= "PRIMARY KEY (Identificador) ";
	$cadena .= " )  ENGINE = InnoDB"; // Especifica el motor de almacenamiento

	// Muestra la consulta SQL generada (útil para depuración)
	echo $cadena;

	// Ejecuta la consulta SQL para crear la tabla en la base de datos
	mysqli_query($enlace, $cadena);
}
?>