<?php
/*
Plugin Name: WooCommerce - Brain Import Price
Plugin URI: https://github.com/Barmaley74/Brain-Import-Price
Description: Import new Products into your WooCommerce store from Brain price.
Version: 1.1.0
Author: Serhiy Vlasevych
Author URI: http://www.neo.poltava.ua/
License: GPL2
*/

// Exit if accessed directly
// Если доступ напрямую - выходим
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WOO_BIP_FILE', __FILE__ );
define( 'WOO_BIP_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'WOO_BIP_RELPATH', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'WOO_BIP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_BIP_PREFIX', 'woo_bip' );
define( 'WOO_BIP_PLUGINPATH', WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) );

// Turn this on to enable additional debugging options within the importer
// Включаем опцию если необходима дополнительная информация в процессе отладки
define( 'WOO_BIP_DEBUG', false );

include_once( WOO_BIP_PATH . 'functions/common.php' );
include_once( WOO_BIP_PATH . 'functions/functions.php' );
include_once( WOO_BIP_PATH . 'classes/wcm_brands.php' );

// Register localization for Plugin
// Регистрация плагина для локализации
function woo_bip_i18n() {
	load_plugin_textdomain( 'woo_bip', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'woo_bip_i18n' );

if( is_admin() ) {

	/* Start of: WordPress Administration */
	/* Начало Админ панели */

	// Register Plugin in the list of available WordPress import tools
	// Регистрируем плагин в списке доступных инстурментов импорта WordPress
	function woo_bip_register_importer() {
		register_importer( 'woo_bip', __( 'Products', 'woo_bip' ), __( '<strong>Brain Import Price</strong> - Import Products into WooCommerce from a price CSV file.', 'woo_bip' ), 'woo_bip_html_page' );
	}
	add_action( 'admin_init', 'woo_bip_register_importer' );

	// Initial scripts and import process
	// Инициализируем скрипты и процесс импорта
	function woo_bip_admin_init() {

		if( isset( $_GET['import'] ) || isset( $_GET['page'] ) ) {
			if( isset( $_GET['import'] ) ) {
				if( $_GET['import'] == WOO_BIP_PREFIX )
					$brain_import_price = true;
			}
			if( isset( $_GET['page'] ) ) {
				if( $_GET['page'] == WOO_BIP_PREFIX )
					$brain_import_price = true;
			}
		}
		if( isset( $brain_import_price ) ) {

			// Process any pre-import notice confirmations
			// Процесс подтверждения предварительных уведомлений
			$action = woo_get_action();
			switch( $action ) {

				// Prompt on Import screen when memory cannot be increased
				// Сообщение на экране Импорта когда объем памяти не может быть увеличен
				case 'dismiss-memory':
					woo_bip_update_option( 'memory_notice', 1 );
					$url = add_query_arg( 'action', null );
					wp_redirect( $url );
					exit();
					break;

				// Prompt on Import screen when insufficient memory (less than 64M is allocated)
				// Сообщение на экране Импорта когда объема памяти не хватает (доступно меньше чем 64Мб)
				case 'dismiss-minimum-memory':
					woo_bip_update_option( 'minimum_memory_notice', 1 );
					$url = add_query_arg( 'action', null );
					wp_redirect( $url );
					exit();
					break;

				// Prompt on Import screen when PHP Safe Mode is detected
				// Сообщение на экране Импорта когда когда обнаружен безопасный режим PHP
				case 'dismiss-safe_mode':
					woo_bip_update_option( 'safe_mode_notice', 1 );
					$url = add_query_arg( 'action', null );
					wp_redirect( $url );
					exit();
					break;

				// Prompt on Import screen when mb_convert() is not available
				// Сообщение на экране Импорта когда не доступна функция mb_convert()
				case 'dismiss-mb_convert':
					woo_bip_update_option( 'mb_convert_notice', 1 );
					$url = add_query_arg( 'action', null );
					wp_redirect( $url );
					exit();
					break;

				// Prompt on Import screen when mb_list() is not available
				// Сообщение на экране Импорта когда не доступна функция mb_list()
				case 'dismiss-mb_list':
					woo_bip_update_option( 'mb_list_notice', 1 );
					$url = add_query_arg( 'action', null );
					wp_redirect( $url );
					exit();
					break;

				// Prompt on Import screen when str_getcsv() is not available
				// Сообщение на экране Импорта когда не доступна функция str_getcsv()
				case 'dismiss-str_getcsv':
					woo_bip_update_option( 'str_getcsv_notice', 1 );
					$url = add_query_arg( 'action', null );
					wp_redirect( $url );
					exit();
					break;

			}

			@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
			woo_bip_import_init();

		}

	}
	add_action( 'admin_init', 'woo_bip_admin_init' );

	// HTML templates and form processor for Brain Import Price screen
	// Шаблоны HTML для экрана Brain Import Price
	function woo_bip_html_page() {

		global $import;

		$action = woo_get_action();
		$title = __( 'Brain Import Price', 'woo_bip' );
		if( in_array( $action, array( 'upload', 'save' ) ) && !$import->cancel_import ) {
			if( $file = woo_bip_get_option( 'csv' ) )
				$title .= ': <em>' . basename( $file ) . '</em>';
		}
		$troubleshooting_url = 'http://www.neo.poltava.ua/';

		woo_bip_template_header( $title );
		switch( $action ) {

			case 'upload':

				if( isset( $import->file ) )
					$file = $import->file;
				else
					$file = array(
						'size' => 0
					);

				// Display the opening Import tab if the import fails
				// Показываем открытой закладку Импорт если предыдущий импорт не удался
				if( $import->cancel_import ) {
					woo_bip_manage_form();
					return;
				}

				$upload_dir = wp_upload_dir();
				if( $file ) {

					woo_bip_prepare_data();
					$i = 0;
					$products = woo_bip_return_product_count();
					// Override the default import method if no Products exist
					// Переписываем метод импорта по умолчанию если товаров не существует
					if( !$products )
						$import->import_method = 'new';
					$import->options = woo_bip_product_fields();
					$import->options_size = count( $import->options );
					$first_row = array();
					$second_row = array();
					if( isset( $import->lines ) ) {
						// Detect character encoding and compare to selected file encoding
						// Определяем кодировку символов и сравниваем с кодировкой в выбраном файле
						$auto_encoding = mb_detect_encoding( $import->raw );
						if( $auto_encoding !== false ) {
							if( strtolower( $auto_encoding ) <> strtolower( $import->encoding ) ) {
								woo_bip_update_option( 'encoding', $auto_encoding );
								$message = sprintf( __( 'It seems the character encoding provided under General Settings on the Settings tab - <code>%s</code> - didn\'t match this import file so we automatically detected the character encoding for you to <code>%s</code>.', 'woo_bip' ), $import->encoding, $auto_encoding );
								// Force the message to the screen as we are post-init
								// Принудительно сообщение на экран, после инициализации
								woo_bip_admin_notice_html( $message );
							}
						}
						$first_row = str_getcsv( $import->lines[0], $import->delimiter );
						$import->columns = count( $first_row );
						// If we only detect a single column then the delimiter may be wrong
						// Если определяем одну колонку значит разделитель неправильный
						if( $import->columns == 1 ) {
							$auto_delimiter = woo_bip_detect_file_delimiter( (string)$first_row[0] );
							if( $import->delimiter <> $auto_delimiter ) {
								$import->delimiter = $auto_delimiter;
								$first_row = str_getcsv( $import->lines[0], $import->delimiter );
								$import->columns = count( $first_row );
								// If the column count is unchanged then the CSV either has only a single column (which won't work) or we've failed our job
								// Если количество колонок остается неизменным, то CSV или имеет только один столбец (который не будет работать) или мы не смогли сделать нашу работу
								$priority = 'updated';
								if( $import->columns > 1 ) {
									$message = sprintf( __( 'It seems the field delimiter provided under Import Options - <code>%s</code> - didn\'t match this CSV so we automatically detected the CSV delimiter for you to <code>%s</code>.', 'woo_bip' ), woo_bip_get_option( 'delimiter', ',' ), $auto_delimiter );
									woo_bip_update_option( 'delimiter', $import->delimiter );
								} else {
									$priority = 'error';
									$message = __( 'It seems either this CSV has only a single column or we were unable to automatically detect the CSV delimiter.', 'woo_bip' ) . ' <a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>';
								}
								// Force the message to the screen as we are post-init
								// Принудительно сообщение на экран, после инициализации
								woo_bip_admin_notice_html( $message, $priority );
							}
							unset( $auto_delimiter );
						}
						$second_row = str_getcsv( $import->lines[1], $import->delimiter );
						unset( $import->lines );
					}
					foreach( $first_row as $key => $cell ) {
						for( $k = 0; $k < $import->options_size; $k++ ) {
							if( woo_bip_format_column( $import->options[$k]['label'] ) == woo_bip_format_column( $cell ) ) {
								$import->skip_first = 1;
								break;
							}
						}
						if( !isset( $second_row[$key] ) )
							$second_row[$key] = '';
					}
					include_once( WOO_BIP_PATH . 'templates/import_upload.php' );

				}
				break;

			case 'save':
				// Display the opening Import tab if the import fails
				// Показываем открытой закладку Импорт если предыдущий импорт не удался
				if( $import->cancel_import == false ) {
					include_once( WOO_BIP_PATH . 'templates/import_save.php' );
				} else {
					woo_bip_manage_form();
					return;
				}
				break;

			default:
				woo_bip_manage_form();
				break;

		}
		woo_bip_template_footer();

	}

	// HTML template for Import screen
	// HTML шаблон для экрана импорта
	function woo_bip_manage_form() {

		$tab = false;
		if( isset( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( $_GET['tab'] );
		} else if( woo_bip_get_option( 'skip_overview', false ) ) {
			$tab = 'import';
		}
		$url = add_query_arg( 'page', 'woo_bip' );

		include_once( WOO_BIP_PATH . 'templates/tabs.php' );

	}

	/* End of: WordPress Administration */
	/* Окончание Админ панели */

}
?>