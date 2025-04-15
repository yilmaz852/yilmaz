<?php
/*
Plugin Name: Product Table
Plugin URI: https://example.com
Description: Display WooCommerce products in a table format using shortcodes.
Version: 1.2
Author: Yılmaz Yıldırım
Author URI: https://example.com
License: GPL2
*/

// Define constants
define('PRODUCT_TABLE_VERSION', '1.2');
define('PRODUCT_TABLE_PATH', plugin_dir_path(__FILE__));
define('PRODUCT_TABLE_URL', plugin_dir_url(__FILE__));

// Admin menu
function product_table_admin_menu() {
    add_menu_page(
        'Product Table Settings',
        'Product Table',
        'manage_options',
        'product-table',
        'product_table_admin_page',
        'dashicons-list-view',
        25
    );
    
    add_submenu_page(
        'product-table',
        'General Settings',
        'General Settings',
        'manage_options',
        'product-table',
        'product_table_admin_page'
    );
    
    add_submenu_page(
        'product-table',
        'Column Settings',
        'Column Settings',
        'manage_options',
        'product-table-columns',
        'product_table_columns_page'
    );
    
    add_submenu_page(
        'product-table',
        'Style Settings',
        'Style Settings',
        'manage_options',
        'product-table-styles',
        'product_table_styles_page'
    );
}
add_action('admin_menu', 'product_table_admin_menu');

// Register settings
function product_table_register_settings() {
    // General settings
    register_setting('product_table_options_group', 'product_table_default_columns');
    register_setting('product_table_options_group', 'product_table_default_widths');
    register_setting('product_table_options_group', 'product_table_per_page', 'intval');
    register_setting('product_table_options_group', 'product_table_enable_search', 'intval');
    register_setting('product_table_options_group', 'product_table_enable_sorting', 'intval');
    register_setting('product_table_options_group', 'product_table_responsive_mode', 'sanitize_text_field');
    register_setting('product_table_options_group', 'product_table_show_variations', 'intval');
    
    // Column settings
    register_setting('product_table_columns_group', 'product_table_available_columns');
    register_setting('product_table_columns_group', 'product_table_column_widths');
    
    // Style settings
    register_setting('product_table_styles_group', 'product_table_header_bg_color', 'sanitize_hex_color');
    register_setting('product_table_styles_group', 'product_table_header_text_color', 'sanitize_hex_color');
    register_setting('product_table_styles_group', 'product_table_border_color', 'sanitize_hex_color');
    register_setting('product_table_styles_group', 'product_table_row_bg_color', 'sanitize_hex_color');
    register_setting('product_table_styles_group', 'product_table_alt_row_bg_color', 'sanitize_hex_color');
    register_setting('product_table_styles_group', 'product_table_button_bg_color', 'sanitize_hex_color');
    register_setting('product_table_styles_group', 'product_table_button_text_color', 'sanitize_hex_color');
}
add_action('admin_init', 'product_table_register_settings');

// Default settings on activation
function product_table_activate() {
    // Default available columns
    $default_columns = [
        'image' => 'Image',
        'name' => 'Product Name',
        'sku' => 'SKU',
        'price' => 'Price',
        'stock' => 'Stock',
        'categories' => 'Categories',
        'tags' => 'Tags',
        'short_description' => 'Short Description',
        'dimensions' => 'Dimensions',
        'weight' => 'Weight',
        'attributes' => 'Attributes',
        'rating' => 'Rating',
        'date' => 'Date Added',
        'add_to_cart' => 'Add to Cart',
    ];
    
    if (!get_option('product_table_available_columns')) {
        update_option('product_table_available_columns', $default_columns);
    }
    
    // Default column widths
    $default_widths = [
        'image' => '80px',
        'name' => '25%',
        'sku' => '10%',
        'price' => '10%',
        'stock' => '10%',
        'categories' => '15%',
        'tags' => '15%',
        'short_description' => '20%',
        'dimensions' => '10%',
        'weight' => '10%',
        'attributes' => '15%',
        'rating' => '10%',
        'date' => '10%',
        'add_to_cart' => '15%',
    ];
    
    if (!get_option('product_table_column_widths')) {
        update_option('product_table_column_widths', $default_widths);
    }
    
    // Default settings
    if (!get_option('product_table_default_columns')) {
        update_option('product_table_default_columns', 'image,name,sku,price,add_to_cart');
    }
    
    if (!get_option('product_table_default_widths')) {
        update_option('product_table_default_widths', '80px,25%,15%,15%,15%');
    }
    
    if (!get_option('product_table_per_page')) {
        update_option('product_table_per_page', 20);
    }
    
    if (!get_option('product_table_enable_search')) {
        update_option('product_table_enable_search', 1);
    }
    
    if (!get_option('product_table_enable_sorting')) {
        update_option('product_table_enable_sorting', 1);
    }
    
    if (!get_option('product_table_responsive_mode')) {
        update_option('product_table_responsive_mode', 'scroll');
    }
    
    if (!get_option('product_table_show_variations')) {
        update_option('product_table_show_variations', 0);
    }
    
    // Default style settings
    if (!get_option('product_table_header_bg_color')) {
        update_option('product_table_header_bg_color', '#0073aa');
    }
    
    if (!get_option('product_table_header_text_color')) {
        update_option('product_table_header_text_color', '#ffffff');
    }
    
    if (!get_option('product_table_border_color')) {
        update_option('product_table_border_color', '#dddddd');
    }
    
    if (!get_option('product_table_row_bg_color')) {
        update_option('product_table_row_bg_color', '#ffffff');
    }
    
    if (!get_option('product_table_alt_row_bg_color')) {
        update_option('product_table_alt_row_bg_color', '#f9f9f9');
    }
    
    if (!get_option('product_table_button_bg_color')) {
        update_option('product_table_button_bg_color', '#ff6600');
    }
    
    if (!get_option('product_table_button_text_color')) {
        update_option('product_table_button_text_color', '#ffffff');
    }
}
register_activation_hook(__FILE__, 'product_table_activate');

