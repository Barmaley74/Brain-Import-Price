<?php
// Display admin notice on screen load
// Показываем административное уведомление при загрузке экрана
function woo_bip_admin_notice( $message = '', $priority = 'updated', $screen = '' ) {

	if( $priority == false || $priority == '' )
		$priority = 'updated';
	if( $message <> '' ) {
		ob_start();
		woo_bip_admin_notice_html( $message, $priority, $screen );
		$output = ob_get_contents();
		ob_end_clean();
		// Check if an existing notice is already in queue
		$existing_notice = get_transient( WOO_BIP_PREFIX . '_notice' );
		if( $existing_notice !== false ) {
			$existing_notice = base64_decode( $existing_notice );
			$output = $existing_notice . $output;
		}
		$response = set_transient( WOO_BIP_PREFIX . '_notice', base64_encode( $output ), MINUTE_IN_SECONDS );
		// Check if the Transient was saved
		if( $response !== false )
			add_action( 'admin_notices', 'woo_bip_admin_notice_print' );
	}

}

// HTML template for admin notice
// Шаблон HTML для административного экрана
function woo_bip_admin_notice_html( $message = '', $priority = 'updated', $screen = '' ) {

	// Display admin notice on specific screen
    // Показываем административное уведомление на указанном экране
	if( !empty( $screen ) ) {

		global $pagenow;

		if( is_array( $screen ) ) {
			if( in_array( $pagenow, $screen ) == false )
				return;
		} else {
			if( $pagenow <> $screen )
				return;
		}

	} ?>
<div id="message" class="<?php echo $priority; ?>">
	<p><?php echo $message; ?></p>
</div>
<?php

}

// Grabs the WordPress transient that holds the admin notice and prints it
// Перехватываем временные значения WordPress, которые содержат админ уведомления
function woo_bip_admin_notice_print() {

	$output = get_transient( WOO_BIP_PREFIX . '_notice' );
	if( $output !== false ) {
		delete_transient( WOO_BIP_PREFIX . '_notice' );
		$output = base64_decode( $output );
		echo $output;
	}

}

// HTML template header on Brain Import Price screen
// Шаблон заголовка для Импорта прайса Брейн
function woo_bip_template_header( $title = '', $icon = 'woocommerce' ) { ?>
<div id="woo-pi" class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-woocommerce-importer"><br /></div>
	<h2><?php echo $title; ?></h2>
<?php

}

// HTML template footer on Brain Import Price screen
// Шаблон подвала для Импорта прайса Брейн
function woo_bip_template_footer() { ?>
</div>
<!-- .wrap -->
<?php

}

// Add Brain Import Price to WordPress Administration menu
// Добавляем пункт Импорт прайса Брейн в Админ меню
function woo_bip_admin_menu() {

	$page = add_submenu_page( 'woocommerce', __( 'Brain Import Price', 'woo_bip' ), __( 'Brain Import Price', 'woo_bip' ), 'manage_woocommerce', 'woo_bip', 'woo_bip_html_page' );
	add_action( 'admin_print_styles-' . $page, 'woo_bip_enqueue_scripts' );

}
add_action( 'admin_menu', 'woo_bip_admin_menu', 11 );

// Load CSS and jQuery scripts for Brain Import Price screen
// Загружаем таблицу стилей и скрипты для экрана
function woo_bip_enqueue_scripts( $hook ) {

	// Simple check that WooCommerce is activated
    // проверяем активирован ли WooCommerce
	if( class_exists( 'WooCommerce' ) ) {

		global $woocommerce;

		// Load WooCommerce default Admin styling
        // Загружаем стиль WooCommerce по умолчанию
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );

	}

	// Common
    // Общее
	wp_enqueue_style( 'woo_bip_styles', plugins_url( '/templates/import.css', WOO_BIP_RELPATH ) );
	wp_enqueue_script( 'woo_bip_scripts', plugins_url( '/js/import.js', WOO_BIP_RELPATH ), array( 'jquery' ) );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'jquery-toggleblock', plugins_url( '/js/toggleblock.js', WOO_BIP_RELPATH ), array( 'jquery' ) );
	wp_enqueue_style( 'woo_vm_styles', plugins_url( '/templates/admin.css', WOO_BIP_RELPATH ) );

}

// HTML active class for the currently selected tab on the Brain Import Price screen
// HTML класс для текущей выбранной закладки на экране Импорт прайса Брейн
function woo_bip_admin_active_tab( $tab_name = null, $tab = null ) {

	if( isset( $_GET['tab'] ) && !$tab )
		$tab = $_GET['tab'];
	else if( !isset( $_GET['tab'] ) && woo_bip_get_option( 'skip_overview', false ) )
		$tab = 'import';
	else
		$tab = 'overview';

	$output = '';
	if( isset( $tab_name ) && $tab_name ) {
		if( $tab_name == $tab )
			$output = ' nav-tab-active';
	}
	echo $output;

}

