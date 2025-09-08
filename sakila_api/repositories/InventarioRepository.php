<?php
class InventarioRepository extends BaseRepository {
    protected $table = 'inventario';
    protected $idField = 'id_inventario';
    
    protected function buildSelectQuery() {
        return "SELECT i.id_inventario as id, p.titulo as pelicula, 
                CONCAT('Tienda ', c.nombre) as tienda,
                i.id_pelicula, i.id_almacen
                FROM inventario i
                LEFT JOIN pelicula p ON i.id_pelicula = p.id_pelicula
                LEFT JOIN almacen a ON i.id_almacen = a.id_almacen
                LEFT JOIN direccion d ON a.id_direccion = d.id_direccion
                LEFT JOIN ciudad c ON d.id_ciudad = c.id_ciudad";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO inventario (id_pelicula, id_almacen) VALUES (:id_pelicula, :id_almacen)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE inventario SET id_pelicula = :id_pelicula, id_almacen = :id_almacen WHERE id_inventario = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':id_pelicula', $data['id_pelicula'], PDO::PARAM_INT);
        $stmt->bindParam(':id_almacen', $data['id_almacen'], PDO::PARAM_INT);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':id_pelicula', $data['id_pelicula'], PDO::PARAM_INT);
        $stmt->bindParam(':id_almacen', $data['id_almacen'], PDO::PARAM_INT);
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
        $query = "SELECT i.id_inventario as id, p.titulo as pelicula, 
                 CONCAT('Tienda ', c.nombre) as tienda,
                 i.id_pelicula, i.id_almacen
                 FROM inventario i
                 LEFT JOIN pelicula p ON i.id_pelicula = p.id_pelicula
                 LEFT JOIN almacen a ON i.id_almacen = a.id_almacen
                 LEFT JOIN direccion d ON a.id_direccion = d.id_direccion
                 LEFT JOIN ciudad c ON d.id_ciudad = c.id_ciudad
                 WHERE p.titulo LIKE :search OR c.nombre LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>