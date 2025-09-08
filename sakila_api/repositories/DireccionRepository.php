<?php
class DireccionRepository extends BaseRepository {
    protected $table = 'direccion';
    protected $idField = 'id_direccion';
    
    protected function buildSelectQuery() {
        return "SELECT d.id_direccion as id, d.direccion, d.direccion2, d.distrito,
                d.codigo_postal, d.telefono, c.nombre as ciudad, p.nombre as pais,
                d.id_ciudad
                FROM direccion d
                LEFT JOIN ciudad c ON d.id_ciudad = c.id_ciudad
                LEFT JOIN pais p ON c.id_pais = p.id_pais";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO direccion (direccion, direccion2, distrito, id_ciudad, codigo_postal, telefono) 
                VALUES (:direccion, :direccion2, :distrito, :id_ciudad, :codigo_postal, :telefono)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE direccion SET direccion = :direccion, direccion2 = :direccion2,
                distrito = :distrito, codigo_postal = :codigo_postal, telefono = :telefono 
                WHERE id_direccion = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':direccion', $data['direccion']);
        $direccion2 = $data['direccion2'] ?? null;
        $stmt->bindParam(':direccion2', $direccion2);
        $stmt->bindParam(':distrito', $data['distrito']);
        $stmt->bindParam(':id_ciudad', $data['id_ciudad'], PDO::PARAM_INT);
        $codigo_postal = $data['codigo_postal'] ?? null;
        $stmt->bindParam(':codigo_postal', $codigo_postal);
        $stmt->bindParam(':telefono', $data['telefono']);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $data['direccion']);
        $direccion2 = $data['direccion2'];
        $stmt->bindParam(':direccion2', $direccion2);
        $stmt->bindParam(':distrito', $data['distrito']);
        $codigo_postal = $data['codigo_postal'];
        $stmt->bindParam(':codigo_postal', $codigo_postal);
        $stmt->bindParam(':telefono', $data['telefono']);
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
        $query = "SELECT d.id_direccion as id, d.direccion, d.direccion2, d.distrito,
                 d.codigo_postal, d.telefono, c.nombre as ciudad, p.nombre as pais
                 FROM direccion d
                 LEFT JOIN ciudad c ON d.id_ciudad = c.id_ciudad
                 LEFT JOIN pais p ON c.id_pais = p.id_pais
                 WHERE d.direccion LIKE :search OR d.distrito LIKE :search OR c.nombre LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>