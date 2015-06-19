<?php
function woo_bip_generate_brands() {

    global $wpdb, $import;

    @ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

        $i = 0;
    $base_brands = array();
    // Check if brands is empty
    // Проверяем если производители пуcтые
    if( !empty( $import->csv_brands ) ) {
        // Check if brands only contains a single header
        // Проверяем если производители содержат один заголовок
        $size = count( $import->csv_brands );
        if( $import->skip_first == 1 && $size == 1 ) {
            $import->log .= "<br />" . __( 'No Brands were provided', 'woo_bip' );
            return;
        }
        for( ; $i < $size; $i++ ) {
            if( isset( $import->csv_brands[$i] ) )
                $base_brands[] = $import->csv_brands[$i];
        }
        unset( $size );
        if( $import->skip_first == 1)
            $i = 1;
        else
            $i = 0;
        $term_taxonomy = 'brands';
        $term_taxonomy_pa = 'pa_brands';
        $size = count( $base_brands );
        $include_log = true;
        if( $size > 1000 ) {
            $import->log .= "<br />>>> " . sprintf( __( 'We have just processed and generated so many Product Brands that we couldn\'t actually show you it in real-time, ~%d to be precise', 'woo_bip' ), $size );
            $include_log = false;
        }
        for( ; $i < $size; $i++ ) {
            $brands = $base_brands[$i];
            if( $include_log )
                $import->log .= "<br />>>> " . sprintf( __( 'Brands: %s', 'woo_bip' ), trim( $brands ) );
            if( !term_exists( $brands, $term_taxonomy ) && ($import->brands_taxonomies == 1) )
                $term = wp_insert_term( $brands , $term_taxonomy );
            if( !term_exists( $brands, $term_taxonomy_pa )  && ($import->brands_attributes == 1) ) {
                $term = wp_insert_term($brands, $term_taxonomy_pa);
            }
            if( $include_log ) {
                if( isset( $term ) && $term )
                    $import->log .= "<br />>>>>>> " . sprintf( __( 'Created Brands: %s', 'woo_bip' ), trim( $brands ) );
                else
                    $import->log .= "<br />>>>>>> " . sprintf( __( 'Duplicate of Brands detected: %s', 'woo_bip' ), trim( $brands ) );
            }
            unset( $brands, $term );
        }
        unset( $size );
        $import->log .= "<br />" . __( 'Brands have been generated', 'woo_bip' );
    } else {
        $import->log .= "<br />" . __( 'No Brands were provided', 'woo_bip' );
    }

}

function woo_bip_process_brands() {

    global $wpdb, $product, $import;

    // Brands association
    // Ассоциация с производителем
    $product->brands_term_id = array();
    if( isset( $product->brands ) ) {
        $term_taxonomy = 'brands';
        $db_brands_sql = $wpdb->prepare( "SELECT terms.term_id FROM "
            . $wpdb->terms . " as terms, " . $wpdb->term_taxonomy . " as term_taxonomy WHERE terms.term_id = term_taxonomy.term_id"
            . " AND terms.name = %s AND term_taxonomy.taxonomy = %s", $product->brands, $term_taxonomy );
        $db_brands = $wpdb->get_var( $db_brands_sql );
        $wpdb->flush();
        $product->brands_term_id[] = $db_brands;

    }

}
?>