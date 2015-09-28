<?php
/**
 * The sidebar containing the secondary widget area
 *
 * Displays on posts and pages.
 *
 * If no active widgets are in this sidebar, hide it completely.
 */?>


<div id="right-content">


<?php
if (46 == $post->post_parent) {
echo '<h4 class="side-title">';
echo 'Apply to a City Now';
echo '<span class="hand-arrow">';
echo '</span>';
echo '</h4>';
}
?>

<?php
// find parent of current page
if ($post->post_parent) {
    $ancestors = get_post_ancestors($post->ID);
    $parent = $ancestors[count($ancestors) - 1];
} else {
    $parent = $post->ID;
}
 
$children = wp_list_pages("title_li=&child_of=" . $parent . "&echo=0");
 
if ($children) { ?>
 <div class="side_page-header">
</div>
    <ul class="child-list">
        <?php echo $children; ?>
    </ul>
 
<?php } ?>


<?php 
$highlightargs = array( 'post_type' => 'highlights', 
 'posts_per_page' => 1,
 'orderby' => 'rand',
 'braven_categories' => 'featured'
 );
$highlight_query = new WP_Query( $highlightargs ); 

?>

<?php while ( $highlight_query->have_posts() ) : $highlight_query->the_post();
$highlightdata = get_post_meta( $highlight_query->post->ID, 'highlights', true ); ?>

<div class="highlights-sec">
<div class="highlight-hdg"><h3>Braven Spotlight</h3></div>
<a href="<?php the_permalink();?>"><?php echo the_post_thumbnail('spotlight-thumb');?></a>
<div class="highlights-sec-description"><h3><?php echo $highlightdata[ 'fellow-name' ]; ?></h3>
<p class="highlight-school"><?php echo $highlightdata[ 'fellow-school' ]; ?></p>
</div>      

</div><!--END highlights-sec -->

<?php endwhile;?>


<?php dynamic_sidebar( 'sidebar-primary' ); ?> 

</div>
	
    

    