// =========================
// MODELO ACTUALIZADO (Model) - Conectado a sakila_es
// =========================
class SakilaModel {
    constructor() {
        // URL base de tu servidor XAMPP - ajusta la ruta según tu estructura
        this.apiUrl = 'http://localhost/Practica1TEPW/api.php'; // Ajusta la ruta según tu estructura
        this.cache = {}; // Cache local para mejorar rendimiento
        this.observers = [];

        this.tableFields = {
            peliculas: [
                {name: 'titulo', label: 'Título', type: 'text', required: true},
                {name: 'descripcion', label: 'Descripción', type: 'textarea'},
                {name: 'año_lanzamiento', label: 'Año de Lanzamiento', type: 'number', required: true},
                {name: 'duracion', label: 'Duración (min)', type: 'number', required: true},
                {name: 'calificacion', label: 'Calificación', type: 'select', options: ['G', 'PG', 'PG-13', 'R', 'NC-17']},
                {name: 'categoria', label: 'Categoría', type: 'text'},
                {name: 'idioma', label: 'Idioma', type: 'text'},
                {name: 'id_idioma', label: 'ID Idioma', type: 'number', required: true}
            ],
            actores: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellido', label: 'Apellidos', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email'},
                {name: 'activo', label: 'Activo', type: 'checkbox'}
            ],
            clientes: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellido', label: 'Apellidos', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email'},
                {name: 'telefono', label: 'Teléfono', type: 'tel'},
                {name: 'direccion', label: 'Dirección', type: 'text'},
                {name: 'activo', label: 'Activo', type: 'checkbox'},
                {name: 'tienda_id', label: 'ID Almacén', type: 'number'},
                {name: 'id_direccion', label: 'ID Dirección', type: 'number'}
            ],
            categorias: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'descripcion', label: 'Descripción', type: 'textarea'}
            ],
            tiendas: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'direccion', label: 'Dirección', type: 'text', required: true},
                {name: 'ciudad', label: 'Ciudad', type: 'text', required: true},
                {name: 'pais', label: 'País', type: 'text', required: true},
                {name: 'gerente', label: 'Gerente', type: 'text'}
            ],
            empleados: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellido', label: 'Apellidos', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email'},
                {name: 'tienda_id', label: 'ID Almacén', type: 'number', required: true},
                {name: 'cargo', label: 'Cargo', type: 'text'},
                {name: 'activo', label: 'Activo', type: 'checkbox'},
                {name: 'username', label: 'Usuario', type: 'text', required: true},
                {name: 'id_direccion', label: 'ID Dirección', type: 'number'}
            ],
            paises: [
                {name: 'nombre', label: 'Nombre del País', type: 'text', required: true}
            ],
            ciudades: [
                {name: 'nombre', label: 'Nombre de la Ciudad', type: 'text', required: true},
                {name: 'pais', label: 'País', type: 'text'},
                {name: 'id_pais', label: 'ID País', type: 'number', required: true}
            ],
            idiomas: [
                {name: 'nombre', label: 'Nombre del Idioma', type: 'text', required: true}
            ],
            inventario: [
                {name: 'pelicula', label: 'Película', type: 'text'},
                {name: 'tienda', label: 'Almacén', type: 'text'},
                {name: 'id_pelicula', label: 'ID Película', type: 'number', required: true},
                {name: 'tienda_id', label: 'ID Almacén', type: 'number', required: true}
            ],
            alquileres: [
                {name: 'fecha_alquiler', label: 'Fecha de Alquiler', type: 'datetime-local'},
                {name: 'fecha_devolucion', label: 'Fecha de Devolución', type: 'datetime-local'},
                {name: 'cliente', label: 'Cliente', type: 'text'},
                {name: 'pelicula', label: 'Película', type: 'text'},
                {name: 'empleado', label: 'Empleado', type: 'text'}
            ],
            pagos: [
                {name: 'total', label: 'Total', type: 'number', step: '0.01'},
                {name: 'fecha_pago', label: 'Fecha de Pago', type: 'datetime-local'},
                {name: 'cliente', label: 'Cliente', type: 'text'},
                {name: 'empleado', label: 'Empleado', type: 'text'}
            ]
        };

        this.tableNames = {
            peliculas: 'Películas',
            actores: 'Actores',
            clientes: 'Clientes',
            categorias: 'Categorías',
            tiendas: 'Almacenes/Tiendas',
            empleados: 'Empleados',
            paises: 'Países',
            ciudades: 'Ciudades',
            idiomas: 'Idiomas',
            inventario: 'Inventario',
            alquileres: 'Alquileres',
            pagos: 'Pagos'
        };
    }

    // Patrón Observer para notificar cambios
    subscribe(observer) {
        this.observers.push(observer);
    }

    notify(event, data) {
        this.observers.forEach(observer => {
            if (observer[event]) {
                observer[event](data);
            }
        });
    }

    // Método auxiliar para hacer peticiones HTTP
    async makeRequest(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                const errorText = await response.text();
                let errorData;
                try {
                    errorData = JSON.parse(errorText);
                } catch {
                    errorData = { error: `HTTP error! status: ${response.status}` };
                }
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error en petición:', error);
            throw error;
        }
    }

    // Operaciones CRUD asíncronas

    // GET - Obtener todos los registros
    async getAll(table) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/${table}`);
            this.cache[table] = data; // Cachear datos
            return data;
        } catch (error) {
            console.error(`Error obteniendo datos de ${table}:`, error);
            // Si hay error, devolver cache si existe
            return this.cache[table] || [];
        }
    }

    // GET - Obtener un registro por ID
    async getById(table, id) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/${table}/${id}`);
            return data;
        } catch (error) {
            console.error(`Error obteniendo registro ${id} de ${table}:`, error);
            // Buscar en cache como fallback
            const cachedData = this.cache[table];
            return cachedData?.find(item => item.id === parseInt(id)) || null;
        }
    }

    // POST - Crear nuevo registro
    async create(table, item) {
        try {
            const newItem = await this.makeRequest(`${this.apiUrl}/${table}`, {
                method: 'POST',
                body: JSON.stringify(item)
            });

            // Actualizar cache
            if (this.cache[table]) {
                this.cache[table].push(newItem);
            }

            this.notify('dataChanged', { table, action: 'create', item: newItem });
            return newItem;
        } catch (error) {
            console.error(`Error creando registro en ${table}:`, error);
            throw error;
        }
    }

    // PUT - Actualizar registro
    async update(table, id, updatedItem) {
        try {
            const updated = await this.makeRequest(`${this.apiUrl}/${table}/${id}`, {
                method: 'PUT',
                body: JSON.stringify(updatedItem)
            });

            // Actualizar cache
            if (this.cache[table]) {
                const index = this.cache[table].findIndex(item => item.id === parseInt(id));
                if (index > -1) {
                    this.cache[table][index] = updated;
                }
            }

            this.notify('dataChanged', { table, action: 'update', item: updated });
            return updated;
        } catch (error) {
            console.error(`Error actualizando registro ${id} en ${table}:`, error);
            throw error;
        }
    }

    // DELETE - Eliminar registro
    async delete(table, id) {
        try {
            const result = await this.makeRequest(`${this.apiUrl}/${table}/${id}`, {
                method: 'DELETE'
            });

            // Actualizar cache
            if (this.cache[table]) {
                this.cache[table] = this.cache[table].filter(item => item.id !== parseInt(id));
            }

            this.notify('dataChanged', { table, action: 'delete', item: { id: parseInt(id) } });
            return result;
        } catch (error) {
            console.error(`Error eliminando registro ${id} de ${table}:`, error);
            throw error;
        }
    }

    // Búsqueda
    async search(table, searchTerm) {
        if (!searchTerm || searchTerm.trim() === '') {
            return await this.getAll(table);
        }

        try {
            const results = await this.makeRequest(`${this.apiUrl}/${table}/search/${encodeURIComponent(searchTerm)}`);
            return results;
        } catch (error) {
            console.error(`Error buscando en ${table}:`, error);
            // Fallback: buscar en cache local
            const cachedData = this.cache[table] || [];
            return cachedData.filter(item => {
                return Object.values(item).some(value => 
                    String(value).toLowerCase().includes(searchTerm.toLowerCase())
                );
            });
        }
    }

    // Métodos de configuración
    getTableFields(table) {
        return this.tableFields[table] || [];
    }

    getTableName(table) {
        return this.tableNames[table] || table;
    }

    getAllTableNames() {
        return Object.keys(this.tableNames);
    }

    // Método para limpiar cache
    clearCache(table = null) {
        if (table) {
            delete this.cache[table];
        } else {
            this.cache = {};
        }
    }

    // Método para verificar conexión con el backend
    async checkConnection() {
        try {
            const response = await fetch(`${this.apiUrl}/peliculas`);
            return response.ok;
        } catch (error) {
            console.error('Error verificando conexión:', error);
            return false;
        }
    }

    // Método para trabajar en modo offline con datos de ejemplo
    useOfflineMode() {
        console.warn('Usando modo offline con datos de ejemplo');
        this.cache = {
            peliculas: [
                {id: 1, titulo: 'El Padrino', descripcion: 'Drama épico sobre la mafia', año_lanzamiento: 1972, duracion: 175, calificacion: 'R', categoria: 'Drama', idioma: 'Español'},
                {id: 2, titulo: 'Pulp Fiction', descripcion: 'Película de culto de Tarantino', año_lanzamiento: 1994, duracion: 154, calificacion: 'R', categoria: 'Crimen', idioma: 'Inglés'}
            ],
            actores: [
                {id: 1, nombre: 'Marlon', apellido: 'Brando', email: 'marlon.brando@email.com', activo: true},
                {id: 2, nombre: 'Al', apellido: 'Pacino', email: 'al.pacino@email.com', activo: true}
            ],
            clientes: [
                {id: 1, nombre: 'Juan', apellido: 'Pérez', email: 'juan.perez@email.com', telefono: '555-0101', direccion: 'Calle Principal 123, Ciudad de México', activo: true, tienda_id: 1}
            ],
            categorias: [
                {id: 1, nombre: 'Drama', descripcion: 'Películas dramáticas y emocionales'},
                {id: 2, nombre: 'Acción', descripcion: 'Películas de acción y aventura'}
            ],
            tiendas: [
                {id: 1, nombre: 'Almacén Centro', direccion: 'Centro Histórico #123', ciudad: 'Ciudad de México', pais: 'México', gerente: 'Ana López'}
            ],
            empleados: [
                {id: 1, nombre: 'Pedro', apellido: 'Sánchez', email: 'pedro.sanchez@sakila.com', tienda_id: 1, activo: true, cargo: 'Gerente', username: 'psanchez'}
            ],
            paises: [
                {id: 1, nombre: 'México'},
                {id: 2, nombre: 'Estados Unidos'},
                {id: 3, nombre: 'España'}
            ],
            ciudades: [
                {id: 1, nombre: 'Ciudad de México', pais: 'México', id_pais: 1},
                {id: 2, nombre: 'Guadalajara', pais: 'México', id_pais: 1}
            ],
            idiomas: [
                {id: 1, nombre: 'Español'},
                {id: 2, nombre: 'Inglés'},
                {id: 3, nombre: 'Francés'}
            ]
        };
    }

    // Método helper para obtener la URL correcta de la API
    setApiUrl(newUrl) {
        this.apiUrl = newUrl;
    }

    // Método para probar diferentes URLs automáticamente
    async autoDetectApiUrl() {
        const possibleUrls = [
            'http://localhost/Practica1TEPW/api.php',
            'http://localhost:80/Practica1TEPW/api.php',
            'http://localhost/api.php',
            'http://127.0.0.1/Practica1TEPW/api.php',
            'http://127.0.0.1:80/Practica1TEPW/api.php',
            'http://localhost:8080/Practica1TEPW/api.php'
        ];

        for (const url of possibleUrls) {
            try {
                const testUrl = url + '/peliculas';
                const response = await fetch(testUrl);
                if (response.ok) {
                    this.apiUrl = url;
                    console.log(`API detectada en: ${url}`);
                    return true;
                }
            } catch (error) {
                // Continuar con la siguiente URL
            }
        }
        
        console.warn('No se pudo detectar automáticamente la URL de la API');
        return false;
    }

    // Métodos específicos para relaciones complejas de sakila_es

    // Obtener películas con sus actores
    async getPeliculasConActores(idPelicula = null) {
        try {
            let query = idPelicula ? 
                `${this.apiUrl}/peliculas/${idPelicula}/actores` : 
                `${this.apiUrl}/peliculas-actores`;
            
            // Como esta consulta específica no está implementada en la API,
            // obtenemos los datos por separado y los combinamos
            const peliculas = idPelicula ? 
                [await this.getById('peliculas', idPelicula)] : 
                await this.getAll('peliculas');
            
            return peliculas;
        } catch (error) {
            console.error('Error obteniendo películas con actores:', error);
            return [];
        }
    }

    // Obtener inventario disponible
    async getInventarioDisponible() {
        try {
            const inventario = await this.getAll('inventario');
            return inventario;
        } catch (error) {
            console.error('Error obteniendo inventario disponible:', error);
            return [];
        }
    }

    // Obtener estadísticas básicas
    async getEstadisticas() {
        try {
            const stats = {
                totalPeliculas: 0,
                totalActores: 0,
                totalClientes: 0,
                totalAlquileres: 0
            };

            // Obtener conteos básicos
            const peliculas = await this.getAll('peliculas');
            const actores = await this.getAll('actores');
            const clientes = await this.getAll('clientes');
            const alquileres = await this.getAll('alquileres');

            stats.totalPeliculas = peliculas.length;
            stats.totalActores = actores.length;
            stats.totalClientes = clientes.length;
            stats.totalAlquileres = alquileres.length;

            return stats;
        } catch (error) {
            console.error('Error obteniendo estadísticas:', error);
            return {
                totalPeliculas: 0,
                totalActores: 0,
                totalClientes: 0,
                totalAlquileres: 0
            };
        }
    }

    // Validar datos antes de enviar
    validateData(table, data) {
        const fields = this.getTableFields(table);
        const errors = [];

        fields.forEach(field => {
            if (field.required && (!data[field.name] || data[field.name].toString().trim() === '')) {
                errors.push(`El campo ${field.label} es requerido`);
            }

            if (field.type === 'email' && data[field.name] && 
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data[field.name])) {
                errors.push(`El campo ${field.label} debe ser un email válido`);
            }

            if (field.type === 'number' && data[field.name] && 
                isNaN(data[field.name])) {
                errors.push(`El campo ${field.label} debe ser un número`);
            }
        });

        return errors;
    }

    // Crear con validación
    async createWithValidation(table, item) {
        const errors = this.validateData(table, item);
        if (errors.length > 0) {
            throw new Error(`Errores de validación: ${errors.join(', ')}`);
        }
        return await this.create(table, item);
    }

    // Actualizar con validación
    async updateWithValidation(table, id, item) {
        const errors = this.validateData(table, item);
        if (errors.length > 0) {
            throw new Error(`Errores de validación: ${errors.join(', ')}`);
        }
        return await this.update(table, id, item);
    }
}