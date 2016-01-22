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
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory')?>/css/jquery.mmenu.all.css">
<?php wp_enqueue_script("jquery"); ?>
<?php wp_enqueue_script("jquery-ui"); ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory')?>/css/sass-compiled.css"><!--//needed for bios-->
<?php wp_head(); ?>

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
<!-- Facebook Pixel Code (created with Siobhan's FB account) -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');

fbq('init', '212978989045892');
fbq('track', "PageView");</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=212978989045892&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
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
				<a href="https://<?php echo braven_join_domain( 'join.bebraven.org' ); ?>/users/sign_in_sso">Log In</a>
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
