<?php

// Function for generation Product Tags
// Функция, создающая метки товара
function woo_bip_generate_tags() {

	global $wpdb, $import;

	// Check if Tags is empty
    // Проверяем если поле метки пуста
	if( !empty( $import->csv_tag ) ) {
		$size = count( $import->csv_tag );
		// Check if Tags only contains a single header
        // Проверяем если метка содержит только единичные заголовок
		if( $import->skip_first == 1 && $size == 1 ) {
			$import->log .= "<br />" . __( 'No Product Tags were provided', 'woo_bip' );
			return;
		}
		if( $import->skip_first == 1 )
			$i = 1;
		else
			$i = 0;
		$term_taxonomy = 'product_tag';
		$tags = array();
		for( ; $i < $size; $i++ ) {
			if( isset( $import->csv_tag[$i] ) ) {
				// Check if this cell contains multiple Tags
                // Проверяем если ячейка содержит множественные метки
				if( strpos( $import->csv_tag[$i], '|' ) ) {
					$tags_explode = explode( '|', $import->csv_tag[$i] );
					for( $j = 0; $j < count( $tags_explode ); $j++ ) {
						if( isset( $tags_explode[$j] ) && trim( $tags_explode[$j] ) !== '' )
							$tags[] = sanitize_text_field( $tags_explode[$j] );
					}
					unset( $tags_explode );
				} else {
					if( isset( $import->csv_tag[$i] ) && trim( $import->csv_tag[$i] ) !== '' )
						$tags[] = sanitize_text_field($import->csv_tag[$i] );
				}
			}
		}
		$size = count( $tags );
		$include_log = true;
		if( !empty( $tags ) ) {
			if( $size > 1000 ) {
				$import->log .= "<br />>>> " . sprintf( __( 'We have just processed and generated so many Product Tags that we couldn\'t actually show you it in real-time, ~%d to be precise', 'woo_bip' ), $size );
				$include_log = false;
			}
			// Generate Product Tags if they do not already exist
            // Генерируем метку товара если она не существует
			foreach( $tags as $tag ) {
				if( $include_log )
					$import->log .= "<br />>>> " . sprintf( __( 'Product Tag: %s', 'woo_bip' ), $tag );
				if( !term_exists( $tag, $term_taxonomy ) ) {
					if( WOO_BIP_DEBUG !== true )
						$response = wp_insert_term( $tag, $term_taxonomy );
					else
						$response = true;
					if( $include_log ) {
						if( !is_wp_error( $response ) ) {
							$import->log .= "<br />>>>>>> " . __( 'Created Product Tag', 'woo_bip' );
						} else {
							if( $import->advanced_log )
								$import->log .= "<br />>>>>>> " . sprintf( __( 'Error creating Product Tag - Error - %s', 'woo_bip' ), $response->get_error_message() );
							else
							$import->log .= "<br />>>>>>> " . __( 'Error creating Product Tag', 'woo_bip' );
						}
					}
				} else {
					if( $include_log )
						$import->log .= "<br />>>>>>> " . __( 'Duplicate of Product Tag detected', 'woo_bip' );
				}
				unset( $response );
			}
			$import->log .= "<br />" . __( 'Product Tags have been generated', 'woo_bip' );
		}
	} else {
		$import->log .= "<br />" . __( 'No Product Tags were provided', 'woo_bip' );
	}

}

function woo_bip_process_tags() {

	global $import, $product;

	$product->tag_term_id = array();
	if( isset( $product->tag ) ) {
		$term_taxonomy = 'product_tag';
		// Check if this cell contains multiple Tags
        // Проверяем если ячейка содержит множественные метки
		if( strpos( $product->tag, '|' ) ) {
			$tags = explode( '|', $product->tag );
			$size = count( $tags );
			for( $i = 0; $i < $size; $i++ ) {
				if( $tag = get_term_by( 'name', sanitize_text_field($tags[$i]), $term_taxonomy ))
					$product->tag_term_id[] = $tag->term_id;
				unset( $tag );
			}
			unset( $tags );
		} else {
			if( $tag = get_term_by( 'name', sanitize_text_field($product->tag), $term_taxonomy ))
				$product->tag_term_id[] = $tag->term_id;
			unset( $tag );
		}
	}

}
?>