<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<title>
<?php wp_title( '|', true, 'right' ); ?>
</title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory')?>/css/sass-compiled.css"><!-- sass-compiled.css is needed for staff bios. let's merge it with braven.css at some point? -->
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory')?>/css/jquery.mmenu.all.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory')?>/js/jquery.mmenu.min.all.js"></script>
<script>
jQuery(document).ready(function($){
		$('nav#menu').mmenu({ 
		navbar: {title: "Braven"} 
	});
	var API = $("#menu").data( "mmenu" );
	$("#close-mobile-btn").click(function() {
		API.close();
	});
}); // end JQuery
</script>

<?php if (is_home() || is_front_page() ) : ?> 
	<script src="<?php bloginfo('template_directory')?>/js/jquery.bxslider.js"></script>
	<script>
		jQuery(document).ready(function($){
			$('#slider_posts').bxSlider({
				mode: 'horizontal',
				useCSS: false,
				infiniteLoop: true,
				hideControlOnEnd: true,
				easing: 'swing',
				auto:true,
				pause: 8000,
				speed: 2000
			});
			$('#slider_testimonials').bxSlider({
				mode: 'horizontal',
				useCSS: false,
				infiniteLoop: true,
				hideControlOnEnd: true,
				easing: 'swing',
				mode: 'fade',
				auto:true,
				pause: 7000,
				speed: 2800
			});
		}); // end JQuery
		/*$('a').click(function(){
			$('html, body').animate({
				scrollTop: $('[name="' + $.attr(this, 'href').substr(1) + '"]').offset().top
			}, 500);
			return false;
		});*/
	</script>
<?php endif; // is home || is front page ?>
<?php if (is_page_template( 'page-with-charts.php' ) || is_home() || is_front_page() ) : ?>
	<script src="<?php echo bloginfo('template_directory')?>/js/jquery.easypiechart.min.js"></script>
	<script src="<?php echo bloginfo('template_directory')?>/js/wow.min.js"></script>
	<script>
		jQuery(document).ready(function($){
			$('.chart').easyPieChart({
				easing: 'easeOutBounce',
				animate: 6000,
				delay:8000,
				onStep: function(from, to, percent) {
					$(this.el).find('.percent').text(Math.round(percent));
				}
			});
			var chart = window.chart = $('.chart').data('easyPieChart');
			$('.js_update').on('click', function() {
				chart.update(Math.random()*200-100);
			});
		}); // end JQuery
		wow = new WOW();
		wow.init();
	</script>
<?php endif; ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page">
<div class="upperheader_red">
	<div class="container">
		<h4 class="tophdr-content">Join Our Mailing List &raquo;
			<span class="social">
				<a class="mail_icon" target="_blank" href="#mailinglist"></a>
				<a class="facebook_icon" href="https://www.facebook.com/BeBraven" target="_blank"></a>
				<a class="ig_icon" href="https://instagram.com/bebraven/" target="_blank"></a>
				<a class="twitter_icon" href="https://twitter.com/bebraven" target="_blank"></a>
			</span>
			<span class="login_container">
				<a href="https://www.beyondz.org/users/sign_in_sso">Log In</a>
			</span>
		</h4>
	</div>
</div>
<div id="topheader">
	<div id="blogo">
		<a href="<?php bloginfo('url')?>">
			<img src="<?php bloginfo ('template_directory');?>/images/braven_logo.png" alt="Braven logo">
		</a>
	</div>
	<div id="navigation" class="navigationbar">
		<nav id="site-navigation" class="navigation main-navigation" role="navigation">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'menu_id' => 'primary-menu' ) ); ?>
		</nav>
		<!-- #site-navigation --> 
		
	</div>
	<!-- #END Navigation --> 
	
	<a class="menuicon" href="#menu"></a> </div>
<!-- #END topHeader -->

<nav id="menu"> <a id="close-mobile-btn" href="#">&times;</a>
	<?php wp_nav_menu( array( 
	'menu' => 'Main Navigation',
	'container'=> '',
  'container_class' => false,
  'container_id'    => '',
  'menu_class'      => 'menu',
  'items_wrap' => '<ul>%3$s</ul>',
  'menu_id'         => ''
  ) ); ?>
</nav>
<div id="main" class="site-main">