// Admin general settings page
function product_table_admin_page() {
    // Enqueue styles and scripts
    wp_enqueue_style('product-table-admin', PRODUCT_TABLE_URL . 'css/admin.css', array(), PRODUCT_TABLE_VERSION);
    wp_enqueue_script('product-table-admin', PRODUCT_TABLE_URL . 'js/admin.js', array('jquery'), PRODUCT_TABLE_VERSION, true);
    ?>
    <div class="wrap product-table-admin">
        <h1>Product Table Settings</h1>
        
        <?php settings_errors(); ?>
        
        <form method="post" action="options.php">
            <?php settings_fields('product_table_options_group'); ?>
            <?php do_settings_sections('product-table'); ?>
            
            <div class="nav-tab-wrapper">
                <a href="#general-settings" class="nav-tab nav-tab-active">General Settings</a>
                <a href="#display-settings" class="nav-tab">Display Settings</a>
                <a href="#advanced-settings" class="nav-tab">Advanced Settings</a>
            </div>
            
            <div class="tab-content" id="general-settings">
                <table class="form-table">
                    <tr>
                        <th scope="row">Default Columns</th>
                        <td>
                            <input type="text" name="product_table_default_columns" value="<?php echo esc_attr(get_option('product_table_default_columns', 'image,name,sku,price,add_to_cart')); ?>" class="regular-text" />
                            <p class="description">Comma-separated column names. Example: image,name,sku,price,add_to_cart</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Default Column Widths</th>
                        <td>
                            <input type="text" name="product_table_default_widths" value="<?php echo esc_attr(get_option('product_table_default_widths', '80px,25%,15%,15%,15%')); ?>" class="regular-text" />
                            <p class="description">Comma-separated width values. Example: 80px,25%,15%,15%,15%</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Products Per Page</th>
                        <td>
                            <input type="number" name="product_table_per_page" value="<?php echo intval(get_option('product_table_per_page', 20)); ?>" min="1" max="100" step="1" class="small-text" />
                            <p class="description">Number of products to display per page</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="tab-content" id="display-settings" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">Responsive Mode</th>
                        <td>
                            <select name="product_table_responsive_mode" class="regular-text">
                                <option value="scroll" <?php selected(get_option('product_table_responsive_mode', 'scroll'), 'scroll'); ?>>Scroll</option>
                                <option value="collapse" <?php selected(get_option('product_table_responsive_mode', 'scroll'), 'collapse'); ?>>Collapse</option>
                                <option value="disabled" <?php selected(get_option('product_table_responsive_mode', 'scroll'), 'disabled'); ?>>Disabled</option>
                            </select>
                            <p class="description">How should the table behave on mobile devices?</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Show Variations as Rows</th>
                        <td>
                            <input type="checkbox" name="product_table_show_variations" value="1" <?php checked(get_option('product_table_show_variations', 0), 1); ?> />
                            <p class="description">Display each variation of a variable product as a separate row in the table</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="tab-content" id="advanced-settings" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable Search</th>
                        <td>
                            <input type="checkbox" name="product_table_enable_search" value="1" <?php checked(get_option('product_table_enable_search', 1), 1); ?> />
                            <p class="description">Allows users to search for products</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Enable Sorting</th>
                        <td>
                            <input type="checkbox" name="product_table_enable_sorting" value="1" <?php checked(get_option('product_table_enable_sorting', 1), 1); ?> />
                            <p class="description">Allows users to sort by columns</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button('Save Settings'); ?>
        </form>
        
        <div class="product-table-usage">
            <h2>Shortcode Usage</h2>
            <p>Basic usage: <code>[product_table]</code></p>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Value</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>category</code></td>
                        <td>category-slug</td>
                        <td>Display products from a specific category</td>
                    </tr>
                    <tr>
                        <td><code>columns</code></td>
                        <td>name,price,...</td>
                        <td>Specify columns to display</td>
                    </tr>
                    <tr>
                        <td><code>per_page</code></td>
                        <td>20</td>
                        <td>Number of products per page</td>
                    </tr>
                    <tr>
                        <td><code>sort_by</code></td>
                        <td>price_asc</td>
                        <td>Default sorting (price_asc, price_desc, name_asc, name_desc, date_new, date_old)</td>
                    </tr>
                    <tr>
                        <td><code>tag</code></td>
                        <td>tag-slug</td>
                        <td>Display products with a specific tag</td>
                    </tr>
                    <tr>
                        <td><code>show_variations</code></td>
                        <td>true</td>
                        <td>Show variations as separate rows</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            // Change active tab
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show/hide content
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
    });
    </script>
    <?php
}

