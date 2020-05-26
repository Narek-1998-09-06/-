<?php				
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// PROCESS 
function woopei_processData(){
	
	if($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')  && !isset($_POST['finalupload']) ){
	
		check_admin_referer( 'excel_upload' );
		check_ajax_referer( 'excel_upload' );	
				
		$filename = $_FILES["file"]["tmp_name"];
		
		if($_FILES["file"]["size"] > 0 ){
		IF($_FILES["file"]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
			//INCLUDE phpExcel
			include( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');

			try {
				$objPHPExcel = PHPExcel_IOFactory::load($filename);
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($filename,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$data = count($allDataInSheet);  // Here get total count of row in that Excel sheet
				
			$rownumber=1;
			$row = $objPHPExcel->getActiveSheet()->getRowIterator($rownumber)->current();
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
		
			 print "<div style='overflow:hidden;min-height:400px;width:100%;'>
			 <form method='POST' style='overflow:hidden;min-height:400px;width:100%;' id ='woo_process' action= ".admin_url( 'admin.php?page=woo-product-importer' ).">";
			 print "<p style='font-style:italic'>DATA MAPPING: Drag and drop excel columns on the right to product properties on the left.</p>";
				print "<div style='float:right;width:50%'>";
					print "<h3>EXCEL COLUMNS</h3><p>";
					foreach ($cellIterator as $cell) {
						//getValue
						echo "<input type='button' class='draggable' style='min-width:200px' key ='".sanitize_text_field($cell->getColumn())."' value='". sanitize_text_field($cell->getValue()) ."' />  <br/>";
					}				
				print "</p></div>";
				print "<div style='float:left;width:50%'>";
				
				?>
								<p class='hideOnUpdateById'>
									<input type='checkbox' name='selectparent' id='selectparent' value='yes'  /> <b> <?php esc_html_e( 'Select Parent Categories as well ', 'wpeie' ) ?></b>
								</p>				
				
				<?php
				print "<h3>PRODUCT FIELDS</h3>";
				
				echo "<p>AUTHOR ID  <input type='text' name='post_author' required readonly class='droppable' placeholder='Drop here column' /></p>";
				echo "<p>URL  <input type='text' name='post_name' required readonly class='droppable' placeholder='Drop here column' /></p>";				
				
				echo "<p>TITLE  <input type='text' name='post_title' required readonly class='droppable' placeholder='Drop here column' /></p>";
				echo "<p>DESCRIPTION <input type='text' name='post_content' required readonly class='droppable' placeholder='Drop here column'  /></p>";
				echo "<p><b>Short DES. - EXCERPT </b><input type='text' name='post_excerpt' required readonly class='droppable' placeholder='Drop here column'  /></p>";				
				$post_meta=array('_sku','_weight','_regular_price','_sale_price','_stock');
				foreach($post_meta as $meta){
					echo "<p>".strtoupper(str_replace('_',' ',esc_attr($meta) ))." <input type='text' style='min-width:200px' name='".esc_attr($meta)."' required readonly class='droppable' placeholder='Drop here column'  /></p>";
				}
				echo "<p>IMAGE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
				echo "<p>IMAGE GALLERY <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
				echo "<p><b>VIRTUAL </b><input type='text' style='min-width:200px' name='_virtual' required readonly class='droppable' placeholder='Downloadable Product'  /></p>";
				echo "<p>DOWNLOADABLE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
				echo "<p>PURCHASE NOTE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
				echo "<p>UPSELL IDS<input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";				
				echo "<p>CROSELL IDS <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";
				echo "<p>TAXABLE <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";	
				echo "<p>TAX CLASS <input style='border:1px solid red;background:#ccc;' type='text' style='min-width:200px' name='image' required readonly class='' placeholder='Premium Version Only'  /></p>";					
				print "<h3>CATEGORY AND TAGS</h3>";
				$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );			
				foreach( $taxonomy_objects as $voc){
					//ADDITION : INCLUDE ONLY PRODUCT CATEGORY AND TAGS NOT CUSTOM TAXONOMIES
					if($voc->name == 'product_tag' ||  $voc->name == 'product_cat' ){
						echo "<p>". strtoupper(str_replace('_',' ',esc_attr($voc->name))). " <input type='text' style='min-width:200px' name='".esc_attr($voc->name)."' required readonly class='droppable' placeholder='Drop here column' key /></p>";
					}
				}
				echo "<p>Custom Taxonomy <input type='text' name='custom_tax' style='border:1px solid red;background:#ccc;' readonly  placeholder='Premium Version Only'  /></p>";	
				echo "<p>Product Attributes <input type='text' name='product_attr' style='border:1px solid red;background:#ccc;' readonly  placeholder='Premium Version Only'  /></p>";				
				print "<input type='hidden' name='finalupload' value='1' />";
				wp_nonce_field('excel_process','secNonce');
				submit_button('Upload','primary','check');
				print "</div>";				
			print "</form></div>";
			
			move_uploaded_file($_FILES["file"]["tmp_name"], plugin_dir_path( __FILE__ ).'import.xlsx');

		} else   "<h3>". _e('Invalid File:Please Upload Excel File')."</h3>";	
		}
	}
	
	if(!empty($_POST['finalupload']) && current_user_can('administrator')){
			
		check_admin_referer( 'excel_process','secNonce' );
		check_ajax_referer( 'excel_process' ,'secNonce');				

		$filename = plugin_dir_path( __FILE__ ).'import.xlsx';

		include( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');
		$objPHPExcel = PHPExcel_IOFactory::load($filename);
		$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$data = count($allDataInSheet);  // Here get total count of row in that Excel sheet		

		if(!empty($_POST['post_title'])){
			
			for($i=2;$i<=$data;$i++){
									
				//SANITIZE AND VALIDATE title and description				
				$title = sanitize_text_field($allDataInSheet[$i][$_POST['post_title']]);
				if(!empty($allDataInSheet[$i][$_POST['post_content']])){
					$content = sanitize_text_field($allDataInSheet[$i][$_POST['post_content']]);
				}else $content='';
				
				if(!empty($allDataInSheet[$i][$_POST['post_excerpt']])){
					$excerpt = sanitize_text_field($allDataInSheet[$i][$_POST['post_excerpt']]);
				}else $excerpt='';
				
				if(!empty($allDataInSheet[$i][$_POST['post_excerpt']])){
					$author = sanitize_text_field($allDataInSheet[$i][$_POST['post_author']]);
				}else $author='';
				if(!empty($allDataInSheet[$i][$_POST['post_name']])){
					$url = sanitize_title_with_dashes($allDataInSheet[$i][$_POST['post_name']]);
				}else $url='';

				//check if post exists
					if(post_exists($title)===0){
						$post = array(
							'post_author'   => $author,
							'post_title'   => $title,
							'post_content' => $content,
							'post_status'  => 'publish',
							'post_excerpt' => $excerpt,
							'post_name'    => $url,
							'post_type'    => 'product'
						 );	
						$id = wp_insert_post( $post);						 
						print "<p><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> created.</p>";			
					}else{
						//update 
						$id = post_exists($title);
							if($content !='' ){ // if column selected update, otherwise dont update
							$post = array(
								'ID' 		   => $id,
								'post_author'   => $author,
								'post_title'   => $title,
								'post_content' => $content,
								'post_status'  => 'publish',
								'post_excerpt' => $excerpt,
								'post_name'   => $url,
								'post_type'    => 'product'
							 );									
							}else{
							$post = array(
								'ID' 		   => $id,
								'post_author'   => $author,
								'post_title'   => $title,
								'post_name'   => $url,
								'post_status'  => 'publish',
								'post_excerpt' => $excerpt,
								'post_type'    => 'product'
							 );	
							}
							wp_update_post($post);
							print "<p><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> already exists. Updated.</p>";
					}

					//IMPORT - UPDATE POST META
					
					//SANITIZE AND VALIDATE meta data
					if(isset($allDataInSheet[$i][$_POST['_sale_price']])){
						$sale_price = sanitize_text_field($allDataInSheet[$i][$_POST['_sale_price']]);					
						if ( !$sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
						  $sale_price = '';
						  print "For sale price of {$title} you need numbers entered.<br/>";
						}

						if ( $sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
							update_post_meta( $id, '_sale_price', $sale_price );
						}
						
					}
					
					if(isset($allDataInSheet[$i][$_POST['_regular_price']])){
						$regular_price = sanitize_text_field($allDataInSheet[$i][$_POST['_regular_price']]);
						if ( !$regular_price  && !empty($allDataInSheet[$i][$_POST['_regular_price']])) {
						  $regular_price = '';
						  print "For regular price of {$title} you need numbers entered.<br/>";
						}else update_post_meta( $id, '_regular_price', $regular_price );						
					}
					
					//ADDITION : IF SALE PRICE IS EMPTY PRICE WILL BE EQUAL TO REGULAR PRICE
					if(isset($allDataInSheet[$i][$_POST['_sale_price']])){
						if ( $sale_price  && !empty($allDataInSheet[$i][$_POST['_sale_price']]) ) {
							update_post_meta( $id, '_price', $sale_price );
						}				
					}elseif(isset($allDataInSheet[$i][$_POST['_regular_price']])){
						update_post_meta( $id, '_price', $regular_price );
					}
					
					if(isset($allDataInSheet[$i][$_POST['_sku']])){
						$sku = sanitize_text_field($allDataInSheet[$i][$_POST['_sku']]);					
						if ( !$sku && !empty($_POST['_sku']) ) {
						  $sku = '';
						  print "For sku of {$title} you need numbers entered.<br/>";
						}else update_post_meta( $id, '_sku', $sku );						
					}
					
					if(isset($allDataInSheet[$i][$_POST['_weight']])){
						$weight = sanitize_text_field($allDataInSheet[$i][$_POST['_weight']]);					
						if ( !$weight  && !empty($_POST['_weight']) ) {
						  $weight = '';
						  print "For weight of {$title} you need numbers entered.<br/>";
						}else update_post_meta( $id, '_weight',$weight );						
					}

					if(isset($allDataInSheet[$i][$_POST['_stock']])){

						$stock = sanitize_text_field($allDataInSheet[$i][$_POST['_stock']]);					
						if ( !$stock  && !empty($_POST['_stock']) ) {
						  $stock = '';
						  print "For stock of {$title} you need numbers entered.<br/>";
						}else update_post_meta( $id, '_stock', $stock );	

						if( !empty($stock) ){
							update_post_meta( $id, '_stock_status', 'instock');
							update_post_meta( $id, '_manage_stock', 'yes');
						}						
					}
					
					if(isset($allDataInSheet[$i][$_POST['_virtual']])){
						$virtual = sanitize_text_field($allDataInSheet[$i][$_POST['_virtual']]);					
						if ( !$virtual  && !empty($_POST['_virtual']) ) {
						  $virtual = '';					  
						}else update_post_meta( $id, '_virtual', $virtual );						
					}

					update_post_meta( $id, '_visibility', 'visible' );

					
					wc_delete_product_transients( $id );
					//TAXONOMIES
					
					$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );			
					foreach( $taxonomy_objects as $voc){
						if($voc->name === 'product_tag' ||  $voc->name === 'product_cat' ){
							if(isset($allDataInSheet[$i][$_POST[$voc->name]])){
									$taxToImport =  explode(',',sanitize_text_field($allDataInSheet[$i][$_POST[$voc->name]]));
									foreach($taxToImport as $taxonomy){
										wp_set_object_terms( $id,$taxonomy,$voc->name,true); //true is critical to append the values
										
										// GET ALL ASSIGNED TERMS AND ADD PARENT FOR PRODUCT_CAT TAXONOMY!!! 
										if($_POST['selectparent']){
											$terms = wp_get_post_terms($id, $voc->name );
											foreach($terms as $term){
												while($term->parent != 0 && !has_term( $term->parent, sanitize_text_field($voc->name), $post )){
													// move upward until we get to 0 level terms
													wp_set_object_terms($id, array($term->parent), sanitize_text_field($voc->name), true);								
													$term = get_term($term->parent, esc_attr($voc->name));
												}
											}								
										} 									
									}
							}
						}			
					}// end for each taxonomy																	
			}
			unlink($filename);
		}else print "<h3 style='color:red' >No title selected for your products.</h3>";
		
	}	
}

 
?>