// HTML template for each tab on the Brain Import Price screen
// HTML шаблон для каждой закладки на экране Импорт прайса Брейн
function woo_bip_tab_template( $tab = '' ) {

	global $import;

	if( !$tab )
		$tab = 'overview';

	$troubleshooting_url = 'http://www.neo.poltava.ua/';

	switch( $tab ) {

		case 'overview':
			$skip_overview = woo_bip_get_option( 'skip_overview', false );
			break;

		case 'import':

			$upload_dir = wp_upload_dir();
			$max_upload = (int)( ini_get( 'upload_max_filesize' ) );
			$max_post = (int)( ini_get( 'post_max_size' ) );
			$memory_limit = (int)( ini_get( 'memory_limit' ) );
			$wp_upload_limit = round( wp_max_upload_size() / 1024 / 1024, 2 );
			$upload_mb = min( $max_upload, $max_post, $memory_limit, $wp_upload_limit );
			$file_path = $upload_dir['basedir'] . '/';
			$file_path_relative = 'imports/store-a.csv';
			$file_url = 'http://www.domain.com/wp-content/uploads/imports/store-a.jpg';
			$file_ftp_host = 'ftp.domain.com';
			$file_ftp_user = 'user';
			$file_ftp_pass = 'password';
			$file_ftp_port = '';
			$file_ftp_path = 'wp-content/uploads/imports/store-a.jpg';
			$file_ftp_timeout = '';
			if( isset( $_POST['csv_file_path'] ) )
				$file_path_relative = $_POST['csv_file_path'];
			$modules = woo_bip_modules_list();

			if( isset( $_GET['import'] ) && $_GET['import'] == WOO_BIP_PREFIX )
				$url = 'import';
			if( isset( $_GET['page'] ) && $_GET['page'] == WOO_BIP_PREFIX )
				$url = 'page';
			break;

		case 'settings':
			$delete_file = woo_bip_get_option( 'delete_file', 0 );
			$timeout = woo_bip_get_option( 'timeout', 0 );
			$encoding = woo_bip_get_option( 'encoding', 'UTF-8' );
			$delimiter = woo_bip_get_option( 'delimiter', ',' );
			$file_encodings = ( function_exists( 'mb_list_encodings' ) ? mb_list_encodings() : false );
			break;

        case 'price':
            $rate = woo_bip_get_option( 'rate' );
            $trade_margin = woo_bip_get_option( 'trade_margin' );
            $enable_discount = woo_bip_get_option( 'enable_discount' );
            $count_discount = woo_bip_get_option( 'count_discount' );
            $discount_margin = woo_bip_get_option( 'discount_margin' );
            break;

    }
	if( $tab ) {
		if( file_exists( WOO_BIP_PATH . 'templates/tabs-' . $tab . '.php' ) ) {
			include_once( WOO_BIP_PATH . 'templates/tabs-' . $tab . '.php' );
		} else {
			$message = sprintf( __( 'We couldn\'t load the export template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woo_bip' ), 'tabs-' . $tab . '.php', WOO_BIP_PATH . 'templates/...' );
			woo_bip_admin_notice_html( $message, 'error' );
			ob_start(); ?>
            <p><?php _e( 'You can see this error for one of a few common reasons', 'woo_bip' ); ?>:</p>
            <ul class="ul-disc">
	            <li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woo_bip' ); ?></li>
	            <li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woo_bip' ); ?></li>
	            <li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woo_bip' ); ?></li>
            </ul>
            <p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woo_bip' ); ?></p>
            <?php
			ob_end_flush();
		}
	}

}

// Returns a list of WordPress Plugins that Brain Import Price integrates with
// Возвращает список плагинов WordPress с которыми интегрирован Импорт прайса Брейн
function woo_bip_modules_list( $modules = array() ) {

	$modules[] = array(
		'name' => 'aioseop',
		'title' => __( 'All in One SEO Pack', 'woo_bip' ),
		'description' => __( 'Optimize your WooCommerce Products for Search Engines', 'woo_bip' ),
		'url' => 'http://wordpress.org/extend/plugins/all-in-one-seo-pack/',
		'slug' => 'all-in-one-seo-pack',
		'function' => 'aioseop_activate'
	);

	$modules = apply_filters( 'woo_bip_modules_addons', $modules );

	if( !empty( $modules ) ) {
		foreach( $modules as $key => $module ) {
			$modules[$key]['status'] = 'inactive';
			// Check if each module is activated
            // Проверяем, активирован ли каждый модуль
			if( isset( $module['function'] ) ) {
				if( function_exists( $module['function'] ) )
					$modules[$key]['status'] = 'active';
			}
			// Check if the current user can install Plugins
            // Проверяем, если текщий пользователь может установить плагин
			if( current_user_can( 'install_plugins' ) && isset( $module['slug'] ) )
				$modules[$key]['url'] = admin_url( sprintf( 'plugin-install.php?tab=search&type=tag&s=%s', $module['slug'] ) );
		}
	}
	return $modules;

}

function woo_bip_modules_status_class( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = 'green';
			break;

		case 'inactive':
			$output = 'yellow';
			break;

	}
	echo $output;

}

function woo_bip_modules_status_label( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = __( 'OK', 'woo_bip' );
			break;

		case 'inactive':
			$output = __( 'Install', 'woo_bip' );
			break;

	}
	echo $output;

}

// Saves the current CSV file to the Past Imports list for future use
// Сохраняем текущий файл CSV для быстрого импорта в будущем использовании
function woo_bip_add_past_import( $file = '' ) {

	global $import;

	$upload_dir = wp_upload_dir();
	if( !empty( $file ) ) {
		if( file_exists( $file ) ) {
			if( $past_imports = woo_bip_get_option( 'past_imports' ) )
				$past_imports = maybe_unserialize( $past_imports );
			else
				$past_imports = array();
			if( is_array( $past_imports ) && !woo_bip_array_search( $past_imports, 'filename', $file ) ) {
				$past_imports[] = array( 'filename' => $file, 'date' => current_time( 'mysql' ) );
				woo_bip_update_option( 'past_imports', $past_imports );
				if( $import->advanced_log )
					$import->log .= "<br /><br />" . sprintf( __( 'Added %s to Past Imports', 'woo_bip' ), basename( $file ) );
			} else {
				if( $import->advanced_log )
					$import->log .= "<br /><br />" . sprintf( __( '%s already appears in Past Imports', 'woo_bip' ), basename( $file ) );
			}
		}
	}

}

?>