// Column settings page
function product_table_columns_page() {
    // Get existing columns and widths
    $available_columns = get_option('product_table_available_columns', array());
    $column_widths = get_option('product_table_column_widths', array());
    
    // Get custom fields
    $custom_fields = array();
    
    // Query custom fields from post meta
    global $wpdb;
    $meta_keys = $wpdb->get_results("
        SELECT DISTINCT meta_key
        FROM {$wpdb->postmeta} pm
        JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_type = 'product'
        AND meta_key NOT LIKE '\_%'
        ORDER BY meta_key
    ");
    
    foreach ($meta_keys as $meta) {
        $custom_fields[$meta->meta_key] = $meta->meta_key;
    }
    
    // Process form submission
    if (isset($_POST['product_table_save_columns']) && check_admin_referer('product_table_save_columns_nonce')) {
        $column_keys = isset($_POST['column_key']) ? $_POST['column_key'] : array();
        $column_names = isset($_POST['column_name']) ? $_POST['column_name'] : array();
        $column_widths_values = isset($_POST['column_width']) ? $_POST['column_width'] : array();
        
        $new_columns = array();
        $new_widths = array();
        
        foreach ($column_keys as $index => $key) {
            if (!empty($key) && !empty($column_names[$index])) {
                $new_columns[$key] = sanitize_text_field($column_names[$index]);
                $new_widths[$key] = sanitize_text_field($column_widths_values[$index]);
            }
        }
        
        update_option('product_table_available_columns', $new_columns);
        update_option('product_table_column_widths', $new_widths);
        
        echo '<div class="notice notice-success"><p>Column settings saved.</p></div>';
        
        // Update data
        $available_columns = $new_columns;
        $column_widths = $new_widths;
    }
    
    // Enqueue color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    ?>
    <div class="wrap product-table-admin">
        <h1>Product Table Column Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('product_table_save_columns_nonce'); ?>
            
            <p>Manage the columns that can be used in the product table. You can add new columns or edit existing ones.</p>
            
            <table class="wp-list-table widefat fixed striped" id="product-table-columns">
                <thead>
                    <tr>
                        <th width="20%">Column Key</th>
                        <th width="30%">Column Label</th>
                        <th width="20%">Width</th>
                        <th width="30%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($available_columns)) : ?>
                        <?php foreach ($available_columns as $key => $name) : ?>
                            <tr>
                                <td>
                                    <input type="text" name="column_key[]" value="<?php echo esc_attr($key); ?>" class="regular-text" readonly />
                                </td>
                                <td>
                                    <input type="text" name="column_name[]" value="<?php echo esc_attr($name); ?>" class="regular-text" />
                                </td>
                                <td>
                                    <input type="text" name="column_width[]" value="<?php echo esc_attr(isset($column_widths[$key]) ? $column_widths[$key] : ''); ?>" class="regular-text" placeholder="e.g. 100px, 15%, auto" />
                                </td>
                                <td>
                                    <?php if (!in_array($key, ['image', 'name', 'price', 'add_to_cart'])) : ?>
                                        <button type="button" class="button remove-column">Remove</button>
                                    <?php else: ?>
                                        <span class="description">Core column (cannot be removed)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td>
                                <input type="text" name="column_key[]" value="" class="regular-text" />
                            </td>
                            <td>
                                <input type="text" name="column_name[]" value="" class="regular-text" />
                            </td>
                            <td>
                                <input type="text" name="column_width[]" value="" class="regular-text" placeholder="e.g. 100px, 15%, auto" />
                            </td>
                            <td>
                                <button type="button" class="button remove-column">Remove</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <button type="button" class="button add-new-column">Add New Column</button>
                            <button type="button" class="button add-custom-field">Add Custom Field</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Custom Field Selector Modal -->
            <div id="custom-field-modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:20px; border:1px solid #ccc; box-shadow:0 0 10px rgba(0,0,0,0.2); z-index:9999; width:400px; max-width:90%;">
                <h3>Select Custom Field</h3>
                
                <select id="custom-field-select" style="width:100%; margin-bottom:15px;">
                    <option value="">-- Select a custom field --</option>
                    <?php foreach ($custom_fields as $key => $label) : ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="button cancel-custom-field">Cancel</button>
                    <button type="button" class="button button-primary add-selected-field">Add Field</button>
                </div>
            </div>
            
            <p class="submit">
                <input type="submit" name="product_table_save_columns" class="button button-primary" value="Save Columns">
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Add new column
        $('.add-new-column').on('click', function() {
            var row = '<tr>';
            row += '<td><input type="text" name="column_key[]" value="" class="regular-text" /></td>';
            row += '<td><input type="text" name="column_name[]" value="" class="regular-text" /></td>';
            row += '<td><input type="text" name="column_width[]" value="" class="regular-text" placeholder="e.g. 100px, 15%, auto" /></td>';
            row += '<td><button type="button" class="button remove-column">Remove</button></td>';
            row += '</tr>';
            
            $('#product-table-columns tbody').append(row);
        });
        
        // Remove column
        $('#product-table-columns').on('click', '.remove-column', function() {
            $(this).closest('tr').remove();
        });
        
        // Custom field modal
        $('.add-custom-field').on('click', function() {
            $('#custom-field-modal').show();
        });
        
        $('.cancel-custom-field').on('click', function() {
            $('#custom-field-modal').hide();
        });
        
        $('.add-selected-field').on('click', function() {
            var field = $('#custom-field-select').val();
            var fieldText = $('#custom-field-select option:selected').text();
            
            if (field) {
                var row = '<tr>';
                row += '<td><input type="text" name="column_key[]" value="cf_' + field + '" class="regular-text" /></td>';
                row += '<td><input type="text" name="column_name[]" value="' + fieldText + '" class="regular-text" /></td>';
                row += '<td><input type="text" name="column_width[]" value="auto" class="regular-text" placeholder="e.g. 100px, 15%, auto" /></td>';
                row += '<td><button type="button" class="button remove-column">Remove</button></td>';
                row += '</tr>';
                
                $('#product-table-columns tbody').append(row);
                $('#custom-field-modal').hide();
            }
        });
    });
    </script>
    <?php
}

