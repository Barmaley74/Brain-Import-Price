<?php

// Define Product fields
// Определяем поля товаров для добавления
function woo_bip_product_fields() {

	$fields = array();
	$fields[] = array(
		'name' => 'sku',
		'label' => __( 'SKU', 'woo_bip' ),
		'alias' => array( 'product_sku', 'product_number' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Product Name', 'woo_bip' ),
		'alias' => array( 'product_name', 'product_title' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woo_bip' ),
		'alias' => array( 'long_description' )
	);
	$fields[] = array(
		'name' => 'supplier_price',
		'label' => __( 'Supplier Price', 'woo_bip' ),
		'alias' => array( 'supplier_price')
	);
	$fields[] = array(
		'name' => 'category',
		'label' => __( 'Category', 'woo_bip' ),
		'alias' => array( 'product_category', 'product_categories', 'categories' )
	);
	$fields[] = array(
		'name' => 'product_url',
		'label' => __( 'Product URL', 'woo_bip' ),
		'alias' => array( 'external_url', 'external_link' )
	);
    $fields[] = array(
        'name' => 'brands',
        'label' => __( 'Brands', 'woo_bip' ),
        'alias' => array( 'brands')
    );
    $fields[] = array(
        'name' => 'supplier_code',
        'label' => __( 'Supplier Code', 'woo_bip' ),
        'alias' => array( 'supplier_code' )
    );
    $fields[] = array(
        'name' => 'warranty',
        'label' => __( 'Warranty', 'woo_bip' ),
        'alias' => array( 'warranty' )
    );


	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( function_exists( 'aioseop_get_version' ) ) {
		$fields[] = array(
			'name' => 'aioseop_keywords',
			'label' => __( 'All in One SEO - Keywords', 'woo_bip' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'aioseop_description',
			'label' => __( 'All in One SEO - Description', 'woo_bip' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'aioseop_title',
			'label' => __( 'All in One SEO - Title', 'woo_bip' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'aioseop_titleatr',
			'label' => __( 'All in One SEO - Title Attributes', 'woo_bip' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'aioseop_menulabel',
			'label' => __( 'All in One SEO - Menu Label', 'woo_bip' ),
			'disabled' => 1
		);
	}

	return $fields;

}

// Downloading and setting images for Product
// Скачиваем и устанавливаем изображения для товара
function woo_bip_set_image ( $ID, $url, $gallery) {

    $url = WOO_BIP_SUPPLIER_URL . $url;
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');

        $image_name = basename ($url);
        $uploads   = wp_upload_dir();
        $targetDir = $uploads['path'];
    if (!file_exists($targetDir . '/' . $image_name)) {
        $image_filename = wp_unique_filename($targetDir, $image_name);
        $image_filepath = $targetDir . '/' . $image_filename;
                $image = @file_get_contents($url);
                if (!empty($image)) {
                    file_put_contents($image_filepath, $image);
                    $image_info = @getimagesize($image_filepath);
                    $attachment = array(
                        'post_mime_type' => image_type_to_mime_type($image_info[2]),
                        'guid' => $targetDir . '/' . $image_filename,
                        'post_title' => $image_filename,
                        'post_content' => '',
                    );
                    $attid = wp_insert_attachment($attachment, $image_filepath, $ID);
                    wp_update_attachment_metadata($attid, wp_generate_attachment_metadata($attid, $image_filepath));
                    if ($gallery == '') {
                        set_post_thumbnail($ID, $attid);
                        $gallery = '_';
                    } else {
                        ($gallery == '_') ? $gallery = $attid : $gallery .= ',' . $attid;
                        update_post_meta($ID, '_product_image_gallery', $gallery);
                    }
                }

    }

    return $gallery;
}

// From URL on supplier site get images and long description
// Со страницы товара на сайте поставщика получаем изображения и длинное описание
function woo_bip_parsing( $ID, $url, $name )
{

    global $import;
    $descrptn = '';

    // Included parameters
    // Подключаем параметры
    include_once(WOO_BIP_PATH . 'include/config.php');
    // Included library
    // Подключаем библиотеку
    include_once(WOO_BIP_PATH . 'classes/simple_html_dom.php');
    // Take out the contents of the file
    // Вытаскиваем содержимое файла
        $html = file_get_html($url);
        // First photo
        // Первое фото
    if ($html !== false) {
        $gallery = '';
        foreach ($html->find('.active') as $list_img) {
            if (strstr($el->href, 'jpg')) {
                // выводим их значение атрибута href
                $gallery = woo_bip_set_image($ID, $el->href, $gallery);
                $import->log .= "<br />>>>>>> " . __('Getting Image file: ', 'woo_bip') . $el->href;
            }
        }

        // Another photo
        // Остальные фото
        foreach ($html->find('.photo') as $list_img) {
            foreach ($list_img->find('a') as $el) {
                if (strstr($el->href, 'jpg')) {
                    // выводим их значение атрибута href
                    $gallery = woo_bip_set_image($ID, $el->href, $gallery);
                    $import->log .= "<br />>>>>>> " . __('Getting Image file: ', 'woo_bip') . $el->href;
                }
            }
        }

        // Founded a long description
        // Находим длинное описание
        foreach ($html->find('.description') as $el) {
            foreach ($html->find('.description') as $el) {
                // Remove unnecessary phrases and symbols
                // Убираем ненужные фразы и символы
                $descrptn = sanitize_text_field($el->plaintext);
                for ($i = 0; $i < count($excludes); $i++)
                    $descrptn = str_replace($excludes[$i], ' ', $descrptn);
                $descrptn = str_replace($name, ' ', $descrptn);
            }

        }

        // Cleans up after itself
        // Подчищаем за собой
        $html->clear();
        unset($html);
    }

    // Returns a description of replacing the first letter in the title
    // Возвращаем описание, заменив первую букву на заглавную
    return ucfirst($descrptn);
}

// Prepare Product data for saving
// Подготавливаем данные товара для сохранения
    function woo_bip_prepare_product($count)
    {

        global $import, $product;

        $product = new stdClass;

        // Set up empty vars
        // Устанавливаем пустые переменные
        $product->deleted = false;
        $product->imported = false;
        $product->duplicate_exists = false;

        $rate = woo_bip_get_option('rate');
        $trade_margin = woo_bip_get_option('trade_margin');

        $product->ID = null;
        $product->sku = (isset($import->csv_sku) && isset($import->csv_sku[$count]) ? $import->csv_sku[$count] : null);
        woo_bip_duplicate_product_exists();
        $product->name = (isset($import->csv_name) && isset($import->csv_name[$count]) ? $import->csv_name[$count] : null);
        $product->supplier_price = (isset($import->csv_supplier_price) && isset($import->csv_supplier_price[$count]) ? woo_bip_is_valid_price($import->csv_supplier_price[$count]) : null);
        $product->price = round($product->supplier_price * $rate * (100 + $trade_margin) / 100, 2);
        $product->description = (isset($import->csv_description[$count]) ? html_entity_decode($import->csv_description[$count]) : null);
        $product->category = (isset($import->csv_category) && isset($import->csv_category[$count]) ? $import->csv_category[$count] : null);
        $product->brands = (isset($import->csv_brands) && isset($import->csv_brands[$count]) ? $import->csv_brands[$count] : null);
        $product->supplier_code = (isset($import->csv_supplier_code) && isset($import->csv_supplier_code[$count]) ? $import->csv_supplier_code[$count] : null);
        $product->product_url = (isset($import->csv_product_url) && isset($import->csv_product_url[$count]) ? $import->csv_product_url[$count] : null);
        $product->warranty = (isset($import->csv_warranty) && isset($import->csv_warranty[$count]) ? $import->csv_warranty[$count] : null);
        $product->tag = (isset($import->csv_category) && isset($import->csv_category[$count]) ? $import->csv_category[$count] . '|' . $import->csv_brands[$count] : null);

        foreach ($product as $key => $value) {
            if (!is_array($value) && $value !== null)
                $product->$key = woo_bip_encode_transient(trim($value));
        }

        if (isset($import->headers)) {
            foreach ($import->headers as $header) {
                if (isset($import->{'csv_' . $header})) {
                    if (isset($import->{'csv_' . $header}[$count]))
                        unset($import->{'csv_' . $header}[$count]);
                }
            }
        }

    }

// Check for duplicate Product
// Проверяем дублирование товара
    function woo_bip_duplicate_product_exists()
    {

        global $product;

        // Check for duplicate Product by ID if present
        // Проверяем дублирование товара по ID
        $post_type = array('product', 'product_variation');
        if ($product->ID !== null) {
            $args = array(
                'post_type' => $post_type,
                'post__in' => array($product->ID),
                'numberposts' => 1,
                'post_status' => 'any',
                'fields' => 'ids'
            );
            $products = new WP_Query($args);
            if (!empty($products->found_posts))
                $product->duplicate_exists = $product->ID;
            // Check for duplicate Product by SKU if present
            // Проверяем дублирование товара по партномеру
        } else if ($product->sku !== null) {
            $meta_key = '_sku';
            $args = array(
                'post_type' => $post_type,
                'meta_key' => $meta_key,
                'meta_value' => $product->sku,
                'numberposts' => 1,
                'post_status' => 'any',
                'fields' => 'ids'
            );
            $products = new WP_Query($args);
            if (!empty($products->found_posts))
                $product->duplicate_exists = $products->posts[0];
            unset($products);
        }

    }

// Validates the given Price value; Price, Sale Price, Shipping, etc.
// Проверяем полученные значения цены
    function woo_bip_is_valid_price($price = null)
    {

        $price = str_replace(',', '.', trim($price));
        return $price;

    }

// Product validation check of required columns.
// Проверка наличия требуемых полей товара
    function woo_bip_validate_product()
    {

        global $import, $product;

        $status = false;
        $product->fail_requirements = false;
        $has_id = (!empty($product->ID) ? true : false);
        $has_sku = (!empty($product->sku) ? true : false);
        $has_name = (!empty($product->name) ? true : false);
        $has_duplicate = (!empty($product->duplicate_exists) ? true : false);

        if ($import->import_method == 'new') {
            // Create new Product - Requires either Name or SKU and no existing Product
            // Создание нового товара - требуется если есть имя или партномер и товар не существует
            if ((!$has_name || !$has_sku) || $has_duplicate) {
                $status = true;
                if (($has_name && !$has_sku) || ($has_sku && !$has_name) && !$has_duplicate)
                    $status = false;
                if ($product->duplicate_exists)
                    $status = true;
            }
        }
        if ($status) {
            $import->fail_requirements = true;
            $failed_reason = array();

                if ($product->duplicate_exists)
                    $failed_reason[] = __('A duplicate SKU already exists.', 'woo_bip');

            if (empty($failed_reason))
                $failed_reason[] = __('No specific reason was given, raise this as a Support issue.', 'woo_bip');
            $product->failed_reason = $failed_reason[0];
            $import->failed_products[] = array(
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price,
                'category' => $product->category,
                'reason' => $failed_reason
            );
            return true;
        }

    }

// Calculate product count
// Подсчет количества товаров
    function woo_bip_return_product_count()
    {

        $post_type = 'product';
        $count = 0;
        if ($statuses = wp_count_posts($post_type)) {
            foreach ($statuses as $key => $status) {
                // Ignore draft
                // Пропускаем черновики
                if (!in_array($key, array('auto-draft')))
                    $count = $count + $status;
            }
        }
        return $count;

    }

// Create Product
// Создаем запись товара
    function woo_bip_create_product()
    {

        global $wpdb, $product, $import, $user_ID;

            $post_type = 'product';
            $post_data = array(
                'post_author' => $user_ID,
                'post_date' => current_time('mysql'),
                'post_date_gmt' => current_time('mysql', 1),
                'post_title' => (!is_null($product->name) ? $product->name : ''),
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => $post_type,
                'post_content' => '',
                'post_excerpt' => (!is_null($product->description) ? $product->description : ''),
                'tax_input' => array(
                    'product_type' => 'simple'
                )
            );

        if( !$product->duplicate_exists )
            $product->ID = wp_insert_post($post_data, true);
        else {
            $product->ID = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_sku' AND meta_value = '" . $product->sku . "'");
            $wpdb->flush();
        }

        if (is_wp_error($product->ID) !== true) {

            if( !$product->duplicate_exists )
                woo_bip_create_product_defaults();

            woo_bip_create_product_details();

            if (function_exists('wc_delete_product_transients'))
                wc_delete_product_transients($product->ID);

            $import->products_added++;
            $product->imported = true;

        } else {

            ob_start();
            var_dump($post_data);
            $output = ob_get_contents();
            ob_end_clean();
            error_log(sprintf(__('Could not save the Product, $post_data: %s | $product: %s', 'woo_bip'), $output, print_r($product, true)));

            if ($errors = $product->ID->get_error_messages()) {
                foreach ($errors as $error)
                    $product->failed_reason = $error;
            }
            $import->products_failed++;

        }

    }

// Set Product default values
// Задаем значения полей товара по умолчанию
    function woo_bip_create_product_defaults()
    {

        global $product;

        $defaults = array(
            '_regular_price' => 0,
            '_price' => '',
            '_sku' => '',
            '_stock_status' => 'instock',
            '_visibility' => 'visible',
            '_featured' => 'no',
            '_downloadable' => 'no',
            '_virtual' => 'no',
            '_product_attributes' => array(),
            '_manage_stock' => 'no',
            '_backorders' => 'no',
            '_stock' => '',
            'supplier_code' => '',
            'updated' => 'no',
            'supplier_price' => 0,
            'warranty' => ''
        );
        if ($defaults = apply_filters('woo_bip_create_product_defaults', $defaults, $product->ID)) {
            if (WOO_BIP_DEBUG !== true) {
                foreach ($defaults as $key => $default)
                    update_post_meta($product->ID, $key, $default);
            }
        }

    }

// Create Product details fields
// Создаем дополнительные поля товара
    function woo_bip_create_product_details()
    {

        global $wpdb, $product, $import, $user_ID;

// Insert Supplier Price
// Добавляем цену поставщика
        if ($product->supplier_price !== null) {
            if (WOO_BIP_DEBUG !== true) {
                update_post_meta($product->ID, 'supplier_price', $product->supplier_price);
                update_post_meta($product->ID, 'updated', 'yes');
            }
            if ($import->advanced_log)
                $import->log .= "<br />>>>>>> " . sprintf(__('Setting Supplier Price: %s', 'woo_bip'), $product->supplier_price);
            else
                $import->log .= "<br />>>>>>> " . __('Setting Supplier Price', 'woo_bip');
        } else if ($import->advanced_log) {
            $import->log .= "<br />>>>>>> " . __('Skipping Supplier Price', 'woo_bip');
        }

        if ($import->only_price == 0 || !$product->duplicate_exists ) {

            // Insert SKU
            // Добавляем партномер
            if ($product->sku !== null) {
                if (WOO_BIP_DEBUG !== true)
                    update_post_meta($product->ID, '_sku', $product->sku);
                if ($import->advanced_log)
                    $import->log .= "<br />>>>>>> " . sprintf(__('Setting SKU: %s', 'woo_bip'), $product->sku);
                else
                    $import->log .= "<br />>>>>>> " . __('Setting SKU', 'woo_bip');
            } else if ($import->advanced_log) {
                $import->log .= "<br />>>>>>> " . __('Skipping SKU', 'woo_bip');
            }

            // Insert Price
            // Добавляем цену
            if ($product->price !== null) {
                if (WOO_BIP_DEBUG !== true) {
                    update_post_meta($product->ID, '_regular_price', $product->price);
                    update_post_meta($product->ID, '_price', $product->price);
                }
                if ($import->advanced_log)
                    $import->log .= "<br />>>>>>> " . sprintf(__('Setting Price: %s', 'woo_bip'), $product->price);
                else
                    $import->log .= "<br />>>>>>> " . __('Setting Price', 'woo_bip');
            } else if ($import->advanced_log) {
                $import->log .= "<br />>>>>>> " . __('Skipping Price', 'woo_bip');
            }

            // Insert Supplier Code
            // Добавляем код поставщика
            if ($product->supplier_code !== null) {
                if (WOO_BIP_DEBUG !== true) {
                    update_post_meta($product->ID, 'supplier_code', $product->supplier_code);
                }
                if ($import->advanced_log)
                    $import->log .= "<br />>>>>>> " . sprintf(__('Setting Supplier Code: %s', 'woo_bip'), $product->supplier_code);
                else
                    $import->log .= "<br />>>>>>> " . __('Setting Supplier Code', 'woo_bip');
            } else if ($import->advanced_log) {
                $import->log .= "<br />>>>>>> " . __('Skipping Supplier Code', 'woo_bip');
            }

            // Insert Warranty
            // Добавляем гарантию
            if ($product->warranty !== null) {
                if (WOO_BIP_DEBUG !== true) {
                    update_post_meta($product->ID, 'warranty', $product->warranty);
                }
                if ($import->advanced_log)
                    $import->log .= "<br />>>>>>> " . sprintf(__('Setting Warranty: %s', 'woo_bip'), $product->warranty);
                else
                    $import->log .= "<br />>>>>>> " . __('Setting Warranty', 'woo_bip');
            } else if ($import->advanced_log) {
                $import->log .= "<br />>>>>>> " . __('Skipping Warranty', 'woo_bip');
            }

            // Insert Images and Long Description from Product URL
            // Добавляем изображения и длинное описание со ссылки товара с сайта поставщика
            if ($import->parsing_data == 1 || !$product->duplicate_exists ) {
                if ($product->product_url !== null) {
                    if (WOO_BIP_DEBUG !== true) {
                        $descrpton = woo_bip_parsing($product->ID, $product->product_url, $product->name);
                        $my_post = array(
                            'ID' => $product->ID,
                            'post_content' => $descrpton
                        );
                        // Update the post into the database
                        wp_update_post($my_post);
                    }
                    if ($import->advanced_log)
                        $import->log .= "<br />>>>>>> " . sprintf(__('Setting Images and Long Description from: %s', 'woo_bip'), $product->product_url);
                    else
                        $import->log .= "<br />>>>>>> " . __('Setting Images and Long Description', 'woo_bip');
                } else if ($import->advanced_log) {
                    $import->log .= "<br />>>>>>> " . __('Skipping Images and Long Description', 'woo_bip');
                }
            }

            // Update stock status
            // Обновляем наличие на складе
            update_post_meta($product->ID, '_stock_status', 'instock');

            // Insert Brands
            // Добавляем производителя
            $term_taxonomy = 'brands';
            if (!empty($product->brands_term_id) && ($import->brands_taxonomies == 1) ) {
                $term_taxonomy_ids = wp_set_object_terms($product->ID, array_unique(array_map('intval', $product->brands_term_id)), $term_taxonomy);
            }

            if( ($import->brands_attributes == 1) ) {
                    $attributes = array();
                    $attributes['pa_brands'] = array(
                        'name' => htmlspecialchars(stripslashes('pa_brands')),
                        'value' => '',
                        'position' => 0,
                        'is_visible' => 1,
                        'is_variation' => 0,
                        'is_taxonomy' => 1
                    );

                    wp_set_object_terms($product->ID, $product->brands, 'pa_brands');
                    update_post_meta($product->ID, '_product_attributes', $attributes);
                }

                if ($import->advanced_log) {
                    if (count($product->brands_term_id) == 1)
                        $import->log .= "<br />>>>>>> " . sprintf(__('Linking Brands: %s', 'woo_bip'), $product->brands);
                    else
                        $import->log .= "<br />>>>>>> " . sprintf(__('Linking Brands: %s', 'woo_bip'), $product->brands);
                } else {
                    if (count($product->brands_term_id) == 1)
                        $import->log .= "<br />>>>>>> " . __('Linking Brands', 'woo_bip');
                    else
                        $import->log .= "<br />>>>>>> " . __('Linking Brands', 'woo_bip');
                }

            // Insert Category
            // Добавляем категорию
            $term_taxonomy = 'product_cat';
            if (!empty($product->category_term_id)) {
                $term_taxonomy_ids = wp_set_object_terms($product->ID, array_unique(array_map('intval', $product->category_term_id)), $term_taxonomy);
                if ($import->advanced_log) {
                    if (count($product->category_term_id) == 1)
                        $import->log .= "<br />>>>>>> " . sprintf(__('Linking Category: %s', 'woo_bip'), $product->category);
                    else
                        $import->log .= "<br />>>>>>> " . sprintf(__('Linking Categories: %s', 'woo_bip'), $product->category);
                } else {
                    if (count($product->category_term_id) == 1)
                        $import->log .= "<br />>>>>>> " . __('Linking Category', 'woo_bip');
                    else
                        $import->log .= "<br />>>>>>> " . __('Linking Categories', 'woo_bip');
                }
            } else if ($import->advanced_log) {
                $import->log .= "<br />>>>>>> " . __('Skipping Category', 'woo_bip');
            }

            // Insert Tag
            // Добавляем метку
            $term_taxonomy = 'product_tag';
            if (!empty($product->tag_term_id)) {
                $term_taxonomy_ids = wp_set_object_terms($product->ID, array_unique(array_map('intval', $product->tag_term_id)), $term_taxonomy);
                if ($import->advanced_log) {
                    if (count($product->tag_term_id) == 1)
                        $import->log .= "<br />>>>>>> " . sprintf(__('Linking Tag: %s', 'woo_bip'), $product->tag);
                    else
                        $import->log .= "<br />>>>>>> " . sprintf(__('Linking Tags: %s', 'woo_bip'), $product->tag);
                } else {
                    if (count($product->tag_term_id) == 1)
                        $import->log .= "<br />>>>>>> " . __('Linking Tag', 'woo_bip');
                    else
                        $import->log .= "<br />>>>>>> " . __('Linking Tags', 'woo_bip');
                }
            } else if ($import->advanced_log) {
                $import->log .= "<br />>>>>>> " . __('Skipping Tag', 'woo_bip');
            }

        }
    }
?>
