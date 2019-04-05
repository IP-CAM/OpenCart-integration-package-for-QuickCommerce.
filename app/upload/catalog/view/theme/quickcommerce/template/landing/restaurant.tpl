<?php echo $header; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <!-- WARNING: for iOS 7, remove the width=device-width and height=device-height attributes. See https://issues.apache.org/jira/browse/CB-4323 -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=medium-dpi" />
    
    <!--<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />-->

    <link href='app/build/css/grommet.min.css' rel='stylesheet' type='text/css'>
    <link href='app/build/css/versla.css' rel='stylesheet' type='text/css'>
    <link href="app/build/css/fonts.css" media="screen, projection" rel="stylesheet" type="text/css">
    <link href="app/build/css/owl.css" media="screen, projection" rel="stylesheet" type="text/css">
    
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="spa/lib/font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet"  type="text/css">
    
    <link rel="stylesheet" type="text/css" href="catalog/view/javascript/quickcommerce/css/bundle.css" />
    <link rel="stylesheet" type="text/css" href="catalog/view/theme/quickcommerce/css/fbwd-theme-sassed.css" />
    <title>Firebrand's Shop Danesi</title>
    
    <style>
    /* Kill grommet styles */
    .grommet p:not(.grommetux-paragraph) {
        margin-top: auto;
    }
    
    /* Hide tabs, trigger programatically */
    /*#dev-tabs ul {
        display: none;
    }*/
    
    .browser-menu-container {
        padding-top: 0;
    }
    
    .browser-content .card {
        text-align: center;
    }
    
    .checkbox-inline+.checkbox-inline, 
    .radio-inline+.radio-inline {
        margin-left: 0; /* Margin only! */
    }
    
    .grommet h1:not(.grommetux-heading), .grommet h2:not(.grommetux-heading), .grommet h3:not(.grommetux-heading), 
    .grommet h4:not(.grommetux-heading), .grommet h5:not(.grommetux-heading), .grommet h6:not(.grommetux-heading) {
        color: #333;
    }
    
    #page-wrap + div header {
        position: absolute;
        top: -80px;
    }
    
    .modal-backdrop {
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.333);
        z-index: 10;
    }
    
    .browser-menu-container {
        margin-left: 105px;
        padding-top: 30px;
    }
    
    .cart-ui {
        display: flex;
        flex-flow: row wrap;
    }
    
    .cart-ui > * {
        flex: 2;
    }
    
    .checkout {
        flex: 1;
    }
    
    .checkout-parts {
       display: flex;
       flex-direction: column;
    }
    
    #outer-container {
        perspective: none !important; /* It's killing the height */ 
    }
    
    .checkout-parts button,
    .checkout-parts button h4 {
        color: black !important;
    }
    
    /* Fix now that we rearranged cart layout */
    .checkout-parts button {
        display: block;
        float: right !important;
    }
    
    .cart-ui .container {
        width: 100%; /* Force fluid */
    }
    
    .total {
        font-size: 2.3rem !important;
    }
    
    /* 384px is set as a max-width somewhere, Grommet is usually the culprit */
    .cart-buttons button {
        max-width: none !important;
    }
    
    #dev-tabs {
        margin-top: 30px;
    }
    
    #dev-tabs ul[role=tablist] {
        margin-left: 100px;
    }
    
    .checkout-parts {
        margin-top: 60px;
    }
    
    .dnd-target-wrapper {
        margin-top: 1.5rem;
    }
    
    .card h5 {
        font-weight: bold !important;
    }
    
    /* ACE Red */
    /*a {
        color: #A91626 !important;
    }*/
    
    /* Danesi Gold */
    .g-infolist .g-infolist-item-icon,
    a {
        color: #9C915A !important;
    }
    
    .grommetux-meter--bar .grommetux-meter__values .grommetux-meter__slice.grommetux-color-index-graph-1, .grommetux-meter--bar .grommetux-meter__values .grommetux-meter__slice.grommetux-color-index-graph-5 {
        stroke: #A91626;
    }
    /* Temporarily override Grommet title color too, we should eventually set up the SASS files */
    .grommetux-title, label.control-label {
        color: #333 !important;
    }

    .dnd-target-wrapper .fa {
        color: #333;
    }
    
    .taco-main--content-top {
        border-top: 1px solid grey;
        color: white;
        background-color: black;
        background-image: url(image/backgrounds/rivets-black-88.png);
        background-position: -40% top;
        background-repeat: repeat;
        -webkit-transform: translateZ(0);
        -moz-transform: translateZ(0);
        -ms-transform: translateZ(0);
        -o-transform: translateZ(0);
        transform: translateZ(0);
        z-index: 2;
        width: 100%;
        bottom: 0%;
        -webkit-box-shadow: 0 2px 17px 0 rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 0 2px 17px 0 rgba(0, 0, 0, 0.5);
        box-shadow: 0 2px 17px 0 rgba(0, 0, 0, 0.5);
    }
    
    #logo img {
        max-width: 220px !important;
        height: auto !important;
    }

    /* Stupid menu borders */
    .journal-header-default .links .no-link, 
    .journal-header-menu .links .no-link,
    header .links > a,
    .super-menu > li {
        border: none !important;
    }
    
    body, html {
        overflow-x: hidden;
    }
    
    .bm-menu {
        box-sizing: content-box !important;
    }
    
    /* Versla */
    td {
        border: none;
    }
    
    .multi-filter-panel .dropdown > .dropdown-toggle, 
    .multi-filter-panel > li {
        font-size: 1.3rem;
    }

    .multi-filter-panel > li {
        padding: 0;
    }

    .multi-filter-panel > li > a {
        padding: 2rem 0;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .multi-filter-panel .dropdown > .dropdown-menu {
        min-width: 250px;
    }
    
    .media-photo-badge {
        top: 0;
    }

    .media-photo-badge img {
        margin: 0px auto;
        background: white;
        padding-left: 0.3rem;
        padding-bottom: 0.3rem;
        padding-right: 0.3rem;
        padding-top: 0.3rem;
    }
    </style>
    
    <script type="text/javascript">
    window.prompt = function () {}
    </script>
    
    <div class="g-grid" id='category-menu' style="margin-top: 17px">
        <div class="g-block flush size-100">
            <div class="g-content g-particle">
                <div class=" g-owlcarousel-showcase g-owlcarousel-4-items">
                    <div id="g-owlcarousel-owlshowcase-7890" class="g-owlcarousel owl-carousel g-owlcarousel-fullwidth  owl-drag">
                        <div class="item">
                            <a href="image/banners/categories/coffee.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Play with Greatness">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/coffee.jpg" alt="Coffee">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Coffee</div>
                                        <div class="item-overlay-desc">Our bread and butter</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                   
                        <div class="item">
                            <a href="image/banners/categories/rocket-tshirts.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Make Beautiful Music">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/rocket-tshirts.jpg" alt="Pianos">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Merch</div>
                                        <div class="item-overlay-desc">ACE t-shirts, etc.</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    
                        <div class="item">
                            <a href="image/banners/categories/rocket-grinder-back.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Have Fun">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/rocket-grinder-back.jpg" alt="Thrilling">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Devices</div>
                                        <div class="item-overlay-desc">Start brewing today</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="item">
                            <a href="image/banners/categories/tamp-station.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Classic Elegance">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/tamp-station.jpg" alt="Violins">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Mats</div>
                                        <div class="item-overlay-desc">Classic elegance</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    
                        <div class="item">
                            <a href="image/banners/categories/rocket-tamper.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Wind Down">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/rocket-tamper.jpg" alt="Bed Time">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Tampers</div>
                                        <div class="item-overlay-desc">Tampers of all sizes</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                   
                        <div class="item">
                            <a href="image/banners/categories/rocket-dump-box.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Escape">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/rocket-dump-box.jpg" alt="Shorts">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Knock Boxes</div>
                                        <div class="item-overlay-desc">Ipsum lorem</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="item">
                            <a href="image/banners/categories/portafilter-handle.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Escape">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/portafilter-handle.jpg" alt="Shorts">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Portafilters</div>
                                        <div class="item-overlay-desc">Bottomless portafilters, all sizes</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    
                        <div class="item">
                            <a href="image/banners/categories/milk-jug.jpg" class="g-showcase-image-link" data-rel="lightcase" title="Build Something">
                                <div class="showcase-image">
                                    <img class="owl-lazy" data-src="image/banners/categories/milk-jug.jpg" alt="Shop">
                                    <div class="item-overlay">
                                        <div class="item-overlay-title">Milk Jugs</div>
                                        <div class="item-overlay-desc">For your lattes and stuff</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>


            </div>

        </div>

    </div>
    
    <div id="container" class="container-fluid j-container">
        
        
        <div class="row">
            <div class="col-sm-12">
                <?php echo $content_top; ?>
            </div>
        </div>
        <div class="row">
            <div id="content" class="<?php echo $class; ?> col-sm-12">
                <div id="main"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $content_bottom; ?>
            </div>
        </div>
    </div>
    
    <div class="g-grid container-fluid" style="background: white">

        <div class="g-block size-100">
            <div class="g-content g-particle">

                <div class="g-infolist g-3cols ">

                    <div class="g-infolist-item g-infolist-with-icon center">

                        <div class="g-infolist-item-text  g-infolist-textstyle-compact">
                            <div class="g-infolist-item-icon center">
                                <i class="fa fa-inbox"></i>
                            </div>

                            <div class="g-infolist-item-title ">Express delivery</div>
                            <div class="g-infolist-item-desc">

                                Need it soon? Items delivered to your door fast and guaranteed.

                            </div>

                        </div>


                    </div>
                    <div class="g-infolist-item g-infolist-with-icon center">

                        <div class="g-infolist-item-text  g-infolist-textstyle-compact">
                            <div class="g-infolist-item-icon center">
                                <i class="fa fa-dollar"></i>
                            </div>

                            <div class="g-infolist-item-title ">Free shipping</div>
                            <div class="g-infolist-item-desc">

                                Who doesn't like free? Order in packs of 4 or 6 and the shipping's on us!

                            </div>

                        </div>


                    </div>
                    <div class="g-infolist-item g-infolist-with-icon center">
                        <div class="g-infolist-item-text  g-infolist-textstyle-compact">
                            <div class="g-infolist-item-icon center">
                                <i class="fa fa-refresh"></i>
                            </div>
                            <div class="g-infolist-item-title ">Track it down</div>
                            <div class="g-infolist-item-desc">

                                Where are you? Have tracking info sent to your mobile phone.

                            </div>

                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>

    <aside class="fullwidth-footer">
        <div class="footer" style="margin: 0; padding: 0; display: table; width: 100%; position: relative; border-bottom: 2px solid #cfa670;">
            <div class="row columns" style="background-attachment: scroll; overflow: hidden; height: 480px; width: 100%; position: absolute; z-index: 1">
                <div id="embedded-map-display" style="height:600px; width:100%; max-width:100%;">
                    <iframe style="height:100%; border:0;" frameborder="0" src="http://streetfoodapp.com/widgets/map/edmonton/jimmy-poblanos?display=name,description,openings" width="100%" height="600"></iframe>
                </div>
            </div>
            <!--<div class="row columns" style="background-attachment: scroll; background-color: rgba(0,0,0,0.555); text-align: center; color: white; position: relative; z-index: 5; padding-top: 180px; padding-bottom: 100px;">
                <div class="column menu xs-100 sm-50 md-33 lg-50 xl-50 " style="height: 200px;">
                    <h3 style="color: white">Our Hours</h3>
                    <div class="column-text-wrap" style="max-width: 80%; margin-left: auto; margin-right: auto; padding-top: 0;">
                        <p>Leva's operating hours vary according to the season. Summer hours are extended to accomodate our beautiful patio and the charming Garneau neighbourhood setting. Our winter hours, are a little more limited, especially during the week. We do, however, have an extensive take-out menu including our amazing 18-inch pizzas, perfect for a house party when it's too cold to go outside.</p>
                    </div>
                </div>
                <div class="column menu xs-100 sm-50 md-33 lg-25 xl-25 " style="height: 200px;">
                    <h3 style="color: white">Summer Hours</h3>
                    <div class="column-menu-wrap">
                        <ul style="list-style-type: none">
                            <li>M-F: 7:00AM - 11:00PM</li>
                            <li>Sat: 8:00AM - 10:00PM</li>
                            <li>Sun: 8:00AM - 10:00PM</li>
                            <li><small>* Holiday hours check below</small></li>
                        </ul>
                    </div>
                </div>
                    
                <div class="column  menu xs-100 sm-50 md-33 lg-25 xl-25 " style="height: 200px;">
                    <h3 style="color: white">Winter Hours</h3>
                    <div class="column-menu-wrap">
                        <ul style="list-style-type: none">
                            <li>M-W: 7:00AM - 5:00PM</li>
                            <li>T-F: 7:00AM - 9:00PM</li>
                            <li>Sat: 8:00AM - 7:00PM</li>
                            <li>Sun: 8:00AM - 5:00PM</li>
                            <li><small>* Holiday hours check below</small></li>
                        </ul>
                    </div>
                </div>
            </div>-->
            
            <div class="row store-location" style="position: relative">
                <!--<svg viewBox="0 0 1920 760" class="hidden-phone" width="100%" height="760" style="position: absolute; top: 0; left: 0">-->
                <svg class="hidden-phone" width="100%" height="760" style="position: absolute; top: 0; left: 0; z-index: 15">
                  <defs>
                    <mask id="mask" x="0" y="0" width="1920" height="760">
                      <rect x="0" y="0" width="1920" height="760" fill="#fff"/>
                      <circle cx="1767" cy="25" r="800" />
                      <!--<ellipse cx="360" cy="380" rx="760" ry="200" />-->
                      
                      
                    </mask>
                  </defs>
                  <!--<rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#020202" fill-opacity="0.888"/>-->
                  <!--<rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#85312c" fill-opacity="0.888"/>-->
                  <rect x="0" y="0" width="1920" height="760" mask="url(#mask)" fill="#9C915A" fill-opacity="0.777"/><!-- Gold -->
                </svg>
                <div class="menu col-xs-12 col-sm-3 col-md-3 col-lg-3 hidden-phone" style="height: 450px;"></div>
                <div class="menu col-xs-12 col-sm-6 col-md-6 col-lg-3" style="position: relative; z-index: 15; height: 450px; padding-top: 150px;">
                    <h4 style="color: white; text-align: center">Now Roasting At...</h4>
                    <div class="column-text-wrap" style="color: white; text-align: center; font-size: 1.25rem; line-height: 2.0rem; max-width: 80%; margin-left: auto; margin-right: auto; padding-top: 0;">
                        <p>10055 80th Avenue NW<br>Edmonton, Alberta, Canada<br><b>Phone: 780.244.0ACE</b><br>info@acecoffeeroasters.com</p>
                    </div>
                </div>
                <!--<div class="column menu xs-100 sm-50 md-25 lg-25 xl-25 " style="height: 200px;">
                    <h3 style="color: white">Normal Hours</h3>
                    <div class="column-menu-wrap">
                        <ul style="list-style-type: none">
                            <li>M-W: 7:00AM - 5:00PM</li>
                            <li>T-F: 7:00AM - 9:00PM</li>
                            <li>Sat: 8:00AM - 7:00PM</li>
                            <li>Sun: 8:00AM - 5:00PM</li>
                        </ul>
                    </div>
                </div>-->
                <!--<div class="column menu xs-100 sm-50 md-25 lg-25 xl-25 " style="height: 200px;">
                    <h3 style="color: white">Holiday Hours</h3>
                    <div class="column-menu-wrap">
                        <ul style="list-style-type: none">
                            <li>December 23rd - 7AM - 5PM</li>
                            <li>December 24th - 8AM - 3PM</li>
                            <li><small>* All day brunch!</small></li>
                            <li>December 25th - CLOSED</li>
                            <li>December 26th - 10AM to 8PM</li>
                        </ul>
                    </div>
                </div>    
                <div class="column  menu xs-100 sm-50 md-25 lg-25 xl-25 " style="height: 200px;">
                    <h3 style="color: white">&nbsp;</h3>
                    <div class="column-menu-wrap">
                        <ul style="list-style-type: none">
                            <li>December 27-30th - 7AM to 8PM</li>
                            <li>December 31st - 8AM - 3PM</li>
                            <li><small>* All day brunch!</small></li>
                            <li>January 1st - CLOSED</li>
                        </ul>
                    </div>
                </div>-->
            </div>
        </div>
    </aside>

    <div class="g-grid container-fluid">


        <div class="g-block center size-65">
            <div class="g-content g-particle">

                <div class="">
                    <div class="g-simplecontent">


                        <div class="g-simplecontent-item g-simplecontent-layout-header">



                            <div class="g-simplecontent-item-content-title">Subscribe to our newsletter and we’ll send you <span>special offers</span></div>



                        </div>

                    </div>
                </div>


            </div>

        </div>


        <div class="g-block center size-35">
            <div class="g-content g-particle">

                <div class="">


                    <div id="g-newsletter-newsletter-3789" class="g-newsletter g-newsletter-fullwidth g-newsletter-aside-compact g-newsletter-square">

                        <form id="g-newsletter-form-newsletter-3789" class="g-newsletter-form" action="//feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('//feedburner.google.com/fb/a/mailverify?uri=', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">

                            <div class="g-newsletter-form-wrapper">
                                <div class="g-newsletter-inputbox">
                                    <input placeholder="Your email address" name="email" type="text">
                                    <input value="" name="uri" type="hidden">
                                    <input name="loc" value="en_US" type="hidden">
                                </div>
                                <div class="g-newsletter-button">
                                    <a class="g-newsletter-button-submit button " href="#" onclick="document.getElementById('g-newsletter-form-newsletter-3789').submit()">
                                        <span class="g-newsletter-button-icon"><i class="fa fa-envelope-o"></i></span>
                                        <span class="g-newsletter-button-text">Join</span>
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>


            </div>

        </div>

    </div>

<?php echo $footer; ?>