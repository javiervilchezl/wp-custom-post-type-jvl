<?php
/**
 * Plugin Name: WP Custom Post Type JVL
 * Plugin URI: https://github.com/javiervilchezl/wp-custom-post-type-jvl
 * Description: Plugin for Wordpress for the management of Custom Post Types (CPTs). You can add new CPTs, edit and delete them.
 * Version: 1.1
 * Requires at least: 5.8
 * Requires PHP: 5.6
 * Author: Javier Vílchez Luque
 * Author URI: https://github.com/javiervilchezl
 * Licence: License MIT
 *
 * Copyright 2023-2024 WP Custom Post Type JVL - Javier Vílchez Luque (javiervilchezl)
 */

defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );

require_once plugin_dir_path( __FILE__ ) . 'functions.php';
require_once plugin_dir_path( __FILE__ ) . 'admin-page.php';

register_activation_hook( __FILE__, 'jvl_activate_plugin' );
register_deactivation_hook( __FILE__, 'jvl_deactivate_plugin' );

add_action( 'admin_menu', 'jvl_add_admin_menu' );

add_action( 'init', 'jvl_register_custom_post_types_from_db' );

function jvl_activate_plugin() {
    jvl_register_custom_post_types_from_db();
    flush_rewrite_rules();
}

function jvl_deactivate_plugin() {
    delete_option('jvl_custom_post_types');
    flush_rewrite_rules();
}


?>
