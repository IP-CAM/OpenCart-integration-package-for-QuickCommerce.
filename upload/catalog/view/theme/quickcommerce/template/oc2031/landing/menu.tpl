<?php echo $header; ?>
<style id="rs-plugin-settings-inline-css" type="text/css">
  .tp-caption a {
    color: #ff7302;
    text-shadow: none;
    -webkit-transition: all 0.2s ease-out;
    -moz-transition: all 0.2s ease-out;
    -o-transition: all 0.2s ease-out;
    -ms-transition: all 0.2s ease-out
  }

  .tp-caption a:hover {
    color: #ffa902
  }

  .tp-caption.title-first-word,
  .title-first-word {
    font-size: 60px;
    line-height: 50px;
    font-family: Kristi;
    color: #cfa670;
    text-decoration: none;
    background-color: transparent;
    border-width: 0px;
    border-color: rgb(0, 0, 0);
    border-style: none;
    text-shadow: none
  }

  .tp-caption.title,
  .title {
    font-size: 65px;
    font-weight: 300;
    font-family: Lato;
    color: rgb(255, 255, 255);
    text-decoration: none;
    background-color: transparent;
    border-width: 0px;
    border-color: rgb(0, 0, 0);
    border-style: none;
    text-shadow: none;
    text-transform: uppercase;
    letter-spacing: -3px
  }

  .tp-caption.sub-title,
  .sub-title {
    font-size: 20px;
    line-height: 24px;
    font-weight: 400;
    font-family: Lato;
    color: rgb(255, 255, 255);
    text-decoration: none;
    background-color: transparent;
    border-width: 0px;
    border-color: rgb(0, 0, 0);
    border-style: none;
    text-shadow: none;
    text-transform: uppercase;
    letter-spacing: -1px
  }

  #rev_slider_1_1_wrapper .tp-loader.spinner3 div {
    background-color: #444444 !important;
  }
</style>
<!-- Menu and info page styles -->
<style scoped>
    .heading-title,
    .breadcrumb {
        color: white !important;
        text-align: center;
        display: none;
    }
    
    .ppb_wrapper {
        background-color: #3D0931;
    }
    
    .menu-container {
        padding-bottom: 200px;
    }
</style>

