<?php /*
function staff_list( $atts ) {
    ob_start();
	 // define attributes and their defaults
    extract( shortcode_atts( array (
        'type' => 'post',
        'order' => '',
        'orderby' => '',
        'posts' => '',
		'staff_categories' => '',
        'category' => '',
		
		'posts_per_page' => '',
    ), $atts ) );
 
    // define query parameters based on attributes
    $options = array(
        'post_type' => $type,
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
		'staff_categories' => $staff_categories,
		'category_name' => $category,
		'posts_per_page' => $posts_per_page,
    );
	 
	$count = 1;
   $query = new WP_Query( $options );
	$braven_global_orderby = ''; 
    if ( $query->have_posts() ) { ?>
    
    <div class="team-section">
            <?php while ( $query->have_posts() ) : $query->the_post();
			$staffdata = get_post_meta( $query->post->ID, 'staff', true ); ?>
      

<div class="fstaff one_quarter">

<div class="pic pic-3d">

<a href="<?php echo $staffdata[ 'plink' ]; ?>" target="_blank">

<?php 
if ( has_post_thumbnail() ) {
	the_post_thumbnail('staff-thumb');
} else { ?>
<img src="/wp-content/uploads/2015/08/braven_profile.jpg" alt="<?php the_title(); ?>" />
<?php } ?>


</a>

<span class="pic-caption left-to-right">
		        <div class="staffboard-title"><h1><?php echo $staffdata[ 'staff-first-name' ]; ?> <?php echo $staffdata[ 'staff-last-name' ]; ?></h1>
		        <p><?php echo $staffdata[ 'staff-position' ]; ?></p></div>
                <a class="btn-success" href="#openModal-<?php echo $count?>">Read More</a>
		    </span>
           
</div>
</div>



<div id="openModal-<?php echo $count?>" class="modalDialog">
	<div>
    <a href="#close" title="Close" class="close">X</a>
    <span class="modal-pic"><?php if ( has_post_thumbnail() ) {
the_post_thumbnail('staff-thumb');
} else { ?>
<img src="https://bebraven.org/wp-content/uploads/2015/08/braven_profile.jpg" alt="<?php the_title(); ?>" />
<?php } ?></span>
 <div class="modal-tag"><strong><?php echo $staffdata[ 'staff-first-name' ]; ?> <?php echo $staffdata[ 'staff-last-name' ]; ?></strong>
 <?php echo $staffdata[ 'staff-position' ]; ?><br />
<span><b>Hometown:</b></span> <?php echo $staffdata[ 'staff-hometown' ]; ?><br />

<?php if ($staffdata[ 'staff-email' ] == '') { 
} 
else { ?>
<p class="education"><b>E-mail:</b> <a href="mailto:<?php echo $staffdata[ 'staff-email' ]; ?>"><?php echo $staffdata[ 'staff-email' ]; ?></a></p>
   <?php }?>
   
 </div>
 <hr />
<div class="modal-bio"><?php the_content();?></div>
</div>
</div>


<?php $count++; ?>


<?php endwhile;?>

</div>


<?php 
		wp_reset_postdata();
		$myvariable = ob_get_clean();
		return $myvariable;
	}
	{
		$myvariable = ob_get_clean();
		return $myvariable;
	}
}

?>



/**/