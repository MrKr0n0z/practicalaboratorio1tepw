// =========================
// MODELO ACTUALIZADO (Model) - Compatible con Repository Pattern
// =========================
class SakilaModel {
    constructor() {
        // URL base de tu servidor XAMPP - ajusta la ruta según tu estructura
        this.apiUrl = 'http://localhost/Practica1TEPW/sakila_api/api.php';
        this.cache = {}; // Cache local para mejorar rendimiento
        this.observers = [];

        this.tableFields = {
            peliculas: [
                {name: 'titulo', label: 'Título', type: 'text', required: true},
                {name: 'descripcion', label: 'Descripción', type: 'textarea'},
                {name: 'anyo_lanzamiento', label: 'Año de Lanzamiento', type: 'number', required: true},
                {name: 'duracion', label: 'Duración (min)', type: 'number', required: true},
                {name: 'clasificacion', label: 'Clasificación', type: 'select', options: ['G', 'PG', 'PG-13', 'R', 'NC-17']},
                {name: 'categoria', label: 'Categoría', type: 'text', readonly: true},
                {name: 'idioma', label: 'Idioma', type: 'text', readonly: true},
                {name: 'id_idioma', label: 'ID Idioma', type: 'number', required: true},
                {name: 'duracion_alquiler', label: 'Duración Alquiler (días)', type: 'number'},
                {name: 'rental_rate', label: 'Precio Alquiler', type: 'number', step: '0.01'},
                {name: 'replacement_cost', label: 'Costo Reemplazo', type: 'number', step: '0.01'},
                {name: 'caracteristicas_especiales', label: 'Características Especiales', type: 'text', readonly: true}
            ],
            actores: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellidos', label: 'Apellidos', type: 'text', required: true},
                {name: 'email_generado', label: 'Email Generado', type: 'email', readonly: true}
            ],
            clientes: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellidos', label: 'Apellidos', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'telefono', label: 'Teléfono', type: 'tel', readonly: true},
                {name: 'direccion_completa', label: 'Dirección Completa', type: 'text', readonly: true},
                {name: 'activo', label: 'Activo', type: 'checkbox'},
                {name: 'id_almacen', label: 'ID Almacén', type: 'number'},
                {name: 'id_direccion', label: 'ID Dirección', type: 'number'},
                {name: 'fecha_creacion', label: 'Fecha de Creación', type: 'datetime-local', readonly: true}
            ],
            categorias: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true}
            ],
            empleados: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellidos', label: 'Apellidos', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'id_almacen', label: 'ID Almacén', type: 'number', required: true},
                {name: 'activo', label: 'Activo', type: 'checkbox'},
                {name: 'username', label: 'Usuario', type: 'text', required: true},
                {name: 'id_direccion', label: 'ID Dirección', type: 'number'},
                {name: 'direccion_completa', label: 'Dirección Completa', type: 'text', readonly: true},
                {name: 'cargo', label: 'Cargo', type: 'text', readonly: true}
            ],
            paises: [
                {name: 'nombre', label: 'Nombre del País', type: 'text', required: true}
            ],
            ciudades: [
                {name: 'nombre', label: 'Nombre de la Ciudad', type: 'text', required: true},
                {name: 'pais', label: 'País', type: 'text', readonly: true},
                {name: 'id_pais', label: 'ID País', type: 'number', required: true}
            ],
            direcciones: [
                {name: 'direccion', label: 'Dirección', type: 'text', required: true},
                {name: 'direccion2', label: 'Dirección 2', type: 'text'},
                {name: 'distrito', label: 'Distrito', type: 'text', required: true},
                {name: 'id_ciudad', label: 'ID Ciudad', type: 'number', required: true},
                {name: 'codigo_postal', label: 'Código Postal', type: 'text'},
                {name: 'telefono', label: 'Teléfono', type: 'tel', required: true},
                {name: 'ciudad', label: 'Ciudad', type: 'text', readonly: true},
                {name: 'pais', label: 'País', type: 'text', readonly: true}
            ],
            idiomas: [
                {name: 'nombre', label: 'Nombre del Idioma', type: 'text', required: true}
            ],
            tiendas: [
                {name: 'nombre', label: 'Nombre', type: 'text', readonly: true},
                {name: 'direccion', label: 'Dirección', type: 'text', readonly: true},
                {name: 'ciudad', label: 'Ciudad', type: 'text', readonly: true},
                {name: 'pais', label: 'País', type: 'text', readonly: true},
                {name: 'gerente', label: 'Gerente', type: 'text', readonly: true},
                {name: 'id_empleado_jefe', label: 'ID Empleado Jefe', type: 'number', required: true},
                {name: 'id_direccion', label: 'ID Dirección', type: 'number', required: true}
            ],
            inventario: [
                {name: 'pelicula', label: 'Película', type: 'text', readonly: true},
                {name: 'tienda', label: 'Tienda', type: 'text', readonly: true},
                {name: 'id_pelicula', label: 'ID Película', type: 'number', required: true},
                {name: 'id_almacen', label: 'ID Almacén', type: 'number', required: true}
            ],
            alquileres: [
                {name: 'fecha_alquiler', label: 'Fecha de Alquiler', type: 'datetime-local'},
                {name: 'fecha_devolucion', label: 'Fecha de Devolución', type: 'datetime-local'},
                {name: 'cliente', label: 'Cliente', type: 'text', readonly: true},
                {name: 'pelicula', label: 'Película', type: 'text', readonly: true},
                {name: 'empleado', label: 'Empleado', type: 'text', readonly: true},
                {name: 'id_cliente', label: 'ID Cliente', type: 'number', required: true},
                {name: 'id_inventario', label: 'ID Inventario', type: 'number', required: true},
                {name: 'id_empleado', label: 'ID Empleado', type: 'number', required: true}
            ],
            pagos: [
                {name: 'total', label: 'Total', type: 'number', step: '0.01', required: true},
                {name: 'fecha_pago', label: 'Fecha de Pago', type: 'datetime-local'},
                {name: 'cliente', label: 'Cliente', type: 'text', readonly: true},
                {name: 'empleado', label: 'Empleado', type: 'text', readonly: true},
                {name: 'id_cliente', label: 'ID Cliente', type: 'number', required: true},
                {name: 'id_empleado', label: 'ID Empleado', type: 'number', required: true},
                {name: 'id_alquiler', label: 'ID Alquiler', type: 'number', required: true}
            ],
            // NUEVAS TABLAS AGREGADAS
            'film-text': [
                {name: 'film_id', label: 'ID Película', type: 'number', required: true},
                {name: 'title', label: 'Título', type: 'text', required: true},
                {name: 'description', label: 'Descripción', type: 'textarea', required: true}
            ],
            'pelicula-actor': [
                {name: 'id_pelicula', label: 'ID Película', type: 'number', required: true},
                {name: 'id_actor', label: 'ID Actor', type: 'number', required: true},
                {name: 'pelicula', label: 'Película', type: 'text', readonly: true},
                {name: 'actor', label: 'Actor', type: 'text', readonly: true},
                {name: 'actor_nombre', label: 'Nombre Actor', type: 'text', readonly: true},
                {name: 'actor_apellidos', label: 'Apellidos Actor', type: 'text', readonly: true},
                {name: 'anyo_lanzamiento', label: 'Año Lanzamiento', type: 'number', readonly: true}
            ],
            'pelicula-categoria': [
                {name: 'id_pelicula', label: 'ID Película', type: 'number', required: true},
                {name: 'id_categoria', label: 'ID Categoría', type: 'number', required: true},
                {name: 'pelicula', label: 'Película', type: 'text', readonly: true},
                {name: 'categoria', label: 'Categoría', type: 'text', readonly: true},
                {name: 'anyo_lanzamiento', label: 'Año Lanzamiento', type: 'number', readonly: true},
                {name: 'duracion', label: 'Duración', type: 'number', readonly: true}
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
            direcciones: 'Direcciones',
            idiomas: 'Idiomas',
            inventario: 'Inventario',
            alquileres: 'Alquileres',
            pagos: 'Pagos',
            // NUEVAS TABLAS
            'film-text': 'Texto de Películas',
            'pelicula-actor': 'Películas-Actores',
            'pelicula-categoria': 'Películas-Categorías'
        };
    }

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
            this.cache[table] = data;
            return data;
        } catch (error) {
            console.error(`Error obteniendo datos de ${table}:`, error);
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
            const cachedData = this.cache[table];
            return cachedData?.find(item => item.id === parseInt(id)) || null;
        }
    }

    // POST - Crear nuevo registro
    async create(table, item) {
        try {
            const cleanedItem = this.cleanDataForAPI(table, item);
            
            const newItem = await this.makeRequest(`${this.apiUrl}/${table}`, {
                method: 'POST',
                body: JSON.stringify(cleanedItem)
            });

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
            const cleanedItem = this.cleanDataForAPI(table, updatedItem);
            
            const updated = await this.makeRequest(`${this.apiUrl}/${table}/${id}`, {
                method: 'PUT',
                body: JSON.stringify(cleanedItem)
            });

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

    // DELETE - Eliminar registro (maneja composite keys)
    async delete(table, id) {
        try {
            let result;
            
            // Para tablas de relación que usan composite keys
            if (table === 'pelicula-actor' || table === 'pelicula-categoria') {
                if (typeof id === 'object') {
                    // Eliminar relación específica
                    const queryParams = new URLSearchParams(id).toString();
                    result = await this.makeRequest(`${this.apiUrl}/${table}?${queryParams}`, {
                        method: 'DELETE'
                    });
                } else {
                    // Eliminar todas las relaciones de una película
                    result = await this.makeRequest(`${this.apiUrl}/${table}/${id}`, {
                        method: 'DELETE'
                    });
                }
            } else {
                // Eliminación normal por ID
                result = await this.makeRequest(`${this.apiUrl}/${table}/${id}`, {
                    method: 'DELETE'
                });
            }

            // Actualizar cache
            if (this.cache[table]) {
                if (typeof id === 'object') {
                    // Filtrar por composite key
                    this.cache[table] = this.cache[table].filter(item => 
                        !(item.id_pelicula === id.id_pelicula && item.id_actor === id.id_actor) &&
                        !(item.id_pelicula === id.id_pelicula && item.id_categoria === id.id_categoria)
                    );
                } else {
                    this.cache[table] = this.cache[table].filter(item => item.id !== parseInt(id));
                }
            }

            this.notify('dataChanged', { table, action: 'delete', item: { id } });
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
            const cachedData = this.cache[table] || [];
            return cachedData.filter(item => {
                return Object.values(item).some(value => 
                    String(value).toLowerCase().includes(searchTerm.toLowerCase())
                );
            });
        }
    }

    // NUEVOS MÉTODOS PARA LAS TABLAS DE RELACIÓN

    // Obtener actores de una película específica
    async getActoresByPelicula(idPelicula) {
        try {
            // Usar método especial del repositorio si está disponible
            const data = await this.makeRequest(`${this.apiUrl}/pelicula-actor/pelicula/${idPelicula}`);
            return data;
        } catch (error) {
            console.error(`Error obteniendo actores de película ${idPelicula}:`, error);
            // Fallback: buscar en datos cacheados
            const cached = this.cache['pelicula-actor'] || [];
            return cached.filter(item => item.id_pelicula === parseInt(idPelicula));
        }
    }

    // Obtener películas de un actor específico
    async getPeliculasByActor(idActor) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/pelicula-actor/actor/${idActor}`);
            return data;
        } catch (error) {
            console.error(`Error obteniendo películas de actor ${idActor}:`, error);
            const cached = this.cache['pelicula-actor'] || [];
            return cached.filter(item => item.id_actor === parseInt(idActor));
        }
    }

    // Obtener categorías de una película específica
    async getCategoriasByPelicula(idPelicula) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/pelicula-categoria/pelicula/${idPelicula}`);
            return data;
        } catch (error) {
            console.error(`Error obteniendo categorías de película ${idPelicula}:`, error);
            const cached = this.cache['pelicula-categoria'] || [];
            return cached.filter(item => item.id_pelicula === parseInt(idPelicula));
        }
    }

    // Obtener películas de una categoría específica
    async getPeliculasByCategoria(idCategoria) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/pelicula-categoria/categoria/${idCategoria}`);
            return data;
        } catch (error) {
            console.error(`Error obteniendo películas de categoría ${idCategoria}:`, error);
            const cached = this.cache['pelicula-categoria'] || [];
            return cached.filter(item => item.id_categoria === parseInt(idCategoria));
        }
    }

    // Búsqueda de texto completo en film_text
    async fullTextSearch(searchTerm) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/film-text/fulltext/${encodeURIComponent(searchTerm)}`);
            return data;
        } catch (error) {
            console.error(`Error en búsqueda de texto completo:`, error);
            // Fallback a búsqueda normal
            return await this.search('film-text', searchTerm);
        }
    }

    // Búsqueda booleana en film_text
    async booleanSearch(searchTerm) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/film-text/boolean/${encodeURIComponent(searchTerm)}`);
            return data;
        } catch (error) {
            console.error(`Error en búsqueda booleana:`, error);
            return await this.search('film-text', searchTerm);
        }
    }

    // Sincronizar film_text con película
    async syncFilmText(idPelicula) {
        try {
            const data = await this.makeRequest(`${this.apiUrl}/film-text/sync/${idPelicula}`, {
                method: 'POST'
            });
            return data;
        } catch (error) {
            console.error(`Error sincronizando film_text para película ${idPelicula}:`, error);
            throw error;
        }
    }

    // Limpiar datos antes de enviar a la API
    cleanDataForAPI(table, data) {
        const fields = this.getTableFields(table);
        const cleanedData = {};
        
        fields.forEach(field => {
            if (!field.readonly && data.hasOwnProperty(field.name)) {
                let value = data[field.name];
                
                if (field.type === 'number' && value !== null && value !== '') {
                    value = parseFloat(value);
                } else if (field.type === 'checkbox') {
                    value = Boolean(value);
                }
                
                cleanedData[field.name] = value;
            }
        });
        
        return cleanedData;
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

    // Validar datos antes de enviar
    validateData(table, data) {
        const fields = this.getTableFields(table);
        const errors = [];

        fields.forEach(field => {
            if (field.required && !field.readonly && (!data[field.name] || data[field.name].toString().trim() === '')) {
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

    // Obtener estadísticas mejoradas
    async getEstadisticas() {
        try {
            const stats = {
                totalPeliculas: 0,
                totalActores: 0,
                totalClientes: 0,
                totalAlquileres: 0,
                totalCategorias: 0,
                totalEmpleados: 0,
                relacionesPeliculaActor: 0,
                relacionesPeliculaCategoria: 0
            };

            // Obtener conteos de todas las tablas
            const [peliculas, actores, clientes, alquileres, categorias, empleados, peliculaActor, peliculaCategoria] = await Promise.all([
                this.getAll('peliculas'),
                this.getAll('actores'),
                this.getAll('clientes'),
                this.getAll('alquileres'),
                this.getAll('categorias'),
                this.getAll('empleados'),
                this.getAll('pelicula-actor'),
                this.getAll('pelicula-categoria')
            ]);

            stats.totalPeliculas = peliculas.length;
            stats.totalActores = actores.length;
            stats.totalClientes = clientes.length;
            stats.totalAlquileres = alquileres.length;
            stats.totalCategorias = categorias.length;
            stats.totalEmpleados = empleados.length;
            stats.relacionesPeliculaActor = peliculaActor.length;
            stats.relacionesPeliculaCategoria = peliculaCategoria.length;

            return stats;
        } catch (error) {
            console.error('Error obteniendo estadísticas:', error);
            return {
                totalPeliculas: 0,
                totalActores: 0,
                totalClientes: 0,
                totalAlquileres: 0,
                totalCategorias: 0,
                totalEmpleados: 0,
                relacionesPeliculaActor: 0,
                relacionesPeliculaCategoria: 0
            };
        }
    }

    // Modo offline mejorado con las nuevas tablas
    useOfflineMode() {
        console.warn('Usando modo offline con datos de ejemplo completos');
        this.cache = {
            peliculas: [
                {id: 1, titulo: 'El Padrino', descripcion: 'Drama épico sobre la mafia', anyo_lanzamiento: 1972, duracion: 175, clasificacion: 'R', categoria: 'Drama', idioma: 'Español', id_idioma: 1, duracion_alquiler: 3, rental_rate: 4.99, replacement_cost: 19.99, caracteristicas_especiales: 'Deleted Scenes,Behind the Scenes'},
                {id: 2, titulo: 'Pulp Fiction', descripcion: 'Película de culto de Tarantino', anyo_lanzamiento: 1994, duracion: 154, clasificacion: 'R', categoria: 'Crimen', idioma: 'Inglés', id_idioma: 2, duracion_alquiler: 5, rental_rate: 3.99, replacement_cost: 24.99, caracteristicas_especiales: 'Trailers,Commentaries'}
            ],
            actores: [
                {id: 1, nombre: 'Marlon', apellidos: 'Brando', email_generado: 'marlon.brando@email.com'},
                {id: 2, nombre: 'Al', apellidos: 'Pacino', email_generado: 'al.pacino@email.com'},
                {id: 3, nombre: 'John', apellidos: 'Travolta', email_generado: 'john.travolta@email.com'}
            ],
            clientes: [
                {id: 1, nombre: 'Juan', apellidos: 'Pérez', email: 'juan.perez@email.com', telefono: '555-0101', direccion_completa: 'Calle Principal 123, Ciudad de México, México', activo: true, id_almacen: 1, id_direccion: 1, fecha_creacion: '2024-01-01T10:00:00'}
            ],
            categorias: [
                {id: 1, nombre: 'Drama'},
                {id: 2, nombre: 'Acción'},
                {id: 3, nombre: 'Comedia'},
                {id: 4, nombre: 'Crimen'}
            ],
            empleados: [
                {id: 1, nombre: 'Pedro', apellidos: 'Sánchez', email: 'pedro.sanchez@sakila.com', id_almacen: 1, activo: true, username: 'psanchez', id_direccion: 1, direccion_completa: 'Av. Reforma 456, Ciudad de México', cargo: 'Empleado'}
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
            direcciones: [
                {id: 1, direccion: 'Calle Principal 123', direccion2: 'Apt 4B', distrito: 'Centro', codigo_postal: '06000', telefono: '555-0101', ciudad: 'Ciudad de México', pais: 'México', id_ciudad: 1}
            ],
            idiomas: [
                {id: 1, nombre: 'Español'},
                {id: 2, nombre: 'Inglés'},
                {id: 3, nombre: 'Francés'}
            ],
            tiendas: [
                {id: 1, nombre: 'Tienda Ciudad de México', direccion: 'Centro Histórico #123', ciudad: 'Ciudad de México', pais: 'México', gerente: 'Ana López', id_empleado_jefe: 1, id_direccion: 1}
            ],
            inventario: [
                {id: 1, pelicula: 'El Padrino', tienda: 'Tienda Ciudad de México', id_pelicula: 1, id_almacen: 1},
                {id: 2, pelicula: 'Pulp Fiction', tienda: 'Tienda Ciudad de México', id_pelicula: 2, id_almacen: 1}
            ],
            alquileres: [
                {id: 1, fecha_alquiler: '2024-01-15T14:30:00', fecha_devolucion: '2024-01-18T16:00:00', cliente: 'Juan Pérez', pelicula: 'El Padrino', empleado: 'Pedro Sánchez', id_cliente: 1, id_inventario: 1, id_empleado: 1}
            ],
            pagos: [
                {id: 1, total: 4.99, fecha_pago: '2024-01-15T14:30:00', cliente: 'Juan Pérez', empleado: 'Pedro Sánchez', id_cliente: 1, id_empleado: 1, id_alquiler: 1}
            ],
            // NUEVAS TABLAS
            'film-text': [
                {id: 1, film_id: 1, title: 'El Padrino', description: 'Drama épico sobre la mafia italiana en Estados Unidos'},
                {id: 2, film_id: 2, title: 'Pulp Fiction', description: 'Película de culto de Quentin Tarantino con narrativa no lineal'}
            ],
            'pelicula-actor': [
                {id_pelicula: 1, id_actor: 1, pelicula: 'El Padrino', actor: 'Marlon Brando', actor_nombre: 'Marlon', actor_apellidos: 'Brando', anyo_lanzamiento: 1972},
                {id_pelicula: 1, id_actor: 2, pelicula: 'El Padrino', actor: 'Al Pacino', actor_nombre: 'Al', actor_apellidos: 'Pacino', anyo_lanzamiento: 1972},
                {id_pelicula: 2, id_actor: 3, pelicula: 'Pulp Fiction', actor: 'John Travolta', actor_nombre: 'John', actor_apellidos: 'Travolta', anyo_lanzamiento: 1994}
            ],
            'pelicula-categoria': [
                {id_pelicula: 1, id_categoria: 1, pelicula: 'El Padrino', categoria: 'Drama', anyo_lanzamiento: 1972, duracion: 175},
                {id_pelicula: 2, id_categoria: 4, pelicula: 'Pulp Fiction', categoria: 'Crimen', anyo_lanzamiento: 1994, duracion: 154}
            ]
        };
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

    // MÉTODOS ESPECIALES PARA MANEJO DE RELACIONES COMPLEJAS

    // Asignar múltiples actores a una película
    async assignActoresToPelicula(idPelicula, actorIds) {
        try {
            const results = [];
            for (const idActor of actorIds) {
                const relation = await this.create('pelicula-actor', {
                    id_pelicula: idPelicula,
                    id_actor: idActor
                });
                results.push(relation);
            }
            return results;
        } catch (error) {
            console.error('Error asignando actores a película:', error);
            throw error;
        }
    }

    // Asignar múltiples categorías a una película
    async assignCategoriesToPelicula(idPelicula, categoriaIds) {
        try {
            const results = [];
            for (const idCategoria of categoriaIds) {
                const relation = await this.create('pelicula-categoria', {
                    id_pelicula: idPelicula,
                    id_categoria: idCategoria
                });
                results.push(relation);
            }
            return results;
        } catch (error) {
            console.error('Error asignando categorías a película:', error);
            throw error;
        }
    }

    // Obtener información completa de una película (con actores y categorías)
    async getPeliculaCompleta(idPelicula) {
        try {
            const [pelicula, actores, categorias] = await Promise.all([
                this.getById('peliculas', idPelicula),
                this.getActoresByPelicula(idPelicula),
                this.getCategoriasByPelicula(idPelicula)
            ]);

            return {
                ...pelicula,
                actores: actores || [],
                categorias: categorias || []
            };
        } catch (error) {
            console.error(`Error obteniendo información completa de película ${idPelicula}:`, error);
            return null;
        }
    }

    // Obtener información completa de un actor (con sus películas)
    async getActorCompleto(idActor) {
        try {
            const [actor, peliculas] = await Promise.all([
                this.getById('actores', idActor),
                this.getPeliculasByActor(idActor)
            ]);

            return {
                ...actor,
                peliculas: peliculas || []
            };
        } catch (error) {
            console.error(`Error obteniendo información completa de actor ${idActor}:`, error);
            return null;
        }
    }

    // Búsquedas especializadas
    async searchPeliculasWithActors(searchTerm) {
        try {
            const results = await this.search('pelicula-actor', searchTerm);
            return results;
        } catch (error) {
            console.error('Error buscando películas con actores:', error);
            return [];
        }
    }

    async searchPeliculasWithCategories(searchTerm) {
        try {
            const results = await this.search('pelicula-categoria', searchTerm);
            return results;
        } catch (error) {
            console.error('Error buscando películas con categorías:', error);
            return [];
        }
    }

    // Métodos de análisis y reportes
    async getPopularActors(limit = 10) {
        try {
            const peliculaActores = await this.getAll('pelicula-actor');
            const actorCount = {};
            
            peliculaActores.forEach(pa => {
                actorCount[pa.id_actor] = (actorCount[pa.id_actor] || 0) + 1;
            });

            const sortedActors = Object.entries(actorCount)
                .sort(([,a], [,b]) => b - a)
                .slice(0, limit);

            const popularActors = [];
            for (const [idActor, count] of sortedActors) {
                const actor = await this.getById('actores', idActor);
                if (actor) {
                    popularActors.push({
                        ...actor,
                        total_peliculas: count
                    });
                }
            }

            return popularActors;
        } catch (error) {
            console.error('Error obteniendo actores populares:', error);
            return [];
        }
    }

    async getPopularCategories(limit = 10) {
        try {
            const peliculaCategorias = await this.getAll('pelicula-categoria');
            const categoryCount = {};
            
            peliculaCategorias.forEach(pc => {
                categoryCount[pc.id_categoria] = (categoryCount[pc.id_categoria] || 0) + 1;
            });

            const sortedCategories = Object.entries(categoryCount)
                .sort(([,a], [,b]) => b - a)
                .slice(0, limit);

            const popularCategories = [];
            for (const [idCategoria, count] of sortedCategories) {
                const categoria = await this.getById('categorias', idCategoria);
                if (categoria) {
                    popularCategories.push({
                        ...categoria,
                        total_peliculas: count
                    });
                }
            }

            return popularCategories;
        } catch (error) {
            console.error('Error obteniendo categorías populares:', error);
            return [];
        }
    }
}