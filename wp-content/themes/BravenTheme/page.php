<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 */

get_header(); ?>
<?php if ( has_post_thumbnail() ) : ?>

<div class="featured-header">
	<div id="featimg">
		<?php the_post_thumbnail('featured-page-thumb'); ?>
	</div>
	<div id="sub-overlay">
		<h1>
			<?php the_title();?>
		</h1>
	</div>
</div>
<?php else : ?>
<div class="featured-header-wo">
	<div id="sub-overlay">
		<h1>
			<?php the_title();?>
		</h1>
	</div>
</div>
<?php endif; ?>
<div id="primary" class="content-area">
	<?php braven_the_breadcrumb(); ?>
	<div id="content" class="site-content" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
		<?php $pull_quote = get_post_meta( get_the_ID(), 'Pull Quote', true ); ?>
		<?php if ( $pull_quote ) : ?>
		<div class="pull-quote"> <?php echo $pull_quote; ?> </div>
		<?php endif; ?>
		<article id="post-<?php the_ID(); ?>">
			<div class="full-width">
				<?php the_content(); ?>
			</div>
			<!-- .entry-content --> 
		</article>
		<!-- #post -->
		<?php endwhile; ?>
	</div>
	<!-- #content --> 
</div>
<!-- #primary -->

<?php get_footer(); ?>
