<?php
/**
 * Template Name: Page w/Sidebar
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
				

			
                  
                  
                  
                  
						<p class="lead"> Whether you're interested in participating as a fellow, volunteering as a speaker or coach, or becoming a partner organization, you're in the right place.</p>
<div id="sign-up-form" class="well well-default">
  <div class="panel-body">
    <form accept-charset="UTF-8" action="/signup" method="post">
      <div style="display:none">
        <input name="utf8" type="hidden" value="&#x2713;" />
        <input name="authenticity_token" type="hidden" value="QQtDzB0sTYZ5Blo+YX2gbA9a1cv9xHKcIfDwRdRJQxE=" />
      </div>
      <input id="referrer" name="referrer" type="hidden" value="<?php bloginfo('url');?>" />
      <div class="form-group">
      <label class="required" for="user_first_name">Name</label>
      <section>
      <div class="col-sm-6">
        <input class="form-control" id="user_first_name" name="user[first_name]" placeholder="First Name" required="required" style="float: left;" type="text" />
      </div>
      <div class="col-sm-6">
      <input class="form-control" id="user_last_name" name="user[last_name]" placeholder="Last Name" required="required" style="float: left;" type="text" />
      <div>
        </section>
      </div>
      <div class="form-group">
        <section>
          <div class="col-sm-8">
            <label class="required" for="user_email">Email</label>
            <input class="form-control" id="user_email" name="user[email]" placeholder="Email" required="required" type="email" />
          </div>
        </section>
      </div>
      <div class="form-group">
        <label class="required">I am...</label>
        <div class="clickable-option">
          <input id="user_applicant_type_undergrad_student" name="user[applicant_type]" required="required" type="radio" value="undergrad_student" />
          <label for="user_applicant_type_undergrad_student">An undergraduate student</label>
          <div class="form-option-details">
            <section>
              <div class="required"> University name: </div>
              <div class="form-group">
                <div>
                  <input id="user_university_name_city_college_of_new_york" name="user[university_name]" required="required" type="radio" value="City College of New York" />
                  <label for="user_university_name_City College of New York">City College of New York</label>
                  <div class="form-option-details"></div>
                </div>
                <div>
                  <input id="user_university_name_hunter_college" name="user[university_name]" required="required" type="radio" value="Hunter College" />
                  <label for="user_university_name_Hunter College">Hunter College</label>
                  <div class="form-option-details"></div>
                </div>
                <div>
                  <input id="user_university_name_rutgers_university_-_newark" name="user[university_name]" required="required" type="radio" value="Rutgers University - Newark" />
                  <label for="user_university_name_Rutgers University - Newark">Rutgers University - Newark</label>
                  <div class="form-option-details"></div>
                </div>
                <div>
                  <input id="user_university_name_san_jose_state_university" name="user[university_name]" required="required" type="radio" value="San Jose State University" />
                  <label for="user_university_name_San Jose State University">San Jose State University</label>
                  <div class="form-option-details"></div>
                </div>
                <div class="form-group">
                  <input id="user_university_name_other" name="user[university_name]" required="required" type="radio" value="other" />
                  <label for="user_university_name_other">None of the above</label>
                  <div class="form-option-details">
                    <input class="form-control" id="undergrad_university_name" maxlength="70" name="undergrad_university_name" placeholder="Your college name and state" required="required" style="max-width: 250px;" type="text" />
                    <div class="clickable-option">
                      <input name="user[like_to_know_when_program_starts]" type="hidden" value="0" />
                      <input id="user_like_to_know_when_program_starts" name="user[like_to_know_when_program_starts]" type="checkbox" value="1" />
                      <label for="user_like_to_know_when_program_starts">I&#39;d like to know when BZ starts a program at my college</label>
                    </div>
                    <div class="clickable-option">
                      <input name="user[like_to_help_set_up_program]" type="hidden" value="0" />
                      <input id="user_like_to_help_set_up_program" name="user[like_to_help_set_up_program]" type="checkbox" value="1" />
                      <label for="user_like_to_help_set_up_program">I&#39;d like to help BZ set up a program in my area</label>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <section>
              <div class="required"> Started college in: </div>
              <div class="col-sm-5">
                <select class="form-control" id="user_started_college_in" include_blank="true" name="user[started_college_in]" required="required">
                  <option value=""></option>
                  <option value="2008">2008</option>
                  <option value="2009">2009</option>
                  <option value="2010">2010</option>
                  <option value="2011">2011</option>
                  <option value="2012">2012</option>
                  <option value="2013">2013</option>
                  <option value="2014">2014</option>
                  <option value="2015">2015</option>
                </select>
              </div>
            </section>
            <section>
              <div class="required"> Anticipated graduation: </div>
              <div class="col-sm-5">
                <select class="form-control" id="user_anticipated_graduation" include_blank="true" name="user[anticipated_graduation]" required="required">
                  <option value=""></option>
                  <option value="2015">2015</option>
                  <option value="2016">2016</option>
                  <option value="2017">2017</option>
                  <option value="2018">2018</option>
                  <option value="2019">2019</option>
                  <option value="2020">2020</option>
                  <option value="2021">2021</option>
                  <option value="2022">2022</option>
                </select>
              </div>
            </section>
            <section>
              <div> Phone number: </div>
              <div class="col-sm-5">
                <input class="form-control short" id="user_phone" name="user[phone]" pattern=".{10,15}" title="phone number including area code" type="tel" />
              </div>
            </section>
            <section>
              <div class="required"> Please create a password to protect any personal information you may provide us. </div>
              <div class="col-sm-8">
                <input class="form-control" id="user_password" name="user[password]" pattern=".{4,}" placeholder="Create a password" required="required" title="make a password at least four characters long" type="password" />
              </div>
            </section>
          </div>
        </div>
        <div class="clickable-option">
          <input id="user_applicant_type_volunteer" name="user[applicant_type]" required="required" type="radio" value="volunteer" />
          <label for="user_applicant_type_volunteer">Interested in volunteering as a coach in the BZ Accelerator</label>
          <div class="form-option-details">
            <section>
              <div> <span class="required">I can coach a cohort of students in:</span> <br />
                <div class="form-group">
                  <div>
                    <label>
                      <input id="user_bz_region_new_york_city" name="user[bz_region]" required="required" type="radio" value="New York City" />
                      <span>New York City</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_newark_nj" name="user[bz_region]" required="required" type="radio" value="Newark, NJ" />
                      <span>Newark, NJ</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_san_francisco_bay_area_east_bay" name="user[bz_region]" required="required" type="radio" value="San Francisco Bay Area, East Bay" />
                      <span>San Francisco Bay Area, East Bay</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_san_francisco_bay_area_san_francisco" name="user[bz_region]" required="required" type="radio" value="San Francisco Bay Area, San Francisco" />
                      <span>San Francisco Bay Area, San Francisco</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_san_francisco_bay_area_san_jose" name="user[bz_region]" required="required" type="radio" value="San Francisco Bay Area, San Jose" />
                      <span>San Francisco Bay Area, San Jose</span> </label>
                  </div>
                  <div>
                    <input class="controls-details" id="user_bz_region_other_" maxlength="60" name="user[bz_region]" required="required" type="radio" value="Other: " />
                    <label for="user_bz_region_other">Other:</label>
                    <div class="form-option-details">
                      <section>
                        <div class="col-sm-6 required required-block">
                          <input class="form-control" id="user_city" name="user[city]" placeholder="City" required="required" type="text" />
                        </div>
                        <div class="col-sm-6 required required-block">
                          <select class="form-control placeholder-style" id="user_state" name="user[state]" onchange="$(this)[this.selectedIndex == 0 ? &quot;addClass&quot; : &quot;removeClass&quot;](&quot;placeholder-style&quot;);" style="margin: 5px 0">
                            <option value="">State</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="DC">District of Columbia</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                          </select>
                        </div>
                      </section>
                      <input name="user[like_to_know_when_program_starts]" type="hidden" value="0" />
                      <input id="user_like_to_know_when_program_starts" name="user[like_to_know_when_program_starts]" type="checkbox" value="1" />
                      <label for="user_like_to_know_when_program_starts">I&#39;d like to know when BZ starts a program in my area</label>
                      <br />
                      <input name="user[like_to_help_set_up_program]" type="hidden" value="0" />
                      <input id="user_like_to_help_set_up_program" name="user[like_to_help_set_up_program]" type="checkbox" value="1" />
                      <label for="user_like_to_help_set_up_program">I&#39;d like to help BZ set up a program in my area</label>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <section>
              <div> Profession / job title: </div>
              <div class="col-sm-7">
                <input class="form-control" id="user_profession" maxlength="70" name="user[profession]" size="70" type="text" />
              </div>
            </section>
            <section>
              <div> Company: </div>
              <div class="col-sm-7">
                <input class="form-control" id="user_company" maxlength="70" name="user[company]" size="70" type="text" />
              </div>
            </section>
            <section>
              <div class="required"> Please create a password to protect any personal information you may provide us. </div>
              <div class="col-sm-7">
                <input class="form-control" id="user_password" name="user[password]" pattern=".{4,}" placeholder="Create a password" required="required" title="make a password at least four characters long" type="password" />
              </div>
            </section>
          </div>
        </div>
        <div class="clickable-option">
          <input id="user_applicant_type_employer" name="user[applicant_type]" required="required" type="radio" value="employer" />
          <label for="user_applicant_type_employer" required="required">Interested in offering employment opportunities to BZ alumni</label>
          <div class="form-option-details">
            <section>
              <div> <span class="required">In the following area:</span> <br />
                <div class="form-group">
                  <div>
                    <label>
                      <input id="user_bz_region_chicago_area" name="user[bz_region]" required="required" type="radio" value="Chicago Area" />
                      <span>Chicago Area</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_metro_dc_area" name="user[bz_region]" required="required" type="radio" value="Metro DC Area" />
                      <span>Metro DC Area</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_new_york_city_area" name="user[bz_region]" required="required" type="radio" value="New York City Area" />
                      <span>New York City Area</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_san_francisco_bay_area" name="user[bz_region]" required="required" type="radio" value="San Francisco Bay Area" />
                      <span>San Francisco Bay Area</span> </label>
                  </div>
                  <div>
                    <label>
                      <input id="user_bz_region_national" name="user[bz_region]" required="required" type="radio" value="National" />
                      <span>National</span> </label>
                  </div>
                  <div>
                    <input class="controls-details" id="user_bz_region_other_" maxlength="60" name="user[bz_region]" required="required" type="radio" value="Other: " />
                    <label for="user_bz_region_other">Other:</label>
                    <div class="form-option-details">
                      <section>
                        <div class="col-sm-6 required required-block">
                          <input class="form-control" id="user_city" name="user[city]" placeholder="City" required="required" type="text" />
                        </div>
                        <div class="col-sm-6 required required-block">
                          <select class="form-control placeholder-style" id="user_state" name="user[state]" onchange="$(this)[this.selectedIndex == 0 ? &quot;addClass&quot; : &quot;removeClass&quot;](&quot;placeholder-style&quot;);" style="margin: 5px 0">
                            <option value="">State</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="DC">District of Columbia</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                          </select>
                        </div>
                      </section>
                      <input name="user[like_to_know_when_program_starts]" type="hidden" value="0" />
                      <input id="user_like_to_know_when_program_starts" name="user[like_to_know_when_program_starts]" type="checkbox" value="1" />
                      <label for="user_like_to_know_when_program_starts">I&#39;d like to know when BZ starts a program in my area</label>
                      <br />
                      <input name="user[like_to_help_set_up_program]" type="hidden" value="0" />
                      <input id="user_like_to_help_set_up_program" name="user[like_to_help_set_up_program]" type="checkbox" value="1" />
                      <label for="user_like_to_help_set_up_program">I&#39;d like to help BZ set up a program in my area</label>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <section>
              <div> Industry: </div>
              <div class="col-sm-7">
                <input class="form-control" id="user_profession" maxlength="70" name="user[profession]" size="70" type="text" />
              </div>
            </section>
            <section>
              <div class="required"> Company: </div>
              <div class="col-sm-7">
                <input class="form-control" id="user_company" maxlength="70" name="user[company]" required="required" size="70" type="text" />
              </div>
            </section>
          </div>
        </div>
        <div class="clickable-option">
          <input id="user_applicant_type_partner" name="user[applicant_type]" required="required" type="radio" value="partner" />
          <label for="user_applicant_type_partner">Working in higher education and am interested in partnering with Braven</label>
          <div class="form-option-details">
            <section>
              <div class="col-sm-12 form-group"> <span class="required">I am on the faculty and/or staff at:</span> <br />
                <div>
                  <input id="user_university_name_city_college_of_new_york" name="user[university_name]" required="required" type="radio" value="City College of New York" />
                  <label for="user_university_name_City College of New York">City College of New York</label>
                  <div class="form-option-details"></div>
                </div>
                <div>
                  <input id="user_university_name_hunter_college" name="user[university_name]" required="required" type="radio" value="Hunter College" />
                  <label for="user_university_name_Hunter College">Hunter College</label>
                  <div class="form-option-details"></div>
                </div>
                <div>
                  <input id="user_university_name_rutgers_university_-_newark" name="user[university_name]" required="required" type="radio" value="Rutgers University - Newark" />
                  <label for="user_university_name_Rutgers University - Newark">Rutgers University - Newark</label>
                  <div class="form-option-details"></div>
                </div>
                <div>
                  <input id="user_university_name_san_jose_state_university" name="user[university_name]" required="required" type="radio" value="San Jose State University" />
                  <label for="user_university_name_San Jose State University">San Jose State University</label>
                  <div class="form-option-details"></div>
                </div>
                <div class="form-group">
                  <input id="user_university_name_other" name="user[university_name]" required="required" type="radio" value="other" />
                  <label for="user_university_name_other">None of the above</label>
                  <div class="form-option-details">
                    <input class="form-control" id="undergrad_university_name" maxlength="70" name="undergrad_university_name" placeholder="Your college name and state" required="required" style="max-width: 250px;" type="text" />
                    <div class="clickable-option">
                      <input name="user[like_to_know_when_program_starts]" type="hidden" value="0" />
                      <input id="user_like_to_know_when_program_starts" name="user[like_to_know_when_program_starts]" type="checkbox" value="1" />
                      <label for="user_like_to_know_when_program_starts">I&#39;d like to know when BZ starts a program at my college</label>
                    </div>
                    <div class="clickable-option">
                      <input name="user[like_to_help_set_up_program]" type="hidden" value="0" />
                      <input id="user_like_to_help_set_up_program" name="user[like_to_help_set_up_program]" type="checkbox" value="1" />
                      <label for="user_like_to_help_set_up_program">I&#39;d like to help BZ set up a program in my area</label>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
        <div class="clickable-option with-text-field">
          <input id="user_applicant_type_other" name="user[applicant_type]" required="required" type="radio" value="other" />
          <label for="user_applicant_type_other">Other?</label>
          <input class="form-control" id="user_applicant_details" maxlength="72" name="user[applicant_details]" onclick="$(&quot;#user_applicant_type_other&quot;).click();" placeholder="What are you interested in?" size="72" type="text" />
          <div class="form-option-details"> <br />
            <section>
              <div class="col-sm-5">
                <input class="form-control" id="user_city" maxlength="60" name="user[city]" placeholder="City" size="60" type="text" />
              </div>
              <div class="col-sm-5">
                <select class="form-control placeholder-style" id="user_state" name="user[state]" onchange="$(this)[this.selectedIndex == 0 ? &quot;addClass&quot; : &quot;removeClass&quot;](&quot;placeholder-style&quot;);">
                  <option value="">State</option>
                  <option value="AL">Alabama</option>
                  <option value="AK">Alaska</option>
                  <option value="AZ">Arizona</option>
                  <option value="AR">Arkansas</option>
                  <option value="CA">California</option>
                  <option value="CO">Colorado</option>
                  <option value="CT">Connecticut</option>
                  <option value="DE">Delaware</option>
                  <option value="DC">District of Columbia</option>
                  <option value="FL">Florida</option>
                  <option value="GA">Georgia</option>
                  <option value="HI">Hawaii</option>
                  <option value="ID">Idaho</option>
                  <option value="IL">Illinois</option>
                  <option value="IN">Indiana</option>
                  <option value="IA">Iowa</option>
                  <option value="KS">Kansas</option>
                  <option value="KY">Kentucky</option>
                  <option value="LA">Louisiana</option>
                  <option value="ME">Maine</option>
                  <option value="MD">Maryland</option>
                  <option value="MA">Massachusetts</option>
                  <option value="MI">Michigan</option>
                  <option value="MN">Minnesota</option>
                  <option value="MS">Mississippi</option>
                  <option value="MO">Missouri</option>
                  <option value="MT">Montana</option>
                  <option value="NE">Nebraska</option>
                  <option value="NV">Nevada</option>
                  <option value="NH">New Hampshire</option>
                  <option value="NJ">New Jersey</option>
                  <option value="NM">New Mexico</option>
                  <option value="NY">New York</option>
                  <option value="NC">North Carolina</option>
                  <option value="ND">North Dakota</option>
                  <option value="OH">Ohio</option>
                  <option value="OK">Oklahoma</option>
                  <option value="OR">Oregon</option>
                  <option value="PA">Pennsylvania</option>
                  <option value="RI">Rhode Island</option>
                  <option value="SC">South Carolina</option>
                  <option value="SD">South Dakota</option>
                  <option value="TN">Tennessee</option>
                  <option value="TX">Texas</option>
                  <option value="UT">Utah</option>
                  <option value="VT">Vermont</option>
                  <option value="VA">Virginia</option>
                  <option value="WA">Washington</option>
                  <option value="WV">West Virginia</option>
                  <option value="WI">Wisconsin</option>
                  <option value="WY">Wyoming</option>
                </select>
              </div>
            </section>
          </div>
        </div>
      </div>
      <div class="form-group">
        <textarea class="form-control" id="user_applicant_comments" maxlength="4000" name="user[applicant_comments]" placeholder="Comments, thoughts, or questions?">
</textarea>
      </div>
      <div class="form-group">
        <button type="submit" class="button-primary btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>
						
					

				
				</article><!-- #post -->

			
			<?php endwhile; ?>
		</div><!-- #content -->
	

<div id="right-content">
<div id="braven_sidebar">

	<h2 class="side-hdg">Why Join</h2>
				<ul class="recent_posts">
						<li>
               
  <blockquote>
    <p>&ldquo;You become part of a team that could actually make a difference. You learn that you have a story and how to tell it. You meet lots of highly motivated, inspiring people.&rdquo;</p>
    <footer>Andy Yitalo, Stanford University, class of 2017</footer>
  </blockquote>
						</li>
                        <li>  <blockquote>
    <p>&ldquo;[Braven] had provided me with innumerable opportunities for personal and professional growth, but the Capstone project took our learning experiences beyond that of our own and empowered me to give back to my community. I really appreciated this opportunity, it was a great learning experience in regards to leadership for me as a project manager.&rdquo;</p>
    <footer>Kelly Hernandez, Stanford University, class of 2016</footer>
  </blockquote>
</li>
</ul>





</div>
	
		</div>

<div class="clear"></div>
</div><!-- #primary -->
<style>
#footer-email,  
#breadcrumb-wrapper,
#footer_bottom {
	display: none;
}

</style>
<?php get_footer(); ?>