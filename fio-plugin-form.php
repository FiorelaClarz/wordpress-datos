<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://blackmichi.com/
 * @since             1.1.5
 * @package           FIO_Plugin_form
 *
 * @wordpress-plugin
 * Plugin Name:       FIO_Plugin_form
 * Plugin URI:        https://github.com/FiorelaClarz/wordpress-datos
 * Description:       Base de datos que recibe datos de un formulario. Utiliza un shortcode [fio-plugin-form]
 * Version:           1.0.0
 * Author:            Fiorela
 * Author URI:        https://https://blackmichi.com//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fromularioDatos
 * Domain Path:       /languages
 */

register_activation_hook(__FILE__, 'Fio_Aspirante_init');

function Fio_Aspirante_init()
{
    global $wpdb;
    $tabla_aspirante = $wpdb->prefix . 'aspirante';
    $charset_collate = $wpdb->get_charset_collate();
    //Prepara la consulta que vamos a lanzar para crear la tabla
    $query = "CREATE TABLE IF NOT EXISTS  $tabla_aspirante (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    nombres varchar(40) NOT NULL,
    apellidos varchar(80) NOT NULL,
    correo varchar(99) NOT NULL,
    empresa varchar(40) NOT NULL,
    cargo varchar(3) NOT NULL,
    vencimiento DATE NOT NULL,
    identificacion varchar(3) NOT NULL,
    numero varchar(20) NOT NULL,
    qr varchar(150) NOT NULL,
    foto varchar(200) NOT NULL,
    created_at datetime NOT NULL,
    UNIQUE(id)
    ) $charset_collate";

    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query);
}



add_shortcode('fio_plugin_form', 'FIO_Plugin_form');



function FIO_Plugin_form()
{

    global $wpdb;

    if (
        !empty($_POST)
        and $_POST['nombres'] != ''
        and $_POST['apellido'] != ''
        and is_email($_POST['email'])
        and $_POST['empresa'] != ''
        and $_POST['cargo'] != ''
        and $_POST['fechaVencimiento'] != ''
        and $_POST['tipoDoc'] != ''
        and $_POST['identidad'] != ""
        and $_POST['qr'] != ''


    ) {

        $tabla_aspirante = $wpdb->prefix . 'aspirante';
        
        $nombres = sanitize_text_field($_POST['nombres']);
        $apellidos = sanitize_text_field($_POST['apellido']);
        $correo = sanitize_email($_POST['email']);
        $empresa = sanitize_text_field($_POST['empresa']);
        $cargo = sanitize_text_field($_POST['cargo']);
        $tipoDoc = sanitize_text_field($_POST['tipoDoc']);
        $identidad = sanitize_text_field($_POST['identidad']);
        $fechaVencimiento = sanitize_text_field($_POST['fechaVencimiento']);
        $qr = sanitize_text_field($_POST['qr']);
        $created_at = date('Y-m-d H:i:s');

        $wpdb->insert(
            $tabla_aspirante,
            array(
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'correo' => $correo,
                'empresa' => $empresa,
                'cargo' => $cargo,
                'vencimiento' => $fechaVencimiento,
                'identificacion' => $tipoDoc,
                'numero' => $identidad,
                'qr' => $qr,
                'created_at' => $created_at,
                'foto' => $_POST['imagen'], 
                

            )
        );
    } 



    ob_start();
?>
    <form action="<?php get_the_permalink(); ?>" method="post" class="cuestionario">
        <div class="form-input">
            <label for="nombres">Nombres</label>
            <input type="text" name="nombres" required="required">
        </div>

        <div class="form-input">
            <label for="apellido">Apellidos</label>
            <input type="text" name="apellido" required="required">
        </div>

        <div class="form-input">
            <!-- Campo de selección desplegable -->
            <label for="tipoDoc">Tipo de documento</label>
            <select id="tipoDoc" name="tipoDoc">

                <option value="0">Seleccione tipo</option>
                <option value="1">DNI</option>
                <option value="2">Carnet Extranj.</option>
                <option value="3">Libreta militar</option>
            </select>
        </div>

        <div class="form-input">
            <label for="identidad">Número</label>
            <input type="text" name="identidad" required="required">
        </div>

        <div class="form-input">
            <label for="empresa">Empresa</label>
            <input type="text" name="empresa" required="required">
        </div>
        <div class="form-input">
            <label for="cargo">Cargo</label>
            <select id="cargo" name="cargo">
                <option value="0">Seleccione cargo</option>
                <option value="1">Gerente</option>
                <option value="2">Ingeniero</option>
                <option value="3">Adjunto</option>
            </select>
        </div>

        <div class="form-input">
            <label for="qr">QR:</label>
            <input type="text" name="qr" required="required">
        </div>


        <div class="form-input">
            <label for="fechaVencimiento">Vencimiento</label>
            <input type="date" name="fechaVencimiento" required="required">
        </div>

        <div class="form-input">
            <!-- Campo de correo electrónico -->
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" placeholder="Escribe el correo">
        </div>

        <!-- Campo para adjuntar imagen -->
        <label for="imagen">Adjuntar foto:</label>
        <input type="file" id="imagen" name="imagen" accept="image/*">

        <div class="form-input">
            <!-- Botón de envío -->
            <input type="submit" value="Enviar">
        </div>

    </form>

<?php
    return ob_get_clean();
}