// Style settings page
function product_table_styles_page() {
    // Enqueue color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    ?>
    <div class="wrap product-table-admin">
        <h1>Product Table Style Settings</h1>
        
        <?php settings_errors(); ?>
        
        <form method="post" action="options.php">
            <?php settings_fields('product_table_styles_group'); ?>
            <?php do_settings_sections('product-table-styles'); ?>
            
            <div class="nav-tab-wrapper">
                <a href="#table-styles" class="nav-tab nav-tab-active">Table Styles</a>
                <a href="#button-styles" class="nav-tab">Button Styles</a>
                <a href="#preview" class="nav-tab">Preview</a>
            </div>
            
            <div class="tab-content" id="table-styles">
                <table class="form-table">
                    <tr>
                        <th scope="row">Header Background Color</th>
                        <td>
                            <input type="text" name="product_table_header_bg_color" value="<?php echo esc_attr(get_option('product_table_header_bg_color', '#0073aa')); ?>" class="color-field" data-default-color="#0073aa" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Header Text Color</th>
                        <td>
                            <input type="text" name="product_table_header_text_color" value="<?php echo esc_attr(get_option('product_table_header_text_color', '#ffffff')); ?>" class="color-field" data-default-color="#ffffff" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Border Color</th>
                        <td>
                            <input type="text" name="product_table_border_color" value="<?php echo esc_attr(get_option('product_table_border_color', '#dddddd')); ?>" class="color-field" data-default-color="#dddddd" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Row Background Color</th>
                        <td>
                            <input type="text" name="product_table_row_bg_color" value="<?php echo esc_attr(get_option('product_table_row_bg_color', '#ffffff')); ?>" class="color-field" data-default-color="#ffffff" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Alternate Row Background Color</th>
                        <td>
                            <input type="text" name="product_table_alt_row_bg_color" value="<?php echo esc_attr(get_option('product_table_alt_row_bg_color', '#f9f9f9')); ?>" class="color-field" data-default-color="#f9f9f9" />
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="tab-content" id="button-styles" style="display:none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">Button Background Color</th>
                        <td>
                            <input type="text" name="product_table_button_bg_color" value="<?php echo esc_attr(get_option('product_table_button_bg_color', '#ff6600')); ?>" class="color-field" data-default-color="#ff6600" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Button Text Color</th>
                        <td>
                            <input type="text" name="product_table_button_text_color" value="<?php echo esc_attr(get_option('product_table_button_text_color', '#ffffff')); ?>" class="color-field" data-default-color="#ffffff" />
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="tab-content" id="preview" style="display:none;">
                <h3>Table Style Preview</h3>
                
                <div class="table-preview" style="margin-top: 20px;">
                    <table class="product-table-preview">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Add to Cart</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><div class="img-placeholder"></div></td>
                                <td>Sample Product</td>
                                <td>$29.99</td>
                                <td>In Stock</td>
                                <td><button>Add to Cart</button></td>
                            </tr>
                            <tr>
                                <td><div class="img-placeholder"></div></td>
                                <td>Another Product</td>
                                <td>$49.99</td>
                                <td>In Stock</td>
                                <td><button>Add to Cart</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <p class="description">This is a live preview of how your table will look with the current style settings.</p>
            </div>
            
            <?php submit_button('Save Style Settings'); ?>
        </form>
    </div>
    
    <style>
    .product-table-preview {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid <?php echo esc_attr(get_option('product_table_border_color', '#dddddd')); ?>;
    }
    
    .product-table-preview thead {
        background-color: <?php echo esc_attr(get_option('product_table_header_bg_color', '#0073aa')); ?>;
        color: <?php echo esc_attr(get_option('product_table_header_text_color', '#ffffff')); ?>;
    }
    
    .product-table-preview th, 
    .product-table-preview td {
        padding: 12px;
        text-align: center;
        border: 1px solid <?php echo esc_attr(get_option('product_table_border_color', '#dddddd')); ?>;
    }
    
    .product-table-preview tbody tr:nth-child(odd) {
        background-color: <?php echo esc_attr(get_option('product_table_row_bg_color', '#ffffff')); ?>;
    }
    
    .product-table-preview tbody tr:nth-child(even) {
        background-color: <?php echo esc_attr(get_option('product_table_alt_row_bg_color', '#f9f9f9')); ?>;
    }
    
    .product-table-preview button {
        background-color: <?php echo esc_attr(get_option('product_table_button_bg_color', '#ff6600')); ?>;
        color: <?php echo esc_attr(get_option('product_table_button_text_color', '#ffffff')); ?>;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 3px;
    }
    
    .img-placeholder {
        width: 50px;
        height: 50px;
        background-color: #eee;
        margin: 0 auto;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            
            // Change active tab
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show/hide content
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
        
        // Initialize color pickers
        $('.color-field').wpColorPicker({
            change: function(event, ui) {
                updatePreview();
            }
        });
        
        // Update preview when color changes
        function updatePreview() {
            var headerBg = $('input[name="product_table_header_bg_color"]').val() || '#0073aa';
            var headerText = $('input[name="product_table_header_text_color"]').val() || '#ffffff';
            var borderColor = $('input[name="product_table_border_color"]').val() || '#dddddd';
            var rowBg = $('input[name="product_table_row_bg_color"]').val() || '#ffffff';
            var altRowBg = $('input[name="product_table_alt_row_bg_color"]').val() || '#f9f9f9';
            var buttonBg = $('input[name="product_table_button_bg_color"]').val() || '#ff6600';
            var buttonText = $('input[name="product_table_button_text_color"]').val() || '#ffffff';
            
            $('.product-table-preview thead').css('background-color', headerBg);
            $('.product-table-preview thead').css('color', headerText);
            $('.product-table-preview, .product-table-preview th, .product-table-preview td').css('border-color', borderColor);
            $('.product-table-preview tbody tr:nth-child(odd)').css('background-color', rowBg);
            $('.product-table-preview tbody tr:nth-child(even)').css('background-color', altRowBg);
            $('.product-table-preview button').css({
                'background-color': buttonBg,
                'color': buttonText
            });
        }
        
        // Initial preview update
        setTimeout(updatePreview, 100);
    });
    </script>
    <?php
}

