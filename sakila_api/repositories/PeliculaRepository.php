<?php
class PeliculaRepository extends BaseRepository {
    protected $table = 'pelicula';
    protected $idField = 'id_pelicula';
    
    protected function buildSelectQuery() {
        return "SELECT p.id_pelicula as id, p.titulo, p.descripcion,
                p.anyo_lanzamiento, p.duracion, p.clasificacion,
                c.nombre as categoria, i.nombre as idioma,
                p.duracion_alquiler, p.rental_rate, p.replacement_cost,
                p.caracteristicas_especiales
                FROM pelicula p 
                LEFT JOIN pelicula_categoria pc ON p.id_pelicula = pc.id_pelicula
                LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria
                LEFT JOIN idioma i ON p.id_idioma = i.id_idioma";
    }
    
    protected function buildInsertQuery() {
        return "INSERT INTO pelicula (titulo, descripcion, anyo_lanzamiento, duracion, clasificacion, id_idioma, duracion_alquiler, rental_rate, replacement_cost) 
                VALUES (:titulo, :descripcion, :anyo_lanzamiento, :duracion, :clasificacion, :id_idioma, :duracion_alquiler, :rental_rate, :replacement_cost)";
    }
    
    protected function buildUpdateQuery() {
        return "UPDATE pelicula SET titulo = :titulo, descripcion = :descripcion, 
                anyo_lanzamiento = :anyo_lanzamiento, duracion = :duracion, clasificacion = :clasificacion,
                id_idioma = :id_idioma WHERE id_pelicula = :id";
    }
    
    protected function bindInsertParams($stmt, array $data) {
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':anyo_lanzamiento', $data['anyo_lanzamiento'], PDO::PARAM_INT);
        $stmt->bindParam(':duracion', $data['duracion'], PDO::PARAM_INT);
        $stmt->bindParam(':clasificacion', $data['clasificacion']);
        $id_idioma = $data['id_idioma'] ?? 1;
        $duracion_alquiler = $data['duracion_alquiler'] ?? 3;
        $rental_rate = $data['rental_rate'] ?? 4.99;
        $replacement_cost = $data['replacement_cost'] ?? 19.99;
        $stmt->bindParam(':id_idioma', $id_idioma, PDO::PARAM_INT);
        $stmt->bindParam(':duracion_alquiler', $duracion_alquiler, PDO::PARAM_INT);
        $stmt->bindParam(':rental_rate', $rental_rate);
        $stmt->bindParam(':replacement_cost', $replacement_cost);
    }
    
    protected function bindUpdateParams($stmt, $id, array $data) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':anyo_lanzamiento', $data['anyo_lanzamiento'], PDO::PARAM_INT);
        $stmt->bindParam(':duracion', $data['duracion'], PDO::PARAM_INT);
        $stmt->bindParam(':clasificacion', $data['clasificacion']);
        $stmt->bindParam(':id_idioma', $data['id_idioma'], PDO::PARAM_INT);
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
        $query = "SELECT p.id_pelicula as id, p.titulo, p.descripcion,
                 p.anyo_lanzamiento, p.duracion, p.clasificacion,
                 c.nombre as categoria, i.nombre as idioma, p.rental_rate, p.replacement_cost
                 FROM pelicula p 
                 LEFT JOIN pelicula_categoria pc ON p.id_pelicula = pc.id_pelicula
                 LEFT JOIN categoria c ON pc.id_categoria = c.id_categoria
                 LEFT JOIN idioma i ON p.id_idioma = i.id_idioma
                 WHERE p.titulo LIKE :search OR p.descripcion LIKE :search OR c.nombre LIKE :search";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>