<?php
// api.php - API para conectar JavaScript con MySQL sakila_es
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración de la base de datos
$host = 'localhost';
$username = 'root';
$password = ''; // XAMPP por defecto no tiene contraseña
$database = 'sakila_es';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

// Obtener parámetros de la URL
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Mapeo de tablas del frontend a las tablas reales de sakila_es
$tableMapping = [
    'peliculas' => 'pelicula',
    'actores' => 'actor',
    'clientes' => 'cliente',
    'categorias' => 'categoria',
    'tiendas' => 'almacen',
    'empleados' => 'empleado',
    'paises' => 'pais',
    'ciudades' => 'ciudad',
    'direcciones' => 'direccion',
    'idiomas' => 'idioma',
    'inventario' => 'inventario',
    'alquileres' => 'alquiler',
    'pagos' => 'pago'
];

// Obtener tabla y acción de la URL
$apiIndex = array_search('api.php', $pathParts);
$table = isset($pathParts[$apiIndex + 1]) ? $pathParts[$apiIndex + 1] : '';
$id = isset($pathParts[$apiIndex + 2]) ? $pathParts[$apiIndex + 2] : '';
$action = isset($pathParts[$apiIndex + 3]) ? $pathParts[$apiIndex + 3] : '';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'GET':
            if ($action === 'search' && !empty($pathParts[$apiIndex + 3])) {
                handleSearch($pdo, $table, $pathParts[$apiIndex + 3]);
            } elseif (!empty($id)) {
                getById($pdo, $table, $id);
            } else {
                getAll($pdo, $table);
            }
            break;
            
        case 'POST':
            create($pdo, $table);
            break;
            
        case 'PUT':
            update($pdo, $table, $id);
            break;
            
        case 'DELETE':
            delete($pdo, $table, $id);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getAll($pdo, $table) {
    $query = buildSelectQuery($table);
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}

function getById($pdo, $table, $id) {
    $query = buildSelectQuery($table) . " WHERE " . getIdField($table) . " = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Registro no encontrado']);
    }
}

function create($pdo, $table) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    switch($table) {
        case 'peliculas':
            $query = "INSERT INTO pelicula (titulo, descripcion, anyo_lanzamiento, duracion, clasificacion, id_idioma) 
                     VALUES (:titulo, :descripcion, :año_lanzamiento, :duracion, :calificacion, :id_idioma)";
            break;
        case 'actores':
            $query = "INSERT INTO actor (nombre, apellidos) 
                     VALUES (:nombre, :apellido)";
            break;
        case 'clientes':
            $query = "INSERT INTO cliente (nombre, apellidos, email, id_almacen, id_direccion, fecha_creacion) 
                     VALUES (:nombre, :apellido, :email, :tienda_id, :id_direccion, NOW())";
            break;
        case 'categorias':
            $query = "INSERT INTO categoria (nombre) VALUES (:nombre)";
            break;
        case 'empleados':
            $query = "INSERT INTO empleado (nombre, apellidos, email, id_almacen, id_direccion, username) 
                     VALUES (:nombre, :apellido, :email, :tienda_id, :id_direccion, :username)";
            break;
        case 'paises':
            $query = "INSERT INTO pais (nombre) VALUES (:nombre)";
            break;
        case 'ciudades':
            $query = "INSERT INTO ciudad (nombre, id_pais) VALUES (:nombre, :id_pais)";
            break;
        case 'idiomas':
            $query = "INSERT INTO idioma (nombre) VALUES (:nombre)";
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tabla no soportada para creación']);
            return;
    }
    
    $stmt = $pdo->prepare($query);
    
    try {
        switch($table) {
            case 'peliculas':
                $stmt->bindParam(':titulo', $data['titulo']);
                $stmt->bindParam(':descripcion', $data['descripcion']);
                $stmt->bindParam(':año_lanzamiento', $data['año_lanzamiento'], PDO::PARAM_INT);
                $stmt->bindParam(':duracion', $data['duracion'], PDO::PARAM_INT);
                $stmt->bindParam(':calificacion', $data['calificacion']);
                $stmt->bindParam(':id_idioma', $data['id_idioma'] ?? 1, PDO::PARAM_INT);
                break;
            case 'actores':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                break;
            case 'clientes':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':tienda_id', $data['tienda_id'] ?? 1, PDO::PARAM_INT);
                $stmt->bindParam(':id_direccion', $data['id_direccion'] ?? 1, PDO::PARAM_INT);
                break;
            case 'categorias':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
            case 'empleados':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':tienda_id', $data['tienda_id'] ?? 1, PDO::PARAM_INT);
                $stmt->bindParam(':id_direccion', $data['id_direccion'] ?? 1, PDO::PARAM_INT);
                $stmt->bindParam(':username', $data['username'] ?? $data['nombre']);
                break;
            case 'paises':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
            case 'ciudades':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':id_pais', $data['id_pais'], PDO::PARAM_INT);
                break;
            case 'idiomas':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
        }
        
        $stmt->execute();
        $newId = $pdo->lastInsertId();
        
        echo json_encode(['id' => $newId, 'message' => 'Registro creado exitosamente'] + $data);
    } catch(PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error creando registro: ' . $e->getMessage()]);
    }
}

