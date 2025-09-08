<?php
class AlmacenRepository extends BaseRepository {
    protected $table = 'almacen';
    protected $idField = 'id_almacen';
    
    protected function buildSelectQuery() {
        return "SELECT a.id_almacen as id, CONCAT('Tienda ', ci.nombre) as nombre,
                d.direccion, ci.nombre as ciudad, p.nombre as pais,
                CONCAT(e.nombre, ' ', e.apellidos) as gerente
                FROM almacen a
                LEFT JOIN direccion d ON a.id_direccion = d.id_direccion
                LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                LEFT JOIN pais p ON ci.id_pais = p.id_pais
                LEFT JOIN empleado e ON a.id_empleado_jefe = e.id_empleado";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO almacen (id_empleado_jefe, id_direccion) VALUES (:id_empleado_jefe, :id_direccion)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE almacen SET id_empleado_jefe = :id_empleado_jefe, id_direccion = :id_direccion WHERE id_almacen = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':id_empleado_jefe', $data['id_empleado_jefe'], PDO::PARAM_INT);
        $stmt->bindParam(':id_direccion', $data['id_direccion'], PDO::PARAM_INT);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id_empleado_jefe', $data['id_empleado_jefe'], PDO::PARAM_INT);
        $stmt->bindParam(':id_direccion', $data['id_direccion'], PDO::PARAM_INT);
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
        $query = "SELECT a.id_almacen as id, CONCAT('Tienda ', ci.nombre) as nombre,
                 d.direccion, ci.nombre as ciudad, p.nombre as pais,
                 CONCAT(e.nombre, ' ', e.apellidos) as gerente
                 FROM almacen a
                 LEFT JOIN direccion d ON a.id_direccion = d.id_direccion
                 LEFT JOIN ciudad ci ON d.id_ciudad = ci.id_ciudad
                 LEFT JOIN pais p ON ci.id_pais = p.id_pais
                 LEFT JOIN empleado e ON a.id_empleado_jefe = e.id_empleado
                 WHERE ci.nombre LIKE :search OR p.nombre LIKE :search OR 
                       CONCAT(e.nombre, ' ', e.apellidos) LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>