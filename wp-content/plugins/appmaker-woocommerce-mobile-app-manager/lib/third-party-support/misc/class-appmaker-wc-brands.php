<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Brands {

	public function __construct() {
		add_filter( 'appmaker_wc_product_widgets', array( $this, 'brand_product_list' ), 10, 3 );	
		add_filter( 'appmaker_wc_product_filters', array( $this, 'brand_filter' ), 10, 3 );	
	}	

	public function brand_filter( $return ) {
		$brands_list = get_terms( 'product_brand', array(
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => true,
		) );

		$brands_list = get_terms( 'product_brand' );

		if ( ! empty( $brands_list ) && is_array( $brands_list ) ) {
			$return['items']['product_brand'] = array(
				'id'     => 'product_brand',
				'type'   => 'checkbox',
				'label'  => 'Brands',
				'values' => array(),
			);

			foreach ( $brands_list as $term ) {
				$return['items']['product_brand']['values'][] = array(
					'label' => strip_tags( html_entity_decode( $term->name ) ),
					'value' => $term->slug,
				);
			}
		}

		return $return;
	}

	/**
	 * @param $return
	 * @param WC_Product $product
	 * @param $data
	 *
	 * @return mixed
	 */
	public function brand_product_list( $return, $product, $data ){
        
        //if ( is_singular( 'product' ) ) {			
          $terms = get_the_terms( $product->get_id(), 'product_brand' );          
         // $brand_count = is_array( $terms ) ? sizeof( $terms ) : 0;
         // $taxonomy = get_taxonomy( 'product_brand' );
         // $labels   = $taxonomy->labels;       
        if(! empty($terms)){
			foreach ( $terms as $term ) {

				/*$posts = get_posts(
					array(
						'post_type'     => 'product',
						'numberposts'   => -1,
						'post_status'   => 'publish',
						'fields'        => 'ids',
						'no_found_rows' => true,
						'tax_query'     => array(
	
							'relation' => 'AND',
							array(
								'taxonomy' => 'product_brand',
								'terms'    => $term,
								'field'    => 'id'
							)
						)
							));		*/
	
				$return['brand'] = array(
					'type'  => 'menu',
					'title' => 'Brand: '.strip_tags( html_entity_decode($term->name)),
			  
					'action' => array(
						'type'   => 'LIST_PRODUCT',
						'params' => array(
							'product_brand' => $term
						),
					)
				);
			}
		}
        
			
		return $return;
	}
}

new APPMAKER_WC_Brands();
