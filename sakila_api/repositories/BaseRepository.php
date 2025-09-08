<?php
abstract class BaseRepository implements RepositoryInterface {
    protected $pdo;
    protected $table;
    protected $idField;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $query = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = $this->buildSelectQuery() . " WHERE " . $this->getIdField() . " = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->idField} = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    protected function getIdField() {
        return $this->idField;
    }
    
    // Métodos abstractos
    abstract protected function buildSelectQuery();
    abstract protected function buildInsertQuery();
    abstract protected function buildUpdateQuery();
    abstract protected function bindInsertParams($stmt, array $data);
    abstract protected function bindUpdateParams($stmt, $id, array $data);
    abstract public function search($term);
}
?>