<div class="extended-container">


    <div class="site">

        <div class="off-canvas-trigger"><!-- For off-canvas burger menu -->
            <!--<input class="burger-check" id="nav-trigger-anim" type="checkbox"><label for="burger-check" class="burger"></label>-->
            <input class="burger-check" id="nav-trigger-anim" type="checkbox"><label for="nav-trigger-anim" class="burger"></label>
            <!--<label id="nav-trigger-anim"><span></span></label>-->
        </div>

        <div id="container" class="container j-container">
          <div class="row">
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
                      <a href="/menus/JP-Menu.web.pdf" class="button cta">Download Our PDF Menu</a>
                    </div>
                  </div>
                </div>
                
                <?php echo $content_top; ?>
                <div class="clear"></div>

            </div>
          </div>
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
                <div class="ppb_wrapper">
            
                <section class="content-block nav-section unloaded nav-section_4 clear" id="menu" style="display: block; position: relative; top: -150px; margin-bottom: -150px; z-index: 1000">

                    <div id="ajax_content" class="content-block">
                        <div class="menu-container">

                            <div class="menu-layout">
                                <div class="menu-wrapper">
                                    <h2 style="text-align: center; display: block !important;">Jimmy Poblano's Mexican Cantina</h2>
                                    <br>
                                    <hr style="margin-top: 1rem; color: black; border-color: black">
                                    <p style="margin-top: 3rem">You can also download the PDF <a href="/menus/JP-Menu.web.pdf" style="text-decoration: underline" title="Download the Jimmy Poblano's menu"><strong>here</strong></a>.</p>

                                    <ul class="menu-tabs pos-ul clearfix">
                                    <?php $idx = 0; ?>
                                    <?php foreach ($categories as $category) { ?>
                                        <?php $active = ($idx == 0) ? 'class="active"' : ''; ?>
                                        <li <?php echo $active; ?> style="padding: 0 10px;"><a href="#cat_<?php echo $category['category_id']; ?>" data-toggle="tab"><?php echo $category['name']; ?><span class="underline"></span></a></li>
                                        <?php $idx++; ?>
                                    <?php } ?>
                                    </ul>

                                    <div class="tab-content" style="display: block">
                                    <?php $idx = 0; ?>
                                    <?php foreach ($categories as $category) { ?>
                                        <?php $active = ($idx == 0) ? 'active' : ''; ?>
                                        <div class="tab-pane <?php echo $active; ?>" id="cat_<?php echo $category['category_id']; ?>">
                                        <?php if (isset($category['products']) && count($category['products']) > 0) { ?>
                                        <?php foreach ($category['products'] as $product) { ?>
                                            <!--<p><strong>Nibbles</strong></p>-->
                                            <p><?php echo $product['name']; ?> <?php //echo $product['price']; ?></p>
                                        <?php } ?>
                                        <?php } ?>
                                        </div>
                                        <?php $idx++; ?>
                                        
                                        <!--
                                        <div class="tab-pane" id="dinner">
                                            <style type="text/css" media="screen">
                                                #available-times {
                                                    margin-bottom: 20px;
                                                    margin-top: -20px;
                                                    position: relative;
                                                }
                                                
                                                #available-times:after {
                                                    content: '';
                                                    position: absolute;
                                                    bottom: -10px;
                                                    left: 25%;
                                                    right: 23%;
                                                    border-bottom: 1px solid #007;
                                                }
                                                
                                                #available-times>span {
                                                    font-size: 1.3em;
                                                }
                                            </style>
                                            <p id="available-times"><span>Available from:</span><br>Mon to Sat 12:00 to 15:00 and 18:00 to 23:00
                                            </p>
                                            <table class="menu-table">
                                                <tbody>
                                                    <tr>
                                                        <td>Puglian green olives</td>
                                                        <td>&nbsp;£3</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Homemade sourdough, butter</td>
                                                        <td>&nbsp;£4</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Short rib croquettes, sour cherry ketchup</td>
                                                        <td>&nbsp;£4</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jersey rock oyster, yuzu, citron, bergamot, orange</td>
                                                        <td>&nbsp;£3 each</td>
                                                    </tr>
                                                    <tr>
                                                        <td>---</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mushroom raviolo, broth, egg yolk, coriander</td>
                                                        <td>&nbsp;£12</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Burrata, shallots, Jerusalem artichoke, malt</td>
                                                        <td>&nbsp;£9</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Dorset crab, parsley root remoulade, Golden Delicious apple, dill</td>
                                                        <td>&nbsp;£12</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sea trout, beetroot, horseradish, elderberry capers, sorrel</td>
                                                        <td>&nbsp;£10</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Quail, corn, pickled chanterelles, coriander</td>
                                                        <td>&nbsp;£10</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Crispy pig’s jowl, egg yolk, piccalilli, heritage cauliflower</td>
                                                        <td>&nbsp;£9</td>
                                                    </tr>
                                                    <tr>
                                                        <td>---</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gnocchi, salsify, spinach, hazelnut vinaigrette</td>
                                                        <td>&nbsp;£16</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cod, hispi cabbage, brown shrimps, cucumber, koji, dill</td>
                                                        <td>&nbsp;£23</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gurnard, smoked pumpkin, coco beans, bonito butter</td>
                                                        <td>&nbsp;£20</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mallard, swede, turnip top, pickled blackberries</td>
                                                        <td>&nbsp;£20</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Short rib, Yukon Gold mash, sweet garlic, Montgomery Cheddar</td>
                                                        <td>&nbsp;£24</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Iberico pork shoulder, wild mushroom, leek, smoked walnut</td>
                                                        <td>&nbsp;£26</td>
                                                    </tr>
                                                    <tr>
                                                        <td>---</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Crispy potato cake, smoked garlic aioli</td>
                                                        <td>&nbsp;£4</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kohlrabi, Granny Smith apple &amp; radish salad, horseradish, whey dressing</td>
                                                        <td>&nbsp;£4</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Charred cauliflower, smoked anchovy, dill</td>
                                                        <td>&nbsp;£4</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kale, miso, carrot vinaigrette</td>
                                                        <td>&nbsp;£4</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>-->
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                </div>
                
            </div>
          </div>
          <div class="row">
            <div class="content-plus">
                <?php echo $content_bottom; ?>
                <div class="clear"></div>

                <div class="hidden-phone parallax title" id="sides" style="height: 1000px; max-height:760px; overflow: hidden; border-top: 1px solid white" data-image="assets/images/menu-sides/IMG_0472.jpg" data-width="1800" data-height="1200" data-content-height="80">
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
                      <a href="/menus/JP-Menu.web.pdf" class="button cta">Download Our PDF Menu</a>
                    </div>
                  </div>
                </div>

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
                    <p class="top-level"><a href="/our-menu">Menu</a></p>
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
    
    <!-- Old stuff we have our own thing going on now -->
    <!--<script type="text/javascript" src="catalog/view/theme/quickcommerce/js/menu-core.js"></script>-->
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
    
    /*$(document).ready(function () {
        var iter = 0,
            interval;
            
        interval = setInterval(function () {
            Journal.equalHeight($('.product-grid-item .image > a img'));
            iter++;
            
            if (iter > 5) clearInterval(interval);
        }, 1500);
    });*/
    
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