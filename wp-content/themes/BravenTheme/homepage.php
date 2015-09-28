<?php
/**
 * Template Name: Home Page
 */

get_header(); ?>

<div id="primary_home">
	<div class="section_slider"> <?php echo do_shortcode('[rev_slider sliderhome]'); ?> </div>
	
	<!-- #END SECTION HOME SLIDER -->
	<?php while ( have_posts() ) : ?>
	<div id="home_top_content" class="braven_section">
		<div class="inner_content">
			<?php
				the_post();
				the_excerpt();
			?>
		</div>
		<!-- #home_content --> 
	</div>
	<div id="about_us" class="braven_section">
		<div class="inner_content">
			<?php
				the_content();
			?>
		</div>
	</div>
	<?php endwhile; ?> 
	<div id="cta-buttons">
		<div class="inner_content">
			<?php dynamic_sidebar( 'hp-cta' ); ?>
			<div class="clear"> </div>
		</div>
	</div>
	<div id="lm_content" class="braven_section">
		<div class="inner_content">
			<p style="text-align:center;"><a class="btn large" href="/become-a-fellow/">Learn More</a></p>
		</div>
	</div>
	<?php 
	$cq = new WP_Query(array('p'=>910, 'post_type'=>'page')); // Our impact page
	if ( $cq->have_posts() ) : 
	?>
	<div id="impact_content" class="braven_section">
		<div class="inner_content">

			<div class="braven_section">
				<?php while ( $cq->have_posts() ) : $cq->the_post(); ?>
					<h1 style="text-align: center; margin-top: 0; font-size: 36px;">
						<?php the_title(); ?>
					</h1>
					<?php echo $post->post_content; ?>
				<?php endwhile; ?>
			</div>
			<!-- #home_content -->
		</div>
	</div>
	<?php 
	endif; 
	wp_reset_query(); 
	?>
	<div id="testimonials">
		<div id="slider_testimonials">
			<?php
$args = array( 'post_type' => 'testimonial', 'posts_per_page' => 10 );
$loop = new WP_Query( $args );
if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post();
$data = get_post_meta( $loop->post->ID, 'testimonial', true );
 { ?>
			<div class="bxslider">
				<div class="slide">
					<div class="testimonial_thumb"><?php echo the_post_thumbnail('testimonial-thumb');?></div>
					<div class="testimonial_all">
						<div class="testimonial-quote">
							<?php the_content(); ?>
						</div>
						<div class="client-contact-info">
							<h5 style="margin:0;"><?php echo $data[ 'person-name' ]; ?></h5>
							<?php echo $data[ 'position' ]; ?>&nbsp;<a href="<?php echo $data[ 'link' ]; ?>" title="<?php echo $data[ 'company' ]; ?>"><?php echo $data[ 'company' ]; ?></a></div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<?php 
} 
endwhile; 
wp_reset_query();
endif; ?>
		</div>
	</div>
	<div id="blogposts_home" class="braven_section">
		<h2 style="text-align:center; margin-top:0; font-size:36px;">LATEST STORIES</h2>
		<div id="slider_posts">
			<?php $rposts_query = new WP_Query('cat=1'); // exclude category 9
while($rposts_query->have_posts()) : $rposts_query->the_post(); ?>
			<div class="bxslider">
				<h3 class="blogtitle"><a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
					</a></h3>
				<h5 class="date"><?php echo the_date('F d, Y')?></h5>
				<?php the_excerpt(); ?>
				<p><a class="readmorelink" href="<?php the_permalink();?>">Read More &raquo;</a></p>
			</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
		</div>
	</div>
	<div id="partners" class="braven_section">
		<h2 style="text-align:center;">Impact Partner</h2>
		<?php 
$pfargs = array( 'post_type' => 'partner', 
'partner_categories' => 'featured-partners',
 'posts_per_page' => 1 );
$the_query = new WP_Query( $pfargs ); 

?>
		<?php if ( $the_query->have_posts() ) : ?>
		<?php while ( $the_query->have_posts() ) : $the_query->the_post();
$pfdata = get_post_meta( $the_query->post->ID, 'partner', true );
 ?>
		<div class="featured_partner"> <a href="<?php echo $pfdata[ 'plink' ]; ?>" target="_blank"><?php echo the_post_thumbnail();?></a> </div>
		<?php wp_reset_query();?>
		<?php endwhile;?>
		<?php endif; ?>
		<div class="support_partners">
			<h4 class="uppercase" style="text-align:center;">Fall 2015 Sites</h4>
			<table class="featpartners">
				<tbody>
					<tr>
						<td style="vertical-align:middle"><img src="/wp-content/uploads/2015/08/SJSU.png" alt="SJSU"  class="aligncenter size-full wp-image-332" /></td>
						<td><img src="/wp-content/uploads/2015/08/Rutgers_Univ.png" alt="Rutgers_Univ"  class="aligncenter size-full wp-image-475" /></td>
					</tr>
				</tbody>
			</table>
			<div class="clear"></div>
		</div>
	</div>
</div>
<!-- #primary_home -->

<?php get_footer(); ?>