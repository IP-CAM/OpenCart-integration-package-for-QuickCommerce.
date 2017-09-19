<?php echo $header; ?>

<style>
.loader {
    position: fixed;
    color: white;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: skyblue;
    z-index: 99999;
}
</style>
<script type="application/javascript">
    $(window).on('load', function () {
        //$(document.body).prepend('<div class="loader">Loading...</div>');
    });
    
    $(document).ready(function() {
        
    });
</script>

<div class="extended-container">

    <div class="site">

        <div class="off-canvas-trigger"><!-- For off-canvas burger menu -->
            <!--<input class="burger-check" id="nav-trigger-anim" type="checkbox"><label for="burger-check" class="burger"></label>-->
            <input class="burger-check" id="nav-trigger-anim" type="checkbox"><label for="nav-trigger-anim" class="burger"></label>
            <!--<label id="nav-trigger-anim"><span></span></label>-->
        </div>

        <div id="container" class="container j-container">
          <div class="row">
            <?php /*
            <?php echo $column_left; ?><?php echo $column_right; ?>
            <?php if ($column_left && $column_right) { ?>
            <?php $class = 'col-sm-6'; ?>
            <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-9'; ?>
            <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
            <?php } ?>
            */ ?>
            <div id="content" class="<?php echo $class; ?>">
                <div style="" class="ppb_wrapper ">
                  <div class="background"
                  <div class="parallax_wrapper scene" style="height: 2600px; overflow: hidden; position: relative; z-index: 0;">
                    <div style="padding: 0 !important; position: relative;" class="parallax">
                        <div style="position: relative;" class="scene inner">
                            <div id="scene-logo" class="parallax_scroll scene" data-top="180" style="top: 180px; z-index: 1500;" data-stellar-ratio="1.2">
                                <img style="display: block; margin: 0 auto; max-width: 40%;" src="image/scene/jp_logo.png"/>
                            </div>
                            <div id="scene-bg" class="parallax_scroll scene" data-top="0" style="top: 0px;" data-stellar-ratio="0.8">
                                <img style="width: 100%" src="image/scene/jp_background.jpg"/>
                            </div>
                            <div id="scene-sun" class="parallax_scroll scene" data-top="340" style="top: 340px; z-index: 900;" data-stellar-ratio="1">
                                <img src="image/scene/jp_sun.png" style="width: 10%; float: left; position: relative; left: 15%;"/>
                            </div>
                            <div id="scene-clouds-1" class="parallax_scroll scene" style="z-index: 1000;" data-stellar-ratio="1">
                                <img src="image/scene/jp_clouds-01.png" style="width: 70%; max-height: 179px; float: right;"/>
                            </div>
                            <div id="scene-clouds-2" class="parallax_scroll scene" data-top="280" style="top: 280px; z-index: 1000;" data-stellar-ratio="1.3">
                                <img src="image/scene/jp_clouds-02.png" style="width: 90%; max-height: 115px; float: left; position: relative; left: 15%;"/>
                            </div>
                            <div id="scene-clouds-3" class="parallax_scroll scene" data-top="510" style="top: 510px; z-index: 1000; display: none;" data-stellar-ratio="1.6">
                                <img src="image/scene/jp_clouds-03.png" style="width: 70%; max-height: 113px; float: left; position: relative; left: 20%;"/>
                            </div>
                            
                            <div id="text" style="position: fixed; top: 620px; width: 800px; z-index: 500">
                                <section class="one withsmallpadding ppb_text" style="text-align: center; padding: 4rem 0;">
                                  <div class="page_content_wrapper">
                                    <div class="inner">
                                      <div class="page_title">
                                        <h2 class="ppb_title"><span class="ppb_title_first" style="color: #424242;">Stay Calm, </span>and Eat Chiles</h2>
                                      </div>
                                      <div class="ppb_subtitle">A different take on Mexican food</div>
                                      <div class="page_title_intro">
                                        <p><b>Jimmy Poblano's</b>, like all wonderful creations, is born from love. Our love of good food, our love of people especially our family, friends & each other and <i>of course our love of chiles</i>. We use locally sourced and sustainable ingredients (where possible) prepared fresh in all our dishes. With one exception, <b>our green and red chiles are direct from the growers in Hatch, New Mexico</b>. Our cuisine is a different take on Mexican food and one we hope you love as much as we do in bringing it to you.</p>
                                      </div>
                                    </div>
                                  </div>
                                </section>
                            </div>
                            
                            <div id="scene-outback" class="parallax_scroll scene" data-top="550" style="top: 550px;" data-stellar-ratio="0.8">
                                <img src="image/scene/jp_outback.gif"/>
                            </div>
                            <div id="scene-over-the-hill" class="parallax_scroll scene" data-top="800" style="top: 800px;" data-stellar-ratio="1.1">
                                <img src="image/scene/jp_over-the-hill.gif"/>
                            </div>
                            <div id="scene-cliff-elements" class="parallax_scroll scene" data-top="1650" style="top: 1650px;" data-stellar-ratio="1.9">
                                <img src="image/scene/jp_cliff-elements.gif"/>
                            </div>
                            <div id="scene-truck" class="parallax_scroll scene" data-top="3800" style="top: 3800px; text-align: right; z-index: 1500" data-stellar-ratio="1.9">
                                <!-- Quick hack to fix height, it's a little off for some reason -->
                                <img src="image/scene/jp_truck.gif"/>
                            </div>
                            <div id="scene-fg-02" class="parallax_scroll scene" data-top="2630" style="top: 2630px;" data-stellar-ratio="1.7">
                                <img src="image/scene/jp_foreground-02.gif"/>
                            </div>
                            <div id="scene-fg-01" class="parallax_scroll scene" data-top="2230" style="top: 2230px;" data-stellar-ratio="1.9">
                                <img src="image/scene/jp_foreground-01.gif"/>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
                <script type="text/javascript">
                    $(document).ready(function () {
                        function getPos(el) {
                          var xPos = 0;
                          var yPos = 0;
                         
                          while (el) {
                            if (el.tagName == 'BODY') {
                              // deal with browser quirks with body/window/document and page scroll
                              var xScroll = el.scrollLeft || document.documentElement.scrollLeft;
                              var yScroll = el.scrollTop || document.documentElement.scrollTop;
                         
                              xPos += (el.offsetLeft - xScroll + el.clientLeft);
                              yPos += (el.offsetTop - yScroll + el.clientTop);
                            } else {
                              // for all other non-BODY elements
                              xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
                              yPos += (el.offsetTop - el.scrollTop + el.clientTop);
                            }
                         
                            el = el.offsetParent;
                          }
                          
                          return {
                            x: xPos,
                            y: yPos
                          };
                        }
                        
                        var ow = 1920, 
                            w, h, s;
                            
                        w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
                        h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                        
                        $window = $(window);
                        
                        $window.on('resize', function () {
                            w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
                            h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                            
                            s = w / ow; // Scale factor
                            
                            // Fix left and top co-ords
                            var sa = document.getElementsByClassName('scene');
                            for (var idx = 0; idx < sa.length; idx++) {
                                var a = sa[idx];
                                a.style.top = (parseInt(a.getAttribute('data-top'), 10) * s) + 'px';
                            }
                            
                            if (w < 1024) {
                                //el = document.getElementById('scene-outback').style.top = '90px'; 
                                el = document.getElementById('scene-over-the-hill').style.display = 'none'; 
                                el = document.getElementById('scene-cliff-elements').style.display = 'none';
                                el = document.getElementById('scene-fg-02').style.display = 'none';
                                el = document.getElementById('scene-truck').style.top = '625px'; 
                                el = document.getElementById('scene-fg-01').style.top = '20px';
                            } else {
                                el = document.getElementById('scene-over-the-hill').style.display = 'block'; 
                                el = document.getElementById('scene-cliff-elements').style.display = 'block';
                                el = document.getElementById('scene-fg-02').style.display = 'block';
                            }
                            
                            // Can't nail this down just fix for now
                            if (w < 768) {
                                el = document.getElementById('scene-truck').style.top = '300px';
                            }
                        });
                        
                        var wrapper = document.getElementsByClassName('ppb_wrapper')[0].getElementsByClassName('background')[0];
                        wrapper.style.minHeight = h + 'px';
                        
                        function isInViewport(el) {
                            var elemTop = el.getBoundingClientRect().top;
                            var elemBottom = el.getBoundingClientRect().bottom;

                            //var isVisible = (elemTop >= 0) && (elemBottom <= window.innerHeight);
                            var isVisible = elemTop < window.innerHeight && elemBottom > 0; // Not = 0, stellar snaps to screen edges
                            return isVisible;
                        }
                                        
                        // Fix parallax wrapper size
                        function setParallaxSceneHeight() {
                            /*var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
                            var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                            
                            // Set height cutoff to wherever the bottom of the truck is
                            var lastSection = document.getElementById('scene-truck'),
                                truck = lastSection.firstElementChild,
                                truckRect = truck.getBoundingClientRect(),
                                wrapperRect = wrapper.getBoundingClientRect();
                            
                            //wrapper.style.height = (truckRect.bottom - wrapperRect.top) + 'px';
                            
                            console.log(isInViewport(truck));
                            
                            if (isInViewport(truck)) {
                                console.log('fuck');
                            }
                            
                            console.log(document.body.scrollHeight);
                            console.log(truck.scrollHeight);*/
                        }
                        
                        var elements = [];
                        elements.push($('#scene-logo'));
                        
                        $window = $(window);
                        // Scrolling stuff
                        //$window.on('scroll resize', positionedInView);
                        //$window.trigger('scroll');
                        
                        var scrollTimeout = null;
                        var scrollEndDelay = 60; // ms
                        
                        $window.on('scroll', function () {
                            var logo = $('#scene-logo'),
                                text = $('#text');
                            
                            if (logo.is(':hidden') === true) {
                                text.fadeIn();
                            } else {
                                text.fadeOut();
                            }
                            
                            if (scrollTimeout === null) {
                                setParallaxSceneHeight();
                            } else {
                                clearTimeout(scrollTimeout);
                            }
                            
                            scrollTimeout = setTimeout(function () {
                                scrollTimeout = null;
                            }, scrollEndDelay);
                        });
                    });
                </script>
                
            </div>
          </div>
        </div><!-- END Main container -->

    </div><!-- END Site container (for off canvas menu) -->

    <div class="off-canvas">
        <div class="off-canvas-menu-wrapper">
            <!-- Temporarily adjust max-height on this area -->
            <div class="primary-nav menu-element">
                <div class="nav-content">
                    <p class="top-level"><a href="http://jimmypoblanos.com">Home</a></p>
                </div>
            </div>
            <!-- Temporarily adjust max-height on this area -->
            <div class="secondary-nav menu-element">
                <div class="nav-content">
                    <p class="top-level"><a href="#">About</a></p>
                    <?php echo $column_left; ?>
                </div>
            </div>
            
            <!-- Temporarily adjust max-height on this area -->
            <div class="quaternary-nav menu-element">
                <div class="nav-content">
                    <p class="top-level"><a href="http://jimmypoblanos.com/our-menu">Menu</a></p>
                </div>
            </div>
            
            <div class="tertiary-nav menu-element">
                <div class="nav-content">
                    <p class="top-level"><a href="#">Contact</a></p>
                    <?php echo $column_right; ?>
                </div>
                <p class="seemore" style="display: none; text-align: center">See More</p>
            </div>
        </div>
    </div>
    
    <aside class="fullwidth-footer location">
        <div class="footer">
            <div id="embedded-map-wrapper" class="row columns">
                <div id="embedded-map-display">
                    <iframe style="height:100%; border:0;" frameborder="0" src="http://streetfoodapp.com/widgets/map/edmonton/jimmy-poblanos?display=name,description,openings" width="100%" height="100%"></iframe>
                </div>
            </div>
            
            <div class="row columns store-location">
                <!--<svg viewBox="0 0 1920 760" class="hidden-phone" width="100%" height="760" style="position: absolute; top: 0; left: 0">
                  <defs>
                    <mask id="mask" x="0" y="0" width="1920" height="760">
                      <rect x="0" y="0" width="1920" height="760" fill="#fff"/>
                      <ellipse cx="960" cy="380" rx="800" ry="680" />
                    </mask>
                  </defs>
                  <rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#020202" fill-opacity="0.3"/>
                </svg>-->
                <div class="hidden-phone column menu lg-12 xl-20" style="height: 200px;"></div>
                <div class="column menu xs-100 md-100 lg-25 xl-20" style="height: 200px;">
                    <h2 style="color: white">Where's Jimmy?</h2>
                    <div class="column-text-wrap" style="max-width: 80%; margin-left: auto; margin-right: auto; padding-top: 0;">
                        <p>The Street Food App helps you conveniently track of all of your favorite food trucks.
                            <span class="hidden-lg-tablet display-desktop"><br>
                                <a href="http://streetfoodapp.com/" target="_blank" title="Learn more about the Street Food App"><b>Download the Street Food App<span class="hidden-desktop"> today</span>!</b></a>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="app-store-column column menu xs-100 md-50 lg-25 xl-20" style="height: 200px;">
                    <h2 class="h2-spacer hidden-phone display-desktop">&nbsp;</h2>
                    <div class="column-text-wrap" style="max-width: 80%; margin-left: auto; margin-right: auto; padding-top: 0;">
                        <p>
                            <a href="https://itunes.apple.com/artist/tatlow-park-software/id391419677" target="_blank" title="Download the iPhone / iPad app">
                                <img src="image/logo/app-store.png" alt="Download the iPhone / iPad app" title="Download the iPhone / iPad app"/>
                            </a>
                        </p>
                    </div>
                </div>
                <div class="google-play-column column menu xs-100 md-50 lg-25 xl-20" style="height: 200px;">
                    <h2 class="h2-spacer hidden-phone display-desktop">&nbsp;</h2>
                    <div class="column-text-wrap" style="max-width: 80%; margin-left: auto; margin-right: auto; padding-top: 0;">
                        <p>
                            <a href="https://play.google.com/store/search?q=pub:Tatlow%20Park%20Software%20Inc." target="_blank" title="Download the Android app">
                                <img src="image/logo/google-play.png" alt="Download the Android app" title="Download the Android app"/>
                            </a>
                        </p>
                    </div>
                </div>
                <div class="hidden-phone column menu lg-12 xl-20" style="height: 200px;"></div>
            </div>
        </div>
    </aside>

    <div class="content-plus">

        <div class="hidden-phone parallax title" id="mains" style="height: 1000px; max-height:760px;" data-image="assets/images/menu-items/2016-Jimmy-Poblanos-001.jpg" data-width="1800" data-height="1200" data-content-height="80">
          
          <!--<svg viewBox="0 0 1920 760" width="100%" height="760" style="position: absolute;">
          <defs>
            <mask id="mask" x="0" y="0" width="1920" height="760">
              <rect x="0" y="0" width="1920" height="760" fill="#fff"/>
              <ellipse cx="960" cy="380" rx="800" ry="680" />
            </mask>
          </defs>
          <rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#EBAB84" fill-opacity="1.0"/>
          </svg>-->
          <div class="parallax_title_intro">
            <div style="display: inline-block;">
                <img class="circle-graphic" src="image/scene/jp_ingredients.png" />
            </div>
            <div class="parallax_title inline">
              <h2 class="ppb_title"><span class="ppb_title_first">our food is crafted with love using</span>LOCALLY SOURCED, FRESH INGREDIENTS</h2>
            </div>
            <!--<p>We use locally sourced and sustainable ingredients (where possible) prepared fresh in all our dishes. With one exception, our green and red chiles are direct from the growers in Hatch, New Mexico. Our dishes are true southwestern, typical of Arizona, New Mexico and California.</p>-->
            <div class="cta-buttons">
              <a href="our-menu" class="button cta">View Our Menu</a>
            </div>
          </div>
        </div>

        <?php echo $content_top; ?>
        <div class="clear"></div>
        
        <?php echo $content_bottom; ?>
        <div class="clear"></div>

        <div class="hidden-phone hidden-tablet hidden-desktop parallax title" id="sides" style="height: 1000px; max-height:760px; overflow: hidden;" data-image="assets/images/menu-sides/IMG_0472.jpg" data-width="1800" data-height="1200" data-content-height="80">
          <!--<svg viewBox="0 0 1920 760" width="100%" height="760" style="position: absolute;">
          <defs>
            <mask id="mask" x="0" y="0" width="1920" height="760">
              <rect x="0" y="0" width="1920" height="760" fill="#fff"/>
              <ellipse cx="960" cy="380" rx="800" ry="680" />
            </mask>
          </defs>
          <rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#EBAB84" fill-opacity="1.0"/>
          </svg>-->

          <div class="parallax_title_intro">
            <div style="display: inline-block;">
                <img class="circle-graphic" src="image/scene/jp_sides.png" />
            </div>
            <div class="parallax_title inline">
              <h2 class="ppb_title"><span class="ppb_title_first">home-made and delicious</span>Salsas, Desserts and More</h2>
            </div>
            <div class="cta-buttons">
              <a href="our-menu" class="button cta">View Our Menu</a>
            </div>
          </div>
        </div>

    </div>
