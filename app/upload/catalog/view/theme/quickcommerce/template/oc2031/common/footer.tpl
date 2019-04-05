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
                    

        <footer id="footer" role="contentinfo">
            <?php if ($this->journal2->settings->get('config_bottom_modules')):  ?>
            <div class="container">       
                <div id="bottom-modules">
                   <?php echo $this->journal2->settings->get('config_bottom_modules'); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="container">
                <?php if ($this->journal2->settings->get('config_footer_modules')):  ?>
                <?php echo $this->journal2->settings->get('config_footer_modules'); ?>
                <?php endif; ?>
            
                <?php echo $this->journal2->settings->get('config_footer_menu'); ?>
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
					<?php if ($this->journal2->settings->get('config_copyright')): ?>
                    <div class="footer__brand">
                        <?php if ($this->journal2->settings->get('config_payments')): ?>
                            <div class="payments">
                            <?php foreach ($this->journal2->settings->get('config_payments') as $payment): ?>
                            <?php if ($payment['url']): ?>
                            <a href="<?php echo $payment['url']; ?>" <?php echo $payment['target']; ?>><img src="<?php echo $this->journal2->settings->get('config_payments_dummy'); ?>" <?php echo Journal2Utils::imgElement($payment['image'], $payment['name'], $payment['width'], $payment['height']); ?> /></a>
                            <?php else: ?>
                            <img src="<?php echo $this->journal2->settings->get('config_payments_dummy'); ?>" <?php echo Journal2Utils::imgElement($payment['image'], $payment['name'], $payment['width'], $payment['height']); ?> />
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <span class="copyright">
							<!--© Copyright 2017 ACE Coffee Roasters Ltd. (acecoffeeroasters.com). All rights reserved.
							<br/>
							Product and company names are trademarks™ or registered® trademarks of their respective holders.
							<br/>
							Website conceptualized and designed by Firebrand Web Solutions.
                            -->
                            
                        <?php echo $this->journal2->settings->get('config_copyright'); ?></div>
						</span>
					</div>
                    <?php endif; ?>
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
    
    <?php $this->journal2->minifier->addScript('catalog/view/theme/journal2/js/init.js', 'footer'); ?>
    <?php echo $this->journal2->minifier->js('footer'); ?>
    <?php if ($this->journal2->cache->getDeveloperMode() || !$this->journal2->minifier->getMinifyJs()): ?>
    <script type="text/javascript" src="index.php?route=journal2/assets/js&amp;j2v=<?php echo JOURNAL_VERSION; ?>"></script>
    <?php endif; ?>
    <?php if ($this->journal2->html_classes->hasClass('is-admin')): ?>
    <script src="catalog/view/theme/journal2/lib/ascii-table/ascii-table.min.js"></script>
    <script type="application/javascript">
        (function () {
            if (console && console.log) {
                var timers = $.parseJSON('<?php echo json_encode(Journal2::getTimer()); ?>');
                timers['Total'] = parseFloat('<?php echo Journal2::getElapsedTime(); ?>');
                var table = new AsciiTable('Journal2 Profiler');
                table.setAlignRight(1);
                $.each(timers, function (index, value) {
                    if (value < 0) {
                        value = 0;
                    }
                    if (value < 100000) {
                        table.addRow(index.replace('ControllerModuleJournal2', ''), Math.round(value * 1000) + ' ms');
                    }
                });
                console.log(table.toString());
            }
        }());
    </script>
    <?php endif; ?>

    <script type="application/javascript">
        // Cross origin doesn't work
        /*(function () {
            // TODO: Tie this into iframe load event
            $('#sfa-schedule').load(function() {
                console.log('sfa schedule loaded');
                $('#sfa-schedule').contents().find('head')
                  .append($("<style type='text/css'>  .streetfoodapp-widget-header, .streetfoodapp-widget-footer {text-align:center;display:none;}  </style>"));
            });
        }());*/
    </script>
    <!--<script type="text/javascript" src="cordova.js"></script>-->

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
