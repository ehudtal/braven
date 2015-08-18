<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

  <?php if ( has_post_thumbnail() ) : ?>
<div class="featured-header">
<div id="featimg"><?php the_post_thumbnail('featured-page-thumb'); ?></div>
<div id="sub-overlay"><h1><?php the_title();?></h1></div>
			</div>
 	<?php else : ?>    
    
    <div class="featured-header-wo">
    
<div id="sub-overlay"><h1><?php the_title();?></h1></div>
			</div>       
            
            <?php endif; ?>

	<div id="primary" class="content-area">
   
            
             <div id="breadcrumb_wrapper">
<div style="margin:0 auto; max-width:65rem;">
 <?php the_breadcrumb(); ?> </div>
 </div>   
 
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
            
            <?php if ( get_post_meta( get_the_ID(), 'Pull Quote', true ) ) : ?>
            <div class="pull-quote">
                 <?php echo get_post_meta( get_the_ID(), 'Pull Quote', true ); ?>
                  </div>
                 
     <?php endif; ?>

				<article id="post-<?php the_ID(); ?>">
				

					<div class="full-width">
                  
                  
                  
                  
						<?php the_content(); ?>
						
					</div><!-- .entry-content -->

				
				</article><!-- #post -->

			
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>