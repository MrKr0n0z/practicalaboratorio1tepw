<?php
// repositories/CategoriaRepository.php
class CategoriaRepository extends BaseRepository {
    protected $table = 'categoria';
    protected $idField = 'id_categoria';
    
    protected function buildSelectQuery() {
        return "SELECT id_categoria as id, nombre FROM categoria";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO categoria (nombre) VALUES (:nombre)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE categoria SET nombre = :nombre WHERE id_categoria = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':nombre', $data['nombre']);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $data['nombre']);
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
        $query = "SELECT id_categoria as id, nombre FROM categoria WHERE nombre LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}