<?php
class PagoRepository extends BaseRepository {
    protected $table = 'pago';
    protected $idField = 'id_pago';
    
    protected function buildSelectQuery() {
        return "SELECT pa.id_pago as id, pa.total, pa.fecha_pago,
                CONCAT(c.nombre, ' ', c.apellidos) as cliente,
                CONCAT(e.nombre, ' ', e.apellidos) as empleado,
                pa.id_alquiler, pa.id_cliente, pa.id_empleado
                FROM pago pa
                LEFT JOIN cliente c ON pa.id_cliente = c.id_cliente
                LEFT JOIN empleado e ON pa.id_empleado = e.id_empleado";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO pago (id_cliente, id_empleado, id_alquiler, total, fecha_pago) 
                VALUES (:id_cliente, :id_empleado, :id_alquiler, :total, :fecha_pago)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE pago SET id_cliente = :id_cliente, id_empleado = :id_empleado, 
                id_alquiler = :id_alquiler, total = :total, fecha_pago = :fecha_pago 
                WHERE id_pago = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':id_cliente', $data['id_cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':id_empleado', $data['id_empleado'], PDO::PARAM_INT);
        $stmt->bindParam(':id_alquiler', $data['id_alquiler'], PDO::PARAM_INT);
        $stmt->bindParam(':total', $data['total']);
        $fecha_pago = $data['fecha_pago'] ?? date('Y-m-d H:i:s');
        $stmt->bindParam(':fecha_pago', $fecha_pago);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $data['id_cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':id_empleado', $data['id_empleado'], PDO::PARAM_INT);
        $stmt->bindParam(':id_alquiler', $data['id_alquiler'], PDO::PARAM_INT);
        $stmt->bindParam(':total', $data['total']);
        $stmt->bindParam(':fecha_pago', $data['fecha_pago']);
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
        $query = "SELECT pa.id_pago as id, pa.total, pa.fecha_pago,
                 CONCAT(c.nombre, ' ', c.apellidos) as cliente,
                 CONCAT(e.nombre, ' ', e.apellidos) as empleado,
                 pa.id_alquiler
                 FROM pago pa
                 LEFT JOIN cliente c ON pa.id_cliente = c.id_cliente
                 LEFT JOIN empleado e ON pa.id_empleado = e.id_empleado
                 WHERE CONCAT(c.nombre, ' ', c.apellidos) LIKE :search OR 
                       CONCAT(e.nombre, ' ', e.apellidos) LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>