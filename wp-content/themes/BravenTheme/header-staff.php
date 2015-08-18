<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
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
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
    
    <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_directory')?>/css/TradeGothicNo.20-CondBold.css"> 
    <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_directory')?>/css/manus-stylesheet.css"> 
    
   <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('template_directory')?>/css/sass-compiled.css">
    <link rel="stylesheet" href="http://getbootstrap.com/dist/css/bootstrap.min.css" />
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>

 <script src="<?php echo bloginfo('template_directory')?>/js/plugins/jquery.easing.1.3.js"></script>
 
   <script src="<?php echo bloginfo('template_directory')?>/js/libs/modernizr.js"></script>
 
  <script src="<?php echo bloginfo('template_directory')?>/js/jquery.bxslider.js"></script>
  
    <script src="<?php echo bloginfo('template_directory')?>/js/jquery.popupoverlay.js"></script>
 


  
  <script>
  jQuery(function($) {

    $('#fadeandscale').popup({
        pagecontainer: '.container',
        transition: 'all 0.3s'
    });

});
</script>
    
    
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="page">
    
    
     <div class="upperheader_red">
    <div class="container">
    <h4 class="tophdr-content">Join Our Mailing List &raquo; <span style="margin-left:2px; margin-right:2px;">
    <span class="social">
     <a class="mail_icon" target="_blank" href="#"></a>
   <a class="facebook_icon" target="_blank" href="#"></a>
     <a class="ig_icon" target="_blank" href="#"></a>
<a class="twitter_icon" target="_blank" href="#"></a>
<a class="search_icon" target="_blank" href="#"></a>
</span> </h4>
  
    
    
    </div>
    </div>
  
    
    
    <div id="topheader">
    
   <div id="blogo">
   <a href="<?php echo bloginfo('url')?>"><img src="<?php echo bloginfo ('template_directory');?>/images/braven_logo.png"></a>
   </div>
		

			<div id="navigation" class="navigationbar">
				<nav id="site-navigation" class="navigation main-navigation" role="navigation">
				
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'menu_id' => 'primary-menu' ) ); ?>
					
				</nav><!-- #site-navigation -->
                
            </div><!-- #END Navigation -->    
                
			</div><!-- #END topHeader -->
	

		<div id="main" class="site-main">