</div><!-- END Extended Container -->

<script type="application/javascript">
    // Kill product links (temp fix)
    $(document).ready(function () {
        $(document.body).on('click', '.product-grid-item .image > a', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
        });
    });
    
    // Fix footer cols (temp fix)
    $(document).ready(function () {
        $('#footer').find('.row.columns > .column').each(function (idx, col) {
            if (idx < 2) {
                $(col).attr('class', 'column menu xs-100 sm-50 md-33 lg-25 xl-25');
            } else {
                $(col).attr('class', 'footer-location column menu xs-100 sm-100 md-33 lg-50 xl-50');
            }
        });
    });
    
    function setEqualHeights() {
        var iter = 0,
            interval;
            
        interval = setInterval(function () {
            var selector = '.journal-carousel.carousel-product .owl-item',
                w = $(selector).width();
            
            $(selector).each(function (idx, item) {
                console.log('equalizing grid item height and width to ' + w + 'px');
                $(item).height(w + 'px');
            });
            
            iter++;
            
            if (iter > 3) clearInterval(interval);
        }, 333);
    }
    
    $(document).ready(function () {
        setEqualHeights();
    });
    
    // TODO: A vanilla version of this off canvas border menu (css only for fallback)
    $(document).ready(function() {
        var dir = 'right'; // Or right
        var menuBoolean = false;
        
        var ocw = $('.off-canvas').width(),
            ew = $('.menu-element').last().width();
           
           
        // We need a way to go left and right, forward and back
        //var menuWidthThird = ocw/(ocw/ew); // Account for flex
        var menuWidth = ew;
        
        // Original
        //var menuWidthThird = $('.off-canvas').width() / $('.menu-element').length;
        
        $('.off-canvas-trigger').click(function(e) {
            // Make sure we aren't dealing with a checkbox / label bubble 
            // Like we're doing with our burger check switch...
            if (e.target.tagName.toLowerCase() === 'label') return; // Don't return false or we kill the event -- let it bubble
            
            $('#nav-trigger-anim').toggleClass('active');
            if (menuBoolean == false) {
                // Show menu
                menuAnimIn($('.site'), menuWidth, 
                function () {
                    // Quick fix for expanding... something is weird with velocity callback, it's executing too slow
                    var iter = 0,
                        interval;
                    
                    interval = setInterval(function () {
                        // Executing this a couple times should fix our issue
                        if (iter < 3) {
                            var menu = $('.off-canvas'),
                                last = menu.find('.menu-element').last().find('.nav-content').first(),
                                h = last.height(),
                                o = last.offset().top + 30;
                            
                            console.log('element:');
                            console.log(last);
                            console.log('height: ' + h);
                            console.log('offset: ' + o);
                            console.log('setting height to ' + (h + o) + 'px');
                            $('.site').height((h + o) + 'px');
                        } else {
                            clearInterval(interval);
                        }
                        
                        iter++;
                    }, 100);
                },
                function () {
                    //console.log('wtf');
                    $('.site').addClass('disabled');
                });
                
                menuBoolean = true;
            } else {
                // Hide menu
                menuAnimIn($('.site'), 0, function () {
                    $('.site').removeClass('disabled').height('auto');
                });
                
                menuBoolean = false;
            }
        });

        $('.seemore').click(function(e) {
            var o = $('.site').offset();
            if (o[dir] != $('.off-canvas').width()) {
                var currentLoc = o[dir];
                menuAnimIn($('.site'), currentLoc + menuWidthThird);
            }
        });

        function menuAnimIn(obj, fromDir, onComplete, onBegin) {
            if (dir === 'left') {
                $(obj).velocity({
                    left: fromDir
                }, {
                    begin: onBegin,
                    complete: onComplete,
                    easing: 'easeInSine'
                })
            } else if (dir === 'right') {
                $(obj).velocity({
                    right: fromDir
                }, {
                    begin: onBegin,
                    complete: onComplete,
                    easing: 'easeInSine'
                })
            }
        }
    });
</script>

<?php echo $footer; ?>