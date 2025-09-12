<?php
// repositories/PeliculaCategoriaRepository.php
class PeliculaCategoriaRepository extends BaseRepository {
    protected $table = 'pelicula_categoria';
    protected $idField = 'id_pelicula'; // Tabla intermedia usa composite key
    
    protected function buildSelectQuery() {
        return "SELECT pc.id_pelicula, pc.id_categoria, 
                p.titulo as pelicula, c.nombre as categoria
                FROM pelicula_categoria pc 
                LEFT JOIN pelicula p ON pc.id_pelicula = p.id_pelicula
                LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO pelicula_categoria (id_pelicula, id_categoria) 
                VALUES (:id_pelicula, :id_categoria)";
    }
    
    protected function buildUpdateQuery() {
        // Para tabla intermedia, normalmente no se actualiza, se elimina y se crea
        return "UPDATE pelicula_categoria SET id_categoria = :id_categoria 
                WHERE id_pelicula = :id_pelicula AND id_categoria = :old_id_categoria";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':id_pelicula', $data['id_pelicula'], PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $data['id_categoria'], PDO::PARAM_INT);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id_pelicula', $data['id_pelicula'], PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $data['id_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(':old_id_categoria', $data['old_id_categoria'], PDO::PARAM_INT);
    }
    
    public function create(array $data) {
        $stmt = $this->pdo->prepare($this->buildInsertQuery());
        $this->bindInsertParams($stmt, $data);
        $stmt->execute();
        return $stmt->rowCount(); // Retorna filas afectadas en lugar de lastInsertId
    }
    
    public function update($id, array $data) {
        $stmt = $this->pdo->prepare($this->buildUpdateQuery());
        $this->bindUpdateParams($stmt, $id, $data);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    public function search($term) {
        $searchTerm = '%' . $term . '%';
        $query = "SELECT pc.id_pelicula, pc.id_categoria, 
                 p.titulo as pelicula, c.nombre as categoria
                 FROM pelicula_categoria pc 
                 LEFT JOIN pelicula p ON pc.id_pelicula = p.id_pelicula
                 LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria
                 WHERE p.titulo LIKE :search OR c.nombre LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Métodos específicos para esta tabla intermedia
    public function getByPelicula($idPelicula) {
        $query = "SELECT pc.id_categoria, c.nombre as categoria
                 FROM pelicula_categoria pc 
                 LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria
                 WHERE pc.id_pelicula = :id_pelicula";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_pelicula', $idPelicula, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByCategoria($idCategoria) {
        $query = "SELECT pc.id_pelicula, p.titulo as pelicula
                 FROM pelicula_categoria pc 
                 LEFT JOIN pelicula p ON pc.id_pelicula = p.id_pelicula
                 WHERE pc.id_categoria = :id_categoria";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_categoria', $idCategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteRelation($idPelicula, $idCategoria) {
        $query = "DELETE FROM pelicula_categoria 
                 WHERE id_pelicula = :id_pelicula AND id_categoria = :id_categoria";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id_pelicula', $idPelicula, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $idCategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
?>