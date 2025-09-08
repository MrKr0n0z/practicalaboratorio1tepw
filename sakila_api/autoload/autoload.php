<?php
// Autoloader personalizado simple
spl_autoload_register(function($class_name) {
    $base_dir = __DIR__ . '/../';
    
    $directories = [
        'config/',
        'interfaces/',
        'repositories/',
        'factories/',
        'controllers/',
        'services/'
    ];
    
    foreach ($directories as $directory) {
        $file = $base_dir . $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
?>