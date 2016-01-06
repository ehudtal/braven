<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 */
?>

<a id="mailinglist"></a>
<div id="footer-email" class="braven_section">
	<h2>KEEP INFORMED WITH OUR NEWSLETTER</h2>
	<?php echo do_shortcode('[gravityform id="1" title="false" description="false"]')?> </div>
</div>
<!-- #main -->
<div id="footer_bottom" class="braven_section">
	<div id="footer_menu">
		<?php dynamic_sidebar( 'fmenu' ); ?>
		<div class="clear"></div>
	</div>
	<!-- #secondary -->
	
	<div class="braven-cc">
		<?php dynamic_sidebar( 'fcopyright' ); ?>
	</div>
	<!-- .site-info --> 
</div>
<!-- #footer_bottom -->
</div>
<!-- #page -->

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

<?php wp_footer(); ?>
</body></html>
