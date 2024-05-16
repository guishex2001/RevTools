<?php
// Función para mostrar el contenido de la página del menú
function mostrar_contenido() {
    global $wpdb;

    // Obtener todos los sliders de Revolution Slider
    $sliders = $wpdb->get_results("SELECT id, title FROM {$wpdb->prefix}revslider_sliders");

    // Verificar si se ha enviado el formulario y si 'slide' está definido en $_POST
    if (isset($_POST['submit']) && isset($_POST['slide']) && isset($_POST['slider'])) {
        // Obtener los valores seleccionados del formulario
        $slider = $_POST['slider'];
        $slide = $_POST['slide'];

        // Determinar el estado actual del slide
        $estado_actual = obtener_estado_slide($wpdb, $slider, $slide);

        // Cambiar el estado del slide
        if ($estado_actual === 'visible') {
            ocultar_slide($wpdb, $slider, $slide);
        } else {
            mostrar_slide($wpdb, $slider, $slide);
        }
    }

    ?>
    <div class="plugin-container">
        <h1>Show Hide</h1>
        <p>Selecciona un slider y un slide para mostrar u ocultar.</p>
        <form method="post">
            <label for="slider">Selecciona el slider:</label>
            <select name="slider" id="slider">
                <option value="">Seleccionar slider</option>
                <?php foreach ($sliders as $s) : ?>
                    <option value="<?php echo $s->id; ?>"><?php echo $s->title; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="slide">Selecciona el slide:</label>
            <select name="slide" id="slide">
                <!-- Aquí se cargarán dinámicamente los slides del slider seleccionado -->
            </select>
            <br>
            <input type="submit" name="submit" value="Mostrar/Ocultar">
        </form>
    </div>
    <style>
        .plugin-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.plugin-container h1 {
    font-size: 24px;
    margin-bottom: 10px;
    text-align: center;
}

.plugin-container p {
    font-size: 16px;
    margin-bottom: 20px;
    text-align: center;
}

.plugin-container form {
    text-align: left;
}

.plugin-container label {
    font-size: 16px;
    display: inline-block;
    margin-bottom: 10px;
    width: 150px;
    text-align: right;
    padding-right: 10px;
    vertical-align: middle;
}

.plugin-container select {
    font-size: 16px;
    padding: 10px;
    margin-bottom: 20px;
    width: calc(100% - 170px); /* Adjust width to align with label */
    box-sizing: border-box;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    display: inline-block;
    vertical-align: middle;
}

.plugin-container input[type="submit"] {
    font-size: 16px;
    padding: 10px 20px;
    background-color: #0073aa;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin: 0 auto;
    text-align: center;
}

.plugin-container input[type="submit"]:hover {
    background-color: #005580;
}

    </style>
    <script>
        // Función para cargar dinámicamente los slides cuando se seleccione un slider
        jQuery(document).ready(function ($) {
            $('#slider').change(function () {
                var slider_id = $(this).val();
                $.ajax({
                    url: ajaxurl, // Esta variable global de WordPress contiene la URL del archivo admin-ajax.php
                    type: 'post',
                    data: {
                        action: 'cargar_slides',
                        slider_id: slider_id
                    },
                    success: function (response) {
                        $('#slide').html(response);
                    }
                });
            });
        });
    </script>
    <?php
}
// Función para cargar dinámicamente los slides de un slider específico
add_action('wp_ajax_cargar_slides', 'cargar_slides_callback');

//filtro de slides con una palabra
function cargar_slides_callback() {
    global $wpdb;
    $slider_id = $_POST['slider_id'];
    // Modificar la consulta para filtrar solo los slides que contengan "video" en el título
    $slides = $wpdb->get_results($wpdb->prepare("SELECT slide_order, params FROM {$wpdb->prefix}revslider_slides WHERE slider_id = %s AND params LIKE '%%\"title\":\"%video%\"%%'", $slider_id));

    $options_html = '';
    foreach ($slides as $slide) {
        $params = json_decode($slide->params);
        $slide_title = isset($params->title) ? $params->title : 'Slide ' . $slide->slide_order;
        $estado_actual = obtener_estado_slide($wpdb, $slider_id, $slide->slide_order);
        $options_html .= "<option value='{$slide->slide_order}'>{$slide_title} ({$estado_actual})</option>";
    }

    echo $options_html;
    exit;
}

// sin filtro de palabra
/*
function cargar_slides_callback() {
    global $wpdb;
    $slider_id = $_POST['slider_id'];
    // Consulta para obtener todos los slides del slider seleccionado
    $slides = $wpdb->get_results($wpdb->prepare("SELECT slide_order, params FROM {$wpdb->prefix}revslider_slides WHERE slider_id = %s", $slider_id));

    $options_html = '';
    foreach ($slides as $slide) {
        $params = json_decode($slide->params);
        $slide_title = isset($params->title) ? $params->title : 'Slide ' . $slide->slide_order;
        // Obtener el estado actual del slide
        $estado_actual = obtener_estado_slide($wpdb, $slider_id, $slide->slide_order);
        $options_html .= "<option value='{$slide->slide_order}'>{$slide_title} ({$estado_actual})</option>";
    }

    echo $options_html;
    exit;
}
*/



// Función para obtener el estado actual del slide
function obtener_estado_slide($wpdb, $slider, $slide) {
    $table_name = $wpdb->prefix . 'revslider_slides';
    $query = $wpdb->prepare("SELECT params FROM $table_name WHERE slider_id = %s AND slide_order = %s", $slider, $slide);
    $params = $wpdb->get_var($query);

    // Verificar el estado del slide en los parámetros
    $params_array = json_decode($params, true);
    if (isset($params_array['publish']['state']) && $params_array['publish']['state'] === 'unpublished') {
        return 'oculto';
    } else {
        return 'visible';
    }
}

// Función para mostrar un slide
function mostrar_slide($wpdb, $slider, $slide) {
    $table_name = $wpdb->prefix . 'revslider_slides';
    // Obtener los parámetros actuales del slide
    $query = $wpdb->prepare("SELECT params FROM $table_name WHERE slider_id = %s AND slide_order = %s", $slider, $slide);
    $params = json_decode($wpdb->get_var($query), true);

    // Actualizar el estado del slide a publicado
    $params['publish']['state'] = 'published';
    $params_json = json_encode($params);

    $wpdb->update(
        $table_name,
        array('params' => $params_json),
        array('slider_id' => $slider, 'slide_order' => $slide)
    );
}

// Función para ocultar un slide
function ocultar_slide($wpdb, $slider, $slide) {
    $table_name = $wpdb->prefix . 'revslider_slides';
    // Obtener los parámetros actuales del slide
    $query = $wpdb->prepare("SELECT params FROM $table_name WHERE slider_id = %s AND slide_order = %s", $slider, $slide);
    $params = json_decode($wpdb->get_var($query), true);

    // Actualizar el estado del slide a no publicado
    $params['publish']['state'] = 'unpublished';
    $params_json = json_encode($params);

    $wpdb->update(
        $table_name,
        array('params' => $params_json),
        array('slider_id' => $slider, 'slide_order' => $slide)
    );
}