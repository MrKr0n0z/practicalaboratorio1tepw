<?php
// factories/RepositoryFactory.php - Factory actualizado con mejor manejo de errores
class RepositoryFactory {
    private static $repositories = [];
    
    public static function create($table) {
        if (!isset(self::$repositories[$table])) {
            switch($table) {
                // Repositorios principales
                case 'peliculas':
                    self::$repositories[$table] = new PeliculaRepository();
                    break;
                case 'actores':
                    self::$repositories[$table] = new ActorRepository();
                    break;
                case 'clientes':
                    self::$repositories[$table] = new ClienteRepository();
                    break;
                case 'categorias':
                    self::$repositories[$table] = new CategoriaRepository();
                    break;
                case 'empleados':
                    self::$repositories[$table] = new EmpleadoRepository();
                    break;
                    
                // Repositorios de ubicación
                case 'paises':
                    self::$repositories[$table] = new PaisRepository();
                    break;
                case 'ciudades':
                    self::$repositories[$table] = new CiudadRepository();
                    break;
                case 'direcciones':
                    self::$repositories[$table] = new DireccionRepository();
                    break;
                    
                // Repositorios de configuración
                case 'idiomas':
                    self::$repositories[$table] = new IdiomaRepository();
                    break;
                case 'tiendas':
                    self::$repositories[$table] = new AlmacenRepository();
                    break;
                    
                // Repositorios de operaciones
                case 'inventario':
                    self::$repositories[$table] = new InventarioRepository();
                    break;
                case 'alquileres':
                    self::$repositories[$table] = new AlquilerRepository();
                    break;
                case 'pagos':
                    self::$repositories[$table] = new PagoRepository();
                    break;
                    
                // Repositorios de relaciones
                case 'pelicula-categoria':
                case 'pelicula_categoria':
                    self::$repositories[$table] = new PeliculaCategoriaRepository();
                    break;
                    
                default:
                    throw new InvalidArgumentException(
                        "Repository para tabla '$table' no encontrado. " .
                        "Tablas disponibles: " . implode(', ', array_keys(self::getAvailableTables()))
                    );
            }
        }
        
        return self::$repositories[$table];
    }
    
    /**
     * Obtiene una lista de todas las tablas disponibles
     */
    public static function getAvailableTables() {
        return [
            // Entidades principales
            'peliculas' => 'Películas',
            'actores' => 'Actores',
            'clientes' => 'Clientes',
            'categorias' => 'Categorías',
            'empleados' => 'Empleados',
            
            // Ubicaciones
            'paises' => 'Países',
            'ciudades' => 'Ciudades', 
            'direcciones' => 'Direcciones',
            
            // Configuración
            'idiomas' => 'Idiomas',
            'tiendas' => 'Tiendas/Almacenes',
            
            // Operaciones
            'inventario' => 'Inventario',
            'alquileres' => 'Alquileres',
            'pagos' => 'Pagos',
            
            // Relaciones
            'pelicula-categoria' => 'Películas-Categorías',
            'pelicula-actor' => 'Películas-Actores',
            'film-text' => 'Texto de Películas'
        ];
    }
    
    /**
     * Verifica si una tabla existe
     */
    public static function exists($table) {
        return array_key_exists($table, self::getAvailableTables()) || 
               in_array($table, ['pelicula_categoria', 'pelicula_actor', 'film_text']);
    }
    
    /**
     * Libera todos los repositorios de la memoria
     */
    public static function clearCache() {
        self::$repositories = [];
    }
    
    /**
     * Obtiene información sobre una tabla específica
     */
    public static function getTableInfo($table) {
        $availableTables = self::getAvailableTables();
        
        if (!isset($availableTables[$table])) {
            return null;
        }
        
        return [
            'name' => $table,
            'display_name' => $availableTables[$table],
            'repository_class' => get_class(self::create($table)),
            'exists' => self::exists($table)
        ];
    }
}

