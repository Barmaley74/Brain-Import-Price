<?php
function woo_bip_generate_categories() {

	global $wpdb, $import;

	@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

	$i = 0;
	$base_categories = array();
	// Check if Categories is empty
	// Проверяем если категории пуcтые
	if( !empty( $import->csv_category ) ) {
		// Check if Categories only contains a single header
		// Проверяем если категории содержат один заголовок
		$size = count( $import->csv_category );
		if( $import->skip_first == 1 && $size == 1 ) {
			$import->log .= "<br />" . __( 'No Categories were provided', 'woo_bip' );
			return;
		}
		for( ; $i < $size; $i++ ) {
				if( isset( $import->csv_category[$i] ) )
					$base_categories[] = $import->csv_category[$i];
		}
		unset( $size );
		if( $import->skip_first == 1)
			$i = 1;
		else
			$i = 0;
		$term_taxonomy = 'product_cat';
		$size = count( $base_categories );
            $include_log = true;
		if( $size > 1000 ) {
			$import->log .= "<br />>>> " . sprintf( __( 'We have just processed and generated so many Product Categories that we couldn\'t actually show you it in real-time, ~%d to be precise', 'woo_bip' ), $size );
			$include_log = false;
		}
		for( ; $i < $size; $i++ ) {
				$category = sanitize_text_field( $base_categories[$i]);
						if( $include_log )
							$import->log .= "<br />>>> " . sprintf( __( 'Category: %s', 'woo_bip' ), $category  );
						if( !term_exists( $category, $term_taxonomy ) )
							$term = wp_insert_term( $category  , $term_taxonomy );
						if( $include_log ) {
							if( isset( $term ) && $term )
								$import->log .= "<br />>>>>>> " . sprintf( __( 'Created Category: %s', 'woo_bip' ), $category );
							else
								$import->log .= "<br />>>>>>> " . sprintf( __( 'Duplicate of Category detected: %s', 'woo_bip' ),  $category ) ;
						}
				unset( $category, $term );
		}
		unset( $size );
		$import->log .= "<br />" . __( 'Categories have been generated', 'woo_bip' );
	} else {
		$import->log .= "<br />" . __( 'No Categories were provided', 'woo_bip' );
	}

}

function woo_bip_process_categories() {

	global $wpdb, $product, $import;

	// Category association
    // Ассоциация с категорей
	$product->category_term_id = array();
	if( isset( $product->category ) ) {
		$term_taxonomy = 'product_cat';
		$db_categories_sql = $wpdb->prepare( "SELECT terms.term_id FROM "
            . $wpdb->terms . " as terms, " . $wpdb->term_taxonomy . " as term_taxonomy WHERE terms.term_id = term_taxonomy.term_id"
            . " AND terms.name = %s AND term_taxonomy.taxonomy = %s", sanitize_text_field( $product->category), $term_taxonomy );
		$db_category = $wpdb->get_var( $db_categories_sql );
		$wpdb->flush();
        $product->category_term_id[] = $db_category;
	}

}
?>