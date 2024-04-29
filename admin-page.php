<?php
defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );

function jvl_add_admin_menu() {
    add_menu_page(
        'Gestión de Custom Post Types',
        'CPTs Add/Edit',
        'manage_options',
        'jvl_custom_post_types',
        'jvl_custom_post_types_page'
    );
}

function jvl_custom_post_types_page() {
    $edit = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : '';
    $delete = isset($_GET['delete']) ? sanitize_text_field($_GET['delete']) : '';
    if (isset($_GET['delete'])) {
        $slug_to_delete = sanitize_text_field($_GET['delete']);
        jvl_delete_custom_post_type_page($delete);
    } elseif (isset($_GET['edit'])) {
        $slug_to_edit = sanitize_text_field($_GET['edit']);
        jvl_edit_custom_post_type_page($edit);
    } else {
        jvl_main_admin_page();
    }
}


function jvl_main_admin_page() {
    ?>
    <style type="text/css">
        .form-table th{
            padding: 5px 10px 5px 0 !important;
        }
        .form-table td {
            padding: 5px 10px !important;
        }
    </style>
    <div class="wrap">
        <h2>Gestión de Custom Post Types</h2>
        <span>Agrega nuevos CPTs a tu Wordpress. Editalos y Eliminalos desde la lista de Custom Post Types Registrados (Editar / Eliminar)</span>
        <h3 id="editarCPTs">Agregar un nuevo Custom Post Types</h3>
        <form method="post" action="">
            <?php wp_nonce_field('jvl_add_cpt_nonce', 'jvl_nonce_field'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="cpt_name">Nombre:</label></th>
                        <td><input type="text" id="cpt_name" name="cpt_name" placeholder="Recetas" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_singular_name">Nombre Singular:</label></th>
                        <td><input type="text" id="cpt_singular_name" name="cpt_singular_name" placeholder="Receta" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_slug">Slug:</label></th>
                        <td><input type="text" id="cpt_slug" name="cpt_slug" placeholder="recetas" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_add_new">Añadir Nuevo:</label></th>
                        <td><input type="text" id="cpt_add_new" name="cpt_add_new" placeholder="Añadir nueva receta" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_add_new_item">Añadir Nuevo Item:</label></th>
                        <td><input type="text" id="cpt_add_new_item" name="cpt_add_new_item" placeholder="Añadir nueva receta" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_edit_item">Editar Item:</label></th>
                        <td><input type="text" id="cpt_edit_item" name="cpt_edit_item" placeholder="Editar receta" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_new_item">Nuevo Item:</label></th>
                        <td><input type="text" id="cpt_new_item" name="cpt_new_item" placeholder="Nueva receta" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_view_item">Ver Item:</label></th>
                        <td><input type="text" id="cpt_view_item" name="cpt_view_item" placeholder="Ver receta" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_view_items">Ver Items:</label></th>
                        <td><input type="text" id="cpt_view_items" name="cpt_view_items" placeholder="Ver recetas" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_search_items">Buscar Items:</label></th>
                        <td><input type="text" id="cpt_search_items" name="cpt_search_items" placeholder="Buscar recetas" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_all_items">Todos los Items:</label></th>
                        <td><input type="text" id="cpt_all_items" name="cpt_all_items" placeholder="Todas las recetas" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_item_published">Item Publicado:</label></th>
                        <td><input type="text" id="cpt_item_published" name="cpt_item_published" placeholder="Receta publicada" required class="regular-text"></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Agregar Custom Post Type">
            </p>
        </form>
        <?php jvl_show_custom_post_types(); ?>
    </div>
    <?php
    // Envío del formulario
    if (isset($_POST['submit'])) {
        if (check_admin_referer('jvl_add_cpt_nonce', 'jvl_nonce_field')) {
            $cpt_data = [
                'name' => sanitize_text_field($_POST['cpt_name']),
                'singular_name' => sanitize_text_field($_POST['cpt_singular_name']),
                'slug' => sanitize_text_field($_POST['cpt_slug']),
                'add_new' => sanitize_text_field($_POST['cpt_add_new'] ?? ''),
                'add_new_item' => sanitize_text_field($_POST['cpt_add_new_item'] ?? ''),
                'edit_item' => sanitize_text_field($_POST['cpt_edit_item'] ?? ''),
                'new_item' => sanitize_text_field($_POST['cpt_new_item'] ?? ''),
                'view_item' => sanitize_text_field($_POST['cpt_view_item'] ?? ''),
                'view_items' => sanitize_text_field($_POST['cpt_view_items'] ?? ''),
                'search_items' => sanitize_text_field($_POST['cpt_search_items'] ?? ''),
                'all_items' => sanitize_text_field($_POST['cpt_all_items'] ?? ''),
                'item_published' => sanitize_text_field($_POST['cpt_item_published'] ?? ''),
            ];
            jvl_save_custom_post_type($cpt_data);
            echo '<div>Custom Post Type agregado.</div>';
        }
    }
}

function jvl_edit_custom_post_type_page($slug) {
    $admin_url = admin_url('admin.php?page=jvl_custom_post_types');
    $cpts = get_option('jvl_custom_post_types', []);
    if (!isset($cpts[$slug])) {
        echo '<div>No se encontró el Custom Post Type solicitado.</div>';
        return;
    }
    $cpt = $cpts[$slug];
    ?>
    <style type="text/css">
        .form-table th{
            padding: 5px 10px 5px 0 !important;
        }
        .form-table td {
            padding: 5px 10px !important;
        }
    </style>
    <div class="wrap">
        <h2>Editando Custom Post Type: <?php echo esc_html($cpt['name']); ?>  <a href="<?php echo $admin_url; ?>" class="page-title-action">Volver a Gestión de CPTs</a> </h2>
        <form method="post" action="" id="jvl_edit_form">
            <?php wp_nonce_field('jvl_edit_cpt_nonce', 'jvl_nonce_field'); ?>
            <input type="hidden" name="original_slug" value="<?php echo esc_attr($slug); ?>">
            <input type="hidden" name="original_slug_hidden" value="<?php echo esc_attr($slug); ?>">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="cpt_name">Nombre:</label></th>
                        <td><input type="text" id="cpt_name" name="cpt_name" value="<?php echo esc_attr($cpt['name']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_singular_name">Nombre Singular:</label></th>
                        <td><input type="text" id="cpt_singular_name" name="cpt_singular_name" value="<?php echo esc_attr($cpt['singular_name']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_slug">Slug:</label></th>
                        <td><input type="text" id="cpt_slug" name="cpt_slug" value="<?php echo esc_attr($slug); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_add_new">Añadir Nuevo:</label></th>
                        <td><input type="text" id="cpt_add_new" name="cpt_add_new" value="<?php echo esc_attr($cpt['add_new']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_add_new_item">Añadir Nuevo Item:</label></th>
                        <td><input type="text" id="cpt_add_new_item" name="cpt_add_new_item" value="<?php echo esc_attr($cpt['add_new_item']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_edit_item">Editar Item:</label></th>
                        <td><input type="text" id="cpt_edit_item" name="cpt_edit_item" value="<?php echo esc_attr($cpt['edit_item']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_new_item">Nuevo Item:</label></th>
                        <td><input type="text" id="cpt_new_item" name="cpt_new_item" value="<?php echo esc_attr($cpt['new_item']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_view_item">Ver Item:</label></th>
                        <td><input type="text" id="cpt_view_item" name="cpt_view_item" value="<?php echo esc_attr($cpt['view_item']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_view_items">Ver Items:</label></th>
                        <td><input type="text" id="cpt_view_items" name="cpt_view_items" value="<?php echo esc_attr($cpt['view_items']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_search_items">Buscar Items:</label></th>
                        <td><input type="text" id="cpt_search_items" name="cpt_search_items" value="<?php echo esc_attr($cpt['search_items']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_all_items">Todos los Items:</label></th>
                        <td><input type="text" id="cpt_all_items" name="cpt_all_items" value="<?php echo esc_attr($cpt['all_items']); ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cpt_item_published">Item Publicado:</label></th>
                        <td><input type="text" id="cpt_item_published" name="cpt_item_published" value="<?php echo esc_attr($cpt['item_published']); ?>" required class="regular-text"></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="update" id="update" class="button button-primary" value="Actualizar Custom Post Type">
            </p>
        </form>
    </div>
    <script>
        document.getElementById('jvl_edit_form').addEventListener('submit', function(event) {
            var originalSlug = document.getElementsByName('original_slug_hidden')[0].value;
            var newSlug = document.getElementsByName('cpt_slug')[0].value;
            if (originalSlug !== newSlug) {
                var confirmChange = confirm('Cambiar el slug de un CPT puede afectar negativamente el SEO de tu sitio si las URLs han sido indexadas por motores de búsqueda. ¿Estás seguro de que quieres cambiar el slug?');
                if (!confirmChange) {
                    event.preventDefault(); // Detener el envío del formulario
                }
            }
        });
    </script>
    <?php
    if (isset($_POST['update'])) {
        if (check_admin_referer('jvl_edit_cpt_nonce', 'jvl_nonce_field')) {
            $original_slug = sanitize_text_field($_POST['original_slug']);
            $updated_data = [
                'name' => sanitize_text_field($_POST['cpt_name']),
                'singular_name' => sanitize_text_field($_POST['cpt_singular_name']),
                'slug' => sanitize_text_field($_POST['cpt_slug']),
                'add_new' => sanitize_text_field($_POST['cpt_add_new']),
                'add_new_item' => sanitize_text_field($_POST['cpt_add_new_item']),
                'edit_item' => sanitize_text_field($_POST['cpt_edit_item']),
                'new_item' => sanitize_text_field($_POST['cpt_new_item']),
                'view_item' => sanitize_text_field($_POST['cpt_view_item']),
                'view_items' => sanitize_text_field($_POST['cpt_view_items']),
                'search_items' => sanitize_text_field($_POST['cpt_search_items']),
                'all_items' => sanitize_text_field($_POST['cpt_all_items']),
                'item_published' => sanitize_text_field($_POST['cpt_item_published']),
            ];
            jvl_update_custom_post_type($original_slug, $updated_data);
            echo '<div>Custom Post Type actualizado.</div>';
        }
    }
}

function jvl_show_custom_post_types() {
    $cpts = get_option('jvl_custom_post_types', []);
    if (!empty($cpts)) {
        echo '<h3 id="editarCPTs">Custom Post Types Registrados (Editar / Eliminar)</h3>';
        echo '<table class="widefat">';
        echo '<thead><tr><th>Nombre</th><th>Nombre Singular</th><th>Slug</th><th>Acciones</th></tr></thead>';
        echo '<tbody>';
        foreach ($cpts as $cpt) {
            echo '<tr><td>' . esc_html($cpt['name']) . '</td><td>' . esc_html($cpt['singular_name']) . '</td><td>' . esc_html($cpt['slug']) . '</td>';
            echo '<td><a href="?page=jvl_custom_post_types&edit=' . esc_attr($cpt['slug']) . '">Editar</a> | ';
            echo '<a href="?page=jvl_custom_post_types&delete=' . esc_attr($cpt['slug']) . '">Eliminar</a></td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<h3 id="editarCPTs">Custom Post Types Registrados (Editar / Eliminar)</h3>';
        echo '<p>No hay Custom Post Types registrados.</p>';
    }
}

function jvl_update_custom_post_type($original_slug, $updated_data) {

    $cpts = get_option('jvl_custom_post_types', []);
    // Si el slug original es diferente al nuevo y ya existe, estamos en un intento de cambio de slug
    if ($original_slug !== $updated_data['slug'] && isset($cpts[$updated_data['slug']])) {
        echo '<div class="error"><p>Error: El nuevo slug ya está en uso. Por favor, elige otro.</p></div>';
        return;
    }
    // Si el slug ha cambiado, actualiza el slug de todas las entradas asociadas
    if ($original_slug !== $updated_data['slug']) {
        // Obtener todas las entradas con el antiguo slug
        $args = [
            'post_type'      => $original_slug,
            'posts_per_page' => -1,  // Selecciona todas las entradas
            'fields'         => 'ids'  // IDs
        ];
        $posts = get_posts($args);

        // Actualizar el post_type de cada entrada con el nuevo slug
        foreach ($posts as $post_id) {
            set_post_type($post_id, $updated_data['slug']);
        }

        unset($cpts[$original_slug]);  // Eliminar el antiguo CPT si el slug ha cambiado
    }

    // Actualizar o añadir el CPT con el nuevo slug
    $cpts[$updated_data['slug']] = $updated_data;
    update_option('jvl_custom_post_types', $cpts);
   
    set_transient('jvl_messages', 'Custom Post Type actualizado correctamente.', 10);
   
    wp_redirect(admin_url('admin.php?page=jvl_custom_post_types'));
    exit;
}

function jvl_delete_custom_post_type_page($slug) {
    $admin_url = admin_url('admin.php?page=jvl_custom_post_types');
    echo '<div class="wrap">';
    echo '<h2>Eliminar Custom Post Type <a href="'.$admin_url.'" class="page-title-action">Volver a Gestión de CPTs</a> </h2>';
    echo '<p><strong>Advertencia:</strong> Estás a punto de eliminar un CPT. Esto puede afectar a tu sitio si hay contenidos asociados con este CPT.</p>';
    echo '<form method="post" action="" id="jvl_delete_form">';
    echo '<input type="hidden" name="cpt_to_delete" value="' . esc_attr($slug) . '">';
    echo '<label for="new_cpt">Reasignar entradas existentes a: </label>';
    echo '<select name="new_cpt" id="new_cpt" onchange="toggleCategoryField(this.value);">';
    echo '<option value="post">Entradas (Posts)</option>';  // Opción para migrar a entradas estándar de WordPress.
    // Obtener todos los CPTs disponibles, excluyendo el actual
    $cpts = get_option('jvl_custom_post_types', []);
    unset($cpts[$slug]);  // Excluir el CPT actual del listado
    foreach ($cpts as $cpt_slug => $cpt_details) {
        echo '<option value="' . esc_attr($cpt_slug) . '">' . esc_html($cpt_details['name']) . '</option>';
    }

    echo '</select>';
    // Campo de categoría
    echo '<div id="category_selection" style="display:none;margin-top:20px;margin-bottom:20px;">';
    echo '<label for="new_category">Seleccionar categoría: </label>';
    echo '<select name="new_category" id="new_category">';
    $categories = get_categories([
        'hide_empty' => false,  // Obtener categorías incluso si están vacías
        'taxonomy' => 'category'  // Asegurarse de que estamos buscando categorías y no otra taxonomía
    ]);
    foreach ($categories as $category) {
        echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '<input type="submit" name="delete_cpt" value="Eliminar y Reasignar Entradas" class="button button-primary">';
    echo '</form>';
    ?>
    <script>
        if(document.getElementById('new_cpt').value=="post"){
            document.getElementById('category_selection').style.display = "block";
        }
        function toggleCategoryField(value) {
            var categoryField = document.getElementById('category_selection');
            categoryField.style.display = (value === 'post') ? 'block' : 'none';
        }
        document.getElementById('jvl_delete_form').addEventListener('submit', function(event) {  
            var confirmChange = confirm('Advertencia: Eliminar un CPT cuyas URLs están indexadas en motores de búsqueda puede ocasionar problemas de SEO y quizás deberás hacer redireccionamientos. Si las URLs no están indexadas, no habrá problema. ¿Deseas continuar con la eliminación y reasignar las entradas al CPT seleccionado?');
            if (!confirmChange) {
                event.preventDefault(); // Detener el envío del formulario
            }  
        });
    </script>
    <?php
    echo '</div>';
    
    if (isset($_POST['delete_cpt'])) {
        $original_slug = sanitize_text_field($_POST['cpt_to_delete']);
        $new_slug = sanitize_text_field($_POST['new_cpt']);
        $new_category_id = isset($_POST['new_category']) ? (int)$_POST['new_category'] : 0;
        $posts = get_posts(['post_type' => $original_slug, 'numberposts' => -1, 'fields' => 'ids']);
    
        // Reasignar entradas al nuevo CPT
        foreach ($posts as $post_id) {
            set_post_type($post_id, $new_slug);
            if ('post' === $new_slug && $new_category_id) {
                wp_set_post_categories($post_id, [$new_category_id], true);
            }
        }
    
        // Eliminar el CPT de la lista de opciones
        $cpts = get_option('jvl_custom_post_types', []);
        unset($cpts[$original_slug]);
        update_option('jvl_custom_post_types', $cpts);

        set_transient('jvl_messages', 'Custom Post Type eliminado con éxito.', 45);

        wp_redirect(admin_url('admin.php?page=jvl_custom_post_types'));
        exit;
    }
    
}


// Función para mostrar mensajes
function jvl_display_messages() {
    if ($message = get_transient('jvl_messages')) {
        echo "<div class='updated'><p>$message</p></div>";
        delete_transient('jvl_messages');
    }
}

add_action('admin_notices', 'jvl_display_messages');
?>
