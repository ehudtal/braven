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
<div id="featimg"><?php the_post_thumbnail('featured-page-thumb'); ?></div>
<div id="sub-overlay"><h1><?php the_title();?></h1></div>
			</div>
 	<?php else : ?>    
    
    <div class="featured-header-wo">
    
<div id="sub-overlay"><h1><?php the_title();?></h1></div>
			</div>       
            
            <?php endif; ?>

	<div id="primary" class="content-area">
   
            
<?php braven_the_breadcrumb(); ?>  
 
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
            
            <?php if ( get_post_meta( get_the_ID(), 'Pull Quote', true ) ) : ?>
            <div class="pull-quote">
                 <?php echo get_post_meta( get_the_ID(), 'Pull Quote', true ); ?>
                  </div>
                 
     <?php endif; ?>

				<article id="post-<?php the_ID(); ?>">
				

					<div class="half-centered">
                     
<div class="plank tertiary-md">
  <div class="section-container">
    <section>
      <div class="col-sm-6 col-sm-offset-3 text-center">
            <!-- We should be able to re-enable apply now in Salesforce, overriding applicatio_received. The second clause here allows that. -->
                <!-- When Salesforce integration is enabled, the apply now button will be controlled
                     by a campaign on that end. Until then though, we want everyone to see it. -->
                  <!-- FIXME: this is a hack where we auto-activate accounts b/c emails are going to spam.  Revert this once we have email activation back. -->
                    <p class="shoutout">You've successfully signed up.  Now you can complete your application.</p>
                <div class="apply-button">
                  <a class="btn-primary" href="/enrollments/new"><div class="apply-icon"></div><div class="apply-text">Apply now!</div></a><br />

      </div>
    </section>
  </div>
</div>

<div class="plank plain-lt">
  <div class="section-container">
    <section>
      <div class="col-sm-4 col-sm-offset-4">
        <h2>Why stop here?</h2>
        <p>Help spread the word so we can discover more leaders and expand the network that supports them:</p>

        <a href="https://twitter.com/intent/tweet?button_hashtag=BeBraven&text=I%20just%20signed%20up%20for" class="twitter-hashtag-button" data-size="large" data-url="<?php bloginfo('url');?>">Tweet #BeBraven</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

      </div>
    </section>
  </div>
</div>

<div class="plank plain-md">
  <div class="section-container">
    <section>
      <div class="col-sm-4 col-sm-offset-4">
        <p>
          You can also check out the following links to discover all the great reasons people are joining Braven:<br />
          <a href="/apply">Become a fellow</a><br />
          <a href="/volunteer">Volunteer with Braven</a><br />
          <a href="/partner">Partner with Braven</a>
        </p>
      </div>
    </section>
  </div>
</div>
						
					</div><!-- .entry-content -->

				
				</article><!-- #post -->

			
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<style>
section {
	margin-top: 30px;	
}
#footer-email,
#breadcrumb-wrapper {
	display: none;
}

</style>
<?php get_footer(); ?>