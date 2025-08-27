<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Base de Datos Sakila - MVC</title>
    <!-- Enlazar archivo CSS externo -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎬 Base de Datos Sakila</h1>
            <p>Sistema de gestión CRUD con arquitectura MVC</p>
        </div>
        
        <div class="main-content">
            <div class="table-selector">
                <h3>Seleccionar Tabla</h3>
                <div class="table-buttons" id="tableButtons">
                    <!-- Los botones se generarán dinámicamente -->
                </div>
            </div>

            <div id="crudContainer">
                <!-- Las secciones CRUD se generarán dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Modal para formularios -->
    <div id="formModal" class="form-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Agregar Registro</h3>
                <button class="close-btn" onclick="appController.closeModal()">&times;</button>
            </div>
            <form id="dataForm">
                <div id="formFields"></div>
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="appController.closeModal()">Cancelar</button>
                    <button type="submit" class="btn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cargar archivos JavaScript en orden -->
    <script src="model/model.js"></script>
    <script src="view/sakila.js"></script>
    <script src="controller/controller.js"></script>
    
    <script>
        // =========================
        // INICIALIZACIÓN
        // =========================
        let appController;

        document.addEventListener('DOMContentLoaded', function() {
            // Verificar que todas las clases estén disponibles
            if (typeof SakilaModel === 'undefined' || 
                typeof SakilaView === 'undefined' || 
                typeof SakilaController === 'undefined') {
                console.error('Error: No se pudieron cargar todas las clases necesarias');
                return;
            }
            
            const model = new SakilaModel();
            const view = new SakilaView();
            appController = new SakilaController(model, view);
        });
    </script>
</body>
</html>