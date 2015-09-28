<?php
/**
 * The template for displaying a single post
 */
get_header(); ?>
<?php $blog_pg_id = 29; // this page is where we dynamically define the title and header image for the blog section ?>
<?php $header_img_size = 'featured-page-thumb'; // as defined in functions.php ?>
<?php $header_img = get_the_post_thumbnail($blog_pg_id, $header_img_size); // calling in the image from the parent page of all blog posts, to display as a header image ?>
<div class="featured-header">
	<div id="featimg">
		<?php echo ($header_img) ? $header_img : the_post_thumbnail($header_img_size); // fall back to the current page's featured image if none is defined for the blog as a whole. ?>
	</div>
	<div id="sub-overlay">
		<h1>
			<?php echo get_the_title($blog_pg_id); // in case we named the Blog section something else ?>
		</h1>
	</div>
</div>
<div id="primary" class="content-area">
	<div id="left-content" class="site-content" role="main">
		<?php while ( have_posts() ) : the_post(); // run "the loop" ?>
		<?php braven_set_post_views(get_the_ID()); // logs this as a pageview for sorting by popularity ?>
		<h1 class="entry-title">
			<?php the_title(); ?>
		</h1>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
		<?php endwhile; ?>
	</div>
	<!-- #content -->
	<?php get_sidebar('blog'); ?>
</div>
<!-- #primary -->

<div class="clear"></div>
<?php get_footer(); ?>
