<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class WooexportProducts{
	
	public $numberOfRows=1;
	public $keyword='';
	public $posts_per_page='';
	public $sale_price='';
	public $regular_price='';
	public $price_selector='';
	public $sale_price_selector='';
	public $sku='';
	public $offset='';

	public function exportProductsDisplay(){?>
		<h2>
			<?php _e( 'EXPORT SIMPLE PRODUCTS', 'wpeiePro' ) ?>
		</h2>
		<p>
			<i><?php _e( 'Important Note: always save the generated export file in xlsx format to a new excel for import use.', 'wpeiePro' ) ?></i>
		</p>
	   <div>	
			<?php  print "<div class='result'>". $this->exportProductsForm()."</div>"; ?>
	   </div>
	   <?php
	}
	
	public function exportProductsForm(){
	
		$query = new WP_Query( array(
			'post_type' => 'product',				
			'posts_per_page' => '-1',								
		) );
		if($query ->have_posts()){	
		?>
				<p class='exportToggler button button-secondary warning   btn btn-danger'><i class='fa fa-eye '></i> 
					<?php _e('Filter & Fields to Show', 'wpeiePro');?>
				</p>
				
				
				<form name='exportProductsForm' id='exportProductsForm' method='post' action= "<?php echo admin_url( 'admin.php?page=woo-product-importer&tab=exportProducts'); ?>" >	
					<table class='wp-list-table widefat fixed table table-bordered'>	
						<tr>
							<td class='premium'>
								<?php _e('Choose Taxonomy - PRO VERSION', 'wpeiePro');?>
							</td>
							<td></td>
						</tr>

						<tr>
							<td>
								<?php _e('Keywords', 'wpeiePro');?> 
							</td>
							<td>
								<input type='text' name='keyword'  id='keyword' placeholder='<?php _e('Search term', 'wpeiePro');?>'/>
							</td>
							<td></td><td></td>
						</tr>
						<tr>
							<td class='premium'><?php _e('SKU', 'wpeiePro');?> - <?php _e('PRO Version', 'wpeiePro');?></td> 
							<td class='premium'>
								<input type='text' name='sku' id='sku' disabled placeholder='<?php _e('by SKU - PRO', 'wpeiePro');?>'/>
							</td>
							<td></td><td></td>
						</tr>
						<tr>
							<td class='premium'>
								<?php _e('Regular Price', 'wpeiePro');?> - <?php _e('PRO Version', 'wpeiePro');?>
							</td>
							<td class='premium'>
								<input type='number' name='regular_price' disabled id='regular_price' placeholder='<?php _e('Regular Price - PRO', 'wpeiePro');?>'/>
							</td>
							<td class='premium'>
								<?php _e('Regular Price Selector', 'wpeiePro');?> - <?php _e('PRO Version', 'wpeiePro');?>
							</td>
							<td>
								<select name='price_selector' disabled id='price_selector'>
									<option value=">">></option>
									<option value=">=">>=</option>
									<option value="<="><=</option>
									<option value="<"><</option>
									<option value="==">==</option>
									<option value="!=">!=</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class='premium'>
								<?php _e('Sale Price', 'wpeiePro');?> - <?php _e('PRO Version', 'wpeiePro');?>
							</td>
							<td>
								<input type='number' name='sale_price' id='sale_price' disabled placeholder='<?php _e('Sale Price - PRO ', 'wpeiePro');?>'/>
							</td>
							
							<td class='premium'>
								<?php _e('Sale Price Selector', 'wpeiePro');?> - <?php _e('PRO Version', 'wpeiePro');?>
							</td>
							<td class='premium'>
								<select name='sale_price_selector' disabled  id='sale_price_selector' >
									<option value=">">></option>
									<option value=">=">>=</option>
									<option value="<="><=</option>
									<option value="<"><</option>
									<option value="==">==</option>
									<option value="!=">!=</option>						
								</select>	
							</td>
						</tr>

						<tr>
							<td>
							<?php _e('Limit Results', 'wpeiePro');?>
							</td>
							<td>
							<input type='number' min="1" max="100000" style='width:100%;'  name='posts_per_page' id='posts_per_page' placeholder='<?php _e('Number to display..', 'wpeiePro');?>' />
							</td>
							<input type='hidden' name='offset' style='width:100%;' id='offset' placeholder='<?php _e('Start from..', 'wpeiePro');?>' />
							<input type='hidden' name='start' /><input type='hidden' name='total' />
							
							<td></td><td></td>
						</tr>
						
					</table>
					
					<?php $taxonomy_objects = array('product_cat','product_tag'); ?>

					<table class='wp-list-table widefat fixed table table-bordered'>
						<legend>
							<h2>
								<?php _e('TAXONOMIES TO SHOW', 'wpeiePro');?> - <span class='premium'><?php _e('More in PRO Version', 'wpeiePro');?></span>
							</h2>
						</legend>
						
						<tr>
							<?php $cols = array();
							$checked = 'checked';
							foreach( $taxonomy_objects as $voc){
									
								print "<td>
								<input type='checkbox' class='fieldsToShow' ".$checked." name='toShow".esc_attr($voc)."' value='1'/>
								<label for='".str_replace('_',' ',esc_attr($voc))."'>". str_replace('_',' ',esc_attr($voc)). "</label>
								</td>";
								array_push($cols,esc_attr($voc));					
							}?>
						</tr>
					</table>
					
					
					<table class='wp-list-table widefat fixed table table-bordered'>
						<legend>
							<h2>
								<?php _e('FIELDS TO SHOW', 'wpeiePro');?> - <span class='premium'><?php _e('More in PRO Version', 'wpeiePro');?></span>
							</h2>
						</legend>
						<?php
						$cols = array("title","description","excerpt",'_sku','_regular_price','_sale_price','_weight','_stock','_stock_status','_width','_length','_height','_virtual'); ?>
						
						<tr>
						
						<?php $checked = 'checked';
						foreach( $cols as $col){					
							print "<td>
								<input type='checkbox' class='fieldsToShow' checked name='toShow".$col."' value='1'/>
								<label for='".$col."'>". $col. "</label>
								</td>";
						} ?>
						
						</tr>
					</table>			
							
					<input type='hidden' name='columnsToShow' value='1'  />
					<input type='hidden' id='action' name='action' value='woopei_exportProducts' />
					<?php wp_nonce_field('columnsToShow'); ?>

					<?php submit_button(__( 'Search', 'wpeiePro' ),'primary','Search'); ?>

				</form>
			
			<div class='resultExport'>
				<?php $this->exportProducts(); ?>
			</div>
		<?php			
		}//end of checking for products
	}


	public function exportProducts(){

		if($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('administrator') && $_REQUEST['columnsToShow'] ){
			
			check_admin_referer( 'columnsToShow' );
			check_ajax_referer( 'columnsToShow' );
				
		
			if(!empty($_POST['keyword']))  $this->keyword = sanitize_text_field( $_POST['keyword'] );
			
			if(!empty($_POST['posts_per_page'])){
				$this->posts_per_page = (int)$_POST['posts_per_page'] ;
			}else $this->posts_per_page = '-1';
			
			if(!empty($_POST['offset'])){
				$this->offset = (int)$_POST['offset'] ;
			}else $this->offset = '-1';
			
			$query = new WP_Query( array(
				'post_type' => 'product',
				's' => $this->keyword,
				'offset' => $this->offset,
				'posts_per_page' => $this->posts_per_page,	
				) );
				
				
			if ( $query->have_posts() ){
				
				$i=0;
				?>
				<p class='message error'>
					<?php _e( 'Wait... Download is loading...', 'wpeiePro' );?>
					<b class='totalPosts' ><?php print $query->post_count;?></b>					
				</p>

				<?php		
				if($query->post_count <= 500){
					$start=0;
				}else $start=500;
				print " <b class='startPosts'>".$start."</b>";
			}
			
			$arrayIDs = array();
				
			$column_name = array(__( 'id', 'wpeiePro' ),__( 'TITLE', 'wpeiePro' ),__( 'DESCRIPTION', 'wpeiePro' ),__( 'EXCERPT', 'wpeiePro' )," ".__( 'SKU', 'wpeiePro' )," ".__( 'REGULAR PRICE', 'wpeiePro' )," ".__( 'SALE PRICE', 'wpeiePro' )," ".__( 'WEIGHT', 'wpeiePro' )," ".__( 'STOCK', 'wpeiePro' )," ".__( 'STOCK STATUS', 'wpeiePro' )," ".__( 'WIDTH', 'wpeiePro' )," ".__( 'LENGTH', 'wpeiePro' )," ".__( 'HEIGHT', 'wpeiePro' )," ".__( 'VIRTUAL', 'wpeiePro' ));	
				
			$post_meta = array('_sku','_regular_price','_sale_price','_weight','_stock','_stock_status','_width','_length','_height','_virtual');
			
			?>
			<div id="myProgress">
				 <div id="myBar"></div>
			</div>
							
			<div class='exportTableWrapper' style='overflow:auto;width:100%;max-height:600px;'>
				<table id='toExport'>
					<thead>
						<tr> 
							<th>
								<?php _e('ID', 'wpeiePro');?>
							</th>
							<?php
							 $taxonomy_objects = array('product_cat','product_tag');
														
							foreach($taxonomy_objects as $tax){									
									if(isset($_REQUEST["toShow".$tax]) ){ //show columns according to what is checked
										array_push($column_name,$tax);
									}
								
							}								

							foreach($column_name as $d){
								if(isset($_REQUEST["toShow".strtolower(str_replace(" ","_",$d))] ) ){
									$d = strtoupper(str_replace("_"," ",$d));
									print "<th>".$d."</th>";										
								}
							}	
							?>
						</tr>
					</thead>
					<tbody class='tableExportAjax'>			
					</tbody>	
				</table>
			</div>						
			<?php	
			
		}//check request						
	}	
	
}



