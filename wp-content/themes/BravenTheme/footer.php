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

<?php wp_footer(); ?>
</body></html>