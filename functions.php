<?php
defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );

function jvl_register_custom_post_types_from_db() {
    $cpts = get_option('jvl_custom_post_types', []);
    foreach ($cpts as $cpt) {
        register_post_type($cpt['slug'], [
            'labels' => [
                'name' => $cpt['name'],
                'singular_name'      => $cpt['singular_name'],
                'add_new'            => $cpt['add_new'],
                'add_new_item'       => $cpt['add_new_item'] ?? 'Add New ' . $cpt['singular_name'],
                'edit_item'          => $cpt['edit_item'] ?? 'Edit ' . $cpt['singular_name'],
                'new_item'           => $cpt['new_item'] ?? 'New ' . $cpt['singular_name'],
                'view_item'          => $cpt['view_item'] ?? 'View ' . $cpt['singular_name'],
                'view_items'         => $cpt['view_items'] ?? 'View ' . $cpt['name'],
                'search_items'       => $cpt['search_items'] ?? 'Search ' . $cpt['name'],
                'all_items'          => $cpt['all_items'] ?? 'All ' . $cpt['name'],
                'item_published'     => $cpt['item_published'] ?? $cpt['singular_name'] . ' Published'
            ],
            'public' => true,
            'publicly_queryable' => true,
            'has_archive' => true,
            'supports' => [
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'comments',
                'trackbacks',
                'custom-fields',
                'post-formats',
                'jvl_meta_details'
            ],
            'taxonomies' => ['category', 'post_tag'],
            'rewrite' => ['slug' => $cpt['slug']], 
            'show_in_rest' => true  // Para soporte de editor de bloques
        ]);
     
    }
   
    flush_rewrite_rules();
}

function jvl_save_custom_post_type($cpt_data) {
   
    $cpt_data = array_map('sanitize_text_field', $cpt_data);

    $cpts = get_option('jvl_custom_post_types', []);
    $cpts[$cpt_data['slug']] = $cpt_data;
    update_option('jvl_custom_post_types', $cpts);
   
    set_transient('jvl_messages', 'Custom Post Type creado con éxito.', 10);
   
    wp_redirect(admin_url('admin.php?page=jvl_custom_post_types'));
    exit;
}

function jvl_delete_custom_post_type($cpt_slug) {
    $cpt_slug = sanitize_text_field($cpt_slug);

    $cpts = get_option('jvl_custom_post_types');
    unset($cpts[$cpt_slug]);
    update_option('jvl_custom_post_types', $cpts);
}

function jvl_add_custom_meta_boxes() {
    $cpts = get_option('jvl_custom_post_types', []);  
    foreach ($cpts as $cpt) {
        add_meta_box(
            'jvl_details_meta_box',                
            __('Datos Adicionales', 'textdomain'),   
            'jvl_custom_meta_box_html',             
            $cpt['slug'],                           
            'normal',                              
            'default'                               
        );
    }
}
add_action('add_meta_boxes', 'jvl_add_custom_meta_boxes');

function jvl_custom_meta_box_html($post) {
   
    wp_nonce_field('jvl_custom_meta_box', 'jvl_custom_meta_box_nonce');

   
    $detail = get_post_meta($post->ID, 'jvl_detail', true);

    ?>
    <label for="jvl_detail"><?php _e('Datos:', 'textdomain'); ?></label>
    
    <textarea id="jvl_detail" name="jvl_detail" class="widefat" rows="4"> <?php echo esc_attr($detail); ?> </textarea>
    <?php
}

// Guardar los datos del meta box cuando se guarda el post
function jvl_save_meta_box_data($post_id) {
    
    if (!isset($_POST['jvl_custom_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['jvl_custom_meta_box_nonce'], 'jvl_custom_meta_box')) {
        return;
    }

    // Verificar si el usuario tiene permisos para editar el post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Verificar si no es una autoguardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Sanitizar y guardar o eliminar el valor
    if (isset($_POST['jvl_detail']) && trim($_POST['jvl_detail']) !== '') {
        update_post_meta($post_id, 'jvl_detail', sanitize_textarea_field($_POST['jvl_detail']));
    } else {
        // Si el campo está vacío, elimina el metadato
        delete_post_meta($post_id, 'jvl_detail');
    }
}
add_action('save_post', 'jvl_save_meta_box_data');

function jvl_add_meta_details_to_content($content) {
    if (is_singular() && is_main_query()) {  // Se aplica a cualquier tipo de post en una página singular
        $post_type = get_post_type();  // Obtiene el tipo de post actual
        if (post_type_supports($post_type, 'jvl_meta_details')) {  // Verifica si este tipo de post soporta 'jvl_meta_details'
            $jvl_detail = get_post_meta(get_the_ID(), 'jvl_detail', true);
            if (!empty($jvl_detail)) {
                $content .= '<blockquote class="wp-block-quote is-layout-flow wp-block-quote-is-layout-flow"><p class="jvl-meta-detail"><span style="font-weight:bold;text-decoration-style: underline;">Datos adicionales:</span><br> ' . esc_html($jvl_detail) . '</p></blockquote>';
            }
        }
    }
    return $content;
}
add_filter('the_content', 'jvl_add_meta_details_to_content', 100);

?>
