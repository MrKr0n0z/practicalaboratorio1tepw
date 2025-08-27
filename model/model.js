// =========================
// MODELO (Model)
// =========================
class SakilaModel {
    constructor() {
        this.data = {
            peliculas: [
                {id: 1, titulo: 'El Padrino', descripcion: 'Drama épico sobre la mafia', año_lanzamiento: 1972, duracion: 175, calificacion: 'R', categoria: 'Drama'},
                {id: 2, titulo: 'Pulp Fiction', descripcion: 'Película de culto de Tarantino', año_lanzamiento: 1994, duracion: 154, calificacion: 'R', categoria: 'Crimen'},
                {id: 3, titulo: 'El Señor de los Anillos', descripcion: 'Aventura épica de fantasía', año_lanzamiento: 2001, duracion: 178, calificacion: 'PG-13', categoria: 'Aventura'},
                {id: 4, titulo: 'Matrix', descripcion: 'Ciencia ficción revolucionaria', año_lanzamiento: 1999, duracion: 136, calificacion: 'R', categoria: 'Ciencia Ficción'},
                {id: 5, titulo: 'Titanic', descripcion: 'Romance trágico en alta mar', año_lanzamiento: 1997, duracion: 195, calificacion: 'PG-13', categoria: 'Romance'}
            ],
            actores: [
                {id: 1, nombre: 'Marlon', apellido: 'Brando', email: 'marlon.brando@email.com', activo: true},
                {id: 2, nombre: 'Al', apellido: 'Pacino', email: 'al.pacino@email.com', activo: true},
                {id: 3, nombre: 'John', apellido: 'Travolta', email: 'john.travolta@email.com', activo: true},
                {id: 4, nombre: 'Samuel L.', apellido: 'Jackson', email: 'samuel.jackson@email.com', activo: true},
                {id: 5, nombre: 'Elijah', apellido: 'Wood', email: 'elijah.wood@email.com', activo: true}
            ],
            clientes: [
                {id: 1, nombre: 'Juan', apellido: 'Pérez', email: 'juan.perez@email.com', telefono: '555-0101', direccion: 'Calle Principal 123', activo: true},
                {id: 2, nombre: 'María', apellido: 'García', email: 'maria.garcia@email.com', telefono: '555-0102', direccion: 'Av. Reforma 456', activo: true},
                {id: 3, nombre: 'Carlos', apellido: 'López', email: 'carlos.lopez@email.com', telefono: '555-0103', direccion: 'Colonia Centro 789', activo: true},
                {id: 4, nombre: 'Ana', apellido: 'Martínez', email: 'ana.martinez@email.com', telefono: '555-0104', direccion: 'Zona Rosa 321', activo: false},
                {id: 5, nombre: 'Luis', apellido: 'Rodríguez', email: 'luis.rodriguez@email.com', telefono: '555-0105', direccion: 'Polanco 654', activo: true}
            ],
            categorias: [
                {id: 1, nombre: 'Drama', descripcion: 'Películas dramáticas y emocionales'},
                {id: 2, nombre: 'Comedia', descripcion: 'Películas divertidas y humorísticas'},
                {id: 3, nombre: 'Acción', descripcion: 'Películas llenas de acción y aventura'},
                {id: 4, nombre: 'Terror', descripcion: 'Películas de miedo y suspense'},
                {id: 5, nombre: 'Romance', descripcion: 'Películas románticas y sentimentales'}
            ],
            tiendas: [
                {id: 1, nombre: 'Sakila Store Centro', direccion: 'Centro Histórico #123', ciudad: 'Ciudad de México', pais: 'México', gerente: 'Ana López'},
                {id: 2, nombre: 'Sakila Store Norte', direccion: 'Zona Norte #456', ciudad: 'Monterrey', pais: 'México', gerente: 'Carlos Ruiz'},
                {id: 3, nombre: 'Sakila Store Sur', direccion: 'Av. Sur #789', ciudad: 'Guadalajara', pais: 'México', gerente: 'María González'}
            ],
            empleados: [
                {id: 1, nombre: 'Pedro', apellido: 'Sánchez', email: 'pedro.sanchez@sakila.com', tienda_id: 1, activo: true, cargo: 'Gerente'},
                {id: 2, nombre: 'Laura', apellido: 'Torres', email: 'laura.torres@sakila.com', tienda_id: 1, activo: true, cargo: 'Vendedor'},
                {id: 3, nombre: 'Miguel', apellido: 'Herrera', email: 'miguel.herrera@sakila.com', tienda_id: 2, activo: true, cargo: 'Gerente'},
                {id: 4, nombre: 'Sofia', apellido: 'Morales', email: 'sofia.morales@sakila.com', tienda_id: 2, activo: false, cargo: 'Vendedor'}
            ]
        };

        this.tableFields = {
            peliculas: [
                {name: 'titulo', label: 'Título', type: 'text', required: true},
                {name: 'descripcion', label: 'Descripción', type: 'textarea'},
                {name: 'año_lanzamiento', label: 'Año de Lanzamiento', type: 'number', required: true},
                {name: 'duracion', label: 'Duración (min)', type: 'number', required: true},
                {name: 'calificacion', label: 'Calificación', type: 'select', options: ['G', 'PG', 'PG-13', 'R', 'NC-17']},
                {name: 'categoria', label: 'Categoría', type: 'text', required: true}
            ],
            actores: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellido', label: 'Apellido', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'activo', label: 'Activo', type: 'checkbox'}
            ],
            clientes: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellido', label: 'Apellido', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'telefono', label: 'Teléfono', type: 'tel'},
                {name: 'direccion', label: 'Dirección', type: 'text'},
                {name: 'activo', label: 'Activo', type: 'checkbox'}
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
                {name: 'gerente', label: 'Gerente', type: 'text', required: true}
            ],
            empleados: [
                {name: 'nombre', label: 'Nombre', type: 'text', required: true},
                {name: 'apellido', label: 'Apellido', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'tienda_id', label: 'ID Tienda', type: 'number', required: true},
                {name: 'cargo', label: 'Cargo', type: 'text', required: true},
                {name: 'activo', label: 'Activo', type: 'checkbox'}
            ]
        };

        this.tableNames = {
            peliculas: 'Películas',
            actores: 'Actores',
            clientes: 'Clientes',
            categorias: 'Categorías',
            tiendas: 'Tiendas',
            empleados: 'Empleados'
        };

        this.observers = [];
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

    // Operaciones CRUD
    getAll(table) {
        return this.data[table] || [];
    }

    getById(table, id) {
        return this.data[table]?.find(item => item.id === id);
    }

    create(table, item) {
        const maxId = Math.max(...this.data[table].map(item => item.id), 0);
        const newItem = { ...item, id: maxId + 1 };
        this.data[table].push(newItem);
        this.notify('dataChanged', { table, action: 'create', item: newItem });
        return newItem;
    }

    update(table, id, updatedItem) {
        const index = this.data[table].findIndex(item => item.id === id);
        if (index > -1) {
            this.data[table][index] = { ...updatedItem, id };
            this.notify('dataChanged', { table, action: 'update', item: this.data[table][index] });
            return this.data[table][index];
        }
        return null;
    }

    delete(table, id) {
        const index = this.data[table].findIndex(item => item.id === id);
        if (index > -1) {
            const deletedItem = this.data[table].splice(index, 1)[0];
            this.notify('dataChanged', { table, action: 'delete', item: deletedItem });
            return deletedItem;
        }
        return null;
    }

    search(table, searchTerm) {
        if (!searchTerm) return this.getAll(table);
        
        return this.data[table].filter(item => {
            return Object.values(item).some(value => 
                String(value).toLowerCase().includes(searchTerm.toLowerCase())
            );
        });
    }

    getTableFields(table) {
        return this.tableFields[table] || [];
    }

    getTableName(table) {
        return this.tableNames[table] || table;
    }

    getAllTableNames() {
        return Object.keys(this.data);
    }
}