/**
 * Process column content
 * 
 * @param string $column_key Column key
 * @param WC_Product $product Product object
 * @return string HTML content
 */
function product_table_get_column_content($column_key, $product, $variation = null) {
    // Use variation data if provided
    $the_product = $variation ? $variation : $product;
    
    // Handle custom field columns
    if (strpos($column_key, 'cf_') === 0) {
        $meta_key = substr($column_key, 3);
        $value = get_post_meta($the_product->get_id(), $meta_key, true);
        return !empty($value) ? $value : '—';
    }
    
    switch ($column_key) {
        case 'image':
            return '<img src="' . wp_get_attachment_image_url($the_product->get_image_id(), 'thumbnail') . '" alt="' . esc_attr($the_product->get_name()) . '" class="product-thumbnail">';
            
        case 'name':
            $output = '<a href="' . get_permalink($the_product->get_id()) . '">' . $the_product->get_name() . '</a>';
            
            // Add variation attributes for variations
            if ($variation && $product->is_type('variable')) {
                $attributes = $variation->get_variation_attributes();
                if (!empty($attributes)) {
                    $output .= '<div class="variation-attributes">';
                    foreach ($attributes as $attr_name => $attr_value) {
                        $taxonomy = str_replace('attribute_', '', $attr_name);
                        $term = get_term_by('slug', $attr_value, $taxonomy);
                        $label = wc_attribute_label($taxonomy);
                        $value = $term ? $term->name : $attr_value;
                        $output .= '<span class="variation-attribute">' . $label . ': ' . $value . '</span>';
                    }
                    $output .= '</div>';
                }
            }
            
            return $output;
            
        case 'sku':
            return $the_product->get_sku() ? $the_product->get_sku() : '—';
            
        case 'price':
            return $the_product->get_price_html();
            
        case 'stock':
            $stock_status = $the_product->get_stock_status();
            $stock_qty = $the_product->get_stock_quantity();
            
            if ($stock_status === 'instock') {
                $text = is_null($stock_qty) ? __('In Stock', 'product-table') : sprintf(__('In Stock (%d)', 'product-table'), $stock_qty);
                return '<span class="in-stock">' . $text . '</span>';
            } else if ($stock_status === 'outofstock') {
                return '<span class="out-of-stock">' . __('Out of Stock', 'product-table') . '</span>';
            } else {
                return '<span class="on-backorder">' . __('On Backorder', 'product-table') . '</span>';
            }
            
        case 'categories':
            $categories = wc_get_product_category_list($the_product->get_id());
            return $categories ? $categories : '—';
            
        case 'tags':
            $tags = wc_get_product_tag_list($the_product->get_id());
            return $tags ? $tags : '—';
            
        case 'short_description':
            return $the_product->get_short_description() ? $the_product->get_short_description() : '—';
            
        case 'dimensions':
            $dimensions = wc_format_dimensions($the_product->get_dimensions(false));
            return $dimensions !== __('N/A', 'woocommerce') ? $dimensions : '—';
            
        case 'weight':
            $weight = $the_product->get_weight();
            return $weight ? $weight . ' ' . get_option('woocommerce_weight_unit') : '—';
            
        case 'attributes':
            $attributes = $the_product->get_attributes();
            if (!empty($attributes)) {
                $output = '<ul class="product-attributes">';
                foreach ($attributes as $attribute) {
                    if ($attribute->is_taxonomy()) {
                        $terms = wp_get_post_terms($the_product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                        if (!empty($terms)) {
                            $output .= '<li>' . wc_attribute_label($attribute->get_name()) . ': ' . implode(', ', $terms) . '</li>';
                        }
                    } else {
                        $values = $attribute->get_options();
                        if (!empty($values)) {
                            $output .= '<li>' . $attribute->get_name() . ': ' . implode(', ', $values) . '</li>';
                        }
                    }
                }
                $output .= '</ul>';
                return $output;
            }
            return '—';
            
        case 'rating':
            $rating = $the_product->get_average_rating();
            if ($rating > 0) {
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= '<span class="star ' . ($i <= $rating ? 'filled' : 'empty') . '">★</span>';
                }
                return $stars . ' (' . $rating . ')';
            }
            return '—';
            
        case 'date':
            return date_i18n(get_option('date_format'), strtotime($the_product->get_date_created()));
            
        case 'add_to_cart':
            $button = '<div class="add-to-cart-container">';
            
            // Quantity field
            if (!$variation) {
                $button .= '<div class="quantity-wrapper">';
                $button .= '<span class="qty-decrease">-</span>';
                $button .= '<input type="number" class="input-text qty text" name="quantity" value="1" min="1" max="' . 
                          ($the_product->get_stock_quantity() ?: '') . '" step="1" inputmode="numeric">';
                $button .= '<span class="qty-increase">+</span>';
                $button .= '</div>';
            }
            
            // Add to cart button
            if ($the_product->is_type('variable') && !$variation) {
                $button .= '<a href="' . get_permalink($the_product->get_id()) . '" class="view-product-button">' . __('View Options', 'product-table') . '</a>';
            } else {
                // Add to cart button with icon
                $product_id = $the_product->get_id();
                $button .= '<button type="submit" name="add-to-cart" value="' . esc_attr($product_id) . '" class="add-to-cart-icon button alt product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product_id . '">';
                $button .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>';
                $button .= '</button>';
            }
            
            $button .= '</div>';
            return $button;
            
        default:
            return apply_filters('product_table_column_content', '—', $column_key, $the_product);
    }
}

