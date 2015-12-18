<?php
/**
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
                  
						<h3>Alert text goes here, e.g. "You have successfully logged out."</h3>
        

        
        <!-- The purpose of this is to log out of all our dependent services, so a user
     doesn't get trapped in a weird circle of logging out of Canvas, but still
     being another user on BZ and thereby getting jerked around in a confusing
     circle when she goes back to the home page.

     Since it is a cross-domain, client side cookie, we had to implement this
     using a helper method on the other side and an iframe on this side.
-->
  <iframe style="visibility: hidden; width: 1px; height: 1px;" src="//<?php echo braven_join_domain( 'join.bebraven.org' ); ?>/users/clear_session_cookie"></iframe>
<form method="post" action="login" id="login-form"
      onsubmit="submitbutton = document.getElementById('login-submit'); submitbutton.value='Please wait...'; submitbutton.disabled=true; return true;">
        <div><label id="username-label" for="username">
          Email
        </label><br />
        <input class="form-control braven-input" autofocus="autofocus" type="text" id="username" name="username"
               size="32" tabindex="1" accesskey="u" /></div>
        <div><label id="password-label" for="password">
          Password
        </label><br />
        <input class="form-control braven-input" type="password" id="password" name="password"
               size="32" tabindex="2" accesskey="p" autocomplete="off" /></div>

      <br />
      <input type="hidden" id="lt" name="lt" value="LT-1439838677rh-E4v-5DkUZSQkI0cK" />
      <input type="hidden" id="service" name="service" value="https:&#x2F;&#x2F;<?php echo braven_portal_domain( 'portal.bebraven.org' ); ?>&#x2F;login&#x2F;cas&#x2F;1" />
      <div><input class="btn-primary" name="commit" type="submit" value="Log in" /></div>
      <div id="infoline">
      </div>
</form>


       <br />
        
  <a href="//<?php echo braven_join_domain( 'join.bebraven.org' ); ?>/signup/new">Sign up</a><br />

  <a href="//<?php echo braven_join_domain( 'join.bebraven.org' ); ?>/users/password/new">Forgot your password?</a><br />

  <a href="//<?php echo braven_join_domain( 'join.bebraven.org' ); ?>/users/confirmation/new">Didn&#39;t receive confirmation instructions?</a><br />



						
					</div><!-- .half-centered -->

				
				</article><!-- #post -->

			
			<?php endwhile; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
<style>
#footer-email, 
#footer_bottom, 
#breadcrumb-wrapper {
	display: none;
}

</style>
<?php get_footer(); ?>
