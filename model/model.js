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
                {name: 'anyo_lanzamiento', label: 'Año de Lanzamiento', type: 'number', required: true},
                {name: 'duracion', label: 'Duración (min)', type: 'number', required: true},
                {name: 'clasificacion', label: 'Clasificación', type: 'select', options: ['G', 'PG', 'PG-13', 'R', 'NC-17']},
                {name: 'categoria', label: 'Categoría', type: 'text'},
                {name: 'idioma', label: 'Idioma', type: 'text'},
                {name: 'id_idioma', label: 'ID Idioma', type: 'number', required: true},
                {name: 'duracion_alquiler', label: 'Duración Alquiler (días)', type: 'number'},
                {name: 'rental_rate', label: 'Precio Alquiler', type: 'number', step: '0.01'},
                {name: 'replacement_cost', label: 'Costo Reemplazo', type: 'number', step: '0.01'}
            ],
            actores: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellidos', label: 'Apellidos', type: 'text', required: true} // Corregido: apellidos no apellido
            ],
            clientes: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellidos', label: 'Apellidos', type: 'text', required: true}, // Corregido: apellidos no apellido
                {name: 'email', label: 'Email', type: 'email'},
                {name: 'telefono', label: 'Teléfono', type: 'tel'},
                {name: 'direccion_completa', label: 'Dirección', type: 'text', readonly: true}, // Solo lectura
                {name: 'activo', label: 'Activo', type: 'checkbox'},
                {name: 'id_almacen', label: 'ID Almacén', type: 'number'}, // Corregido: id_almacen no tienda_id
                {name: 'id_direccion', label: 'ID Dirección', type: 'number'}
            ],
            categorias: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true}
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
                {name: 'apellidos', label: 'Apellidos', type: 'text', required: true}, // Corregido: apellidos no apellido
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'id_almacen', label: 'ID Almacén', type: 'number', required: true}, // Corregido: id_almacen no tienda_id
                {name: 'activo', label: 'Activo', type: 'checkbox'},
                {name: 'username', label: 'Usuario', type: 'text', required: true},
                {name: 'id_direccion', label: 'ID Dirección', type: 'number'}
            ],
            paises: [
                {name: 'nombre', label: 'Nombre del País', type: 'text', required: true}
            ],
            ciudades: [
                {name: 'nombre', label: 'Nombre de la Ciudad', type: 'text', required: true},
                {name: 'pais', label: 'País', type: 'text', readonly: true}, // Solo lectura
                {name: 'id_pais', label: 'ID País', type: 'number', required: true}
            ],
            direcciones: [ // Añadido soporte completo para direcciones
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
            inventario: [
                {name: 'pelicula', label: 'Película', type: 'text', readonly: true},
                {name: 'tienda', label: 'Almacén', type: 'text', readonly: true},
                {name: 'id_pelicula', label: 'ID Película', type: 'number', required: true},
                {name: 'id_almacen', label: 'ID Almacén', type: 'number', required: true} // Corregido: id_almacen no tienda_id
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
                {name: 'fecha_pago', label: 'Fecha de Pago', type: 'datetime-local', required: true},
                {name: 'cliente', label: 'Cliente', type: 'text', readonly: true},
                {name: 'empleado', label: 'Empleado', type: 'text', readonly: true},
                {name: 'id_cliente', label: 'ID Cliente', type: 'number', required: true},
                {name: 'id_empleado', label: 'ID Empleado', type: 'number', required: true},
                {name: 'id_alquiler', label: 'ID Alquiler', type: 'number'}
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
            direcciones: 'Direcciones', // Añadido
            idiomas: 'Idiomas',
            inventario: 'Inventario',
            alquileres: 'Alquileres',
            pagos: 'Pagos'
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
            // Limpiar datos antes de enviar - remover campos readonly
            const cleanedItem = this.cleanDataForAPI(table, item);
            
            const newItem = await this.makeRequest(`${this.apiUrl}/${table}`, {
                method: 'POST',
                body: JSON.stringify(cleanedItem)
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
            // Limpiar datos antes de enviar - remover campos readonly
            const cleanedItem = this.cleanDataForAPI(table, updatedItem);
            
            const updated = await this.makeRequest(`${this.apiUrl}/${table}/${id}`, {
                method: 'PUT',
                body: JSON.stringify(cleanedItem)
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

    // Nuevo método para limpiar datos antes de enviar a la API
    cleanDataForAPI(table, data) {
        const fields = this.getTableFields(table);
        const cleanedData = {};
        
        fields.forEach(field => {
            if (!field.readonly && data.hasOwnProperty(field.name)) {
                // Convertir valores según el tipo
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

    // Método para trabajar en modo offline con datos de ejemplo compatibles con sakila_es
    useOfflineMode() {
        console.warn('Usando modo offline con datos de ejemplo compatibles con sakila_es');
        this.cache = {
            peliculas: [
                {id: 1, titulo: 'El Padrino', descripcion: 'Drama épico sobre la mafia', anyo_lanzamiento: 1972, duracion: 175, clasificacion: 'R', categoria: 'Drama', idioma: 'Español', duracion_alquiler: 3, rental_rate: 4.99, replacement_cost: 19.99},
                {id: 2, titulo: 'Pulp Fiction', descripcion: 'Película de culto de Tarantino', anyo_lanzamiento: 1994, duracion: 154, clasificacion: 'R', categoria: 'Crimen', idioma: 'Inglés', duracion_alquiler: 5, rental_rate: 3.99, replacement_cost: 24.99}
            ],
            actores: [
                {id: 1, nombre: 'Marlon', apellidos: 'Brando', email_generado: 'marlon.brando@email.com'},
                {id: 2, nombre: 'Al', apellidos: 'Pacino', email_generado: 'al.pacino@email.com'}
            ],
            clientes: [
                {id: 1, nombre: 'Juan', apellidos: 'Pérez', email: 'juan.perez@email.com', telefono: '555-0101', direccion_completa: 'Calle Principal 123, Ciudad de México, México', activo: true, id_almacen: 1, fecha_creacion: '2024-01-01 10:00:00'}
            ],
            categorias: [
                {id: 1, nombre: 'Drama'},
                {id: 2, nombre: 'Acción'},
                {id: 3, nombre: 'Comedia'}
            ],
            tiendas: [
                {id: 1, nombre: 'Tienda Ciudad de México', direccion: 'Centro Histórico #123', ciudad: 'Ciudad de México', pais: 'México', gerente: 'Ana López'}
            ],
            empleados: [
                {id: 1, nombre: 'Pedro', apellidos: 'Sánchez', email: 'pedro.sanchez@sakila.com', id_almacen: 1, activo: true, username: 'psanchez', direccion_completa: 'Av. Reforma 456, Ciudad de México'}
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
            inventario: [
                {id: 1, pelicula: 'El Padrino', tienda: 'Tienda Ciudad de México', id_pelicula: 1, id_almacen: 1},
                {id: 2, pelicula: 'Pulp Fiction', tienda: 'Tienda Ciudad de México', id_pelicula: 2, id_almacen: 1}
            ],
            alquileres: [
                {id: 1, fecha_alquiler: '2024-01-15 14:30:00', fecha_devolucion: '2024-01-18 16:00:00', cliente: 'Juan Pérez', pelicula: 'El Padrino', empleado: 'Pedro Sánchez', id_cliente: 1, id_inventario: 1, id_empleado: 1}
            ],
            pagos: [
                {id: 1, total: 4.99, fecha_pago: '2024-01-15 14:30:00', cliente: 'Juan Pérez', empleado: 'Pedro Sánchez', id_cliente: 1, id_empleado: 1, id_alquiler: 1}
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
}