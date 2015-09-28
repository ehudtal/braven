<?php
/**
 * Template Name: Page w/Children
 */

get_header(); ?>

 <?php if ( has_post_thumbnail() ) : ?>
<div class="featured-header">
<div id="featimg"><?php the_post_thumbnail('featured-page-thumb'); ?></div>

<div id="sub-overlay"><h1><?php the_title();?></h1></div>

			</div>
            <?php endif; ?>

	<div id="primary" class="content-area">
   
            
<?php braven_the_breadcrumb(); ?>  
 
	
  <div id="left-content">
			<?php while ( have_posts() ) : the_post(); ?>
            
            <?php if ( get_post_meta( get_the_ID(), 'Pull Quote', true ) ) : ?>
            <div class="pull-quote">
                 <?php echo get_post_meta( get_the_ID(), 'Pull Quote', true ); ?>
                  </div>
                 
     <?php endif; ?>

				<article id="post-<?php the_ID(); ?>">
				

			
                  
                  
                  
                  
						<?php the_content(); ?>
						
					

				
				</article><!-- #post -->

			
			<?php endwhile; ?>
            
        <?php wp_reset_query(); ?> 
            

         
 
            
            
		</div><!-- #content -->
	

<?php get_sidebar(); ?>

<div class="clear"></div>
</div><!-- #primary -->

<?php get_footer(); ?>