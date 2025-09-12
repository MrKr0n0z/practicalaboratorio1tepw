<?php
// services/ResponseService.php - Servicio para manejar respuestas de API
class ResponseService {

    
    
    /**
     * Verifica si está en modo debug
     */
    private static function isDebugMode() {
        define('DEBUG', true);
        // Método 1: Verificar constante DEBUG
        if (defined('DEBUG') && DEBUG) {
            return true;
        }
        
        // Método 2: Verificar variable de entorno
        if (getenv('APP_DEBUG') === 'true' || getenv('DEBUG') === 'true') {
            return true;
        }
        
        // Método 3: Verificar configuración de PHP
        if (ini_get('display_errors') === '1') {
            return true;
        }
        
        // Método 4: Verificar si estamos en localhost/desarrollo
        $devHosts = ['localhost', '127.0.0.1', '::1', 'dev.local'];
        if (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], $devHosts)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Envía una respuesta JSON exitosa
     */
    public static function success($data, $message = 'Operación exitosa', $code = 200) {
        self::sendResponse([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], $code);
    }
    
    /**
     * Envía una respuesta de error
     */
    public static function error($message, $code = 500, $details = null) {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('c')
        ];
        
        if ($details !== null) {
            $response['details'] = $details;
        }
        
        self::sendResponse($response, $code);
    }
    
    /**
     * Respuesta para recursos no encontrados
     */
    public static function notFound($resource = 'Recurso') {
        self::sendResponse([
            'success' => false,
            'message' => "$resource no encontrado",
            'timestamp' => date('c')
        ], 404);
    }
    
    /**
     * Respuesta para recursos creados exitosamente
     */
    public static function created($data, $message = 'Recurso creado exitosamente') {
        self::sendResponse([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], 201);
    }
    
    /**
     * Respuesta para recursos actualizados exitosamente
     */
    public static function updated($data, $message = 'Recurso actualizado exitosamente') {
        self::sendResponse([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], 200);
    }
    
    /**
     * Respuesta para recursos eliminados exitosamente
     */
    public static function deleted($message = 'Recurso eliminado exitosamente', $deletedId = null) {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => date('c')
        ];
        
        if ($deletedId !== null) {
            $response['deleted_id'] = $deletedId;
        }
        
        self::sendResponse($response, 200);
    }
    
    /**
     * Respuesta para errores de validación
     */
    public static function validationError($message, $errors = null) {
        $response = [
            'success' => false,
            'message' => $message,
            'type' => 'validation_error',
            'timestamp' => date('c')
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        self::sendResponse($response, 422);
    }
    
    /**
     * Respuesta para métodos no permitidos
     */
    public static function methodNotAllowed($allowedMethods = []) {
        $response = [
            'success' => false,
            'message' => 'Método no permitido',
            'allowed_methods' => $allowedMethods,
            'timestamp' => date('c')
        ];
        
        // Establecer header Allow
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        
        self::sendResponse($response, 405);
    }
    
    /**
     * Respuesta para resultados de búsqueda
     */
    public static function searchResults($results, $term, $totalFound, $executionTime = null) {
        $response = [
            'success' => true,
            'message' => "Búsqueda completada para: '$term'",
            'data' => $results,
            'meta' => [
                'search_term' => $term,
                'total_found' => $totalFound,
                'result_count' => count($results)
            ],
            'timestamp' => date('c')
        ];
        
        if ($executionTime !== null) {
            $response['meta']['execution_time'] = round($executionTime, 4) . 's';
        }
        
        self::sendResponse($response, 200);
    }
    
    /**
     * Respuesta para estadísticas
     */
    public static function statistics($stats) {
        self::sendResponse([
            'success' => true,
            'message' => 'Estadísticas obtenidas exitosamente',
            'data' => $stats,
            'timestamp' => date('c')
        ], 200);
    }
    
    /**
     * Respuesta para health check
     */
    public static function healthCheck($services) {
        $allHealthy = true;
        
        // Verificar si todos los servicios están saludables
        foreach ($services as $service => $status) {
            if (!in_array($status, ['connected', 'ok', 'healthy', 'active'])) {
                $allHealthy = false;
                break;
            }
        }
        
        $response = [
            'success' => $allHealthy,
            'message' => $allHealthy ? 'Todos los servicios están operativos' : 'Algunos servicios presentan problemas',
            'services' => $services,
            'timestamp' => date('c')
        ];
        
        $code = $allHealthy ? 200 : 503;
        self::sendResponse($response, $code);
    }
    
    /**
     * Respuesta para operaciones batch
     */
    public static function batchResult($successful, $failed, $message = 'Operación batch completada') {
        $response = [
            'success' => empty($failed),
            'message' => $message,
            'data' => [
                'successful' => $successful,
                'failed' => $failed
            ],
            'meta' => [
                'total_processed' => count($successful) + count($failed),
                'successful_count' => count($successful),
                'failed_count' => count($failed),
                'success_rate' => count($successful) / (count($successful) + count($failed)) * 100
            ],
            'timestamp' => date('c')
        ];
        
        // Si hay fallos parciales, usar código 207 (Multi-Status)
        $code = empty($failed) ? 200 : 207;
        self::sendResponse($response, $code);
    }
    
    /**
     * Maneja excepciones PDO
     */
    public static function handlePDOException($e) {
        $message = 'Error de base de datos';
        $code = 500;
        
        // Personalizar mensaje según el tipo de error PDO
        switch($e->getCode()) {
            case '23000': // Integrity constraint violation
                $message = 'Error de integridad de datos: violación de restricción';
                $code = 409;
                break;
            case '42S02': // Table doesn't exist
                $message = 'Tabla no encontrada en la base de datos';
                $code = 404;
                break;
            case '42S22': // Column not found
                $message = 'Columna no encontrada en la tabla';
                $code = 400;
                break;
            default:
                $message = 'Error interno de base de datos';
        }
        
        $details = [
            'code' => $e->getCode(),
            'sql_state' => $e->getCode()
        ];
        
        // En desarrollo, incluir más detalles
        // En desarrollo, incluir más detalles
        if (self::isDebugMode()) {
            $details['debug_message'] = $e->getMessage();
            $details['file'] = $e->getFile();
            $details['line'] = $e->getLine();
        }
        self::error($message, $code, $details);
    }
    
    /**
     * Maneja excepciones generales
     */
    public static function handleException($e) {
        $message = 'Error interno del servidor';
        $code = 500;
        
        $details = [
            'type' => get_class($e)
        ];
        
        // En desarrollo, incluir más detalles
        // En desarrollo, incluir más detalles
            if (self::isDebugMode()) {
                $details['message'] = $e->getMessage();
                $details['file'] = $e->getFile();
                $details['line'] = $e->getLine();
                $details['trace'] = $e->getTraceAsString();
            }
        
        self::error($message, $code, $details);
    }
    
    /**
     * Envía la respuesta JSON y termina la ejecución
     */
    private static function sendResponse($data, $code = 200) {
        // Establecer headers HTTP
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Headers CORS (si es necesario)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Codificar y enviar respuesta
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        // Terminar ejecución
        exit;
    }
    
    /**
     * Respuesta para solicitudes OPTIONS (CORS preflight)
     */
    public static function handleOptions() {
        http_response_code(200);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400'); // Cache preflight por 24 horas
        exit;
    }
    
    /**
     * Respuesta para rate limiting
     */
    public static function rateLimitExceeded($retryAfter = null) {
        $response = [
            'success' => false,
            'message' => 'Límite de solicitudes excedido',
            'type' => 'rate_limit_exceeded',
            'timestamp' => date('c')
        ];
        
        if ($retryAfter) {
            $response['retry_after'] = $retryAfter;
            header("Retry-After: $retryAfter");
        }
        
        self::sendResponse($response, 429);
    }
    
    /**
     * Respuesta personalizada
     */
    public static function custom($data, $message, $success = true, $code = 200) {
        self::sendResponse([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], $code);
    }
}
?>