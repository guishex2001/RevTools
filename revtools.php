<?php
/*
Plugin Name: RevTools
Description: Plugin para mostrar/ocultar slides de Revolution Slider y reemplazar shortcodes de Revolution Slider.
Version: 1.0
Author: guishex2001
*/

// Función para activar el plugin
function activar_plugin() {
    // Aquí puedes realizar cualquier configuración necesaria al activar el plugin
}

// Función para desactivar el plugin
function desactivar_plugin() {
    // Aquí puedes realizar cualquier limpieza necesaria al desactivar el plugin
}

// Función para borrar el plugin
function borrar_plugin() {
    // Aquí puedes realizar cualquier limpieza necesaria al borrar el plugin
}

// Registrando los hooks de activación, desactivación y desinstalación del plugin
register_activation_hook(__FILE__, 'activar_plugin');
register_deactivation_hook(__FILE__, 'desactivar_plugin');
register_uninstall_hook(__FILE__, 'borrar_plugin');

// Agregar el menú de administración
add_action('admin_menu', 'crear_menu');

function crear_menu() {
    $parent_slug = 'revslider-tools';
    $icon_url = plugin_dir_url(__FILE__) . 'admin/img/icon.png';

    add_menu_page(
        'RevSlider Tools',
        'RevSlider Tools',
        'manage_options',
        $parent_slug,
        'mostrar_contenido_revtools',
        $icon_url,
        2
    );

    add_submenu_page(
        $parent_slug,
        'Show Hide RevSlider Slide',
        'Show Hide Slide',
        'manage_options',
        'sh_menu',
        'mostrar_contenido_showhide'
    );

    add_submenu_page(
        $parent_slug,
        'Replace RevSlider Shortcode',
        'Replace Shortcode',
        'manage_options',
        'rrs_menu',
        'mostrar_contenido_replace'
    );
}

// Función para mostrar el contenido del menú principal
function mostrar_contenido_revtools() {
    ?>
    <div class="wrap">
        <h1>Bienvenido a RevSlider Tools</h1>
        <p>Utiliza las herramientas a continuación para gestionar tus slides y shortcodes de Revolution Slider.</p>
        
        <div style="margin-top: 20px;">
            <a href="admin.php?page=sh_menu" class="button-primary" style="margin-right: 10px;">Show Hide Slide</a>
            <a href="admin.php?page=rrs_menu" class="button-primary">Replace Shortcode</a>
        </div>

        <div style="margin-top: 30px; padding: 15px; background-color: #f1f1f1; border: 1px solid #ddd;">
            <h2>¿Cómo funciona el plugin?</h2>
            <p><strong>Show Hide Slide:</strong> Esta opción te permite mostrar u ocultar slides específicos de Revolution Slider. Puedes seleccionar cuáles slides deseas que sean visibles en tu sitio web.</p>
            <p><strong>Replace Shortcode:</strong> Esta herramienta permite reemplazar shortcodes de Revolution Slider con contenido personalizado. Esto es útil si necesitas actualizar el contenido de tus sliders sin modificar manualmente cada shortcode en tu sitio.</p>
        </div>
    </div>
    <style>
        .wrap h1 {
            font-size: 2em;
            margin-bottom: 0.5em;
        }
        .wrap p {
            font-size: 1.2em;
            margin-bottom: 1em;
        }
        .wrap .button-primary {
            font-size: 1em;
            padding: 10px 20px;
        }
        .wrap div {
            margin-bottom: 1.5em;
        }
    </style>
    <?php
}

// Función para mostrar el contenido del submenú "Show Hide RevSlider Slide"
function mostrar_contenido_showhide() {
    include_once 'sh.php'; // Incluir el archivo del plugin "Show Hide RevSlider Slide"
    mostrar_contenido(); // Llamar a la función original del plugin
}

// Función para mostrar el contenido del submenú "Replace RevSlider Shortcode"
function mostrar_contenido_replace() {
    include_once 'rss.php'; // Incluir el archivo del plugin "Replace RevSlider Shortcode"
    mostrar_contenido(); // Llamar a la función original del plugin
}
?>
