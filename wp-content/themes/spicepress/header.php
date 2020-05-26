<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>	
	<meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
	
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php esc_url(bloginfo( 'pingback_url' )); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
</head>
<body <?php body_class( ); ?> >
<div id="wrapper">
	<?php get_template_part('header/header-navbar'); ?>
<div class='search_online_xanut_category' style=''>
	
	
<aside style="width:300px;" id="woocommerce_product_search-5" class="widget woocommerce widget_product_search wow fadeInDown animated" data-wow-delay="0.4s"><form role="search" method="get" class="woocommerce-product-search" action="https://bebest.am/">
	<label class="screen-reader-text" for="woocommerce-product-search-field-0">Search for:</label>
	<input type="search" id="woocommerce-product-search-field-0" class="search-field" placeholder="Որոնել այստեղ" value="" name="s"><button type="submit" value="Որոնել" style="background:#0795d5">Որոնել</button>
	<input type="hidden" name="post_type" value="product">
</form>
</aside>
	
	

	<a class='online_xanut' href='https://bebest.am/shop/' style='color:#FFF;background:#0795d5;padding:23px'>Առցանց խանութ</a>
		<a class='online_xanut' href='https://bebest.am/cart/' style='color:#FFF;background:#0795d5;padding:23px'><i class="fa fa-shopping-cart" style="width:50px;"></i></a>
	<br>

<select class='product_categorys' name="forma" onchange="location = this.value;" style="width:300px;">
 <option value="" disabled="" selected="">Ապրանքների դասակարգում</option>

 <option value="https://bebest.am/product-category/%d5%bf%d5%a5%d5%bd%d5%a1%d5%b0%d5%bd%d5%af%d5%b8%d6%82%d5%b4/">Տեսահսկում</option>
 <option value="https://bebest.am/product-category/%d5%a1%d5%b6%d5%be%d5%bf%d5%a1%d5%b6%d5%a3%d5%b8%d6%82%d5%a9%d5%b5%d5%a1%d5%b6-%d5%b0%d5%a1%d5%b4%d5%a1%d5%af%d5%a1%d6%80%d5%a3/">Անվտանգության համակարգ</option>
 <option value="https://bebest.am/product-category/%d5%ac%d5%a1%d5%b4%d5%ba%d5%a5%d6%80/">Լամպեր</option>
<option value="http://bebest.am/product-category/%d5%bd%d5%a1%d5%b6%d5%bf%d5%a5%d5%ad%d5%b6%d5%ab%d5%af%d5%a1/">Սանտեխնիկա</option>
<option value="http://bebest.am/product-category/%d5%bd%d5%a1%d5%b6%d5%bf%d5%a5%d5%ad%d5%b6%d5%ab%d5%af%d5%a1/%d5%af%d5%b8%d5%b5%d5%b8%d6%82%d5%b2%d5%b8%d6%82-%d5%af%d6%81%d5%a1%d5%b4%d5%a1%d5%bd%d5%a5%d6%80/">կոյուղու-կցամասեր</option>

<option value="http://bebest.am/product-category/%d5%bd%d5%a1%d5%b6%d5%bf%d5%a5%d5%ad%d5%b6%d5%ab%d5%af%d5%a1/%d5%ba%d5%a1%d6%80%d5%b8%d6%82%d6%80%d5%a1%d5%af%d5%a1%d5%b5%d5%ab%d5%b6-%d5%af%d6%81%d5%a1%d5%b4%d5%a1%d5%bd%d5%a5%d6%80/">Պարուրակային կցամասեր</option>
<option value="http://bebest.am/product-category/%d5%bd%d5%a1%d5%b6%d5%bf%d5%a5%d5%ad%d5%b6%d5%ab%d5%af%d5%a1/%d5%ba%d5%b8%d5%ac%d5%ab%d5%ba%d6%80%d5%b8%d5%ba%d5%ab%d5%ac%d5%a5%d5%b6%d5%a1%d5%b5%d5%ab%d5%b6-%d5%af%d6%81%d5%a1%d5%b4%d5%a1%d5%bd%d5%a5%d6%80/">Պոլիպրոպիլենային կցամասեր
</option>
<option value="http://bebest.am/product-category/%d5%bd%d5%a1%d5%b6%d5%bf%d5%a5%d5%ad%d5%b6%d5%ab%d5%af%d5%a1/%d5%ad%d5%b8%d5%b2%d5%b8%d5%be%d5%a1%d5%af%d5%b6%d5%a5%d6%80-%d5%bd%d5%a1%d5%b6%d5%bf%d5%a5%d5%ad%d5%b6%d5%ab%d5%af%d5%a1/">խողովակներ

</option>
	<option value="http://bebest.am/product-category/%d5%b0%d5%a1%d5%b2%d5%b8%d6%80%d5%a4%d5%a1%d5%ac%d5%a1%d6%80%d5%a5%d6%80/">հաղորդալարեր

</option>
	<option value="http://bebest.am/product-category/%d5%a7%d5%ac%e2%80%a4-%d5%bd%d5%a1%d6%80%d6%84%d5%a1%d5%be%d5%b8%d6%80%d5%b8%d6%82%d5%b4%d5%b6%d5%a5%d6%80/">էլ․սարքավորումներ

</option>
</select>
	</div>