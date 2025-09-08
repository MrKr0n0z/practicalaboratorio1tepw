<?php
class ActorRepository extends BaseRepository {
    protected $table = 'actor';
    protected $idField = 'id_actor';
    
    protected function buildSelectQuery() {
        return "SELECT id_actor as id, nombre, apellidos,
                CONCAT(nombre, '.', apellidos, '@email.com') as email_generado
                FROM actor";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO actor (nombre, apellidos) VALUES (:nombre, :apellidos)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE actor SET nombre = :nombre, apellidos = :apellidos WHERE id_actor = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':apellidos', $data['apellidos']);
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
        $query = "SELECT id_actor as id, nombre, apellidos,
                 CONCAT(nombre, '.', apellidos, '@email.com') as email
                 FROM actor 
                 WHERE nombre LIKE :search OR apellidos LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>