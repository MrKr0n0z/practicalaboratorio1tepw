<?php
class AlquilerRepository extends BaseRepository {
    protected $table = 'alquiler';
    protected $idField = 'id_alquiler';
    
    protected function buildSelectQuery() {
        return "SELECT al.id_alquiler as id, al.fecha_alquiler, al.fecha_devolucion,
                CONCAT(c.nombre, ' ', c.apellidos) as cliente, p.titulo as pelicula,
                CONCAT(e.nombre, ' ', e.apellidos) as empleado, 
                al.id_cliente, al.id_inventario, al.id_empleado
                FROM alquiler al
                LEFT JOIN cliente c ON al.id_cliente = c.id_cliente
                LEFT JOIN inventario i ON al.id_inventario = i.id_inventario
                LEFT JOIN pelicula p ON i.id_pelicula = p.id_pelicula
                LEFT JOIN empleado e ON al.id_empleado = e.id_empleado";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO alquiler (fecha_alquiler, id_inventario, id_cliente, fecha_devolucion, id_empleado) 
                VALUES (:fecha_alquiler, :id_inventario, :id_cliente, :fecha_devolucion, :id_empleado)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE alquiler SET fecha_alquiler = :fecha_alquiler, id_inventario = :id_inventario, 
                id_cliente = :id_cliente, fecha_devolucion = :fecha_devolucion, id_empleado = :id_empleado 
                WHERE id_alquiler = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $fecha_alquiler = $data['fecha_alquiler'] ?? date('Y-m-d H:i:s');
        $stmt->bindParam(':fecha_alquiler', $fecha_alquiler);
        $stmt->bindParam(':id_inventario', $data['id_inventario'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $data['id_cliente'], PDO::PARAM_INT);
        $fecha_devolucion = $data['fecha_devolucion'] ?? null;
        $stmt->bindParam(':fecha_devolucion', $fecha_devolucion);
        $stmt->bindParam(':id_empleado', $data['id_empleado'], PDO::PARAM_INT);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alquiler', $data['fecha_alquiler']);
        $stmt->bindParam(':id_inventario', $data['id_inventario'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $data['id_cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_devolucion', $data['fecha_devolucion']);
        $stmt->bindParam(':id_empleado', $data['id_empleado'], PDO::PARAM_INT);
    }
    
    public function create(array $data) {
        $stmt = $this->pdo->prepare($this->buildInsertQuery());
        $this->bindInsertParams($stmt, $data);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    
    public function update($id, array $data) {
        $stmt = $this->pdo->prepare($this->buildUpdateQuery());
        $this->bindUpdateParams($stmt, $id, $data);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function search($term) {
        $searchTerm = '%' . $term . '%';
        $query = "SELECT al.id_alquiler as id, al.fecha_alquiler, al.fecha_devolucion,
                 CONCAT(c.nombre, ' ', c.apellidos) as cliente, p.titulo as pelicula,
                 CONCAT(e.nombre, ' ', e.apellidos) as empleado
                 FROM alquiler al
                 LEFT JOIN cliente c ON al.id_cliente = c.id_cliente
                 LEFT JOIN inventario i ON al.id_inventario = i.id_inventario
                 LEFT JOIN pelicula p ON i.id_pelicula = p.id_pelicula
                 LEFT JOIN empleado e ON al.id_empleado = e.id_empleado
                 WHERE CONCAT(c.nombre, ' ', c.apellidos) LIKE :search OR 
                       p.titulo LIKE :search OR 
                       CONCAT(e.nombre, ' ', e.apellidos) LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>