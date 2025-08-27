// =========================
// CONTROLADOR (Controller)
// =========================
class SakilaController {
    constructor(model, view) {
        this.model = model;
        this.view = view;
        
        // Verificar que model y view estén disponibles
        if (!this.model || !this.view) {
            console.error('Controller requires both model and view');
            return;
        }
        
        // Configurar las referencias cruzadas
        this.view.setController(this);
        this.model.subscribe(this);
        
        this.init();
    }

    init() {
        this.view.init();
        this.setupInitialView();
        this.switchTable('peliculas'); // Tabla por defecto
    }

    setupInitialView() {
        const tables = this.model.getAllTableNames();
        this.view.renderTableButtons(tables);
        this.view.renderCrudSections(tables);
    }

    // Observer method - se ejecuta cuando el modelo cambia
    dataChanged(data) {
        // Recargar datos cuando hay cambios
        this.loadTableData();
        this.view.resetSelection();
        
        let message = '';
        const itemName = data.item.nombre || data.item.titulo || `ID ${data.item.id}`;
        
        switch (data.action) {
            case 'create':
                message = `"${itemName}" ha sido agregado correctamente.`;
                break;
            case 'update':
                message = `"${itemName}" ha sido actualizado correctamente.`;
                break;
            case 'delete':
                message = `"${itemName}" ha sido eliminado correctamente.`;
                break;
        }
        
        this.view.showMessage(message, 'success');
    }

    switchTable(tableName) {
        try {
            this.view.switchTable(tableName);
            this.loadTableData();
            this.view.updateButtons();
        } catch (error) {
            console.error('Error switching table:', error);
            this.view.showMessage('Error al cambiar de tabla.', 'error');
        }
    }

    loadTableData() {
        try {
            const data = this.model.getAll(this.view.getCurrentTable());
            this.view.setFilteredData([...data]);
            this.view.renderTable(this.view.getFilteredData());
            this.view.renderPagination(this.view.getFilteredData().length);
        } catch (error) {
            console.error('Error loading table data:', error);
            this.view.showMessage('Error al cargar los datos.', 'error');
        }
    }

    selectRow(index) {
        try {
            this.view.selectRow(index);
        } catch (error) {
            console.error('Error selecting row:', error);
        }
    }

    changePage(page) {
        try {
            this.view.updateCurrentPage(page);
            this.view.renderTable(this.view.getFilteredData());
            this.view.renderPagination(this.view.getFilteredData().length);
        } catch (error) {
            console.error('Error changing page:', error);
            this.view.showMessage('Error al cambiar de página.', 'error');
        }
    }

    searchTable(event) {
        try {
            const searchTerm = event.target.value.toLowerCase();
            const currentTable = this.view.getCurrentTable();
            
            let filteredData;
            if (!searchTerm) {
                filteredData = this.model.getAll(currentTable);
            } else {
                filteredData = this.model.search(currentTable, searchTerm);
            }
            
            this.view.setFilteredData(filteredData);
            this.view.updateCurrentPage(1);
            this.view.resetSelection();
            this.view.renderTable(filteredData);
            this.view.renderPagination(filteredData.length);
        } catch (error) {
            console.error('Error searching table:', error);
            this.view.showMessage('Error al realizar la búsqueda.', 'error');
        }
    }

    showAddForm(table) {
        try {
            const tableName = this.model.getTableName(table);
            const title = `Agregar ${tableName.slice(0, -1)}`;
            this.view.showModal(title, 'add', table);
        } catch (error) {
            console.error('Error showing add form:', error);
            this.view.showMessage('Error al mostrar el formulario.', 'error');
        }
    }

    editSelected() {
        try {
            const selectedRow = this.view.getSelectedRow();
            if (selectedRow === null) {
                this.view.showMessage('Por favor selecciona un registro para editar.', 'error');
                return;
            }
            
            const item = this.view.getFilteredData()[selectedRow];
            if (!item) {
                this.view.showMessage('No se pudo obtener el registro seleccionado.', 'error');
                return;
            }
            
            const currentTable = this.view.getCurrentTable();
            const tableName = this.model.getTableName(currentTable);
            const title = `Editar ${tableName.slice(0, -1)}`;
            
            this.view.showModal(title, 'edit', currentTable, item);
        } catch (error) {
            console.error('Error editing selected:', error);
            this.view.showMessage('Error al editar el registro.', 'error');
        }
    }

    deleteSelected() {
        try {
            const selectedRow = this.view.getSelectedRow();
            if (selectedRow === null) {
                this.view.showMessage('Por favor selecciona un registro para eliminar.', 'error');
                return;
            }
            
            const item = this.view.getFilteredData()[selectedRow];
            if (!item) {
                this.view.showMessage('No se pudo obtener el registro seleccionado.', 'error');
                return;
            }
            
            const itemName = item.nombre || item.titulo || `ID ${item.id}`;
            
            if (confirm(`¿Estás seguro de que deseas eliminar "${itemName}"?`)) {
                this.model.delete(this.view.getCurrentTable(), item.id);
            }
        } catch (error) {
            console.error('Error deleting selected:', error);
            this.view.showMessage('Error al eliminar el registro.', 'error');
        }
    }

    handleFormSubmit(event) {
        try {
            const form = event.target;
            const mode = form.dataset.mode;
            const table = form.dataset.table;
            const formData = new FormData(form);
            
            // Construir objeto con los datos del formulario
            const data = {};
            const fields = this.model.getTableFields(table);
            
            fields.forEach(field => {
                if (field.type === 'checkbox') {
                    data[field.name] = formData.has(field.name);
                } else {
                    data[field.name] = formData.get(field.name) || '';
                    
                    // Convertir números
                    if (field.type === 'number') {
                        data[field.name] = parseInt(data[field.name]) || 0;
                    }
                }
            });
            
            if (mode === 'add') {
                this.model.create(table, data);
            } else if (mode === 'edit') {
                const id = parseInt(form.dataset.id);
                this.model.update(table, id, data);
            }
            
            this.closeModal();
        } catch (error) {
            console.error('Error al procesar el formulario:', error);
            this.view.showMessage('Error al procesar la solicitud.', 'error');
        }
    }

    closeModal() {
        try {
            this.view.closeModal();
        } catch (error) {
            console.error('Error closing modal:', error);
        }
    }
}