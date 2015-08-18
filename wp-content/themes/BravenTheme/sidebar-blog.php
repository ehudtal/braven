<?php
/**
 * sidebar for blogs
 */
 ?>


<div id="right-content">
<div id="braven_sidebar">

	<h2 class="side-hdg">Trending</h2>
				<ul class="recent_posts">
                
			<?php
$catquery = new WP_Query(array( 
	'paged' => $paged,
				'posts_per_page' => 3, 
				'meta_key' => 'wpb_post_views_count', 
				'orderby' => 'meta_value_num', 
				'order' => 'DESC',
				'cat' => 'blog'
			
)
);
while($catquery->have_posts()) : $catquery->the_post();

?>

											
						<li>

<a href="<?php echo the_permalink();?>">
	<?php if ( get_the_post_thumbnail($post_id) != '' ) {

  echo '<div class="side-article-image">';
   the_post_thumbnail('sidebar-thumb');
  echo '</div>';
   echo '<div class="side-article-content">';
   the_title();
    
    echo '</div>';
} else {
 echo '<div class="side-full-content">';
  the_title();

    echo '</div>';

}?>
<div class="clear"></div>
</a>
</li>




<?php endwhile; ?>


</ul>


<?php dynamic_sidebar( 'sidebar-blog-sec' ); ?>



</div>
	