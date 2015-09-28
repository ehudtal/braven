<?php
/**
 * sidebar for blogs
 */
 ?>


<div id="right-content">
	<div id="braven_sidebar">
		<h2 class="side-hdg"><?php echo __('Trending', 'braven'); ?></h2>
			<ul class="recent_posts">
				<?php
				$catquery = new WP_Query(
					array( 
						'paged' => $paged,
						'posts_per_page' => 3, 
						'meta_key' => 'braven_post_views_count', 
						'orderby' => 'meta_value_num', 
						'order' => 'DESC',
						'cat' => 'blog'
					)
				);
				while($catquery->have_posts()) : $catquery->the_post();
				?>
					<li>
						<a href="<?php echo the_permalink();?>">
							<?php if ( has_post_thumbnail() != '' ) { ?>
								<div class="side-article-image">
									<?php the_post_thumbnail('sidebar-thumb');?>
								</div>
								<div class="side-article-content">
									<?php the_title(); ?>
								</div>
							<?php } else { ?>
								<div class="side-full-content">
									<?php the_title(); ?>
								</div>
							<?php } ?>
							<div class="clear"></div>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php dynamic_sidebar( 'sidebar-blog-sec' ); ?>
	</div><!--#braven_sidebar-->	
</div><!--#right-content-->
	