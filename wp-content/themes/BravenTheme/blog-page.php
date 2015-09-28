<?php
/**
 * Template Name: Blog
 */

get_header(); ?>

 <?php if ( has_post_thumbnail() ) : ?>
<div class="featured-header">
<div id="featimg"><?php the_post_thumbnail('featured-page-thumb'); ?></div>

<div id="sub-overlay"><h1><?php the_title();?></h1></div>

			</div>
            <?php endif; ?>

	<div id="primary" class="content-area">
    
    <div id="left-content">
   
  <div id="braven-blog">
		<?php 
		$temp = $wp_query; $wp_query= null;
		
		
				$wp_query = new WP_Query(array(
				
				'paged' => $paged,
				'posts_per_page' => 4, 
				'orderby'=> 'ASC',
				)
				); 
			
		
		while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
        
        <div class="blog-entry item <?php
foreach((get_the_category()) as $childcat) {
if (cat_is_ancestor_of(1, $childcat)) {
echo $childcat->cat_name; 
}}
?>">

  <div class="metainfo">

</div>
<?php $url = get_post_meta($post->ID, "url", true);

	          if($url!='') {

 echo '<h2><a href="'.$url.'" target="_blank">';
  echo the_title();


	            echo '</a></h2>';

	          } else {

	                  echo '<h2><a href="';
					  echo the_permalink();
					  echo '">';
					 echo the_title();
					    echo '</a></h2>';

	          } ?>


<?php
if ( has_post_thumbnail() ) { 
 echo '<div class="blog-thumb">';
        the_post_thumbnail( 'blog-thumb' );
  echo '</div>';
   echo '<div class="blog-content"> ';
} else {
	echo '<div>';
}
?>
<p><?php braven_excerpt(50); ?></p>
       </div><!--End Blog Content -->
        <div class="clear"></div>
</div>
      
      
		<?php endwhile; ?>
<div class="clear"></div>
	
<div class="wrapper">
		<nav id="nav-posts">
			<?php if (function_exists("pagination")) {
    pagination($wp_query->max_num_pages);
} ?>
		</nav>
</div>


		<?php wp_reset_query(); ?>

			
</div>		




</div><!--End Left Page -->

<?php get_sidebar ('blog'); ?>

		</div><!-- #content -->


	</div><!-- #primary -->

<div class="clear"></div>



<?php get_footer(); ?>