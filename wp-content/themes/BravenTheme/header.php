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
    <link type="text/css" rel="stylesheet" href="<?php echo bloginfo('template_directory')?>/css/jquery.mmenu.all.css" />
<link rel="stylesheet" href="<?php echo bloginfo('template_directory')?>/css/nerveSlider.min.css">
	
	
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>

 <script src="<?php echo bloginfo('template_directory')?>/js/plugins/jquery.easing.1.3.js"></script>
 
  <script src="<?php echo bloginfo('template_directory')?>/js/jquery.bxslider.js"></script>
    <script src="<?php echo bloginfo('template_directory')?>/js/fontsmoothie.min.js"></script>
    
      <script src="<?php echo bloginfo('template_directory')?>/js/jquery.easypiechart.min.js"></script>
      
       <script src="<?php echo bloginfo('template_directory')?>/js/wow.min.js"></script>
       	<script type="text/javascript" src="<?php echo bloginfo('template_directory')?>/js/jquery.mmenu.min.all.js"></script>
  
  <script src="<?php echo bloginfo('template_directory')?>/js/jquery.nerveSlider.js" type="text/javascript"></script>



    

 <script language="javascript"> 
 $(document).ready(function(){
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
});	
</script>

 <script language="javascript"> 
 $(document).ready(function(){
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
});	
</script>
    
   <script>
  $(document).ready(function($) {
	$('a').click(function(){
    $('html, body').animate({
        scrollTop: $('[name="' + $.attr(this, 'href').substr(1) + '"]').offset().top
    }, 500);
    return false;
});
});
   </script>

	<script>
	jQuery(function($) {
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
	});
	</script>
    
<script>
$(document).ready(function($) {
$('nav#menu').mmenu({
  navbar: {
    title: "Braven"
  }
  });
  
     var API = $("#menu").data( "mmenu" );

      $("#close-mobile-btn").click(function() {
         API.close();
      });
			});
	
</script>
    
   
  <script>
   wow = new WOW(
    {
      boxClass:     'wow',      // default
      animateClass: 'fade', // default
      offset:       200,          // default
      mobile:       true,       // default
      live:         true        // default
    }
  )
  wow.init();
  
  </script>
    
    
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>



	<div id="page">
    
  
    
     <div class="upperheader_red">
    <div class="container">
    <h4 class="tophdr-content">Join Our Mailing List &raquo; <span style="margin-left:2px; margin-right:2px;">
    <span class="social">
     <a class="mail_icon" target="_blank" href="#mailinglist"></a>
   <a class="facebook_icon" target="_blank" href="https://www.facebook.com/BeBraven" target="_blank"></a>
   <a class="ig_icon" target="_blank" href="https://instagram.com/bebraven/" target="_blank"></a>
<a class="twitter_icon" target="_blank" href="https://twitter.com/bebraven" target="_blank"></a>
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
            
            	<a class="menuicon" href="#menu"></a>
                
               

			</div><!-- #END topHeader -->
   
                 <nav id="menu">
                 
                  <a id="close-mobile-btn" href="#">&times;</a>       
              
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
