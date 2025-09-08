<?php
class EmpleadoRepository extends BaseRepository {
    protected $table = 'empleado';
    protected $idField = 'id_empleado';
    
    protected function buildSelectQuery() {
        return "SELECT e.id_empleado as id, e.nombre, e.apellidos, e.email,
                e.id_almacen, 'Empleado' as cargo, e.activo, e.username,
                CONCAT(d.direccion, ', ', c.nombre) as direccion_completa
                FROM empleado e
                LEFT JOIN direccion d ON e.id_direccion = d.id_direccion
                LEFT JOIN ciudad c ON d.id_ciudad = c.id_ciudad";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO empleado (nombre, apellidos, email, id_almacen, id_direccion, username, activo) 
                VALUES (:nombre, :apellidos, :email, :id_almacen, :id_direccion, :username, :activo)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE empleado SET nombre = :nombre, apellidos = :apellidos, email = :email,
                username = :username, activo = :activo WHERE id_empleado = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
        $stmt->bindParam(':email', $data['email']);
        $id_almacen = $data['id_almacen'] ?? 1;
        $id_direccion = $data['id_direccion'] ?? 1;
        $username = $data['username'] ?? strtolower($data['nombre']);
        $activo = $data['activo'] ?? true;
        $stmt->bindParam(':id_almacen', $id_almacen, PDO::PARAM_INT);
        $stmt->bindParam(':id_direccion', $id_direccion, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':username', $data['username']);
        $activo = $data['activo'];
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
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
        $query = "SELECT e.id_empleado as id, e.nombre, e.apellidos, e.email, e.username, e.activo,
                 a.id_almacen as id_almacen
                 FROM empleado e
                 LEFT JOIN almacen a ON e.id_almacen = a.id_almacen
                 WHERE e.nombre LIKE :search OR e.apellidos LIKE :search OR e.email LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>