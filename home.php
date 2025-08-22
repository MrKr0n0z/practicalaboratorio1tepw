<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Base de Datos Sakila</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .main-content {
            padding: 30px;
        }

        .table-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .table-selector h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .table-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }

        .table-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .table-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .table-btn.active {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .crud-section {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }

        .crud-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .crud-controls {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }

        .btn.btn-edit {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }

        .btn.btn-edit:hover {
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }

        .btn.btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .btn.btn-delete:hover {
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        .search-box {
            flex: 1;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            min-width: 250px;
        }

        .search-box:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .data-table th {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        .data-table tr.selected {
            background-color: #e3f2fd;
        }

        .form-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease-in;
        }

        .form-modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #2c3e50;
            font-size: 1.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #7f8c8d;
            padding: 5px;
        }

        .close-btn:hover {
            color: #e74c3c;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn-cancel {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        }

        .btn-cancel:hover {
            box-shadow: 0 5px 15px rgba(149, 165, 166, 0.3);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            background: linear-gradient(135deg, #ecf0f1, #bdc3c7);
            color: #2c3e50;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .pagination button:hover {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .pagination button.active {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .crud-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: 100%;
            }

            .data-table {
                font-size: 0.9rem;
            }

            .data-table th,
            .data-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎬 Base de Datos Sakila</h1>
            <p>Sistema de gestión CRUD para tienda de películas</p>
        </div>
        
        <div class="main-content">
            <div class="table-selector">
                <h3>Seleccionar Tabla</h3>
                <div class="table-buttons">
                    <button class="table-btn" data-table="peliculas">Películas</button>
                    <button class="table-btn" data-table="actores">Actores</button>
                    <button class="table-btn" data-table="clientes">Clientes</button>
                    <button class="table-btn" data-table="categorias">Categorías</button>
                    <button class="table-btn" data-table="tiendas">Tiendas</button>
                    <button class="table-btn" data-table="empleados">Empleados</button>
                </div>
            </div>

            <div id="peliculas" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="showAddForm('peliculas')">+ Agregar Película</button>
                    <button class="btn btn-edit" onclick="editSelected()" disabled id="editBtn">Editar</button>
                    <button class="btn btn-delete" onclick="deleteSelected()" disabled id="deleteBtn">Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar películas..." onkeyup="searchTable()">
                </div>
                <div id="tableContainer"></div>
                <div class="pagination" id="pagination"></div>
            </div>

            <div id="actores" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="showAddForm('actores')">+ Agregar Actor</button>
                    <button class="btn btn-edit" onclick="editSelected()" disabled>Editar</button>
                    <button class="btn btn-delete" onclick="deleteSelected()" disabled>Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar actores..." onkeyup="searchTable()">
                </div>
                <div id="tableContainer"></div>
                <div class="pagination"></div>
            </div>

            <div id="clientes" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="showAddForm('clientes')">+ Agregar Cliente</button>
                    <button class="btn btn-edit" onclick="editSelected()" disabled>Editar</button>
                    <button class="btn btn-delete" onclick="deleteSelected()" disabled>Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar clientes..." onkeyup="searchTable()">
                </div>
                <div id="tableContainer"></div>
                <div class="pagination"></div>
            </div>

            <div id="categorias" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="showAddForm('categorias')">+ Agregar Categoría</button>
                    <button class="btn btn-edit" onclick="editSelected()" disabled>Editar</button>
                    <button class="btn btn-delete" onclick="deleteSelected()" disabled>Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar categorías..." onkeyup="searchTable()">
                </div>
                <div id="tableContainer"></div>
                <div class="pagination"></div>
            </div>

            <div id="tiendas" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="showAddForm('tiendas')">+ Agregar Tienda</button>
                    <button class="btn btn-edit" onclick="editSelected()" disabled>Editar</button>
                    <button class="btn btn-delete" onclick="deleteSelected()" disabled>Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar tiendas..." onkeyup="searchTable()">
                </div>
                <div id="tableContainer"></div>
                <div class="pagination"></div>
            </div>

            <div id="empleados" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="showAddForm('empleados')">+ Agregar Empleado</button>
                    <button class="btn btn-edit" onclick="editSelected()" disabled>Editar</button>
                    <button class="btn btn-delete" onclick="deleteSelected()" disabled>Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar empleados..." onkeyup="searchTable()">
                </div>
                <div id="tableContainer"></div>
                <div class="pagination"></div>
            </div>
        </div>
    </div>

    <!-- Modal para formularios -->
    <div id="formModal" class="form-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Registro</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="dataForm">
                <div id="formFields"></div>
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Datos simulados para demostración
        const mockData = {
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
                {id: 2, nombre: 'Comedia', descripcion: 'Películas divertidas and humorísticas'},
                {id: 3, nombre: 'Acción', descripcion: 'Películas llenas de acción y aventura'},
                {id: 4, nombre: 'Terror', descripcion: 'Películas de miedo y suspense'},
                {id: 5, nombre: 'Romance', descripción: 'Películas románticas y sentimentales'}
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

        // Configuración de campos para cada tabla
        const tableFields = {
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

        let currentTable = '';
        let selectedRow = null;
        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredData = [];

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners para botones de tabla
            document.querySelectorAll('.table-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    switchTable(this.dataset.table);
                });
            });

            // Event listener para el formulario
            document.getElementById('dataForm').addEventListener('submit', handleFormSubmit);

            // Mostrar la primera tabla por defecto
            switchTable('peliculas');
        });

        function switchTable(tableName) {
            currentTable = tableName;
            selectedRow = null;
            currentPage = 1;

            // Actualizar botones activos
            document.querySelectorAll('.table-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-table="${tableName}"]`).classList.add('active');

            // Mostrar sección correspondiente
            document.querySelectorAll('.crud-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(tableName).classList.add('active');

            // Cargar datos
            loadTableData();
            updateButtons();
        }

        function loadTableData() {
            const data = mockData[currentTable] || [];
            filteredData = [...data];
            renderTable();
            renderPagination();
        }

        function renderTable() {
            const container = document.querySelector('.crud-section.active #tableContainer');
            
            if (filteredData.length === 0) {
                container.innerHTML = '<div class="no-data">No hay datos disponibles</div>';
                return;
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageData = filteredData.slice(startIndex, endIndex);

            const fields = Object.keys(pageData[0]);
            
            let html = '<table class="data-table">';
            html += '<thead><tr>';
            fields.forEach(field => {
                html += `<th>${field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ')}</th>`;
            });
            html += '</tr></thead>';
            html += '<tbody>';
            
            pageData.forEach((row, index) => {
                const actualIndex = startIndex + index;
                html += `<tr onclick="selectRow(${actualIndex})" data-index="${actualIndex}">`;
                fields.forEach(field => {
                    let value = row[field];
                    if (typeof value === 'boolean') {
                        value = value ? '✓' : '✗';
                    }
                    html += `<td>${value || ''}</td>`;
                });
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const paginationContainer = document.querySelector('.crud-section.active .pagination');
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = '';
            
            if (currentPage > 1) {
                html += '<button onclick="changePage(1)">Primera</button>';
                html += '<button onclick="changePage(' + (currentPage - 1) + ')">Anterior</button>';
            }
            
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                html += `<button onclick="changePage(${i})" ${i === currentPage ? 'class="active"' : ''}>${i}</button>`;
            }
            
            if (currentPage < totalPages) {
                html += '<button onclick="changePage(' + (currentPage + 1) + ')">Siguiente</button>';
                html += '<button onclick="changePage(' + totalPages + ')">Última</button>';
            }
            
            paginationContainer.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            renderTable();
            renderPagination();
        }

        function selectRow(index) {
            // Deseleccionar fila anterior
            if (selectedRow !== null) {
                const prevRow = document.querySelector(`tr[data-index="${selectedRow}"]`);
                if (prevRow) prevRow.classList.remove('selected');
            }

            selectedRow = index;
            const row = document.querySelector(`tr[data-index="${index}"]`);
            if (row) {
                row.classList.add('selected');
            }

            updateButtons();
        }

        function updateButtons() {
            const editBtn = document.querySelector('.crud-section.active .btn-edit');
            const deleteBtn = document.querySelector('.crud-section.active .btn-delete');
            
            if (selectedRow !== null) {
                editBtn.disabled = false;
                deleteBtn.disabled = false;
            } else {
                editBtn.disabled = true;
                deleteBtn.disabled = true;
            }
        }

        function searchTable() {
            const searchTerm = event.target.value.toLowerCase();
            const data = mockData[currentTable] || [];
            
            if (!searchTerm) {
                filteredData = [...data];
            } else {
                filteredData = data.filter(item => {
                    return Object.values(item).some(value => 
                        String(value).toLowerCase().includes(searchTerm)
                    );
                });
            }
            
            currentPage = 1;
            selectedRow = null;
            renderTable();
            renderPagination();
            updateButtons();
        }

        function showAddForm(table) {
            const modal = document.getElementById('formModal');
            const title =