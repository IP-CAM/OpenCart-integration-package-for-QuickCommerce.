<?php echo $header; ?>
        <main role="main">
            <div class="mainContent home">
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
                    
                    <div id="container" class="container-fluid j-container">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Wrap in normal div so we don't flow over the container -->
                                <div class="bg-ace-red">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-8 col-lg-6 col-md-push-2 col-lg-push-3">
                                            <?php echo $content_top; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="content" class="<?php echo $class; ?> col-sm-12">
                                <div id="main"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Wrap in normal div so we don't flow over the container -->
                                <div id="contact-form" class="bg-ace-red">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-8 col-lg-6 col-md-push-2 col-lg-push-3">
                                            <?php echo $content_bottom; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
<?php echo $footer; ?>