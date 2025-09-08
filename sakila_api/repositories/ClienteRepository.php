<?php
class ClienteRepository extends BaseRepository {
    protected $table = 'cliente';
    protected $idField = 'id_cliente';
    
    protected function buildSelectQuery() {
        return "SELECT c.id_cliente as id, c.nombre, c.apellidos, c.email,
                d.telefono, CONCAT(d.direccion, ', ', ci.nombre, ', ', p.nombre) as direccion_completa, 
                c.activo, a.id_almacen as id_almacen, c.fecha_creacion
                FROM cliente c 
                LEFT JOIN direccion d ON c.id_direccion = d.id_direccion
                LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                LEFT JOIN pais p ON ci.id_pais = p.id_pais
                LEFT JOIN almacen a ON c.id_almacen = a.id_almacen";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO cliente (nombre, apellidos, email, id_almacen, id_direccion, fecha_creacion, activo) 
                VALUES (:nombre, :apellidos, :email, :id_almacen, :id_direccion, NOW(), :activo)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE cliente SET nombre = :nombre, apellidos = :apellidos, email = :email,
                activo = :activo WHERE id_cliente = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
        $stmt->bindParam(':email', $data['email']);
        $id_almacen = $data['id_almacen'] ?? 1;
        $id_direccion = $data['id_direccion'] ?? 1;
        $activo = $data['activo'] ?? true;
        $stmt->bindParam(':id_almacen', $id_almacen, PDO::PARAM_INT);
        $stmt->bindParam(':id_direccion', $id_direccion, PDO::PARAM_INT);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
        $stmt->bindParam(':email', $data['email']);
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
        $query = "SELECT c.id_cliente as id, c.nombre, c.apellidos, c.email,
                 d.telefono, CONCAT(d.direccion, ', ', ci.nombre) as direccion_completa, c.activo,
                 a.id_almacen as id_almacen
                 FROM cliente c
                 LEFT JOIN direccion d ON c.id_direccion = d.id_direccion
                 LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                 LEFT JOIN almacen a ON c.id_almacen = a.id_almacen
                 WHERE c.nombre LIKE :search OR c.apellidos LIKE :search OR c.email LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>