// Shortcode function
function product_table_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'category' => '', // Category slug
            'columns' => get_option('product_table_default_columns', 'image,name,sku,price,add_to_cart'),
            'per_page' => get_option('product_table_per_page', 20),
            'sort_by' => '',
            'tag' => '',
            'show_variations' => get_option('product_table_show_variations', 0),
        ),
        $atts
    );

    $columns = explode(',', $atts['columns']);
    $column_widths_option = get_option('product_table_column_widths', array());
    
    // Query parameters
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => intval($atts['per_page']),
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    );
    
    // Category filter
    if (!empty($atts['category'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => sanitize_title($atts['category']),
        );
    }
    
    // Tag filter
    if (!empty($atts['tag'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => sanitize_title($atts['tag']),
        );
    }
    
    // Sorting
    switch ($atts['sort_by']) {
        case 'price_asc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
            
        case 'price_desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
            
        case 'name_asc':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;
            
        case 'name_desc':
            $args['orderby'] = 'title';
            $args['order'] = 'DESC';
            break;
            
        case 'date_new':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
            
        case 'date_old':
            $args['orderby'] = 'date';
            $args['order'] = 'ASC';
            break;
    }
    
    // Enqueue styles and scripts
    wp_enqueue_style('product-table-style', PRODUCT_TABLE_URL . 'css/style.css', array(), PRODUCT_TABLE_VERSION);
    wp_enqueue_script('product-table-script', PRODUCT_TABLE_URL . 'js/script.js', array('jquery'), PRODUCT_TABLE_VERSION, true);
    
    // Generate dynamic CSS based on style settings
    $dynamic_css = '
        .product-table-wrapper thead {
            background-color: ' . esc_attr(get_option('product_table_header_bg_color', '#0073aa')) . ';
            color: ' . esc_attr(get_option('product_table_header_text_color', '#ffffff')) . ';
        }
        .product-table-wrapper th, 
        .product-table-wrapper td {
            border-color: ' . esc_attr(get_option('product_table_border_color', '#dddddd')) . ';
        }
        .product-table-wrapper tbody tr:nth-child(odd) {
            background-color: ' . esc_attr(get_option('product_table_row_bg_color', '#ffffff')) . ';
        }
        .product-table-wrapper tbody tr:nth-child(even) {
            background-color: ' . esc_attr(get_option('product_table_alt_row_bg_color', '#f9f9f9')) . ';
        }
        .product-table-wrapper .add-to-cart-icon,
        .product-table-wrapper .view-product-button {
            background-color: ' . esc_attr(get_option('product_table_button_bg_color', '#ff6600')) . ';
            color: ' . esc_attr(get_option('product_table_button_text_color', '#ffffff')) . ';
        }
    ';
    
    wp_add_inline_style('product-table-style', $dynamic_css);
    
    // Check WooCommerce dependency
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce must be active to use this feature.', 'product-table') . '</p>';
    }

    $products = new WP_Query($args);
    
    $enable_search = get_option('product_table_enable_search', 1);
    $enable_sorting = get_option('product_table_enable_sorting', 1);
    $responsive_mode = get_option('product_table_responsive_mode', 'scroll');
    $show_variations = isset($atts['show_variations']) ? filter_var($atts['show_variations'], FILTER_VALIDATE_BOOLEAN) : get_option('product_table_show_variations', 0);
    
    $unique_id = 'product-table-' . uniqid();
    
    ob_start();
    
    // Search and filter controls
    if ($enable_search || $enable_sorting) {
        echo '<div class="product-table-controls">';
        
        if ($enable_search) {
            echo '<div class="product-table-search">';
            echo '<input type="text" id="search-' . $unique_id . '" class="product-table-search-input" placeholder="' . __('Search in table...', 'product-table') . '" />';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    // Table container
    echo '<div class="product-table-wrapper ' . esc_attr('responsive-' . $responsive_mode) . '">';
    echo '<table class="product-table ' . esc_attr($unique_id) . '">';
    
    // Header row
    echo '<thead><tr>';
    foreach ($columns as $index => $column) {
        $column = trim($column);
        $available_columns = get_option('product_table_available_columns', array());
        $column_name = isset($available_columns[$column]) ? $available_columns[$column] : ucfirst($column);
        
        $width = isset($column_widths_option[$column]) ? $column_widths_option[$column] : 'auto';
        
        echo '<th style="width: ' . esc_attr($width) . ';" class="column-' . esc_attr($column) . '">';
        echo esc_html($column_name);
        
        // Sorting icons
        if ($enable_sorting && in_array($column, array('name', 'price', 'date', 'sku'))) {
            echo ' <span class="sort-icon" data-column="' . esc_attr($column) . '">↕</span>';
        }
        
        echo '</th>';
    }
    echo '</tr></thead>';

    echo '<tbody>';
    
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            
            // Skip variations as we'll handle them separately if needed
            if ($product->is_type('variation')) {
                continue;
            }
            
            // Show the main product if it's not a variable product or if we're not showing variations
            if (!$product->is_type('variable') || !$show_variations) {
                echo '<tr>';
                foreach ($columns as $column) {
                    $column = trim($column);
                    echo '<td class="column-' . esc_attr($column) . '">';
                    echo product_table_get_column_content($column, $product);
                    echo '</td>';
                }
                echo '</tr>';
            }
            
            // Handle variable products if show_variations is enabled
            if ($product->is_type('variable') && $show_variations) {
                $variations = $product->get_available_variations();
                
                if (!empty($variations)) {
                    foreach ($variations as $variation_data) {
                        $variation_id = $variation_data['variation_id'];
                        $variation = wc_get_product($variation_id);
                        
                        if ($variation) {
                            echo '<tr class="variation-row">';
                            foreach ($columns as $column) {
                                $column = trim($column);
                                echo '<td class="column-' . esc_attr($column) . '">';
                                echo product_table_get_column_content($column, $product, $variation);
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                    }
                } else {
                    // If no variations are available, show the main product
                    echo '<tr>';
                    foreach ($columns as $column) {
                        $column = trim($column);
                        echo '<td class="column-' . esc_attr($column) . '">';
                        echo product_table_get_column_content($column, $product);
                        echo '</td>';
                    }
                    echo '</tr>';
                }
            }
        }
    } else {
        echo '<tr><td colspan="' . count($columns) . '">' . __('No products found matching your criteria.', 'product-table') . '</td></tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    // Pagination
    if ($products->max_num_pages > 1) {
        echo '<div class="product-table-pagination">';
        echo paginate_links(array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $products->max_num_pages,
            'prev_text' => '&laquo; ' . __('Previous', 'product-table'),
            'next_text' => __('Next', 'product-table') . ' &raquo;',
        ));
        echo '</div>';
    }
    
    // JavaScript for filtering and sorting
    ?>
    <script>
    jQuery(document).ready(function($) {
        var tableId = '<?php echo $unique_id; ?>';
        var $table = $('.' + tableId);
        var $searchInput = $('#search-' + tableId);
        
        // Search functionality
        if ($searchInput.length) {
            $searchInput.on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                
                $table.find('tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    var match = rowText.indexOf(searchText) > -1;
                    $(this).toggle(match);
                });
            });
        }
        
        // Sorting functionality
        $table.find('th .sort-icon').on('click', function() {
            var column = $(this).data('column');
            var $th = $(this).closest('th');
            var $tbody = $table.find('tbody');
            var rows = $tbody.find('tr').toArray();
            var isAscending = $(this).hasClass('asc');
            
            // Toggle sort direction
            $table.find('th .sort-icon').text('↕');
            $(this).text(isAscending ? '↓' : '↑');
            $(this).toggleClass('asc');
            
            // Sort rows
            rows.sort(function(a, b) {
                var aValue = $(a).find('.column-' + column).text();
                var bValue = $(b).find('.column-' + column).text();
                
                // Handle numeric values
                if (!isNaN(parseFloat(aValue)) && !isNaN(parseFloat(bValue))) {
                    aValue = parseFloat(aValue);
                    bValue = parseFloat(bValue);
                }
                
                if (aValue < bValue) return isAscending ? -1 : 1;
                if (aValue > bValue) return isAscending ? 1 : -1;
                return 0;
            });
            
            // Append to DOM
            $.each(rows, function(index, row) {
                $tbody.append(row);
            });
        });
        
        // Quantity increment/decrement
        $table.on('click', '.qty-decrease', function() {
            var $input = $(this).siblings('input.qty');
            var qty = parseInt($input.val()) - 1;
            qty = qty < 1 ? 1 : qty;
            $input.val(qty).change();
        });
        
        $table.on('click', '.qty-increase', function() {
            var $input = $(this).siblings('input.qty');
            var qty = parseInt($input.val()) + 1;
            var max = $input.attr('max') ? parseInt($input.attr('max')) : 9999;
            qty = qty > max ? max : qty;
            $input.val(qty).change();
        });
    });
    </script>
    <?php
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('product_table', 'product_table_shortcode');

