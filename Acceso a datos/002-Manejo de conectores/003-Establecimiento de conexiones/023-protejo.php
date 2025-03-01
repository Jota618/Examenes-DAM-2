<?php
// Configura la visualización de errores para facilitar la depuración
ini_set("display_errors", 1); // Activa la visualización de errores
ini_set("display_startup_errors", 1); // Activa la visualización de errores de inicio
error_reporting(E_ALL); // Reporta todos los tipos de errores

// Define una clase llamada 'conexionDB'
class conexionDB
{
	// Propiedades de la clase para almacenar la configuración de la base de datos
	private $servidor; // Almacena el nombre del servidor de la base de datos (privado)
	private $usuario; // Almacena el nombre de usuario para la conexión (privado)
	private $contrasena; // Almacena la contraseña para la conexión (privado)
	private $basededatos; // Almacena el nombre de la base de datos a la que se conectará (privado)
	private $conexion; // Almacena la conexión a la base de datos (privado)

	// Constructor de la clase que se ejecuta al crear una instancia de la clase
	public function __construct()
	{
		// Inicializa las propiedades con los valores de conexión
		$this->servidor = "localhost"; // Establece el servidor como 'localhost'
		$this->usuario = "accesoadatos"; // Establece el usuario
		$this->contrasena = "accesoadatos"; // Establece la contraseña
		$this->basededatos = "accesoadatos"; // Establece la base de datos

		// Establece la conexión a la base de datos MySQL y la almacena en la propiedad $conexion
		$this->conexion = mysqli_connect($this->servidor, $this->usuario, $this->contrasena, $this->basededatos); // Conecta al servidor MySQL
	}

	// Método para seleccionar todos los registros de una tabla específica
	public function seleccionaTabla($tabla)
	{
		// Define la consulta SQL para seleccionar todos los registros de la tabla proporcionada
		$query = "SELECT * FROM " . $tabla . ";"; // Crea la consulta SQL usando el nombre de la tabla pasado como argumento

		// Ejecuta la consulta en la base de datos y almacena el resultado
		$result = mysqli_query($this->conexion, $query); // Ejecuta la consulta y guarda el resultado en la variable $result

		// Inicializa un array vacío para almacenar los resultados
		$resultado = []; // Crea un array que almacenará todas las filas obtenidas de la consulta

		// Itera sobre cada fila del resultado de la consulta
		while ($row = mysqli_fetch_assoc($result)) {
			// Mientras haya filas en el resultado
			$resultado[] = $row; // Agrega cada fila al array $resultado
		}

		// Convierte el array de resultados a formato JSON con un formato legible
		$json = json_encode($resultado, JSON_PRETTY_PRINT); // Codifica el array $resultado en formato JSON con sangrías para mejor legibilidad

		// Devuelve el JSON generado
		return $json; // Retorna el JSON que contiene los registros de la tabla
	}
}

// Crea una nueva instancia de la clase conexionDB
$conexion = new conexionDB(); // Instancia de la clase que establece la conexión a la base de datos

// Llama al método 'seleccionaTabla' con el argumento "empleados" y muestra el resultado
echo $conexion->seleccionaTabla("empleados"); // Ejecuta la función y muestra el JSON de la tabla 'empleados'
?>
