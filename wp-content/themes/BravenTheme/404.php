<?php
/**
 * The template for displaying 404 pages (Not Found)
 */

get_header(); ?>

	<div id="error" class="error-area">
		<div class="inner-content">

			<hgroup class="wrong">
      <h1><?php echo __('Oh no! Page not found.', 'braven');?></h1>
      <p><a class="btn-secondary" href="/"><?php echo __('Go Home', 'braven'); ?></a></p>
    </hgroup>
			
		

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>