<?php
class ApiController {
    private $repository;
    
    public function __construct($table) {
        $this->repository = RepositoryFactory::create($table);
    }
    
    public function handleRequest($method, $id = null, $action = null) {
        try {
            switch($method) {
                case 'GET':
                    if ($action === 'search' && !empty($id)) {
                        return $this->search($id);
                    } elseif (!empty($id)) {
                        return $this->getById($id);
                    } else {
                        return $this->getAll();
                    }
                    
                case 'POST':
                    return $this->create();
                    
                case 'PUT':
                    return $this->update($id);
                    
                case 'DELETE':
                    return $this->delete($id);
                    
                default:
                    http_response_code(405);
                    return ['error' => 'Método no permitido'];
            }
        } catch(Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getAll() {
        return $this->repository->getAll();
    }
    
    private function getById($id) {
        $result = $this->repository->getById($id);
        if ($result) {
            return $result;
        } else {
            http_response_code(404);
            return ['error' => 'Registro no encontrado'];
        }
    }
    
    private function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $newId = $this->repository->create($data);
            return ['id' => $newId, 'message' => 'Registro creado exitosamente'] + $data;
        } catch(PDOException $e) {
            http_response_code(400);
            return ['error' => 'Error creando registro: ' . $e->getMessage()];
        }
    }
    
    private function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $rowCount = $this->repository->update($id, $data);
            
            if ($rowCount > 0) {
                return ['id' => $id, 'message' => 'Registro actualizado exitosamente'] + $data;
            } else {
                http_response_code(404);
                return ['error' => 'Registro no encontrado o sin cambios'];
            }
        } catch(PDOException $e) {
            http_response_code(400);
            return ['error' => 'Error actualizando registro: ' . $e->getMessage()];
        }
    }
    
    private function delete($id) {
        try {
            $rowCount = $this->repository->delete($id);
            
            if ($rowCount > 0) {
                return ['message' => 'Registro eliminado exitosamente', 'id' => $id];
            } else {
                http_response_code(404);
                return ['error' => 'Registro no encontrado'];
            }
        } catch(PDOException $e) {
            if ($e->getCode() == '23000') {
                http_response_code(409);
                return ['error' => 'No se puede eliminar: el registro está siendo usado por otros datos'];
            } else {
                http_response_code(400);
                return ['error' => 'Error eliminando registro: ' . $e->getMessage()];
            }
        }
    }
    
    private function search($term) {
        return $this->repository->search($term);
    }
}
?>