// =========================
// VISTA (View)
// =========================
class SakilaView {
    constructor() {
        this.currentTable = '';
        this.selectedRow = null;
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.filteredData = [];
        this.controller = null;
    }

    setController(controller) {
        this.controller = controller;
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Event listener para el formulario
        const dataForm = document.getElementById('dataForm');
        if (dataForm) {
            dataForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (this.controller) {
                    this.controller.handleFormSubmit(e);
                }
            });
        }

        // Event listener para cerrar modal con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modal = document.getElementById('formModal');
                if (modal && modal.classList.contains('active')) {
                    if (this.controller) {
                        this.controller.closeModal();
                    }
                }
            }
        });

        // Event listener para cerrar modal clickeando fuera
        const formModal = document.getElementById('formModal');
        if (formModal) {
            formModal.addEventListener('click', (e) => {
                if (e.target === e.currentTarget) {
                    if (this.controller) {
                        this.controller.closeModal();
                    }
                }
            });
        }
    }

    renderTableButtons(tables) {
        const container = document.getElementById('tableButtons');
        if (!container) return;
        
        container.innerHTML = tables.map(table => 
            `<button class="table-btn" data-table="${table}" onclick="appController.switchTable('${table}')">
                ${this.controller ? this.controller.model.getTableName(table) : table}
            </button>`
        ).join('');
    }

    renderCrudSections(tables) {
        const container = document.getElementById('crudContainer');
        if (!container) return;
        
        container.innerHTML = tables.map(table => `
            <div id="${table}" class="crud-section">
                <div class="crud-controls">
                    <button class="btn" onclick="appController.showAddForm('${table}')">+ Agregar ${this.controller ? this.controller.model.getTableName(table).slice(0, -1) : table}</button>
                    <button class="btn btn-edit" onclick="appController.editSelected()" disabled id="editBtn-${table}">Editar</button>
                    <button class="btn btn-delete" onclick="appController.deleteSelected()" disabled id="deleteBtn-${table}">Eliminar</button>
                    <input type="text" class="search-box" placeholder="Buscar ${this.controller ? this.controller.model.getTableName(table).toLowerCase() : table}..." onkeyup="appController.searchTable(event)">
                </div>
                <div id="tableContainer-${table}"></div>
                <div class="pagination" id="pagination-${table}"></div>
            </div>
        `).join('');
    }

    switchTable(tableName) {
        this.currentTable = tableName;
        this.selectedRow = null;
        this.currentPage = 1;

        // Actualizar botones activos
        document.querySelectorAll('.table-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.querySelector(`[data-table="${tableName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Mostrar sección correspondiente
        document.querySelectorAll('.crud-section').forEach(section => {
            section.classList.remove('active');
        });
        const activeSection = document.getElementById(tableName);
        if (activeSection) {
            activeSection.classList.add('active');
        }
    }

    renderTable(data) {
        const container = document.getElementById(`tableContainer-${this.currentTable}`);
        if (!container) return;
        
        if (data.length === 0) {
            container.innerHTML = '<div class="no-data">No hay datos disponibles</div>';
            return;
        }

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageData = data.slice(startIndex, endIndex);

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
            html += `<tr onclick="appController.selectRow(${actualIndex})" data-index="${actualIndex}">`;
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

    renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / this.itemsPerPage);
        const paginationContainer = document.getElementById(`pagination-${this.currentTable}`);
        if (!paginationContainer) return;
        
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = '';
        
        if (this.currentPage > 1) {
            html += '<button onclick="appController.changePage(1)">Primera</button>';
            html += `<button onclick="appController.changePage(${this.currentPage - 1})">Anterior</button>`;
        }
        
        for (let i = Math.max(1, this.currentPage - 2); i <= Math.min(totalPages, this.currentPage + 2); i++) {
            html += `<button onclick="appController.changePage(${i})" ${i === this.currentPage ? 'class="active"' : ''}>${i}</button>`;
        }
        
        if (this.currentPage < totalPages) {
            html += `<button onclick="appController.changePage(${this.currentPage + 1})">Siguiente</button>`;
            html += `<button onclick="appController.changePage(${totalPages})">Última</button>`;
        }
        
        paginationContainer.innerHTML = html;
    }

    selectRow(index) {
        // Deseleccionar fila anterior
        if (this.selectedRow !== null) {
            const prevRow = document.querySelector(`tr[data-index="${this.selectedRow}"]`);
            if (prevRow) prevRow.classList.remove('selected');
        }

        this.selectedRow = index;
        const row = document.querySelector(`tr[data-index="${index}"]`);
        if (row) {
            row.classList.add('selected');
        }

        this.updateButtons();
    }

    updateButtons() {
        const editBtn = document.getElementById(`editBtn-${this.currentTable}`);
        const deleteBtn = document.getElementById(`deleteBtn-${this.currentTable}`);
        
        if (editBtn && deleteBtn) {
            if (this.selectedRow !== null) {
                editBtn.disabled = false;
                deleteBtn.disabled = false;
            } else {
                editBtn.disabled = true;
                deleteBtn.disabled = true;
            }
        }
    }

    showModal(title, mode, table, data = null) {
        const modal = document.getElementById('formModal');
        const titleElement = document.getElementById('modalTitle');
        const form = document.getElementById('dataForm');
        const fieldsContainer = document.getElementById('formFields');
        
        if (!modal || !titleElement || !form || !fieldsContainer) {
            console.error('Modal elements not found');
            return;
        }
        
        titleElement.textContent = title;
        form.dataset.mode = mode;
        form.dataset.table = table;
        if (data) {
            form.dataset.id = data.id;
        }
        
        // Generar campos del formulario
        const fields = this.controller ? this.controller.model.getTableFields(table) : [];
        fieldsContainer.innerHTML = '';
        
        fields.forEach(field => {
            const div = document.createElement('div');
            div.className = 'form-group';
            
            const label = document.createElement('label');
            label.textContent = field.label + (field.required ? ' *' : '');
            div.appendChild(label);
            
            let input = this.createFormInput(field, data);
            div.appendChild(input);
            fieldsContainer.appendChild(div);
        });
        
        modal.classList.add('active');
    }

    createFormInput(field, data) {
        let input;
        
        if (field.type === 'textarea') {
            input = document.createElement('textarea');
            input.rows = 3;
            input.value = (data && data[field.name]) || '';
        } else if (field.type === 'select') {
            input = document.createElement('select');
            field.options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                opt.selected = data && data[field.name] === option;
                input.appendChild(opt);
            });
        } else if (field.type === 'checkbox') {
            input = document.createElement('input');
            input.type = 'checkbox';
            input.checked = data ? (data[field.name] || false) : true;
        } else {
            input = document.createElement('input');
            input.type = field.type;
            input.value = (data && data[field.name]) || '';
        }
        
        input.name = field.name;
        input.required = field.required || false;
        
        return input;
    }

    closeModal() {
        const modal = document.getElementById('formModal');
        if (modal) {
            modal.classList.remove('active');
        }
    }

    showMessage(message, type = 'success') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        
        document.body.appendChild(messageDiv);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            messageDiv.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 300);
        }, 3000);
    }

    resetSelection() {
        this.selectedRow = null;
        this.updateButtons();
        // Remover selección visual
        document.querySelectorAll('tr.selected').forEach(row => {
            row.classList.remove('selected');
        });
    }

    updateCurrentPage(page) {
        this.currentPage = page;
    }

    setFilteredData(data) {
        this.filteredData = data;
    }

    getCurrentTable() {
        return this.currentTable;
    }

    getSelectedRow() {
        return this.selectedRow;
    }

    getFilteredData() {
        return this.filteredData;
    }

    getCurrentPage() {
        return this.currentPage;
    }

    getItemsPerPage() {
        return this.itemsPerPage;
    }
}