<?php
class CiudadRepository extends BaseRepository {
    protected $table = 'ciudad';
    protected $idField = 'id_ciudad';
    
    protected function buildSelectQuery() {
        return "SELECT c.id_ciudad as id, c.nombre, p.nombre as pais, c.id_pais
                FROM ciudad c
                LEFT JOIN pais p ON c.id_pais = p.id_pais";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO ciudad (nombre, id_pais) VALUES (:nombre, :id_pais)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE ciudad SET nombre = :nombre, id_pais = :id_pais WHERE id_ciudad = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id_pais', $data['id_pais'], PDO::PARAM_INT);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id_pais', $data['id_pais'], PDO::PARAM_INT);
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
        $query = "SELECT c.id_ciudad as id, c.nombre, p.nombre as pais, c.id_pais
                 FROM ciudad c
                 LEFT JOIN pais p ON c.id_pais = p.id_pais
                 WHERE c.nombre LIKE :search OR p.nombre LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>