<?php
/**
 * The template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>


<?php 
$pheader_query = new WP_Query('page_id=29');
?>
<?php if ( $pheader_query->have_posts() ) : ?>
<?php while ( $pheader_query->have_posts() ) : $pheader_query->the_post();
 ?>
<div class="featured-header">
<div id="featimg"><?php the_post_thumbnail('featured-page-thumb'); ?></div>
<div id="sub-overlay"><h1><?php the_title();?></h1></div>
			</div>
<?php wp_reset_postdata(); ?>
<?php endwhile;?>
<?php endif; ?>

	<div id="primary" class="content-area">
		<div id="left-content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
<?php wpb_set_post_views(get_the_ID());?>
		
        
        <h1 class="entry-title">
			<?php the_title(); ?>
		</h1>


	<div class="entry-content">
				
             <?php the_content(); ?>   
                </div>

			<?php endwhile; ?>



		</div><!-- #content -->
        
        
              <?php get_sidebar('blog'); ?>
    
</div><!-- #primary -->

    <div class="clear"></div>

<?php get_footer(); ?>