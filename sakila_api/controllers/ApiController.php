<?php
// controllers/ApiController.php - Controlador actualizado con ResponseService
class ApiController {
    private $repository;
    private $table;
    
    public function __construct($table) {
        $this->table = $table;
        
        try {
            $this->repository = RepositoryFactory::create($table);
        } catch (InvalidArgumentException $e) {
            ResponseService::error($e->getMessage(), 400);
        }
    }
    
    public function handleRequest($method, $id = null, $action = null) {
        try {
            // Medir tiempo de ejecución
            $startTime = microtime(true);
            
            switch($method) {
                case 'GET':
                    return $this->handleGetRequest($id, $action);
                    
                case 'POST':
                    return $this->handlePostRequest($id, $action);
                    
                case 'PUT':
                    return $this->handlePutRequest($id, $action);
                    
                case 'DELETE':
                    return $this->handleDeleteRequest($id, $action);
                    
                default:
                    ResponseService::methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
            }
            
        } catch (PDOException $e) {
            ResponseService::handlePDOException($e);
        } catch (Exception $e) {
            ResponseService::handleException($e);
        }
    }
    
    private function handleGetRequest($id, $action) {
        if ($action === 'search' && !empty($id)) {
            return $this->search($id);
        } elseif ($action === 'stats' && empty($id)) {
            return $this->getStatistics();
        } elseif ($action === 'health') {
            return $this->healthCheck();
        } elseif (!empty($id)) {
            return $this->getById($id);
        } else {
            return $this->getAll();
        }
    }
    
    private function handlePostRequest($id, $action) {
        if ($action === 'sync' && $this->table === 'film-text' && !empty($id)) {
            return $this->syncFilmText($id);
        } elseif ($action === 'batch') {
            return $this->batchCreate();
        } else {
            return $this->create();
        }
    }
    
    private function handlePutRequest($id, $action) {
        if (empty($id)) {
            ResponseService::validationError('ID requerido para actualización');
        }
        
        if ($action === 'batch') {
            return $this->batchUpdate();
        } else {
            return $this->update($id);
        }
    }
    
    private function handleDeleteRequest($id, $action) {
        if (empty($id)) {
            ResponseService::validationError('ID requerido para eliminación');
        }
        
        if ($action === 'batch') {
            return $this->batchDelete();
        } else {
            return $this->delete($id);
        }
    }
    
    private function getAll() {
        $results = $this->repository->getAll();
        
        if (empty($results)) {
            ResponseService::success([], 'No se encontraron registros');
        }
        
        ResponseService::success($results);
    }
    
    private function getById($id) {
        if (!is_numeric($id)) {
            ResponseService::validationError('ID debe ser numérico');
        }
        
        $result = $this->repository->getById($id);
        
        if (!$result) {
            ResponseService::notFound('Registro');
        }
        
        ResponseService::success($result);
    }
    
    private function create() {
        $data = $this->getRequestData();
        
        if (empty($data)) {
            ResponseService::validationError('Datos requeridos para creación');
        }
        
        // Validación específica para tablas de relación
        if (in_array($this->table, ['pelicula-actor', 'pelicula-categoria'])) {
            $this->validateRelationData($data);
        }
        
        $newId = $this->repository->create($data);
        
        $responseData = array_merge($data, ['id' => $newId]);
        ResponseService::created($responseData, 'Registro creado exitosamente');
    }
    
    private function update($id) {
        if (!is_numeric($id)) {
            ResponseService::validationError('ID debe ser numérico');
        }
        
        $data = $this->getRequestData();
        
        if (empty($data)) {
            ResponseService::validationError('Datos requeridos para actualización');
        }
        
        $rowCount = $this->repository->update($id, $data);
        
        if ($rowCount === 0) {
            ResponseService::notFound('Registro');
        }
        
        $responseData = array_merge($data, ['id' => $id]);
        ResponseService::updated($responseData, 'Registro actualizado exitosamente');
    }
    
    private function delete($id) {
        // Para tablas de relación, $id puede ser un array con composite key
        if (in_array($this->table, ['pelicula-actor', 'pelicula-categoria']) && is_string($id)) {
            // Intentar parsear como composite key desde query params
            parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queryParams);
            
            if (isset($queryParams['id_pelicula']) && isset($queryParams['id_actor'])) {
                $id = ['id_pelicula' => $queryParams['id_pelicula'], 'id_actor' => $queryParams['id_actor']];
            } elseif (isset($queryParams['id_pelicula']) && isset($queryParams['id_categoria'])) {
                $id = ['id_pelicula' => $queryParams['id_pelicula'], 'id_categoria' => $queryParams['id_categoria']];
            }
        }
        
