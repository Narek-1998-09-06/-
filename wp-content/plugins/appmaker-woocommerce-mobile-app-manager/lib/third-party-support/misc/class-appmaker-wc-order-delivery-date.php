<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Order_Delivery_Date
{
    public function __construct()
    {
       // add_action('appmaker_wc_before_checkout',array($this, 'checkout_fields_response'));
       add_filter('appmaker_wc_checkout_fields', array( $this,'checkout_fields_response_fix'), 10, 2 );
      add_filter('appmaker_wc_validate_checkout',array($this,'Validation_checkout'));
      add_filter('appmaker_wc_validate_checkout_review',array($this,'Validation_checkout'));
    }

    public function  Validation_checkout($return){        
        $date = $return['e_deliverydate'];
        preg_match("/([a-zA-z]{3}[\s][\d]{2}[\s][\d]{4}[\s][\d\:]+)[\s](.*)/i",$date,$new_date);
        $new_date = $new_date[1];
        $timezone = $new_date[2];
        $date = new DateTime($new_date, new DateTimeZone($timezone));
        $timestamp = $date->format('d-m-Y');        
        $date=strtotime($timestamp);

        global $orddd_weekdays;
        foreach ( $orddd_weekdays as $n => $day_name) {
            if( "checked" == get_option( $n ) ) {
               $selected_delivery_days[] = $orddd_weekdays[$n];              
            }
        }
        $day_selected = date('l',$date);       
        if(!in_array($day_selected,$selected_delivery_days)){
           return new wp_error('invalid_delivery_day', "Sorry, we won't be delivering on this date");          
        }


        if(isset($return['time_slot'])){

            $_POST['orddd_time_slot'] = $_REQUEST['orddd_time_slot'] = $return['time_slot'];
            if($date == strtotime(date('d-m-Y'))){
                $timezone = date_default_timezone_get();
                date_default_timezone_set($timezone);
                $time_format = get_option( 'orddd_delivery_time_format' );
                $time_format_to_show = ( $time_format == '1' ) ? 'h:i A' : 'H:i';
                $current_time = current_time($time_format_to_show);
                $current_time = strtotime($current_time);
                
                $time_slot = explode( '-',  $return['time_slot'] );
                if(is_array($time_slot)){
                    $time_slot_from = strtotime($time_slot[0]);
                    //$time_slot_to = strtotime($time_slot[1]);
                    $time_slot = $time_slot_from;
                }
    
                if ($current_time >= $time_slot) {
                    return new wp_error('invalid_time_slot', "Please select a valid slot");
                }
            }           
    
        }
        if ( get_option( 'orddd_lockout_date_after_orders' ) > 0 ) {
            $lockout_days_arr = array();
            $lockout_days = get_option( 'orddd_lockout_days' );
            if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != "null" ) {
                $lockout_days_arr = json_decode( get_option( 'orddd_lockout_days' ) );
            }
            foreach ( $lockout_days_arr as $k => $v ) {
                if ( $v->o >= get_option( 'orddd_lockout_date_after_orders' ) ) {
                    $lockout_days_str[] =  $v->d;
                }
            }
            if(!empty($lockout_days_str)){
                foreach($lockout_days_str as $key => $locked_date){
                    $lock_date = str_replace('-', '/', $locked_date);    
               
                if($date == strtotime($lock_date) ){

                    return new wp_error('locked_day', " Sorry, this date is not available for delivery ");           
                        

                }
                }
            }
        }       

        $return['e_deliverydate'] = date('d F, Y',$date);
        $_POST['e_deliverydate'] = $return['e_deliverydate'];
        $_POST['h_deliverydate'] = $timestamp;
        $_POST['orddd_minimumOrderDays'] = $timestamp;
        $_POST['orddd_min_date_set'] = $timestamp;
        
        return $return ;

    }
    public function get_delivery_intervals(){

        if ( get_option( 'orddd_enable_time_slot' ) == 'on' ) {
            $time_slot_str = get_option( 'orddd_delivery_time_slot_log' );
            $time_slots = json_decode( $time_slot_str, true );
            //$result = array ( __( "Select a time slot", "order-delivery-date" ) );
            $time_format = get_option( 'orddd_delivery_time_format' );
            $time_format_to_show = ( $time_format == '1' ) ? 'h:i A' : 'H:i';
            $options=array();
            if ( count( $time_slots ) >0 ) {
                if ($time_slots == 'null') {
                    $time_slots = array();
                }
                foreach ($time_slots as $k => $v) {
                    $from_time = $v['fh'].":".$v['fm'];
                    $ft =  date( $time_format_to_show, strtotime( $from_time ) );
                    if ( $v['th'] != 00 ){
                        $to_time = $v['th'].":".$v['tm'];
                        $tt = date( $time_format_to_show, strtotime( $to_time ) );
                        $key = $ft." - ".$tt;
                    } else {
                        $key = $ft;
                    }
                    $options[$key]=$key;
                    if(!empty($v['additional_charges'])){
                        $price = APPMAKER_WC_Helper::get_display_price($v['additional_charges']);
                       $options[$key]=$key.'('.$price.')';
                    }
                }
            }
          return $options;

        }else {
            $from = get_option('orddd_delivery_from_hours');
            $from = $from . date(':i');
            $from = (strtotime($from));
            $to = get_option('orddd_delivery_to_hours');
            $to = $to . date(':i');
            $to = strtotime($to);
            $return = array();
            do {
                $time = date('h:i A', $from);
                $return[$time] = $time;
                $from = strtotime("+15 minutes", $from);
            } while ($from <= $to);
            return $return;
        }
      
    }
        
    public function checkout_fields_response_fix( $return, $section )
    {
       // print_r(orddd_common::load_hidden_fields());exit;
       
        $additional_fields = array();
        $timezone = date_default_timezone_get();
            
            $gmt_offset = get_option('gmt_offset');
            $wp_timezone = get_option('timezone_string'); 
             
            if($wp_timezone){
                date_default_timezone_set($wp_timezone);
            }else if($gmt_offset){

                $offset = $gmt_offset*60*60;
                $abbrarray = timezone_abbreviations_list();
                foreach ($abbrarray as $abbr) {                
                        foreach ($abbr as $city) {                     
                                if ($city['offset'] == $offset) {                         
                                    date_default_timezone_set($city['timezone_id']);
                                }
                        }
                } 
            }else{
                date_default_timezone_set($timezone);
            }

       // $date = date('d-m-y');
        $min_date=date('d-m-Y', strtotime( ' + 1 days'));
        $cut_off_max_date = get_option('orddd_number_of_dates'); 
        $min_delivery_time = get_option('orddd_minimumOrderDays');        
        if( !empty( $min_delivery_time ) ) {
            //Min Date based on Delivery Hours start
             $delivery_days = round($min_delivery_time / 24);
             $min_date = date('d-m-Y');
             $min_date = date('d-m-Y', strtotime( $min_date.'+ ' . $delivery_days . ' days'));
        }       
        if( 'on' == get_option( 'orddd_enable_same_day_delivery' ) ) {

            $cut_off_hour = get_option( 'orddd_disable_same_day_delivery_after_hours' );
            $cut_off_minute = get_option( 'orddd_disable_same_day_delivery_after_minutes' );
            $cut_off_time = $cut_off_hour.":".$cut_off_minute;
            $cut_off_time = strtotime( $cut_off_time );        
            
            $current_time = current_time('H:i');
            $current_time = strtotime($current_time);

            if ($cut_off_time > $current_time) {
                $min_date=date('d-m-Y');
            }
                   
         } 
         if( 'on' == get_option( 'orddd_enable_next_day_delivery' ) ) {

            $cut_off_hour = get_option( 'orddd_disable_next_day_delivery_after_hours' );
            $cut_off_minute = get_option( 'orddd_disable_next_day_delivery_after_minutes' );
            $cut_off_time = $cut_off_hour.":".$cut_off_minute;
            $cut_off_time = strtotime( $cut_off_time );        
            
            $current_time = current_time('H:i');
            $current_time = strtotime($current_time);

            if ($cut_off_time < $current_time) {
                $min_date = date('d-m-Y', strtotime( $min_date.'+ 1 days'));
            }
                   
         } 

         if( 'on' != get_option( 'orddd_enable_same_day_delivery' ) && 'on' != get_option( 'orddd_enable_next_day_delivery' )) {
            //get min date from order delivery date plugin- calendar customisation html

            $load_hidden_fields_html = orddd_common::load_hidden_fields();
            preg_match( '/id="orddd_minimumOrderDays"\s+value="([0-9\-]{8,12})">/',  $load_hidden_fields_html, $min_date_array );
            if( ! empty($min_date_array[1] )){
                $min_date = $min_date_array[1];           
                $min_date = date('d-m-Y', strtotime($min_date));
            }
        }
         
         
        //  if( get_option( 'orddd_lockout_date_quantity_based' ) == 'on' ) {
	    //     $total_quantities = orddd_common::orddd_get_total_product_quantities();
	    // } else {
	    //     $total_quantities = 1;
        // } 
        // $available_date_quantities = get_option( 'orddd_lockout_date_after_orders' );
        
        // $existing_timeslots_arr = json_decode( get_option( 'orddd_delivery_time_slot_log' ) );
       //echo $min_date;
        if ( get_option( 'orddd_lockout_date_after_orders' ) > 0 ) {
            $lockout_days_arr = array();
            $lockout_days = get_option( 'orddd_lockout_days' );
            if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' && $lockout_days != "null" ) {
                $lockout_days_arr = json_decode( get_option( 'orddd_lockout_days' ) );
            }
            foreach ( $lockout_days_arr as $k => $v ) {
                if ( $v->o >= get_option( 'orddd_lockout_date_after_orders' ) ) {
                    $lockout_days_str[] =  $v->d;
                }
            }
           
            if(!empty($lockout_days_str)){

                foreach($lockout_days_str as $key => $locked_date){
                    $lock_date = str_replace('-', '/', $locked_date);    
                
                   if(strtotime($min_date) == strtotime($lock_date) ){
    
                    $min_date = date('d-m-Y', strtotime( $min_date.' + 1 days'));                   
                    
                   }               
                }     
            }
                   
        } 
       