function update($pdo, $table, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    switch($table) {
        case 'peliculas':
            $query = "UPDATE pelicula SET titulo = :titulo, descripcion = :descripcion, 
                     anyo_lanzamiento = :año_lanzamiento, duracion = :duracion, clasificacion = :calificacion 
                     WHERE id_pelicula = :id";
            break;
        case 'actores':
            $query = "UPDATE actor SET nombre = :nombre, apellidos = :apellido 
                     WHERE id_actor = :id";
            break;
        case 'clientes':
            $query = "UPDATE cliente SET nombre = :nombre, apellidos = :apellido, email = :email 
                     WHERE id_cliente = :id";
            break;
        case 'categorias':
            $query = "UPDATE categoria SET nombre = :nombre WHERE id_categoria = :id";
            break;
        case 'empleados':
            $query = "UPDATE empleado SET nombre = :nombre, apellidos = :apellido, email = :email 
                     WHERE id_empleado = :id";
            break;
        case 'paises':
            $query = "UPDATE pais SET nombre = :nombre WHERE id_pais = :id";
            break;
        case 'ciudades':
            $query = "UPDATE ciudad SET nombre = :nombre WHERE id_ciudad = :id";
            break;
        case 'idiomas':
            $query = "UPDATE idioma SET nombre = :nombre WHERE id_idioma = :id";
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tabla no soportada para actualización']);
            return;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    try {
        switch($table) {
            case 'peliculas':
                $stmt->bindParam(':titulo', $data['titulo']);
                $stmt->bindParam(':descripcion', $data['descripcion']);
                $stmt->bindParam(':año_lanzamiento', $data['año_lanzamiento'], PDO::PARAM_INT);
                $stmt->bindParam(':duracion', $data['duracion'], PDO::PARAM_INT);
                $stmt->bindParam(':calificacion', $data['calificacion']);
                break;
            case 'actores':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                break;
            case 'clientes':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                $stmt->bindParam(':email', $data['email']);
                break;
            case 'categorias':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
            case 'empleados':
                $stmt->bindParam(':nombre', $data['nombre']);
                $stmt->bindParam(':apellido', $data['apellido']);
                $stmt->bindParam(':email', $data['email']);
                break;
            case 'paises':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
            case 'ciudades':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
            case 'idiomas':
                $stmt->bindParam(':nombre', $data['nombre']);
                break;
        }
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['id' => $id, 'message' => 'Registro actualizado exitosamente'] + $data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Registro no encontrado']);
        }
    } catch(PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error actualizando registro: ' . $e->getMessage()]);
    }
}

function delete($pdo, $table, $id) {
    global $tableMapping;
    $realTable = $tableMapping[$table] ?? $table;
    $idField = getIdField($table);
    
    $query = "DELETE FROM $realTable WHERE $idField = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    try {
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Registro eliminado exitosamente', 'id' => $id]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Registro no encontrado']);
        }
    } catch(PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Error eliminando registro: ' . $e->getMessage()]);
    }
}

