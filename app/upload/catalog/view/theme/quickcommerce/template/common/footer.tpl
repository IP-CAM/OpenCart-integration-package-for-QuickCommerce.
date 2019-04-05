<?php
    if (!defined('QUICKCOMMERCE_INSTALLED')) {
        echo '
            <h3>QuickCommerce installation error!</h3>
            <p>Make sure you have uploaded all package files to your server and successfully replaced the <b>system/engine/front.php</b> file.</p>
            <p>You can find more information <a href="#" target="_blank">here</a>.</p>
        ';
        exit();
    }
?>
                    <div class="g-grid container-fluid" style="background: white">
                        <div class="g-block size-100">
                            <div class="g-content g-particle">
                                <div class="g-infolist g-3cols ">
                                    <div class="g-infolist-item g-infolist-with-icon center">
                                        <div class="g-infolist-item-text  g-infolist-textstyle-compact">
                                            <div class="g-infolist-item-icon center">
                                                <div class="icon_box icon_position_top no_border">
                                                    <a class="load-checkout">
                                                        <div class="icon_wrapper">
                                                            <div class="icon">
                                                                <i class="icon-paper-plane"></i>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="g-infolist-item-title"><h4>EXPRESS DELIVERY</h4></div>
                                            <div class="g-infolist-item-desc">
                                                Our items are always delivered to your door fast. Express and overnight delivery available upon request.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="g-infolist-item g-infolist-with-icon center">
                                        <div class="g-infolist-item-text  g-infolist-textstyle-compact">
                                            <div class="g-infolist-item-icon center">
                                                <div class="icon_box icon_position_top no_border">
                                                    <a class="load-checkout">
                                                        <div class="icon_wrapper">
                                                            <div class="icon">
                                                                <i class="icon-bag"></i>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="g-infolist-item-title"><h4>BUY MORE AND SAVE</h4></div>
                                            <div class="g-infolist-item-desc">
                                                We like to reward our friends and help them save! Order ACE coffee in packs of 3, 6 or more and the shipping's on us.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="g-infolist-item g-infolist-with-icon center">
                                        <div class="g-infolist-item-text  g-infolist-textstyle-compact">
                                            <div class="g-infolist-item-icon center">
                                                <div class="icon_box icon_position_top no_border">
                                                    <a class="load-checkout">
                                                        <div class="icon_wrapper">
                                                            <div class="icon">
                                                                <i class="icon-flag"></i>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="g-infolist-item-title"><h4>DELIVERED TO YOUR DOOR</h4></div>
                                            <div class="g-infolist-item-desc">
                                                Sign up for a subscription and have a selection of ACE delivered directly to your door, on a schedule you get to choose.*
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="fullwidth-footer">
                        <div class="footer">
                            <div class="columns" style="background-attachment: scroll; overflow: hidden; height: 480px; width: 100%; position: absolute; z-index: 1">
                                <div id="embedded-map-display" style="height:600px; width:100%; max-width:100%;">
                                    <iframe style="height:100%; border:0;" frameborder="0" src="http://streetfoodapp.com/widgets/map/edmonton/jimmy-poblanos?display=name,description,openings" width="100%" height="600"></iframe>
                                </div>
                            </div>
                            
                            <div class="row store-location" style="position: relative">
                                <!--<svg viewBox="0 0 1920 760" class="hidden-phone" width="100%" height="760" style="position: absolute; top: 0; left: 0">-->
                                <svg width="100%" height="760" style="position: absolute; top: 0; left: 0; z-index: 15">
                                  <defs>
                                    <mask id="mask" x="0" y="0" width="1920" height="760">
                                      <rect x="0" y="0" width="1920" height="760" fill="#fff"/>
                                      <circle cx="1767" cy="25" r="800" />
                                    </mask>
                                  </defs>
                                  <rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#85312C" fill-opacity="0.888"/><!-- ACE Red -->
                                </svg>
                                <div class="menu col-xs-12 col-sm-3 col-md-3 col-lg-3 hidden-phone" style="height: 450px;"></div>
                                <div class="menu col-xs-12 col-sm-6 col-md-6 col-lg-3" style="position: relative; z-index: 15; height: 450px; padding-top: 150px;">
                                    <h4 style="color: white; text-align: center">Now Roasting At...</h4>
                                    <div class="column-text-wrap" style="color: white; text-align: center; font-size: 1.05rem; line-height: 2.0rem; max-width: 80%; margin-left: auto; margin-right: auto; padding-top: 0;">
                                        <p>10055 80th Avenue NW<br>Edmonton, Alberta, Canada<br><b>Phone: 780.244.0ACE</b><br>info@acecoffeeroasters.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div class="g-grid container-fluid">
                        <div class="g-block center size-65">
                            <div class="g-content g-particle">
                                <div class="">
                                    <div class="g-simplecontent">
                                        <div class="g-simplecontent-item g-simplecontent-layout-header">
                                            <div class="g-simplecontent-item-content-title">
                                                <h4>Subscribe to our newsletter and we’ll send you our <span>specials and deals</span> whenever they're available.</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- container -->
            </div>
            <!-- mainContent -->
        </main>
        <!-- main -->

        <footer id="footer" role="contentinfo">
            <div class="container">       
                <div id="bottom-modules">
                </div>
            </div>
            
            <div class="container">
            </div>
            
            <div class="container">
                <div class="footer__content">
                    <div class="footerContacts">
                        <div class="footerContacts__title">
                            <h5>CONTACT</h5>
                        </div>
                        <!-- footerContacts__title -->
                        <div class="footerContacts__content">
                            <span>
			  					Tel. <b>780.244.0ACE</b><br />
								Cell <b>780.244.0ACE</b><br />
								<br>
								<a href="mailto:info@acecoffeeroasters.com">
									<b>info@acecoffeeroasters.com</b>
								</a>
							</span>
                        </div>
                        <!-- footerContacts__content -->
                    </div>
					<div class="footerContacts">
                        <div class="footerContacts__title">
                            <h5>LOCATION</h5>
                        </div>
                        <!-- footerContacts__title -->
                        <div class="footerContacts__content">
                            <span>
								10055 80 AVE NW, Edmonton
								<br />
								Alberta, Canada T6E 1T4
							</span>
                        </div>
                        <!-- footerContacts__content -->
                    </div>
                    <!-- footerContacts -->
                    <div class="footerSocial">
                        <div class="footerSocial__title">
                            <h5>SOCIALIZE</h5>
                        </div>
                        <!-- footerSocial__title -->
                        <div class="footerSocial__content">
                            <div class="socialBadges socialBadges--footer">
                                <ul class="socialBadges-ul">
                                    <li class="socialBadges-li socialBadges-li--facebook">
                                        <a href="https://www.facebook.com/acecoffeeroasters" target="_blank">
                                            <i class="fa fa-facebook"></i>
                                        </a>
                                    </li>

                                    <li class="socialBadges-li socialBadges-li--twitter">
                                        <a href="https://twitter.com/acecoffeeroasters" target="_blank">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                    </li>

                                    <li class="socialBadges-li socialBadges-li--instagram">
                                        <a href="https://www.instagram.com/acecoffeeroasters" target="_blank">
                                            <i class="fa fa-instagram"></i>
                                        </a>
                                    </li>

                                    <li class="socialBadges-li socialBadges-li--youtube">
                                        <a href="https://www.youtube.com/user/acecoffeeroasters" target="_blank">
                                            <i class="fa fa-youtube"></i>
                                        </a>
                                    </li>

                                </ul>

                            </div>
                            <!-- socialBadges -->
                        </div>
                        <!-- footerSocial__content -->
                    </div>
                    <!-- footerSocial -->
                    <div class="footer__brand">
                        <div class="payments">
                        </div>
                        <span class="copyright">
                        </div>
						</span>
					</div>
					<!-- brand -->
					<div class="footerCredits">
                        <a href="http://firebrandwebsolutions.com" target="_blank">
  							SITE BY
							<span>FIREBRAND</span>
						</a>
                    </div>
                    <!-- footerCredits -->
                    <div class="scroll-top"></div>
                </div>
                <!-- footer__content -->
            </div>
            <!-- container -->
        </footer>
        <!-- footer -->
    </div>
    <!-- site -->
    
    <div class="spinner">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
            </svg>
            <!-- circular -->
        </div>
        <!-- loader -->
    </div>
    <!-- spinner -->
    <div class="changePageLoader">
        <div class="changePageLoader__content" style="transform-origin: 50% 50% 0px; visibility: inherit; opacity: 1; transform: matrix(1, 0, 0, 1, 0, 0);">
        </div>
        <!-- changePageLoader__content -->
    </div>
    <!-- changePageLoader -->
    

	<div id="error-content" style="display: none">
        <div class="mainContent http404">
            <div class="container">
                <section class="contentTop contentTop--coverBig">
                    <div class="contentTop__image">
                        <div class="heroBg heroBg--default animateInView animateInView--pix"></div>
                    </div>
                    <div class="contentTop__title">
                        <h1>
							<div class="animateInView animateInView--text">
								<span>NOT FOUND</span>
							</div>
						</h1>
                    </div>
                </section>
                <div class="contentEntry">
                    <div class="contentEntry__body animateInView">
                        <p>PAGE NOT FOUND. SCROLL DOWN TO BROWSE OUR CATALOG.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script id="qc-cart" type="text/html">
        <div class="mainContent">
            <div class="container">
                <div class="entryModule" style="width: 100%;">
                    <div id="main"></div>
                </div>
            </div>
        </div>
	</script>

    <script id="qc-shop-script" type="text/html">
        <div class="mainContent">
            <div class="container">
                <div class="intro triggrParallx" style="max-height: 1080px; position: fixed">
                    <div class="intro__hero">
                        <div class="animateInView animateInView--pix isInView">
                            <div class="bigIntroBg slideDown" style="transform: translate(0%, 7.5%) translate3d(0px, 21.4483px, 0px);"></div>
                            <div class="bigIntroLogo">
                                <div class="logoMask"></div>
                                <!-- logoMask -->
                                <div class="logoType">
                                    <span style="top: 15px; left: 6px; transform: matrix(1, 0, 0, 1, 6.73817, -6.33665);"></span>
                                </div>
                                <!-- logoType -->
                            </div>
                            <!-- bigIntroLogo -->
                            <div class="mainClaim">
                                <!--<h2 class="animateInView animateInView--text isInView" data-delay="wider" style="top: 0px; left: 0px; transform: matrix(1, 0, 0, 1, 4.15878, -4.55777);">
															<span>ACE</span>
														</h2>-->
                            </div>
                            <!-- mainClaim -->
                        </div>
                        <!-- animateInView -->
                    </div>
                    <!-- intro__hero -->

                </div>
                <!-- hero -->

				<div class="section_wrapper mcb-section-inner">
                    <div class="wrap mcb-wrap one dark valign-top clearfix" style="">
                        <div class="mcb-wrap-inner">
                            <div class="column mcb-column one-sixth column_placeholder">
                                <div class="placeholder">&nbsp;</div>
                            </div>
                            <div class="column mcb-column two-third column_column  column-margin-">
                                <div class="column_attr clearfix align_center" style="">
                                    <h2 class="heading-with-border">The ACE Shop</h2>
                                    <h3>Awesome coffee. Great accessories.</h3>
                                    <h4>The dev is working on this section. Please check back soon!</h4>
                                    <h5>Find just about everything you need to ensure your best possible coffee loving experience!</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section_wrapper mcb-section-inner">
                    <div class="wrap mcb-wrap one dark valign-top clearfix" style="">
                        <div class="mcb-wrap-inner">
                            <div class="column mcb-column one-sixth column_placeholder">
                                <div class="placeholder">&nbsp;</div>
                            </div>

                            <div class="column mcb-column one column_divider column-margin-40px">
                                <hr class="no_line">
                            </div>
                            
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-cup-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Coffee</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-t-shirt-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Merchandise</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-tag-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Devices</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-wallet-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Subscriptions</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="column mcb-column one column_divider column-margin-40px">
                                <hr class="no_line">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
	</script>

    <script id="qc-catalog-script" type="text/html" data-bundle="pos-bundle.js">
        <div class="mainContent">
            <div class="container">
                <div class="intro triggrParallx" style="max-height: 1080px; position: fixed">
                    <div class="intro__hero">
                        <div class="animateInView animateInView--pix isInView">
                            <div class="bigIntroBg slideDown" style="transform: translate(0%, 7.5%) translate3d(0px, 21.4483px, 0px);"></div>
                            <div class="bigIntroLogo">
                                <div class="logoMask"></div>
                                <!-- logoMask -->
                                <div class="logoType">
                                    <span style="top: 15px; left: 6px; transform: matrix(1, 0, 0, 1, 6.73817, -6.33665);"></span>
                                </div>
                                <!-- logoType -->
                            </div>
                            <!-- bigIntroLogo -->
                            <div class="mainClaim">
                                <!--<h2 class="animateInView animateInView--text isInView" data-delay="wider" style="top: 0px; left: 0px; transform: matrix(1, 0, 0, 1, 4.15878, -4.55777);">
															<span>ACE</span>
														</h2>-->
                            </div>
                            <!-- mainClaim -->
                        </div>
                        <!-- animateInView -->
                    </div>
                    <!-- intro__hero -->

                </div>
                <!-- hero -->
                
                <div class="entryModule" style="width: 100%; margin-top: 0; margin-bottom: 0;">
                    <div id="main"></div>
                </div>
            </div>
        </div>
	</script>

    <script id="qc-checkout-script" type="text/html" data-bundle="pos-bundle.js">
        <div class="mainContent">
            <div class="container">
                <div class="intro triggrParallx" style="max-height: 1080px; position: fixed">
                    <div class="intro__hero">
                        <div class="animateInView animateInView--pix isInView">
                            <div class="bigIntroBg slideDown" style="transform: translate(0%, 7.5%) translate3d(0px, 21.4483px, 0px);"></div>
                            <div class="bigIntroLogo">
                                <div class="logoMask"></div>
                                <!-- logoMask -->
                                <div class="logoType">
                                    <span style="top: 15px; left: 6px; transform: matrix(1, 0, 0, 1, 6.73817, -6.33665);"></span>
                                </div>
                                <!-- logoType -->
                            </div>
                            <!-- bigIntroLogo -->
                            <div class="mainClaim">
                                <!--<h2 class="animateInView animateInView--text isInView" data-delay="wider" style="top: 0px; left: 0px; transform: matrix(1, 0, 0, 1, 4.15878, -4.55777);">
															<span>ACE</span>
														</h2>-->
                            </div>
                            <!-- mainClaim -->
                        </div>
                        <!-- animateInView -->
                    </div>
                    <!-- intro__hero -->

                </div>
                <!-- hero -->
                
                <div class="section_wrapper mcb-section-inner">
                    <div class="wrap mcb-wrap one dark valign-top clearfix" style="">
                        <div class="mcb-wrap-inner">
                            <div class="column mcb-column one-sixth column_placeholder">
                                <div class="placeholder">&nbsp;</div>
                            </div>
                            <div class="column mcb-column two-third column_column  column-margin-">
                                <div class="column_attr clearfix align_center" style="">
                                    <h2 class="heading-with-border">The ACE Shop</h2>
                                    <h3>Awesome coffee. Great accessories.</h3>
                                    <h4>The dev is working on this section. Please check back soon!</h4>
                                    <h5>Find just about everything you need to ensure your best possible coffee loving experience!</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section_wrapper mcb-section-inner">
                    <div class="wrap mcb-wrap one dark valign-top clearfix" style="">
                        <div class="mcb-wrap-inner">
                            <div class="column mcb-column one-sixth column_placeholder">
                                <div class="placeholder">&nbsp;</div>
                            </div>

                            <div class="column mcb-column one column_divider column-margin-40px">
                                <hr class="no_line">
                            </div>
                            
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-cup-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Coffee</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-t-shirt-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Merchandise</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-tag-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Devices</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="column mcb-column one-fourth column_icon_box ">
                                <div class="icon_box icon_position_top no_border">
                                    <a class="load-checkout">
                                        <div class="icon_wrapper">
                                            <div class="icon">
                                                <i class="icon-wallet-line"></i>
                                            </div>
                                        </div>
                                        <div class="desc_wrapper">
                                            <h4 class="title">Subscriptions</h4>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="column mcb-column one column_divider column-margin-40px">
                                <hr class="no_line">
                            </div>

                        </div>
                    </div>
                </div>
                
                <div class="entryModule" style="width: 100%;">
                    <div id="main"></div>
                </div>
            </div>
        </div>
	</script>
    <!--<script type="text/javascript" src="cordova.js"></script>-->
    <script type="text/javascript" src="site/js/react-bundle.js"></script>
    <script type="text/javascript" src="site/js/pos-bundle.js"></script>
    <!--<script type="text/javascript" src="catalog/view/javascript/quickcommerce/js/bower-bundle.js"></script>-->
    <!--<script type="text/javascript" src="app/build/js/jquery/dist/jquery.min.js"></script>-->
    <script type="text/javascript" src="app/build/js/grommet/grommet.min.js"></script>

    <script type="text/javascript">
        // Rudimentary scroll watcher, for complex apps we might want a dispatcher
        // Also, would be interesting to see if there's a React equivalent
        var w = window,
            doc = document;
        
        
        // This is very inefficient need to look at throttling
        function productSnapTop() {
            var scrollY = w.scrollY;
            var parent = doc.getElementById('product-tabs');
            
            if (!parent || typeof parent === 'undefined') return false;
            
            var summary = parent.getElementsByClassName('summary-component')[0],
                productForm = doc.getElementsByClassName('product-form-component')[0];
                pinnedOffsetTop = parent.getElementsByClassName('pinned-offset-top')[0]; /* TODO: Multiple offsets and elements */
            
            
            
            if (scrollY > 120) {
                summary.classList.add('pinned');
                productForm.classList.add('pinned');
                
                pinnedOffsetTop.classList.add('offset-pinned'); // TODO: Use data attributes
            } else {
                summary.classList.remove('pinned');
                productForm.classList.remove('pinned');
                
                pinnedOffsetTop.classList.remove('offset-pinned'); // TODO: Use data attributes
            }
        }
        
        function categorySnapTop() {
            var scrollY = w.scrollY;
            var parent = doc.getElementById('category-tabs');
            
            if (!parent || typeof parent === 'undefined') return false;
                
            var summary = parent.getElementsByClassName('summary-component')[0],
                categoryForm = doc.getElementsByClassName('category-form-component')[0];
                pinnedOffsetTop = parent.getElementsByClassName('pinned-offset-top')[0]; /* TODO: Multiple offsets and elements */
            
            if (scrollY > 120) {
                summary.classList.add('pinned');
                categoryForm.classList.add('pinned');
                
                pinnedOffsetTop.classList.add('offset-pinned'); // TODO: Use data attributes
            } else {
                summary.classList.remove('pinned');
                categoryForm.classList.remove('pinned');
                
                pinnedOffsetTop.classList.remove('offset-pinned'); // TODO: Use data attributes
            }
        }
        
        function toggleMotionMenu() {
            /*var scrollY = w.scrollY;
            var menus = document.getElementsByClassName('motion-menu');
            if (typeof menus !== 'undefined') {
                if (scrollY > 382) {
                    menus[0].style.display = 'none';
                } else {
                    menus[0].style.display = 'block';
                }
            }*/
        }
        
        // Disable for this theme...
        window.addEventListener('scroll', function() {
            //productSnapTop();
            //categorySnapTop();
            //toggleMotionMenu();
        });
    </script>
</body>
</html>
