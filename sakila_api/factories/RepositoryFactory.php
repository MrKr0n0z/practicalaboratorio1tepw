<?php
class RepositoryFactory {
    private static $repositories = [];
    
    public static function create($table) {
        if (!isset(self::$repositories[$table])) {
            switch($table) {
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
                case 'pais':
                    self::$repositories[$table] = new PaisRepository();
                    break;
                case 'ciudad':
                    self::$repositories[$table] = new CiudadRepository();
                    break;    
                case 'direccion':
                    self::$repositories[$table] = new DireccionRepository();
                    break;        
                case 'idioma':
                    self::$repositories[$table] = new IdiomaRepository();
                    break;        
                case 'almacen':
                    self::$repositories[$table] = new AlmacenRepository();
                    break;
                case 'inventario':
                    self::$repositories[$table] = new InventarioRepository();
                    break;      
                case 'alquiler':
                    self::$repositories[$table] = new AlquilerRepository();
                    break;           
                default:
                    throw new InvalidArgumentException("Repository for table '$table' not found");
            }
        }
        
        return self::$repositories[$table];
    }
}
?>