//echo $min_date;exit;
       // print_r($locked_days_str);exit;
        
        if(!empty($cut_off_max_date)){
            $cut_off_max_date = $cut_off_max_date - 1;
            $max_date=date('d-m-Y', strtotime( $min_date.'+' . $cut_off_max_date . ' days'));
        }  

       // $result = array ( __( "Select a time slot", "order-delivery-date" ) );
        if ( $section === 'order' ) {
            $delivery_enabled = orddd_common::orddd_is_delivery_enabled();
            $is_delivery_enabled = 'yes';
            if ($delivery_enabled == 'no') {
                $is_delivery_enabled = 'no';
            }
            if ($is_delivery_enabled == 'yes') {
                $date_field_label = get_option( 'orddd_delivery_date_field_label' );
                if( '' == $date_field_label ) {
                    $date_field_label = 'Delivery Date';
                }
                $delivery_date_field_note = get_option('orddd_delivery_date_field_note');
                $additional_fields['e_deliverydate'] = array(
                    'type' => 'datepicker',
                    'label' => __( $date_field_label, 'order-delivery-date' ),
                    'required' => true,
                    'minDate' => $min_date,
                    'maxDate' => $max_date,
                    'placeholder' => 'Select date',
                    'default' => $min_date,
                    'description'=>  $delivery_date_field_note,

                );
                $validate_wpefield = false;
    				if (  get_option( 'orddd_time_slot_mandatory' ) == 'checked' ) {
    					$validate_wpefield = true;
                    }
                    if( is_cart() ) {
                        $custom_attributes = array( 'disabled'=>'disabled', 'style'=>'cursor:not-allowed !important;max-width:300px;' );
                    } else {
                        $custom_attributes = array( 'disabled'=>'disabled', 'style'=>'cursor:not-allowed !important;' );
                    }
                $time_field_label = get_option( 'orddd_delivery_timeslot_field_label' );
                if( '' == $time_field_label ) {
                    $time_field_label = 'Delivery Time';
                }
                $enable_delivery_time = get_option( 'orddd_enable_delivery_time' );
                $enable_delivery_time_slot = get_option( 'orddd_enable_time_slot');
                if($enable_delivery_time || $enable_delivery_time_slot ){

                    $additional_fields['time_slot'] = array(
                        'type' => 'select',
                        'label' => __( $time_field_label, 'order-delivery-date' ),
                        'required' =>$validate_wpefield,
                        'placeholder' => 'select time',
                        'options'=> $this->get_delivery_intervals(),
                        'custom_attributes' => $custom_attributes,
                    );
                }
                
            }
        }
        return array_merge( $additional_fields, $return );
    }
}
new APPMAKER_WC_Order_Delivery_Date();