function woopei_exportProducts(){

	if($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('administrator') ){
				
		check_admin_referer( 'columnsToShow' );
		check_ajax_referer( 'columnsToShow' );
		
		if(!empty($_POST['keyword']))  $keyword = sanitize_text_field( $_POST['keyword'] );		
			
		if(!empty($_POST['posts_per_page'])){
			$posts_per_page = (int)$_POST['posts_per_page'] ;
		}else $posts_per_page = '-1';
			
		if(!empty($_POST['offset'])){
			$offset = (int)$_POST['offset'] ;
		}else $offset = '0';
			
		$query = new WP_Query( 
			array(
				'post_type' => 'product',
				's' => $keyword,				
				'posts_per_page' => $posts_per_page,	
				'offset' => $offset,			
			) 
		);		
		
		if ( $query ->have_posts() ){
			
			$post_meta = array('_sku','_regular_price','_sale_price','_weight','_stock','_stock_status','_width','_length','_height','_virtual');				
				
			while ( $query->have_posts() ){
				$query->the_post();
				global $product;	
				global $woocommerce;
				
				if ( $product->is_type( 'simple' ) ) { ?>		
					<tr>
						<td><?php print esc_attr(get_the_ID()) ;?></td>					
						<?php if($_REQUEST["toShowtitle"] ){ ?>								 
							<td><?php esc_attr(the_title()) ;?></td>
						<?php } ?>					
						<?php if(isset($_REQUEST["toShowdescription"]) ){ ?>									
							<td>
								<?php print esc_attr(strip_tags(get_post_field('post_content', get_the_ID()) ))  ; ?>
							</td>
						<?php } ?>
						<?php if(isset($_REQUEST["toShowexcerpt"]) ){ ?>								 
							<td>
								<?php print esc_attr(strip_tags(get_post_field('post_excerpt', get_the_ID()) ))  ; ?>							
							</td>
						<?php } ?>												

									
						<?php foreach($post_meta as $meta){
							if(isset($_REQUEST["toShow".$meta]) ){ ?>
									<td><?php print esc_attr(get_post_meta(get_the_ID(), $meta, true ) ); ?></td>
							<?php }
						} 
						
						$terms = get_post_taxonomies( get_the_ID());
						foreach($terms as $tax){									
							$term = get_the_terms( get_the_ID(), $tax );
								if(isset($_REQUEST["toShow".$tax]) ){//show columns according to what is checked
										$countTerms = count($term);
										$i=0;
										print "<td>";
										$myterm = array();
										while($i<$countTerms){
											array_push($myterm, $term[$i]->name);
											$i++;
										}
										$terms = implode(',',$myterm);
										print esc_attr($terms);
										print "</td>";
								}
						}							

					print "</tr>";	
				}	
				
			}//end while
			die;												
		}else print "<p class='warning' >".__('No Product Found', 'wpeiePro')."</p>";//end if						
	}//check request						
}