// Enqueue styles and scripts
function product_table_enqueue_styles() {
    wp_enqueue_style('product-table-styles', PRODUCT_TABLE_URL . 'css/style.css', array(), PRODUCT_TABLE_VERSION);
    wp_enqueue_script('product-table-scripts', PRODUCT_TABLE_URL . 'js/script.js', array('jquery'), PRODUCT_TABLE_VERSION, true);
    
    // AJAX variables
    wp_localize_script('product-table-scripts', 'productTable', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('product_table_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'product_table_enqueue_styles');

// Admin styles and scripts
function product_table_admin_enqueue_styles() {
    $screen = get_current_screen();
    
    if ($screen && strpos($screen->id, 'product-table') !== false) {
        wp_enqueue_style('product-table-admin-styles', PRODUCT_TABLE_URL . 'css/admin.css', array(), PRODUCT_TABLE_VERSION);
        wp_enqueue_script('product-table-admin-scripts', PRODUCT_TABLE_URL . 'js/admin.js', array('jquery', 'wp-color-picker'), PRODUCT_TABLE_VERSION, true);
        
        wp_localize_script('product-table-admin-scripts', 'productTableAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('product_table_admin_nonce'),
            'placeholder_image' => PRODUCT_TABLE_URL . 'assets/placeholder.png'
        ));
    }
}
add_action('admin_enqueue_scripts', 'product_table_admin_enqueue_styles');