<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Estimated_Delivery {

	public function __construct() {

		add_filter( 'appmaker_wc_product_tabs', array( $this, 'estimated_delivery_tabs' ), 2, 1 );
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'estimated_delivery_widget' ), 2, 2 );      
		
    }
    
    public function estimated_delivery_tabs( $tabs ) {

        global $product;
        
        if(!empty($product)){                      
 
              $tabs['pi-edd-product'] = array(
                  'title'    => __( 'Note', 'appmaker-woocommerce-mobile-app-manager' ),
                  'priority' => 2,
                  'callback' => 'woocommerce_product_description_tab',
              );                     
  
          }
      
      return $tabs; 
    }

    public function estimated_delivery_widget( $return, $product_local ) {
     
        global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;
        
		$tabs    = apply_filters( 'woocommerce_product_tabs', array() );
        $tabs    = apply_filters( 'appmaker_wc_product_tabs', $tabs );       
        $content = '';

        $pi_Edd = new Pi_Edd();
        $plugin_name = $pi_Edd->get_plugin_name();
        $version =  $pi_Edd->get_version();
        $plugin_public = new Pi_Edd_Public(  $plugin_name, $version );       
        $plugin_public->initialize();
         //$plugin_public->user_selection();
        // echo $plugin_public->delivery_estimate;
        // $plugin_public->estimated_date = date($plugin_public->calc_date_format, strtotime(' + '.$plugin_public->delivery_estimate.' days'));

        ob_start();
        $plugin_public->estimate_on_product_page();
        $content  = ob_get_clean();
        
        foreach($tabs as $key => $tab){

            if(!empty($content)){
                //$title   = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_'.$key );
                if ( 'pi-edd-product' === $key ) { 
                    $return['pi-edd-product'] = array(
						'type'       => 'menu',
						'expandable' => false,
						'expanded'   => false,
						'title'      => strip_tags(html_entity_decode($content)),
						'content'    => '',
                        'action'     => array('type' => 'NO_ACTION','params' => array()),
                    );
                }       
            }else {
                unset($return['pi-edd-product']);
            }
                
        }        
        
		return $return;
    }
}
new APPMAKER_WC_Estimated_Delivery();


