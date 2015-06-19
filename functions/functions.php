<?php
include_once( WOO_BIP_PATH . 'functions/product.php' );
include_once( WOO_BIP_PATH . 'functions/brands.php' );
include_once( WOO_BIP_PATH . 'functions/category.php' );
include_once( WOO_BIP_PATH . 'functions/tag.php' );

if( is_admin() ) {

	/* Start of: WordPress Administration */
	/* Начало Административной панели */

	include_once( WOO_BIP_PATH . 'functions/admin.php' );

    // Init import process
    // Инициализация процесса импорта
	function woo_bip_import_init() {

		global $import, $wpdb, $woocommerce;

		$troubleshooting_url = 'http://www.neo.poltava.ua/';

		// Notice that we cannot increase memory limits
		// Сообщение что мы не можем увеличить лимит памяти
		if( !ini_get( 'memory_limit' ) && !woo_bip_get_option( 'memory_notice', false ) ) {
			$message = sprintf( __( 'Your WordPress site does not allow changes to allocated memory limits, because of this memory-related errors are likely. %s', 'woo_bip' ), '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
			$dismiss_url = add_query_arg( array( 'page' => 'woo_bip', 'action' => 'dismiss-memory' ), 'admin.php' );
			$dismiss_link = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_bip' ) . '</a></span>';
			woo_bip_admin_notice( $message . $dismiss_link, 'error' );
		}

		// Notice to increase WordPress memory allocation
		// Сообщение что нужно увеличить объем памяти для WordPress
		if( !woo_bip_get_option( 'minimum_memory_notice', 0 ) ) {
			$memory_limit = (int)( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				$memory_url = add_query_arg( array( 'page' => 'woo_bip', 'action' => 'dismiss-minimum-memory' ), 'admin.php' );
				$message = sprintf( __( 'We recommend setting memory to at least %dMB prior to importing, your site has only %dMB allocated to it. See <a href="%s" target="_blank">Increasing memory allocated to PHP</a> for more information.<span style="float:right;"><a href="%s">Dismiss</a></span>', 'woo_bip' ), $minimum_memory_limit, $memory_limit, $troubleshooting_url, $memory_url );
				woo_bip_admin_notice( $message, 'error' );
			}
		}

		// Notice that PHP safe mode is active
		// Сообщение , что запущен безопасный режим PHP
		if( ini_get( 'safe_mode' ) && !woo_bip_get_option( 'safe_mode_notice', false ) ) {
			$message = sprintf( __( 'Your WordPress site appears to be running PHP in \'Safe Mode\', because of this the script timeout cannot be adjusted. This will limit the importing of large catalogues. %s', 'woo_bip' ), '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
			$dismiss_url = add_query_arg( array( 'page' => 'woo_bip', 'action' => 'dismiss-safe_mode' ), 'admin.php' );
			$dismiss_link = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_bip' ) . '</a></span>';
			woo_bip_admin_notice( $message . $dismiss_link, 'error' );
		}

		// Notice that mb_convert_encoding() does not exist
		// Сообщение, что недоступна функция mb_convert_encoding()
		if( !function_exists( 'mb_convert_encoding' ) && !woo_bip_get_option( 'mb_convert_notice', false ) ) {
			$message = sprintf( __( 'The function mb_convert_encoding() requires the mb_strings extension to be enabled, multi-lingual import support has been disabled. %s', 'woo_bip' ), '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
			$dismiss_url = add_query_arg( array( 'page' => 'woo_bip', 'action' => 'dismiss-mb_convert' ), 'admin.php' );
			$dismiss_link = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_bip' ) . '</a></span>';
			woo_bip_admin_notice( $message . $dismiss_link, 'error' );
		}

		// Notice that mb_list_encodings() does not exist
		// Сообщение, что недоступна функция mb_list_encodings()
		if( !function_exists( 'mb_list_encodings' ) && !woo_bip_get_option( 'mb_list_notice', false ) ) {
			$message = sprintf( __( 'The function mb_list_encodings() requires the mb_strings extension to be enabled, if you are importing non-English and/or special characters the WordPress Transients we use during import will be corrupted and cause the import to fail.', 'woo_bip' ), '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
			$dismiss_url = add_query_arg( array( 'page' => 'woo_bip', 'action' => 'dismiss-mb_list' ), 'admin.php' );
			$dismiss_link = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_bip' ) . '</a></span>';
			woo_bip_admin_notice( $message . $dismiss_link, 'error' );
		}

		// Notice that PHP version does not include required dismiss-str_getcsv()
		// Сообщение, что версия PHP старая и недоступна функция dismiss-str_getcsv()
		if( phpversion() < '5.3.0' && !woo_bip_get_option( 'str_getcsv_notice', false ) ) {
			$message = sprintf( __( 'Your WordPress site is running an older version of PHP which does not support the function str_getcsv(), a substitute will be used. %s', 'woo_bip' ), '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
			$dismiss_url = add_query_arg( array( 'page' => 'woo_bip', 'action' => 'dismiss-str_getcsv' ), 'admin.php' );
			$dismiss_link = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_bip' ) . '</a></span>';
			woo_bip_admin_notice( $message . $dismiss_link, 'error' );
		}

		$wpdb->hide_errors();
		@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

		// Prevent header sent errors for the import
		// Убираем заголовок с ошибками импорта
		@ob_start();

		$action = woo_get_action();
		switch( $action ) {

			// Save changes on Settings screen
			// Сохраняем значения экрана Настройки
			case 'save-settings':
				woo_bip_update_option( 'delete_file', ( isset( $_POST['delete_file'] ) ? absint( $_POST['delete_file'] ) : 0 ) );
				woo_bip_update_option( 'encoding', ( isset( $_POST['encoding'] ) ? sanitize_text_field( $_POST['encoding'] ) : 'UTF-8' ) );
				woo_bip_update_option( 'timeout', ( isset( $_POST['timeout'] ) ? absint( $_POST['timeout'] ) : 0 ) );
				woo_bip_update_option( 'delimiter', ( isset( $_POST['delimiter'] ) ? sanitize_text_field( $_POST['delimiter'] ) : ',' ) );

				$message = __( 'Settings saved.', 'woo_bip' );
				woo_bip_admin_notice( $message );
				break;

            // Calculating the price's
            // Выполняем пересчет цен
            case 'price':
                woo_bip_update_option( 'rate', ( isset( $_POST['rate'] ) ? $_POST['rate'] : 0 ) );
                woo_bip_update_option( 'trade_margin', ( isset( $_POST['trade_margin'] ) ? absint( $_POST['trade_margin'] ) : 0 ) );
                woo_bip_update_option( 'enable_discount', ( isset( $_POST['enable_discount'] ) ? absint( $_POST['enable_discount'] ) : 0 ) );
                woo_bip_update_option( 'count_discount', ( isset( $_POST['count_discount'] ) ? absint( $_POST['count_discount'] ) : 0 ) );
                woo_bip_update_option( 'discount_margin', ( isset( $_POST['discount_margin'] ) ? absint( $_POST['discount_margin'] ) : 0 ) );
                woo_bip_update_prices( $_POST['rate'], $_POST['trade_margin']);
                if (absint( $_POST['enable_discount'] ) == '1')
                    woo_bip_update_sale_prices( $_POST['rate'], $_POST['discount_margin'], $_POST['count_discount']);

                $message = __( 'Prices are recalculated.', 'woo_bip' );
                woo_bip_admin_notice( $message );
                break;

            default:
				$import = new stdClass;
				$import->upload_method = woo_bip_get_option( 'upload_method', 'upload' );
				$import->rate = woo_bip_get_option( 'rate' );
				$import->trade_margin = woo_bip_get_option( 'trade_margin' );
                $import->enable_discount = woo_bip_get_option( 'enable_discount' );
                $import->count_discount = woo_bip_get_option( 'count_discount' );
                $import->discount_margin = woo_bip_get_option( 'discount_margin' );
                $import->delimiter = woo_bip_get_option( 'delimiter', ',' );
				if( $import->delimiter == "\t" )
					$import->delimiter = 'TAB';
				if( $import->delimiter == '' || $import->delimiter == false )
					$import->delimiter = ',';
				$import->delete_file = woo_bip_get_option( 'delete_file', 0 );
				$import->encoding = woo_bip_get_option( 'encoding', 'UTF-8' );
				break;

            // Running import price
            // Если выбран Импорт прайса
			case 'upload':
				$import = new stdClass;
				$import->cancel_import = false;
                $import->skip_first = absint( woo_bip_get_option( 'skip_first', 1 ) );
                $import->only_price = absint( woo_bip_get_option( 'only_price', 1 ) );
                $import->parsing_data = absint( woo_bip_get_option( 'parsing_data', 1 ) );
                $import->brands_taxonomies = absint( woo_bip_get_option( 'brands_taxonomies', 1 ) );
                $import->brands_attributes = absint( woo_bip_get_option( 'brands_attributes', 1 ) );
                $import->upload_method = ( isset( $_POST['upload_method'] ) ? $_POST['upload_method'] : 'upload' );
				$import->import_method = sanitize_text_field( woo_bip_get_option( 'import_method', 'new' ) );
				$import->advanced_log = absint( woo_bip_get_option( 'advanced_log', 1 ) );
				$import->rate = $_POST['rate'];
				$import->trade_margin = $_POST['trade_margin'];;
				$import->delimiter = ( isset( $_POST['delimiter'] ) ? substr( $_POST['delimiter'], 0, 3 ) : ',' );
				if( $import->delimiter == 'TAB' )
					$import->delimiter = "\t";
				$import->delete_file = absint( woo_bip_get_option( 'delete_file', 0 ) );
				$import->encoding = ( isset( $_POST['encoding'] ) ? sanitize_text_field( $_POST['encoding'] ) : 'UTF-8' );
				$import->timeout = absint( woo_bip_get_option( 'timeout', 600 ) );
                $import->enable_discount = woo_bip_get_option( 'enable_discount' );
                $import->count_discount = woo_bip_get_option( 'count_discount' );
                $import->discount_margin = woo_bip_get_option( 'discount_margin' );
                $import->upload_mb = wp_max_upload_size();
				woo_bip_update_option( 'rate', $import->rate );
				woo_bip_update_option( 'trade_margin', $import->trade_margin );
				woo_bip_update_option( 'delimiter', $import->delimiter );
				woo_bip_update_option( 'encoding', $import->encoding );

				// Capture the CSV file uploaded
                // Делаем захват загруженного файла CSV
				if( $_FILES['csv_file']['error'] == 0 ) {
					$file = $_FILES['csv_file'];
				} else {
					$file = array(
						'size' => 0,
						'error' => ( isset( $_FILES['csv_file']['error'] ) ? $_FILES['csv_file']['error'] : 0 )
					);
				}

				// Validation of the import method chosen and uploaded file
                // Проверяем метод импорта и загруженный файл
				if( $file['error'] <> 4 && $file['size'] == 0 ) {
					if( $file['error'] == 0 && $file['size'] == 0 ) {
						// User has uploaded an empty file
                        // Пользователь загрузил пустой файл
						$import->cancel_import = true;
						$message = sprintf( __( 'Your CSV file is empty, re-upload a populated CSV file from the opening import screen. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
						woo_bip_admin_notice( $message, 'error' );
					} else {
						// User has uploaded the CSV file but it has expired, usually due to PHP timeout or completed import
                        // Пользователь загрузил файл, но он просрочен, обычно из=за ошибки тайм-аута
						$import->cancel_import = true;
						$message = sprintf( __( 'Your CSV file upload has expired, re-upload it from the opening import screen. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
						woo_bip_admin_notice( $message, 'error' );
					}
				} else if( $file['error'] == 4 && $file['size'] == 0 ) {
					// No file uploaded
                    // Не был загружен файл
					$import->cancel_import = true;
					$message = sprintf( __( 'No CSV file was uploaded, check that a file is uploaded from the opening import screen. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
					woo_bip_admin_notice( $message, 'error' );
				} else if( strpos( strtolower( $file['name'] ), 'csv' ) == false ) {
					// Not a CSV file or lacking a *.csv file extension
                    // Файл не CSV формата
					$import->cancel_import = true;
					$message = sprintf( __( 'Brain Import Price requires a CSV-formatted upload, if you are sure the file is a CSV please change the file extension and re-upload. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
					woo_bip_admin_notice( $message, 'error' );
				} else if( $file['size'] == 0 && empty( $file['name'] ) ) {
					// No file uploaded
                    // Не был загружен файл
					$import->cancel_import = true;
					$message = sprintf( __( 'No CSV file was uploaded. Please select a CSV to upload from the \'Choose File\' dialog or other available upload options, alternatively select a CSV from Past Imports. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
					woo_bip_admin_notice( $message, 'error' );
				}

				// Validation of the WordPress site and memory allocation against the uploaded file
                // Проверка сайта WordPress и распределения памяти с загруженного файла
				if( $file['size'] > $import->upload_mb ) {
					$import->cancel_import = true;
					$message = sprintf( __( 'The file you\'re importing exceeded the maximum allowed filesize (see Maximum size), increase the file upload limit or import a smaller CSV file. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
					woo_bip_admin_notice( $message, 'error' );
				}
				// Check if the currency rate is set
				// Проверяем установлен ли курс валют
				if( $import->rate == '0' || $import->rate == '' ) {
					$import->cancel_import = true;
					$message = sprintf( __( 'You cannot leave the Rate under Import Options empty.', 'woo_bip' ));
					woo_bip_admin_notice( $message, 'error' );
				}
				// Check if the currency rate is set
				// Проверяем установлен ли курс валют
				if( $import->trade_margin == '0' || $import->trade_margin == '' ) {
					$import->cancel_import = true;
					$message = sprintf( __( 'You cannot leave the Margin under Import Options empty.', 'woo_bip' ));
					woo_bip_admin_notice( $message, 'error' );
				}
				// Check if the delimiter is set
				// Проверяем установлен ли разделитель
				if( $import->delimiter == '' ) {
					$import->cancel_import = true;
					$message = sprintf( __( 'You cannot leave the Field delimiter or Product Category separator options under Import Options empty. <a href="%s" target="_blank">Need help?</a>', 'woo_bip' ), $troubleshooting_url );
					woo_bip_admin_notice( $message, 'error' );
				}

				if( $import->cancel_import )
					continue;

				if( in_array( $import->upload_method, array( 'upload' ) ) ) {
					$upload = wp_upload_bits( $file['name'], null, file_get_contents( $file['tmp_name'] ) );
					// Fail import if WordPress cannot save the uploaded CSV file
                    // Ошибка импорта если WordPress не может сохранить загруженный файл
					if( $upload['error'] ) {
						$import->cancel_import = true;
						$message = sprintf( __( 'There was an error while uploading your CSV file, <em>%s</em>. %s', 'woo_bip' ), $upload['error'], '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
						woo_bip_admin_notice( $message, 'error' );
					}
					if( !$import->cancel_import ) {
						$import->file = $file;
						woo_bip_update_option( 'csv', $upload['file'] );
					}
				} else {
					$import->file = $file;
				}
				break;

			case 'save':

				global $product;

				$import = new stdClass;
				$import->cancel_import = false;
				$import->log = '';
				$import->rate = woo_bip_get_option( 'rate' );
				$import->trade_margin = woo_bip_get_option( 'trade_margin' );
				$import->delimiter = woo_bip_get_option( 'delimiter', ',' );
				$import->delete_file = woo_bip_get_option( 'delete_file', 0 );
				$import->encoding = woo_bip_get_option( 'encoding', 'UTF-8' );
                $import->skip_first = ( isset( $_POST['skip_first'] ) ? 1 : 0 );
                $import->only_price = ( isset( $_POST['only_price'] ) ? 1 : 0 );
                $import->parsing_data = ( isset( $_POST['parsing_data'] ) ? 1 : 0 );
                $import->brands_taxonomies = ( isset( $_POST['brands_taxonomies'] ) ? 1 : 0 );
                $import->brands_attributes = ( isset( $_POST['brands_attributes'] ) ? 1 : 0 );
				$import->import_method = 'new' ;
				$import->advanced_log = ( isset( $_POST['advanced_log'] ) ? 1 : 0 );
				woo_bip_update_option( 'import_method', $import->import_method );
                woo_bip_update_option( 'only_price', absint( $import->only_price ) );
                woo_bip_update_option( 'parsing_data', absint( $import->parsing_data ) );
                woo_bip_update_option( 'brands_taxonomies', absint( $import->brands_taxonomies ) );
                woo_bip_update_option( 'brands_attributes', absint( $import->brands_attributes ) );
                woo_bip_update_option( 'skip_first', absint( $import->skip_first ) );
				woo_bip_update_option( 'advanced_log', absint( $import->advanced_log ) );
				if( isset( $_POST['timeout'] ) )
					woo_bip_update_option( 'timeout', sanitize_text_field( $_POST['timeout'] ) );

				// Check if our import has expired
                // Проверка, если вышло время
				if( !woo_bip_get_option( 'csv' ) ) {
					$import->cancel_import = true;
					$message = sprintf( __( 'Your CSV file upload has expired, re-upload it from the opening import screen. %s', 'woo_bip' ), '<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_bip' ) . '</a>' );
					woo_bip_admin_notice( $message, 'error' );
				}

				if( $import->cancel_import )
					return;

				// Check if this is a resumed import
                // Проверка, если это возобновление импорта
				if ( isset( $_POST['refresh_step'] ) ) {
					$step = sanitize_text_field( $_POST['refresh_step'] );
					$transient = get_transient( WOO_BIP_PREFIX . '_import' );
					$transient->log = '<br /><br />' . __( 'Resuming import...', 'woo_bip' );
					if( sanitize_text_field( $_POST['import_method'] ) == 'new' )
						$transient->log .= "<br /><br />" . __( 'Generating Products...', 'woo_bip' );
					$settings = array(
						'skip_first' => $transient->skip_first,
                        'only_price' => $transient->only_price,
                        'parsing_data' => $transient->parsing_data,
                        'brands_taxonomies' => $transient->brands_taxonomies,
                        'brands_attributes' => $transient->brands_attributes,
						'import_method' => ( isset( $_POST['import_method'] ) ? sanitize_text_field( $_POST['import_method'] ) : 'new' ),
						'restart_from' => ( isset( $_POST['restart_from'] ) ? absint( (int)$_POST['restart_from'] ) : 0 ),
						'progress' => ( isset( $_POST['progress'] ) ? absint( $_POST['progress'] ) : 0 ),
						'total_progress' => ( isset( $_POST['total_progress'] ) ? absint( $_POST['total_progress'] ) : 0 ),
						'log' => __( 'Resuming import...', 'woo_bip' )
					);
					$response = set_transient( WOO_BIP_PREFIX . '_import', $transient );
					// Check if the Transient was saved
                    // Проверка если временные значения сохранены
					if( is_wp_error( $response ) )
						error_log( '[product-importer] Could not save the resume import Transient', 'woo_bip' );
					unset( $transient );
				} else {
					$step = 'prepare_data';
					$settings = $_POST;
				}

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'progressBar', plugins_url( '/js/progress.js', WOO_BIP_FILE ), array( 'jquery' ) );
				wp_enqueue_script( 'ajaxUpload', plugins_url( '/js/ajaxupload.js', WOO_BIP_FILE ), array( 'jquery' ) );
				wp_register_script( 'ajaxImporter', plugins_url( '/js/engine.js', WOO_BIP_FILE ), array( 'jquery' ) );
				wp_enqueue_script( 'ajaxImporter' );
				wp_localize_script( 'ajaxImporter', 'ajaxImport', array(
					'settings'	=> $settings,
					'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
					'step'		=> $step
				) );
				unset( $step, $settings );
				break;

		}

	}

    // General function of import AJAX
    // Основная фукнция импорта AJAX
	function woo_bip_ajax_brain_import_price() {

        global $import;

 		if( isset( $_POST['step'] ) ) {

			@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

			ob_start();

            // Split the CSV data from the main transient
            // Разделение данных CSV
			if( $_POST['step'] != 'prepare_data' ) {
				$import = get_transient( WOO_BIP_PREFIX . '_import' );
				if( is_object( $import ) ) {
					if( isset( $_POST['settings'] ) && !is_string( $_POST['settings'] ) ) {
						foreach( $_POST['settings'] as $key => $value ) {
							if( is_array( $value ) ) {
								foreach( $value as $value_key => $value_value ) {
									if( !is_array( $value_value ) )
										$value[$value_key] = stripslashes( $value_value );
								}
								$import->$key = $value;
							} else {
								$import->$key = stripslashes( $value );
							}
						}
					}
					// Merge the split transients into the $import global
                    // Объединение разделенных данных в $import global
					if( isset( $import->headers ) ) {
						$args = array(
                            'generate_categories',
                            'generate_brands',
							'generate_tags',
							'prepare_product_import',
							'save_product'
						);
						foreach( $import->headers as $header ) {
                            // Exclude $import->csv_category and $import->csv_brands for most of the import
							if( in_array( $header, array( 'category', 'brands') ) ) {
								if( in_array( $_POST['step'], $args ) )
									$import->{'csv_' . $header} = get_transient( WOO_BIP_PREFIX . '_csv_' . $header );
							} else {
								$import->{'csv_' . $header} = get_transient( WOO_BIP_PREFIX . '_csv_' . $header );
							}
						}
					}
				} else {
					$import = new stdClass;
					$import->cancel_import = true;
					$troubleshooting_url = 'http://www.neo.poltava.ua/';
					$import->failed_import = sprintf( __( 'Your CSV contained special characters that WordPress and Brain Import Price could not filter. %s', 'woo_bip' ), __( 'Need help?', 'woo_bip' ) . ' ' . $troubleshooting_url );
				}
			}

			$timeout = 0;
			if( isset( $import->timeout ) )
				$timeout = $import->timeout;

			if( !ini_get( 'safe_mode' ) )
				@set_time_limit( $timeout );

			switch ( $_POST['step'] ) {

				case 'prepare_data':

                    global $wpdb;

					$import = new stdClass;
					$import->start_time = time();
					$import->cancel_import = false;
					$import->failed_import = '';
					$import->log = '';
					$import->timeout = ( isset( $_POST['timeout'] ) ? $_POST['timeout'] : false );
					$import->rate = $_POST['rate'];
					$import->trade_margin = $_POST['trade_margin'];
					$import->delimiter = $_POST['delimiter'];
					$import->delete_file = woo_bip_get_option( 'delete_file', 0 );
					$import->import_method = 'new' ;
                    $import->skip_first = ( isset( $_POST['skip_first'] ) ? (int)$_POST['skip_first'] : 0 );
                    $import->only_price = woo_bip_get_option( 'only_price' );
                    $import->parsing_data = woo_bip_get_option( 'parsing_data' );
                    $import->brands_taxonomies = woo_bip_get_option( 'brands_taxonomies' );
                    $import->brands_attributes = woo_bip_get_option( 'brands_attributes' );
                    $import->advanced_log = ( isset( $_POST['advanced_log'] ) ? (int)$_POST['advanced_log'] : 0 );
					$import->log .= '<br />' . sprintf( __( 'Import method: %s', 'woo_bip' ), $import->import_method );
                        woo_bip_prepare_data( 'prepare_data' );

                    if( $import->advanced_log )
						$import->log .= "<br />" . __( 'Validating required columns...', 'woo_bip' );
					if( !$import->cancel_import )
						woo_bip_prepare_columns();
					$import->log .= "<br />" . __( 'Product columns have been grouped', 'woo_bip' );

                    // Update stock status
                    // Обновляем наличие на складе
                    $import->log .= "<br /><br />" . __( 'Update stock status...', 'woo_bip' );
                    $db_update_sql = $wpdb->prepare( "UPDATE " . $wpdb->postmeta . " SET meta_value=%s WHERE meta_key='updated'", 'no');
                    $wpdb->query( $db_update_sql);
                    $wpdb->flush();

                    $import->log .= "<br /><br />" . __( 'Generating Categories...', 'woo_bip' );
					$import->loading_text = __( 'Generating Categories...', 'woo_bip' );
					break;

                case 'generate_categories':
					$import->products_added = 0;
					$import->products_deleted = 0;
					$import->products_failed = 0;
					// Category generation
                    // Генерирование категорий
					if( in_array( $import->import_method, array( 'new' ) ) && isset( $import->csv_category ) )
						woo_bip_generate_categories();
					else
						$import->log .= "<br />" . __( 'Categories skipped', 'woo_bip' );
					$import->log .= "<br /><br />" . __( 'Generating Brands...', 'woo_bip' );
					$import->loading_text = __( 'Generating Brands...', 'woo_bip' );
					break;

                case 'generate_brands':
                    // Brands generation
                    // Генерирование производителей
                    if( in_array( $import->import_method, array( 'new' ) ) && isset( $import->csv_brands ) )
                        woo_bip_generate_brands();
                    else
                        $import->log .= "<br />" . __( 'Brands skipped', 'woo_bip' );
                    $import->log .= "<br /><br />" . __( 'Generating Tags...', 'woo_bip' );
                    $import->loading_text = __( 'Generating Tags...', 'woo_bip' );
                    break;

                case 'generate_tags':
					// Tag generation
                    // Генерирование меток
					if( in_array( $import->import_method, array( 'new' ) ) && isset( $import->csv_tag ) )
						woo_bip_generate_tags();
					else {
                        $import->log .= "<br />" . __($import->csv_tag, 'woo_bip');
                        $import->log .= "<br />" . __($import->import_method, 'woo_bip');
                        $import->log .= "<br />" . __('Tags skipped', 'woo_bip');
                    }
					if( $import->import_method == 'new' )
						$import->log .= "<br /><br />" . __( 'Generating Products...', 'woo_bip' );
					$import->loading_text = __( 'Importing Products...', 'woo_bip' );
					break;

				case 'prepare_product_import':

					global $import, $product;

					if( $import->advanced_log )
						$import->log .= "<br />>>> " . __( 'Including non-essential reporting in this import log', 'woo_bip' );
					if( $import->skip_first == 1) {
						$import->i = 1;
						$import->log .= "<br />>>> " . __( 'Skipping import of first CSV row', 'woo_bip' );
					} else {
						$import->i = 0;
						$import->log .= "<br />>>> " . __( 'Starting import at first CSV row', 'woo_bip' );
					}
					$import->failed_products = array();

					$i = $import->i;
					woo_bip_prepare_product( $i );

					// This runs once as part of the import preparation
                    // Это выполняется как часть подготовки импорта
					$import->active_product = $product;
						if( !empty( $product->name ) )
							$import->log .= "<br />>>> " . sprintf( __( 'Importing %s...', 'woo_bip' ), $product->name );
						else
							$import->log .= "<br />>>> " . sprintf( __( 'Importing (no title) - SKU: %s...', 'woo_bip' ), $product->sku );
						$import->loading_text = sprintf( __( 'Importing Product %d of %d...', 'woo_bip' ), $i, ( $import->skip_first == 1 ? $import->rows - 1 : $import->rows ) );
					break;

				case 'save_product':

					global $import, $product, $wpdb;

					$i = $_POST['i'];

					if( $import->active_product ) {
						if( !isset( $product ) )
							$product = new stdClass;
						foreach( $import->active_product as $key => $value )
							$product->$key = $value;
					}

					$import->product_start_time = microtime( true );

                        // Build Brands
                        // Строим производителей
                        woo_bip_process_brands();
						// Build Categories
                        // Строим категории
						woo_bip_process_categories();

						// Build Tags
                        // Строим метки
						woo_bip_process_tags();

                    // Check for duplicate SKU
                    // Проверяем дублирование партномеров
                    woo_bip_duplicate_product_exists();
					woo_bip_validate_product();

					if( $product->fail_requirements ) {

						if( $import->advanced_log )
							$import->log .= "<br />>>>>>> " . sprintf( __( 'Skipping Product, see Import Report for full explanation. Reason: %s', 'woo_bip' ), $product->failed_reason );
						else
							$import->log .= "<br />>>>>>> " . sprintf( __( 'Skipping Product, reason: %s', 'woo_bip' ), $product->failed_reason );
						$import->products_failed++;

					} else {

                        woo_bip_create_product();

							if( $product->imported ) {
								if( $import->import_method == 'new' ) {
									if( !empty( $product->name ) )
										$import->log .= "<br />>>>>>> " . sprintf( __( '%s successfully imported', 'woo_bip' ), $product->name );
									else
										$import->log .= "<br />>>>>>> " . sprintf( __( '(no title) - SKU: %s successfully imported', 'woo_bip' ), $product->sku );
								}
							} else {
								if( $import->advanced_log )
									$import->log .= "<br />>>>>>> " . sprintf( __( 'Skipping Product, see Import Report for full explanation. Reason: %s', 'woo_bip' ), $product->failed_reason );
								else
									$import->log .= "<br />>>>>>> " . sprintf( __( 'Skipping Product, reason: %s', 'woo_bip' ), $product->failed_reason );
							}

					}
					$import->product_end_time = microtime( true );
					$import->product_min_time = ( isset( $import->product_min_time ) ? $import->product_min_time : ( $import->product_end_time - $import->product_start_time ) );
					$import->product_max_time = ( isset( $import->product_max_time ) ? $import->product_max_time : ( $import->product_end_time - $import->product_start_time ) );
					// Update minimum product import time if it is shorter than the last
                    // Обновляем минимальное время импорта, если оно было короче чем предыдущее
					if( ( $import->product_end_time - $import->product_start_time ) < $import->product_min_time )
						$import->product_min_time = ( $import->product_end_time - $import->product_start_time );
					// Update maximum product import time if it is longer than the last
                    // Обновляем максимальное время импорта, если оно было длиннее чем предыдущее
					if( ( $import->product_end_time - $import->product_start_time ) > $import->product_max_time )
						$import->product_max_time = ( $import->product_end_time - $import->product_start_time );

					// All import rows have been processed
                    // Все строки импортированы
					if( $i+1 == $import->rows ) {
						if( $import->import_method == 'new' )
							$import->log .= "<br />" . __( 'Products have been generated', 'woo_bip' );

                        // Update stock status
                        // Обновляем наличие на складе
                        $import->log .= "<br /><br />" . __( 'Update stock status...', 'woo_bip' );
                        $outofstock = $wpdb->get_col("SELECT t1.post_id FROM " . $wpdb->postmeta . " AS t1," . $wpdb->postmeta . " AS t2"
                        ." WHERE t1.meta_key='updated' AND t1.meta_value='no' AND t1.post_id=t2.post_id AND t2.meta_key='_stock_status' AND t2.meta_value='instock'");
                        $wpdb->flush();
                        for ($z=0; $z<count($outofstock); $z++) {
                                update_post_meta($outofstock[$z], '_stock_status', 'outofstock');
                        }

                        $import->log .= "<br /><br />" . __( 'Cleaning up...', 'woo_bip' );
						$import->loading_text = __( 'Cleaning up...', 'woo_bip' );
					} else {
						unset( $import->active_product );

						woo_bip_prepare_product( $i + 1 );

						// This runs for each additional Product imported
                        // Это выполняется для каждой дополнительной строки импорта
						$import->active_product = $product;
						if( $import->import_method == 'delete' ) {
							if( !empty( $product->name ) )
								$import->log .= "<br />>>> " . sprintf( __( 'Searching for %s...', 'woo_bip' ), $product->name );
							else
								$import->log .= "<br />>>> " . sprintf( __( 'Searching for (no title) - SKU: %s...', 'woo_bip' ), $product->sku );
							$import->loading_text = sprintf( __( 'Searching for Product %d of %d...', 'woo_bip' ), $i + 1, ( $import->skip_first == 1 ? $import->rows - 1 : $import->rows ) );
						} else {
							if( !empty( $product->name ) )
								$import->log .= "<br />>>> " . sprintf( __( 'Importing %s...', 'woo_bip' ), $product->name );
							else
								$import->log .= "<br />>>> " . sprintf( __( 'Importing (no title) - SKU: %s...', 'woo_bip' ), $product->sku );
							$import->loading_text = sprintf( __( 'Importing Product %d of %d...', 'woo_bip' ), $i + 1, ( $import->skip_first == 1 ? $import->rows - 1 : $import->rows ) );
						}
					}
					break;

                case 'update_price':
                        $import->log .= "<br />" . __( 'Updating price', 'woo_bip' );
                    break;

                case 'clean_up':

					global $wpdb, $product;

                    // Organise Brands
                    // Организуем производителей
                    if( isset( $import->csv_brands ) ) {
                        $term_taxonomy = 'brands';
                        $import->log .= "<br />>>> " . __( 'Organise Brands', 'woo_bip' );
                    }

                    // Organise Categories
                    // Организуем категории
					if( isset( $import->csv_category ) ) {
						$term_taxonomy = 'product_cat';
						$import->log .= "<br />>>> " . __( 'Organise Categories', 'woo_bip' );
					}

					// Organise Tags
                    // Организуем метки
					if( isset( $import->csv_tag ) ) {
                        $import->log .= "<br />>>> " . __( 'Organise Tags', 'woo_bip' );
					}

					$import->log .= "<br />" . __( 'Clean up has completed', 'woo_bip' );
					$import->end_time = time();

                    // Recalculating sale prices
                    // Пересчитываем цены со скидкой
                        $rate = woo_bip_get_option('rate');
                        $count_discount = woo_bip_get_option('count_discount');
                        $discount_margin = woo_bip_get_option('discount_margin');
                        woo_bip_update_sale_prices($rate, $discount_margin, $count_discount);

                    // Post-import Product details
                    // Подробности после импорта
					if( $import->advanced_log ) {
						$import->log .= "<br /><br />" . __( 'Import summary', 'woo_bip' );
						if( in_array( $import->import_method, array( 'new' ) ) )
							$import->log .= "<br />>>> " . sprintf( __( '%d Products added', 'woo_bip' ), $import->products_added );
						$import->log .= "<br />>>> " . sprintf( __( '%d Products skipped', 'woo_bip' ), $import->products_failed );
						$import->log .= "<br />>>> " . sprintf( __( 'Import took %s to complete', 'woo_bip' ), woo_bip_display_time_elapsed( $import->start_time, $import->end_time ) );
						$import->log .= "<br />>>> " . sprintf( __( 'Fastest Product took < %s to process', 'woo_bip' ), woo_bip_display_time_elapsed( time(), strtotime( sprintf( '+%d seconds', $import->product_min_time ) ) ) );
						$import->log .= "<br />>>> " . sprintf( __( 'Slowest Product took > %s to process', 'woo_bip' ), woo_bip_display_time_elapsed( time(), strtotime( sprintf( '+%d seconds', $import->product_max_time ) ) ) );
					}

					$import->log .= "<br /><br />" . __( 'Import complete!', 'woo_bip' );
					$import->loading_text = __( 'Completed', 'woo_bip' );
					break;

			}

			// Clear transients
            // Очищаем временные значения
			if( function_exists( 'wc_delete_product_transients' ) )
				wc_delete_product_transients();

			$import->step = $_POST['step'];
			$import->errors = ob_get_clean();

			// Encode our transients in UTF-8 before storing them
            // Перекодируем наши временные значения в UTF-8 перед сохранением
			add_filter( 'pre_set_transient_woo_bip_import', 'woo_bip_filter_set_transient' );

			// Split the import data from the main transient
            // Разделяем импортированные данные в основном временном хранилище
			if( isset( $import->headers ) ) {
				foreach( $import->headers as $header ) {
					if( isset( $import->{'csv_' . $header} ) ) {
						$response = set_transient( WOO_BIP_PREFIX . '_csv_' . $header, $import->{'csv_' . $header} );
						// Check if the Transient was saved
                        // Проверяем что временные значения были сохранены
						if( is_wp_error( $response ) )
							error_log( sprintf( __( '[product-importer] Could not save the import data Transient for the column %s', 'woo_bip' ), $header ) );
						unset( $import->{'csv_' . $header} );
					}
				}
			}

			$response = set_transient( WOO_BIP_PREFIX . '_import', $import );
			// Check if the Transient was saved
            // Проверяем что временные значения были сохранены
			if( is_wp_error( $response ) )
				error_log( '[product-importer] Could not save the import Transient prior to starting AJAX import engine', 'woo_bip' );

			$return = array();
			if( isset( $import->log ) )
				$return['log'] = $import->log;
			if( isset( $import->rows ) )
				$return['rows'] = $import->rows;
			if( isset( $import->skip_first ) )
				$return['skip_first'] = $import->skip_first;
			if( isset( $import->loading_text ) )
				$return['loading_text'] = $import->loading_text;
			if( isset( $import->cancel_import ) )
				$return['cancel_import'] = $import->cancel_import;
			if( isset( $import->failed_import ) )
				$return['failed_import'] = $import->failed_import;
			if( isset( $i ) )
				$return['i'] = $i;
			if( isset( $import->next ) )
				$return['next'] = $import->next;
			if( isset( $import->html ) )
				$return['html'] = $import->html;
			if( isset( $import->step ) )
				$return['step'] = $import->step;
			@array_map( 'utf8_encode', $return );
			header( "Content-type: application/json" );
			echo json_encode( $return );

		}
		die();

	}
	add_action( 'wp_ajax_brain_import_price', 'woo_bip_ajax_brain_import_price' );

    // End of AJAX import
    // Окончание процесса импорта AJAX
	function woo_bip_ajax_finish_import() {

		global $import;

		$return = array();

		ob_start();

		$import = get_transient( WOO_BIP_PREFIX . '_import' );
		foreach( $_POST['settings'] as $key => $value ) {
			if( is_array( $value ) ) {
				foreach( $value as $value_key => $value_value )
					if( !is_array( $value_value ) ) $value[$value_key] = stripslashes( $value_value );
				$import->$key = $value;
			} else {
				$import->$key = stripslashes( $value );
			}
		}
		$return['next'] = 'finish-import';
		$post_type = 'product';
		$manage_products_url = add_query_arg( 'post_type', $post_type, 'edit.php' );

		// Terminate import session as Products have been imported/merged
        // Прерываем сессию импорта когда товар был импортирован/объединен
		delete_transient( WOO_BIP_PREFIX . '_import' );
		if( isset( $import->headers ) ) {
			foreach( $import->headers as $header )
				delete_transient( WOO_BIP_PREFIX . '_csv_' . $header );
		}
		woo_bip_delete_file();

		include_once( WOO_BIP_PATH . 'templates/import_finish.php' );

		$return['html'] = ob_get_clean();
		header( "Content-type: application/json" );
		echo json_encode( $return );

		die();

	}
	add_action( 'wp_ajax_finish_import', 'woo_bip_ajax_finish_import' );

    // Messages about end of import process
    // Сообщения об окончании процесса импорта
	function woo_bip_finish_message() {

		global $import;

		$message = '';
		if( !$import->failed_products ) {
			if( $import->import_method == 'new' )
				$message = apply_filters( 'woo_bip_finish_success_import', __( 'Good news! All of your Products have been successfully imported into WooCommerce.', 'woo_bip' ) );
		} else {
			if( $import->products_added || $import->products_deleted ) {
				if( $import->import_method == 'new' )
					$message = apply_filters( 'woo_bip_finish_partial_import', __( 'Here\'s the news. Some of your Products have been successfully imported into WooCommerce.', 'woo_bip' ) );
			} else {
				if( $import->import_method == 'new' )
					$message = apply_filters( 'woo_bip_finish_fail_import', __( 'Here\'s the news. No new Products were imported into WooCommerce.', 'woo_bip' ) );
			}
		}
		if( $message ) { ?>
<div class="updated settings-error below-h2">
	<p><?php echo $message; ?></p>
</div>
<?php
		}

	}

	// Increase memory for AJAX importer process and Brain Import Price screens
    // Увеличиваем память для процессов импорта AJAX и экранов Импорт прайса Брейн
	function woo_bip_init_memory() {

		$page = $_SERVER['SCRIPT_NAME'];
		if( isset( $_POST['action'] ) )
			$action = $_POST['action'];
		elseif( isset( $_GET['action'] ) )
			$action = $_GET['action'];
		else
			$action = '';

		$allowed_actions = array( 'brain_import_price', 'finish_import', 'upload_image' );

		if( $page == '/wp-admin/admin-ajax.php' && in_array( $action, $allowed_actions ) )
			@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

	}
	add_action( 'plugins_loaded', 'woo_bip_init_memory' );

	// Returns the number of columns detected within a CSV file
    // Возвращаем количество колонок, обнаруженных в файле CSV
	function woo_bip_total_columns() {

		global $import;

		if( $import->rows ) {
			if( $import->skip_first == 1 )
				$message = sprintf( __( '%s rows have been detected within this import file, and by the looks of it the first row contains the column headers. Let\'s get started!', 'woo_bip' ), $import->rows );
			else
				$message = sprintf( __( '%s rows have been detected within this import file. Let\'s get started!', 'woo_bip' ), $import->rows );
			woo_bip_admin_notice_html( $message );
		}

	}

	// Searches an array for a needle and returns the results
    // Поиск в массиве необходимого значения
	function woo_bip_array_search( $array, $key, $value ) {

		$results = array();
		if( is_array( $array ) ) {
			if( isset( $array[$key] ) && $array[$key] == $value )
			$results[] = $array;
			foreach( $array as $subarray )
				$results = array_merge( $results, woo_bip_array_search( $subarray, $key, $value ) );
		}
		return $results;

	}

	/* End of: WordPress Administration */
    /* Окончание Административной панели */

}

// Preparing data for import
// Подготавливаем данные для импорта
function woo_bip_prepare_data( $step = false ) {

	global $import;

    $skip_first = $import->skip_first;
	$import->skip_first = 0;
	if( $file = woo_bip_get_option( 'csv' ) ) {
		ini_set( 'auto_detect_line_endings', true );
		if( @filesize( $file ) > 0 ) {
			// Skip generating first and second rows for AJAX import engine
            // Пропускаем первую и вторую строку для движка импорта AJAX
			if( $step == false ) {
				if( $handle = @fopen( $file, 'r' ) ) {
					$import->lines = array();
					$line = 0;
					while( ( $buffer = fgets( $handle ) ) !== false ) {
						// First row of import file
                        // Первая строка импортируемого файла
						if( $line == 0 ) {
							// Save the first row intact for later import issue detection
                            // Сохраняем первую строку без изменений для последующего определения импорта
							$import->raw = $buffer;
							$import->lines[0] = woo_bip_encode_transient( $buffer );
						}
						// Second row of import file
                        // Вторая строка импортируемого файла
						if( $line == 1 ) {
							$import->lines[1] = woo_bip_encode_transient( $buffer );
							break;
						}
						$line++;
					}
					fclose( $handle );
				}
			}
			if( $handle = @fopen( $file, 'r' ) ) {
				$data = array();
				while( ( $csv_data = @fgetcsv( $handle, filesize( $handle ), $import->delimiter ) ) !== false ) {
					$size = count( $csv_data );
					for( $i = 0; $i < $size; $i++ ) {
						if( !isset( $data[$i] ) || !is_array( $data[$i] ) )
							$data[$i] = array();
						$csv_data[$i] = woo_bip_encode_transient( trim( $csv_data[$i] ) );
						array_push( $data[$i], $csv_data[$i] );
					}
					unset( $csv_data );
				}
				fclose( $handle );
				$import->csv_data = $data;
				unset( $csv_data, $data );
				$import->rows = count( $import->csv_data[0] );
				if( $import->advanced_log )
					$import->log .= "<br />" . sprintf( __( 'Sufficient memory is available... %s', 'woo_bip' ), woo_bip_current_memory_usage() );
			} else {
				$import->cancel_import = true;
				$import->failed_import = __( 'Could not read file. Could not open the import file or URL.', 'woo_bip' );
			}
		} else {
			$import->cancel_import = true;
			$import->failed_import = __( 'Could not read file. An empty import file was detected.', 'woo_bip' );
		}
		ini_set( 'auto_detect_line_endings', false );
		unset( $handle );
	} else {
		$import->cancel_import = true;
		$import->failed_import = __( 'Could not read file. Brain Import Price doesn\'t have a record of this import file.', 'woo_bip' );
	}
    $import->skip_first = $skip_first;
}

// Encoding transient data
// Перекодировка временных данных
function woo_bip_filter_set_transient( $var ) {

	if( is_object( $var ) ) {
		foreach( $var as $key => $value )
			$var->$key = woo_bip_encode_transient( $value );
	} else if( is_array( $var ) ) {
		foreach( $var as $key => $value )
			$var[$key] = woo_bip_encode_transient( $value );
	}
	return $var;

}

// Encoding transient data - external function call
// Перекодировка временных данных внешней функцией
function woo_bip_encode_transient( $var = null ) {

	// Check that the Encoding class by Sebasti�n Grignoli exists
    // Проверка, существует ли внешний класс кодировки
	if( file_exists( WOO_BIP_PATH . 'classes/Encoding.php' ) ) {
		include_once( WOO_BIP_PATH . 'classes/Encoding.php' );
		if( class_exists( 'Encoding' ) ) {
			$encoding = new Encoding();
			return $encoding->toUTF8( $var );
		}
	} else {
		return $var;
	}

}

// Return error codes when upload
// Возвращает код ошибки при загрузке файла
function woo_bip_format_upload_error_code( $error_code = 0 ) {

	$error_codes = array(
		1 => __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 'woo_bip' ),
		2 => __( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'woo_bip' ),
		3 => __( 'The uploaded file was only partially uploaded', 'woo_bip' ),
		4 => __( 'No file was uploaded', 'woo_bip' ),
		6 => __( 'Missing a temporary folder', 'woo_bip' ),
		7 => __( 'Failed to write file to disk', 'woo_bip' ),
		8 => __( 'A PHP extension stopped the file upload', 'woo_bip' )
	);
	if( empty( $error_code ) )
		$error_code = __( 'Unknown upload error', 'woo_bip' );
	$output = ( isset( $error_codes[$error_code] ) ? $error_codes[$error_code] : $error_code );
	return $output; 

}

// Format column name
// Форматирует заголовки столбцов, убирая неподходящие символы
function woo_bip_format_column( $column ) {

	$output = $column;
	$output = strtolower( $output );
	// Strip out any confusing characters
	$output = str_replace( ' - ', '_', $output );
	$output = str_replace( array( ' ', '-' ), '_', $output );
	$output = str_replace( array( ':', '(', ')' ), '', $output );
	return $output;

}

// Formatting cell values for preview
// Форматируем содержимое ячейки для просмотра
function woo_bip_format_cell_preview( $output = '', $key = '', $cell = '' ) {

	global $import;

	$matches = array(
		'image',
		'product_gallery',
        'brands',
		'category',
		'tag'
	);
	foreach( $matches as $match ) {
		if( strpos( strtolower( $cell ), $match ) !== false ) {
			if( !empty( $output ) ) {
				$output = str_replace( $import->category_separator, "<br />", $output ). "<br />";
				return $output;
			}
		}
	}
	return $output;

}

// Prepare column values for import
// Подготовка содержимого столбцов для импорта
function woo_bip_prepare_columns( $value_data = array() ) {

	global $import;

	if( !$value_data )
		$value_data = $_POST['value_name'];

	if( $value_data ) {
		$csv_data = array();
		foreach( $value_data as $key => $value ) {
            if( isset( $import->csv_data[$value] ) ) {
                $name = woo_bip_get_column_name($key);
                woo_bip_update_option($name, $value);
                $csv_data[$name] = $import->csv_data[$value];
            }
		}
	}
	unset( $import->csv_data );


	$import->rows = 0;
	if( woo_bip_validate_columns( $csv_data ) ) {
		$import->cancel_import = true;
		$import->log .= ' ' . __( 'import file column validation failed', 'woo_bip' );
		$import->log .= "<br /><br />" . __( 'Import cancelled', 'woo_bip' );
		unset( $csv_data );
		return false;
	} else {
		$import->log .= ' ' . __( 'sufficient data was provided', 'woo_bip' );
		$import->log .= "<br />" . __( 'Beginning import...', 'woo_bip' );
	}
	if( WOO_BIP_DEBUG == true )
		$import->log .= "<br /><br />*** " . __( 'PID debugging mode is enabled, no record changes will be made till WOO_BIP_DEBUG is de-activated from product-importer.php on line #22', 'woo_bip' ) . " ***";

	$import->log .= "<br /><br />" . __( 'Detect and group Product columns...', 'woo_bip' );

	$import->headers = array();
	if( isset( $csv_data['sku'] ) ) {
		$import->headers[] = 'sku';
		$import->csv_sku = array_filter( $csv_data['sku'] );
		$import->rows = count( $import->csv_sku );
		$import->log .= "<br />>>> " . __( 'SKU has been detected and grouped', 'woo_bip' );
	}
	if( isset( $csv_data['name'] ) ) {
		$import->headers[] = 'name';
		$import->csv_name = array_filter( $csv_data['name'] );
		array_walk_recursive( $import->csv_name, 'woo_bip_prepare_columns_filter' );
		// Use Product ID or SKU row count if it is higher
		if( $import->rows < count( $import->csv_name ) )
			$import->rows = count( $import->csv_name );
		$import->log .= "<br />>>> " . __( 'Product Name has been detected and grouped', 'woo_bip' );
	}
	if( $import->rows == 0 ) {
		$import->cancel_import = true;
		$import->failed_import = __( 'The SKU or Product Name column depending on the import method chosen must be selected to process an import.', 'woo_bip' );
	}
	if( isset( $csv_data['description'] ) ) {
		$import->headers[] = 'description';
		$import->csv_description = array_filter( $csv_data['description'] );
		array_walk_recursive( $import->csv_description, 'woo_bip_prepare_columns_filter' );
		$import->log .= "<br />>>> " . __( 'Description has been detected and grouped', 'woo_bip' );
	}
	if( isset( $csv_data['supplier_price'] ) ) {
		$import->headers[] = 'supplier_price';
		$import->csv_supplier_price = array_filter( $csv_data['supplier_price'] );
		$import->log .= "<br />>>> " . __( 'Price has been detected and grouped', 'woo_bip' );
	}
    if( isset( $csv_data['brands'] ) ) {
        $import->headers[] = 'brands';
        $import->csv_brands = array_filter( $csv_data['brands'] );
        $import->log .= "<br />>>> " . __( 'Brands has been detected and grouped', 'woo_bip' );
    }
    if( isset( $csv_data['supplier_code'] ) ) {
        $import->headers[] = 'supplier_code';
        $import->csv_supplier_code = array_filter( $csv_data['supplier_code'] );
        $import->log .= "<br />>>> " . __( 'Supplier Code has been detected and grouped', 'woo_bip' );
    }
    if( isset( $csv_data['warranty'] ) ) {
        $import->headers[] = 'warranty';
        $import->csv_warranty = array_filter( $csv_data['warranty'] );
        $import->log .= "<br />>>> " . __( 'Warranty has been detected and grouped', 'woo_bip' );
    }
	if( isset( $csv_data['category'] ) ) {
		$import->headers[] = 'category';
		$import->csv_category = array_filter( $csv_data['category'] );
		$import->log .= "<br />>>> " . __( 'Category has been detected and grouped', 'woo_bip' );
        $import->headers[] = 'tag';
        $import->csv_tag = woo_bip_tag_array();
        $import->log .= "<br />>>> " . __( 'Tag has been detected and grouped', 'woo_bip' );
	}
    if( isset( $csv_data['product_url'] ) ) {
        $import->headers[] = 'product_url';
        $import->csv_product_url = array_filter( $csv_data['product_url'] );
        $import->log .= "<br />>>> " . __( 'Product URL has been detected and grouped', 'woo_bip' );
    }

	// All in One SEO Pack integration
    // Интеграция с плагином All in One SEO Pack
	if( isset( $csv_data['aioseop_keywords'] ) ) {
        $import->headers[] = 'aioseop_keywords';
        $import->csv_aioseop_keywords = array_filter( $csv_data['aioseop_keywords'] );
		$import->log .= "<br />>>> " . __( 'All in One SEO Pack - Keywords has been detected', 'woo_bip' );
	}
	if( isset( $csv_data['aioseop_description'] ) ) {
        $import->headers[] = 'aioseop_description';
        $import->csv_aioseop_description = array_filter( $csv_data['aioseop_description'] );
		$import->log .= "<br />>>> " . __( 'All in One SEO Pack - Description has been detected', 'woo_bip' );
	}
	if( isset( $csv_data['aioseop_title'] ) ) {
        $import->headers[] = 'aioseop_title';
        $import->csv_aioseop_title = array_filter( $csv_data['aioseop_title'] );
		$import->log .= "<br />>>> " . __( 'All in One SEO Pack - Title has been detected', 'woo_bip' );
	}
	if( isset( $csv_data['aioseop_titleatr'] ) ) {
        $import->headers[] = 'aioseop_titleatr';
        $import->csv_aioseop_titleatr = array_filter( $csv_data['aioseop_titleatr'] );
		$import->log .= "<br />>>> " . __( 'All in One SEO Pack - Title Attributes has been detected', 'woo_bip' );
	}
	if( isset( $csv_data['aioseop_menulabel'] ) ) {
        $import->headers[] = 'aioseop_menulabel';
        $import->csv_aioseop_menulabel = array_filter( $csv_data['aioseop_menulabel'] );
		$import->log .= "<br />>>> " . __( 'All in One SEO Pack - Menu Label has been detected', 'woo_bip' );
	}

}

// Filter column values for import
// Фильтрация значений столбцов для импорта
function woo_bip_prepare_columns_filter( $var = null ) {

	$var = filter_var( $var, FILTER_SANITIZE_ENCODED );
	return $var;

}

// An early validation check of required columns based on the import method
// Ранняя проверка наличия необходимых колонок, в зависимости от метода импорта
function woo_bip_validate_columns( $csv_data = array() ) {

	global $import;

	$status = false;
	if( $import->import_method == 'new' ) {
		// Create new Product - Requires Product Name
		if( !isset( $csv_data['name'] ) )
			$status = true;
	}
	if( $status ) {
		$failed_reason = array();
		if( $import->import_method == 'new' ) {
			// Create new Product
			if( !isset( $csv_data['product_title'] ) )
				$failed_reason[] = __( 'You must provide a minimum of Product Title to create new Products, hit Return to options screen to assign this column', 'woo_bip' );
		}
		if( empty( $failed_reason ) )
			$failed_reason[] = __( 'No specific reason was given for why the provided columns from this import file could not be validated, please raise this as a Premium Support issue with our team :)', 'woo_bip' );
		$size = ( count( $failed_reason ) - 1 );
		for( $i = 0; $i <= $size; $i++ ) {
			if( $failed_reason[$i] ) {
				$import->loading_text = __( 'Import validation failed', 'woo_bip' );
				$import->failed_import = sprintf( __( 'Import validation issue: %s', 'woo_bip' ), $failed_reason[$i] );
			}
		}
		return true;
	}

}

// Update Product
// Обновляем запись о товаре
function woo_bip_force_update_post( $post_id = 0, $column = '', $value = '' ) {

	global $wpdb;

	if( $post_id && !empty( $column ) ) {
		$wpdb->show_errors = true;
		$response = $wpdb->update( $wpdb->posts, array(
			($column) => $value
		), array( 'ID' => $post_id ), array(
			'%s'
		) );
		if( $response !== false )
			return true;
	}

}

// Detect delimiter in file CSV
// Определяет разделитель, используемый в файле CSV
function woo_bip_detect_file_delimiter( $row = '' ) {

	$delimiters = array(
		'semicolon' => ";",
		'tab'       => "\t",
		'comma'     => ",",
	);
	$count = array();
	foreach( $delimiters as $key => $delimiter )
		$count[$key] = substr_count( $row, $delimiter );
	arsort( $count );
	reset( $count );
	$first_key = key( $count );
	return $delimiters[$first_key]; 

}

// Deletes the temporary CSV file used at import time
// Удаление временного CSV файла, используемого при импорте
function woo_bip_delete_file() {

	global $import;

	switch( $import->delete_file ) {

		case '1':
			// Delete CSV from /wp-content/uploads/
			if( $file = woo_bip_get_option( 'csv' ) ) {
				if( file_exists( $file ) )
					@unlink( $file );
			}
			if( $import->advanced_log )
				$import->log .= "<br /><br />" . __( 'Temporary CSV deleted', 'woo_bip' );
			break;

		case '0':
		default:
			// Add CSV to Past Imports
			if( $file = woo_bip_get_option( 'csv' ) )
				woo_bip_add_past_import( $file );
			break;

	}
	woo_bip_update_option( 'csv', '' );

}

// Returns the current memory usage at that moment
// Возвращает текущее значение размера используемой памяти
function woo_bip_current_memory_usage( $echo = false ) {

	$output = sprintf( '%d Mb / %d Mb', round( memory_get_usage( true ) / 1024 / 1024, 2 ), (int)ini_get( 'memory_limit' ) );
	if( $echo )
		echo $output;
	else
		return $output;

}

// Finds the position of the first occurrence of a string inside another string
// Возвращает позицию первого вхождения подстроки
function woo_bip_strposa( $haystack, $needles = array(), $offset = 0 ) {

	$chr = array();
	foreach( $needles as $needle ) {
		$res = strpos( $haystack, $needle, $offset );
		if( $res !== false )
			$chr[$needle] = $res;
	}
	if( empty( $chr ) )
		return false;
	return min( $chr );

}

// Display elapsed time
// Показываем оставшееся время
function woo_bip_display_time_elapsed( $from = 0, $to = 0, $output = '-' ) {

	if( $from && $to ) {
		$output = __( '1 second', 'woo_bip' );
		$time = $to - $from;
		$tokens = array (
			31536000 => __( 'year', 'woo_bip' ),
			2592000 => __( 'month', 'woo_bip' ),
			604800 => __( 'week', 'woo_bip' ),
			86400 => __( 'day', 'woo_bip' ),
			3600 => __( 'hour', 'woo_bip' ),
			60 => __( 'minute', 'woo_bip' ),
			1 => __( 'second', 'woo_bip' )
		);
		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			$output = $numberOfUnits . ' ' . $text . ( ( $numberOfUnits > 1 ) ? 's' : '' );
		}
	}
	return $output;

}

// Get saved option value
// Получаем значение сохраненной опции
function woo_bip_get_option( $option = null, $default = false, $allow_empty = false ) {

	$output = '';
	if( isset( $option ) ) {
		$separator = '_';
		$output = get_option( WOO_BIP_PREFIX . $separator . $option, $default );
		if( $allow_empty == false && $output != 0 && ( $output == false || $output == '' ) )
			$output = $default;
	}
	return $output;

}

// Update option value
// Обновляем значение опции
function woo_bip_update_option( $option = null, $value = null ) {

	$output = false;
	if( isset( $option ) && isset( $value ) ) {
		$separator = '_';
		$output = update_option( WOO_BIP_PREFIX . $separator . $option, $value );
	}
	return $output;

}

// Return column name by order key
// Возвращаем имя колонки по порядковому номеру
function woo_bip_get_column_name( $key ) {

    global $import;

    $output = '';
    $import->options = woo_bip_product_fields();
    $import->options_size = count( $import->options );

    for( $k = 0; $k < $import->options_size; $k++ ) {
        if ($key == $k) {
            $output = $import->options[$k]['name'];
            return $output;
        }
    }

    return $output;

}

// Recalculating prices
// Пересчитываем цены
function woo_bip_update_prices( $rate, $margin ) {

    global $wpdb;

    $db_update_sql = $wpdb->prepare( "UPDATE " . $wpdb->postmeta . " AS meta1 INNER JOIN " . $wpdb->postmeta . " AS meta2 ON meta1.post_id=meta2.post_id"
    . " AND meta1.meta_key='_price' AND meta2.meta_key='supplier_price'"
        . " SET meta1.meta_value=meta2.meta_value* %d * (100 + %d) / 100", $rate, $margin);
    $wpdb->get_results( $db_update_sql );
    $wpdb->flush();
    $db_update_sql = $wpdb->prepare( "UPDATE " . $wpdb->postmeta . " AS meta1 INNER JOIN " . $wpdb->postmeta . " AS meta2 ON meta1.post_id=meta2.post_id"
        . " AND meta1.meta_key='_regular_price' AND meta2.meta_key='supplier_price'"
        . " SET meta1.meta_value=meta2.meta_value* %d * (100 + %d) / 100", $rate, $margin);
    $wpdb->get_results( $db_update_sql );
    $wpdb->flush();

}

// Recalculating sale prices
// Пересчитываем цены со скидкой
function woo_bip_update_sale_prices( $rate, $margin, $count_discount ) {

    global $wpdb;

    $prices = $wpdb->get_results("SELECT t1.post_id, t1.meta_value FROM {$wpdb->prefix}postmeta AS t1, {$wpdb->prefix}postmeta AS t2"
    ." WHERE t1.meta_key='_regular_price' AND t2.meta_key='_price' AND t1.post_id=t2.post_id AND t1.meta_value<>t2.meta_value");
    $wpdb->flush();
    foreach ($prices as $price){
        update_post_meta($price['post_id'], '_price', $price['meta_value']);
    }

    $wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_sale_price' ) );

    $enable_discount = woo_bip_get_option( 'enable_discount' );
    if ($enable_discount == '1') {
        $products = $wpdb->get_col("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='supplier_price'");
        $wpdb->flush();
        $cnt_products = count($products);
        for ($z = 1; $z <= $count_discount; $z++) {

            $id = rand(0,$cnt_products-1);
            $product_id = $products[$id];
            $supplier_price = get_post_meta($product_id, 'supplier_price', true);
            $sale_price = $supplier_price * $rate * (100 + $margin) / 100;
            add_post_meta($product_id, '_sale_price', $sale_price);
            update_post_meta($product_id, '_price', $sale_price);

        }
    }
    delete_transient( 'wc_products_onsale' );
}

// Generating tags array
// Генерируем массив меток
function woo_bip_tag_array() {

    global $import;

    $tags = array();
    $size = count( $import->csv_category );
    if( $import->skip_first == 1 )
        $i = 1;
    else
        $i = 0;
    for( ; $i < $size; $i++ )
        $tags[$i] = $import->csv_category[$i] . '|' . $import->csv_brands[$i];

        return $tags;
}
?>