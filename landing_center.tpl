{literal}
<script type="text/javascript">

</script>
{/literal}

		<div class="main-page-wrapper">
		<div style="width:100%;height:120px;">
           <div class="container">
			<div class="main-landing-header-row">
				<div class="main-landing-logo landing-logo-wrapper">
					<a href="http://shaukk.com/?sk=home"></a><img class="main-landing-logo"  src="images/Logo-Landing-Page (1).png" alt="Shaukk"/>
                    <h1 style="float:left;width:0;height:0;overflow:hidden">Shaukk</h1>
				</div>
<!--70,777 -->
                <div class="landing-login-container">
                    {if $isGuest eq "true"}
                    <div class="flipper">
                        <div class="landing-login-div" {if $winXp eq "true"}style="position:relative;"{/if} id="landing-form-sign-form">
                                <a {if $winXp eq "false"}onClick="document.querySelector('.landing-login-container').classList.toggle('flip-class');return false;"{/if} href="{$URL_login}" class="landing-button">Login</a>
                                <a class="fb-reg-button active-buttons" href="{$fb_login_Url}">
                                                 Connect with
                                </a>
                        </div>
                        {if $winXp eq "false"}
                        <div class="landing-login-div landing-login-form-div" id="landing-form-sign">
                             <form name="login-form" action="{$URL_login}" method="post">
                             <div class="input-box">
                                <label>Username</label>
                                <input type="text" name="username" />
                             </div>
                             <div class="input-box" style="margin-left:10px;">
                                <label>Password</label>
                                <input style="width:120px;height:16px;margin-top:2px;" type="password" name="password" />
                             </div>
                             <input type="hidden" name="processlogin" value="1"/>
                             <input type="hidden" name="return" value="{$get.return}"/>
                             <input type="submit" id="landing-login-submit" value="Login"/>
                        </form>
                        </div>
                        {/if}
                    </div>
                    {/if}
                 </div>
             </div>
          </div>
          <div class="landing-big-conatiner">
                <div class="landing-image-container">
                  <ul class="landing-cont-wrapper slideshow" id="landing-image-container">
                    <li id="landing_center_main_container-10">
                        <div class="landing-image-wrapper">
                            <img class="landing_center_image" src="{$my_base_url}{$my_pligg_base}/images/landing_images/landing_center_image-3.jpg"/>
                        </div>
                        <div class="landing-imag-info-wrapper">
                            <div class="landing-imag-info">
                            Don't find company to enjoy your interests locally? <br><br> Find like-minded around you - Plan and play together. Post a query. Share posts and news.
                            </div>
                        </div>
                    </li>
                    <li id="landing_center_main_container-2">
                        <div class="landing-image-wrapper">
                            <img class="landing_center_image" src="{$my_base_url}{$my_pligg_base}/images/landing_images/landing_center_image1.jpg"/>
                        </div>
                        <div class="landing-imag-info-wrapper">
                            <div class="landing-imag-info">
                            Bored of watching the TV or going to the same restaurant over the weekend? <br><br> Discover new interesting things to do near you.
                            </div>
                        </div>
                    </li>
                      <li id="landing_center_main_container-1">
                          <div class="landing-image-wrapper">
                              <img class="landing_center_image" src="{$my_base_url}{$my_pligg_base}/images/landing_images/landing_center_image-2.jpg"/>
                          </div>
                          <div class="landing-imag-info-wrapper">
                              <div class="landing-imag-info">
                               Wish to try out Adventure Sports, Board Games, Photography & more? <br><br> Explore & Develop new interests with people just like you.
                              </div>
                          </div>
                      </li>

                    <li id="landing_center_main_container-3">
                        <div class="landing-image-wrapper">
                            <img class="landing_center_image" src="{$my_base_url}{$my_pligg_base}/images/landing_images/landing_center_image-1.jpg"/>
                        </div>
                        <div class="landing-imag-info-wrapper">
                            <div class="landing-imag-info">
                            Want to pursue your passion and take it to the next level? <br><br> <a href="http://shaukk.com/bawraas" style="color:#f16667;">Click here</a> and Apply to "Tu Bawraa hai toh chal" contest.<br> (Any Indian can apply!)
                            </div>
                        </div>
                    </li>


                  </ul>
              </div>
        <div class="landing-form">
            <div class=container>
                    <div style="float:left;width:300px;">
                        <div class="input-box">
                            <div class="placeholder modern-placeholder" id="placeholder-landing-interest" style="font-family:Open sans;font-weight:300;font-size: 18px;" >&nbsp;&nbsp;What interests you?</div>
                            <input placeholder="What interests you?" type="text" style="font-weight:400;padding-left:15px;width:290px;" altname="interest" class="text validate[required]" name="interest" id="landing-interest"  />
                        </div>
                        <div id="interest_suggestions" style="float:left;"></div>
                    </div>
                    <div style="float:left;width:530px; margin-left:10px;">
                        <div class="input-box">
                            <div class="placeholder modern-placeholder" id="placeholder-landing-location" style="font-size: 18px;font-family:Open sans;font-weight:300;">&nbsp;&nbsp;Where do you spend most of your time?</div>
                            <input type="text" altname="details" style="font-weight:400;width:529px;" class="text validate[required]" name="details" id="landing-location"  placeholder="Your preferred Location? e.g. powai, bandra "/>
                        </div>
                    <div id="location_suggestions" style="float:left;"></div>
                    </div>
                    <button id="landing-submit" class="landing-button active-buttons" style="margin-top:4px;margin-left:8px;">
                            Explore
                    </button>

                </div>

                <!-- red: #f16667
                 blue: #27a9e1-->
        </div>
        <div class="container">
        <div class="landing-features">
            <div class="landing-features-div">
                <h3>Interest Portal</h3>
                <div class="landing-features-content">
                    No need to look further.. Shaukk has it all.. Enjoy all your interests at one stop ranging from adventure sports to dining.
                </div>
            </div>
            <div class="landing-features-div">
                <h3>Local Communities</h3>
                <div class="landing-features-content">
                You can enjoy your interests with like minded people within your locality. Plan things together or find interesting things to do nearby.
                </div>
            </div>
            <div class="landing-features-div" style="margin-right:10px;">
                <h3><i>"Tu Bawraa hai to chal"</i> contest</h3>
                <div class="landing-features-content">
                    If you want to pursue your passion, enter "tu bawraa hai to chal" contest. <a href="http://shaukk.com/bawraas" style="color:#f16667;">Click here</a> to know more
                </div>
            </div>
            <iframe  src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FShaukk&amp;width=150&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;header=false&amp;stream=false&amp;show_border=false" scrolling="no" frameborder="0" style="margin-top:15px;overflow:hidden; width:150px; height:62px;" allowTransparency="true"></iframe>
            <div>
            <iframe id="twitter-widget-1" scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/follow_button.1384994725.html#_=1386239310335&id=twitter-widget-1&lang=en&screen_name=shaukked&show_count=false&show_screen_name=true&size=m" class="twitter-follow-button twitter-follow-button" title="Twitter Follow Button" data-twttr-rendered="true" style="float: right;margin-right: -161px;margin-top: 15px;">

            </iframe>
            </div>
        </div>

        </div>