function handleSearch($pdo, $table, $searchTerm) {
    $searchTerm = '%' . $searchTerm . '%';
    
    switch($table) {
        case 'peliculas':
            $query = "SELECT p.id_pelicula as id, p.titulo, p.descripcion,
                     p.anyo_lanzamiento as año_lanzamiento, p.duracion, p.clasificacion as calificacion,
                     c.nombre as categoria, i.nombre as idioma
                     FROM pelicula p 
                     LEFT JOIN pelicula_categoria pc ON p.id_pelicula = pc.id_pelicula
                     LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria
                     LEFT JOIN idioma i ON p.id_idioma = i.id_idioma
                     WHERE p.titulo LIKE :search OR p.descripcion LIKE :search OR c.nombre LIKE :search";
            break;
        case 'actores':
            $query = "SELECT id_actor as id, nombre, apellidos as apellido,
                     CONCAT(nombre, '.', apellidos, '@email.com') as email, 1 as activo
                     FROM actor 
                     WHERE nombre LIKE :search OR apellidos LIKE :search";
            break;
        case 'clientes':
            $query = "SELECT c.id_cliente as id, c.nombre, c.apellidos as apellido, c.email,
                     d.telefono, CONCAT(d.direccion, ', ', ci.nombre) as direccion, c.activo
                     FROM cliente c
                     LEFT JOIN direccion d ON c.id_direccion = d.id_direccion
                     LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                     WHERE c.nombre LIKE :search OR c.apellidos LIKE :search OR c.email LIKE :search";
            break;
        case 'categorias':
            $query = "SELECT id_categoria as id, nombre, nombre as descripcion 
                     FROM categoria WHERE nombre LIKE :search";
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Búsqueda no soportada para esta tabla']);
            return;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}

function buildSelectQuery($table) {
    switch($table) {
        case 'peliculas':
            return "SELECT p.id_pelicula as id, p.titulo, p.descripcion,
                    p.anyo_lanzamiento as año_lanzamiento, p.duracion, p.clasificacion as calificacion,
                    c.nombre as categoria, i.nombre as idioma
                    FROM pelicula p 
                    LEFT JOIN pelicula_categoria pc ON p.id_pelicula = pc.id_pelicula
                    LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria
                    LEFT JOIN idioma i ON p.id_idioma = i.id_idioma";
            
        case 'actores':
            return "SELECT id_actor as id, nombre, apellidos as apellido,
                    CONCAT(nombre, '.', apellidos, '@email.com') as email, 1 as activo
                    FROM actor";
            
        case 'clientes':
            return "SELECT c.id_cliente as id, c.nombre, c.apellidos as apellido, c.email,
                    d.telefono, CONCAT(d.direccion, ', ', ci.nombre) as direccion, c.activo,
                    a.id_almacen as tienda_id
                    FROM cliente c 
                    LEFT JOIN direccion d ON c.id_direccion = d.id_direccion
                    LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                    LEFT JOIN almacen a ON c.id_almacen = a.id_almacen";
            
        case 'categorias':
            return "SELECT id_categoria as id, nombre, nombre as descripcion FROM categoria";
            
        case 'tiendas':
            return "SELECT a.id_almacen as id, CONCAT('Almacén ', ci.nombre) as nombre,
                    d.direccion, ci.nombre as ciudad, p.nombre as pais,
                    CONCAT(e.nombre, ' ', e.apellidos) as gerente
                    FROM almacen a
                    LEFT JOIN direccion d ON a.id_direccion = d.id_direccion
                    LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                    LEFT JOIN pais p ON ci.id_pais = p.id_pais
                    LEFT JOIN empleado e ON a.id_empleado_jefe = e.id_empleado";
            
        case 'empleados':
            return "SELECT id_empleado as id, nombre, apellidos as apellido, email,
                    id_almacen as tienda_id, 'Empleado' as cargo, activo, username
                    FROM empleado";
            
        case 'paises':
            return "SELECT id_pais as id, nombre FROM pais";
            
        case 'ciudades':
            return "SELECT c.id_ciudad as id, c.nombre, p.nombre as pais, c.id_pais
                    FROM ciudad c
                    LEFT JOIN pais p ON c.id_pais = p.id_pais";
            
        case 'idiomas':
            return "SELECT id_idioma as id, nombre FROM idioma";
            
        case 'inventario':
            return "SELECT i.id_inventario as id, p.titulo as pelicula, a.id_almacen as tienda,
                    i.id_pelicula, i.id_almacen as tienda_id
                    FROM inventario i
                    LEFT JOIN pelicula p ON i.id_pelicula = p.id_pelicula
                    LEFT JOIN almacen a ON i.id_almacen = a.id_almacen";
            
        case 'alquileres':
            return "SELECT al.id_alquiler as id, al.fecha_alquiler, al.fecha_devolucion,
                    CONCAT(c.nombre, ' ', c.apellidos) as cliente, p.titulo as pelicula,
                    CONCAT(e.nombre, ' ', e.apellidos) as empleado
                    FROM alquiler al
                    LEFT JOIN cliente c ON al.id_cliente = c.id_cliente
                    LEFT JOIN inventario i ON al.id_inventario = i.id_inventario
                    LEFT JOIN pelicula p ON i.id_pelicula = p.id_pelicula
                    LEFT JOIN empleado e ON al.id_empleado = e.id_empleado";
            
        case 'pagos':
            return "SELECT pa.id_pago as id, pa.total, pa.fecha_pago,
                    CONCAT(c.nombre, ' ', c.apellidos) as cliente,
                    CONCAT(e.nombre, ' ', e.apellidos) as empleado
                    FROM pago pa
                    LEFT JOIN cliente c ON pa.id_cliente = c.id_cliente
                    LEFT JOIN empleado e ON pa.id_empleado = e.id_empleado";
            
        default:
            global $tableMapping;
            $realTable = $tableMapping[$table] ?? $table;
            return "SELECT * FROM $realTable";
    }
}

function getIdField($table) {
    $mapping = [
        'peliculas' => 'id_pelicula',
        'actores' => 'id_actor',
        'clientes' => 'id_cliente',
        'categorias' => 'id_categoria',
        'tiendas' => 'id_almacen',
        'empleados' => 'id_empleado',
        'paises' => 'id_pais',
        'ciudades' => 'id_ciudad',
        'idiomas' => 'id_idioma',
        'inventario' => 'id_inventario',
        'alquileres' => 'id_alquiler',
        'pagos' => 'id_pago'
    ];
    
    return $mapping[$table] ?? 'id';
}
?>