        $rowCount = $this->repository->delete($id);
        
        if ($rowCount === 0) {
            ResponseService::notFound('Registro');
        }
        
        ResponseService::deleted('Registro eliminado exitosamente', $id);
    }
    
    private function search($term) {
        $startTime = microtime(true);
        
        $results = $this->repository->search($term);
        $totalFound = count($results);
        $executionTime = microtime(true) - $startTime;
        
        ResponseService::searchResults($results, $term, $totalFound, $executionTime);
    }
    
    private function getStatistics() {
        // Obtener estadísticas básicas del repositorio
        $all = $this->repository->getAll();
        $total = count($all);
        
        $stats = [
            'total_records' => $total,
            'table' => $this->table,
            'last_updated' => date('Y-m-d H:i:s')
        ];
        
        ResponseService::statistics($stats);
    }
    
    private function healthCheck() {
        $services = [
            'database' => 'connected',
            'table' => $this->table,
            'repository' => get_class($this->repository)
        ];
        
        ResponseService::healthCheck($services);
    }
    
    private function syncFilmText($id) {
        if (!method_exists($this->repository, 'syncWithPelicula')) {
            ResponseService::error('Operación no soportada para esta tabla', 405);
        }
        
        $result = $this->repository->syncWithPelicula($id);
        
        ResponseService::success(['synced_records' => $result], 'Sincronización completada');
    }
    
    private function batchCreate() {
        $data = $this->getRequestData();
        
        if (!is_array($data) || empty($data)) {
            ResponseService::validationError('Array de datos requerido para operación batch');
        }
        
        $successful = [];
        $failed = [];
        
        foreach ($data as $index => $item) {
            try {
                $newId = $this->repository->create($item);
                $successful[] = array_merge($item, ['id' => $newId, 'index' => $index]);
            } catch (Exception $e) {
                $failed[] = ['index' => $index, 'data' => $item, 'error' => $e->getMessage()];
            }
        }
        
        ResponseService::batchResult($successful, $failed, 'Operación batch de creación completada');
    }
    
    private function batchUpdate() {
        $data = $this->getRequestData();
        
        if (!is_array($data) || empty($data)) {
            ResponseService::validationError('Array de datos requerido para operación batch');
        }
        
        $successful = [];
        $failed = [];
        
        foreach ($data as $index => $item) {
            if (!isset($item['id'])) {
                $failed[] = ['index' => $index, 'data' => $item, 'error' => 'ID requerido'];
                continue;
            }
            
            try {
                $id = $item['id'];
                unset($item['id']);
                
                $rowCount = $this->repository->update($id, $item);
                
                if ($rowCount > 0) {
                    $successful[] = array_merge($item, ['id' => $id, 'index' => $index]);
                } else {
                    $failed[] = ['index' => $index, 'data' => $item, 'error' => 'Registro no encontrado'];
                }
            } catch (Exception $e) {
                $failed[] = ['index' => $index, 'data' => $item, 'error' => $e->getMessage()];
            }
        }
        
        ResponseService::batchResult($successful, $failed, 'Operación batch de actualización completada');
    }
    
    private function batchDelete() {
        $data = $this->getRequestData();
        
        if (!is_array($data) || empty($data)) {
            ResponseService::validationError('Array de IDs requerido para operación batch');
        }
        
        $successful = [];
        $failed = [];
        
        foreach ($data as $index => $id) {
            try {
                $rowCount = $this->repository->delete($id);
                
                if ($rowCount > 0) {
                    $successful[] = ['id' => $id, 'index' => $index];
                } else {
                    $failed[] = ['index' => $index, 'id' => $id, 'error' => 'Registro no encontrado'];
                }
            } catch (Exception $e) {
                $failed[] = ['index' => $index, 'id' => $id, 'error' => $e->getMessage()];
            }
        }
        
        ResponseService::batchResult($successful, $failed, 'Operación batch de eliminación completada');
    }
    
    private function getRequestData() {
        $input = file_get_contents('php://input');
        
        if (empty($input)) {
            return null;
        }
        
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ResponseService::validationError('JSON inválido: ' . json_last_error_msg());
        }
        
        return $data;
    }
    
    private function validateRelationData($data) {
        if ($this->table === 'pelicula-actor') {
            if (!isset($data['id_pelicula']) || !isset($data['id_actor'])) {
                ResponseService::validationError('id_pelicula e id_actor son requeridos');
            }
        } elseif ($this->table === 'pelicula-categoria') {
            if (!isset($data['id_pelicula']) || !isset($data['id_categoria'])) {
                ResponseService::validationError('id_pelicula e id_categoria son requeridos');
            }
        }
    }
}
?>