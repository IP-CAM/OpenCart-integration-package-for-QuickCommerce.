<!DOCTYPE html>
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
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="<?php echo $this->journal2->html_classes->getAll(); ?>" data-j2v="<?php echo JOURNAL_VERSION; ?>">
<head>
    <meta charset="UTF-8" />
    <?php if ($this->journal2->settings->get('responsive_design')): ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <?php endif; ?>
    <meta name="format-detection" content="telephone=no">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"/><![endif]-->
    <title><?php echo $title; ?></title>
    <base href="<?php echo $base; ?>" />
    <?php if ($meta_title = $this->journal2->settings->get('blog_meta_title')): ?>
    <meta name="title" content="<?php echo $meta_title; ?>" />
    <?php endif; ?>
    <?php if ($description) { ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php } ?>
    <?php if ($keywords) { ?>
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <?php } ?>
    <?php foreach ($this->journal2->settings->get('share_metas', array()) as $sm): ?>
    <meta property="<?php echo $sm['type']; ?>" content="<?php echo $sm['content']; ?>" />
    <?php endforeach ;?>
    <?php if (version_compare(VERSION, '2.1', '<')): ?>
    <?php if ($icon) { ?>
    <link href="<?php echo $icon; ?>" rel="icon" />
    <?php } ?>
    <?php endif; ?>
    <?php if ($blog_feed_url = $this->journal2->settings->get('blog_blog_feed_url')): ?>
    <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $blog_feed_url; ?>" />
    <?php endif; ?>
    <?php foreach ($links as $link) { ?>
    <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>
    <?php foreach ($styles as $style) { ?>
    <?php $this->journal2->minifier->addStyle($style['href']); ?>
    <?php } ?>
    <?php foreach ($this->journal2->google_fonts->getFonts() as $font): ?>
    <link rel="stylesheet" href="<?php echo $font; ?>"/>
    <?php endforeach; ?>
    <?php foreach ($scripts as $script) { ?>
    <?php $this->journal2->minifier->addScript($script, 'header'); ?>
    <?php } ?>
    <?php
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/hint.min.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/journal.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/features.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/header.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/module.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/pages.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/account.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/blog-manager.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/side-column.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/product.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/category.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/footer.css');
        //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/icons.css');
        if ($this->journal2->settings->get('responsive_design')) {
            //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/responsive.css');
        }
    ?>
    <?php //echo $this->journal2->minifier->css(); ?>

    <?php $this->journal2->minifier->addScript('catalog/view/theme/journal2/js/journal.js', 'header'); ?>

    <?php if ($this->journal2->minifier->loadGoogleRecaptcha()): ?>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <?php endif; ?>

    <?php echo $this->journal2->minifier->js('header'); ?>
    <!--[if (gte IE 6)&(lte IE 8)]><script src="catalog/view/theme/journal2/lib/selectivizr/selectivizr.min.js"></script><![endif]-->
    <?php if (isset($stores)): /* v1541 compatibility */ ?>
    <?php if ($stores) { ?>
    <script type="text/javascript"><!--
    $(document).ready(function() {
    <?php foreach ($stores as $store) { ?>
    $('body').prepend('<iframe src="<?php echo $store; ?>" style="display: none;"></iframe>');
    <?php } ?>
    });
    //--></script>
    <?php } ?>
    <?php endif; /* end v1541 compatibility */ ?>
    <?php if (version_compare(VERSION, '2.1', '<')): ?>
    <?php echo $google_analytics; ?>
    <?php else: ?>
    <?php foreach ($analytics as $analytic) { ?>
    <?php echo $analytic; ?>
    <?php } ?>
    <?php endif; ?>
    <script>
        <?php if ($this->journal2->settings->get('show_countdown', 'never') !== 'never' || $this->journal2->settings->get('show_countdown_product_page', 'on') == 'on'): ?>
        Journal.COUNTDOWN = {
            DAYS    : "<?php echo $this->journal2->settings->get('countdown_days', 'Days'); ?>",
            HOURS   : "<?php echo $this->journal2->settings->get('countdown_hours', 'Hours'); ?>",
            MINUTES : "<?php echo $this->journal2->settings->get('countdown_min', 'Min'); ?>",
            SECONDS : "<?php echo $this->journal2->settings->get('countdown_sec', 'Sec'); ?>"
        };
        <?php endif; ?>
        Journal.NOTIFICATION_BUTTONS = '<?php echo $this->journal2->settings->get('notification_buttons'); ?>';
    </script>

    <!-- TODO: We need to add this to bower/npm -->
    <!-- For animations and off canvas burger menu -->
    <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/velocity/1.4.3/velocity.min.js"></script>

    <script type="text/javascript" src="site/js/react-bundle.js"></script>
    <!--<script type="text/javascript" src="site/js/bower-bundle.js"></script>-->
    <script type="text/javascript" src="site/js/libs.js"></script>
    <!--<script src="./index_files/jquery.min.js"></script>-->
    <script src="./index_files/TweenMax.min.js"></script>
    <script src="./index_files/bluebird.min.js"></script>
    <!--<script async="" defer="" src="./index_files/js" type="text/javascript"></script>-->
    <script src="./index_files/all.js"></script>
    <script src="./index_files/site.js"></script>

   
	<!--<link href="spa/imported/owl.css" media="screen, projection" rel="stylesheet" type="text/css">-->
    <!--<link media="all" href="app/build/css/grommet.min.css" rel="stylesheet" type="text/css">-->
    <link href="./site/styles/versla.css" rel="stylesheet" type="text/css">
    <!--<link href="app/build/css/fonts.css" media="screen, projection" rel="stylesheet" type="text/css">-->
    <link media="all" rel="stylesheet" type="text/css" href="app/build/css/owl.css">
    <link media="all" rel="stylesheet" type="text/css" href="site/fonts/font-awesome/css/font-awesome.css">  
    <!--<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">-->
    
    <!-- Journal Styles -->
    <link media="all" rel="stylesheet" type="text/css" href="catalog/view/theme/quickcommerce/css/fbwd-theme-sassed.css" />
    
    <link rel="stylesheet" type="text/css" href="./site/styles/betheme/betheme.css" />
    <link rel="stylesheet" type="text/css" href="catalog/view/javascript/quickcommerce/css/bundle.css" />
    <link media="all" rel="stylesheet" type="text/css" href="site/css/bundle.css" />
    
    
    <link rel="stylesheet" type="text/css" href="./site/styles/heading.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/spinner.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/loader.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/button.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/burger.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/social.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/socialbadges.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/animate.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/animateview.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/style.css" />

    <!--<link href="site/css/style_apireceipt.css" rel="stylesheet" type="text/css">-->

    <style>
        /* Body & HTML scrolling */

        .form-control {
            font-size: 1.15rem;
        }
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
            margin-left: 0;
            /* Margin only! */
        }
        .grommet h1:not(.grommetux-heading),
        .grommet h2:not(.grommetux-heading),
        .grommet h3:not(.grommetux-heading),
        .grommet h4:not(.grommetux-heading),
        .grommet h5:not(.grommetux-heading),
        .grommet h6:not(.grommetux-heading) {
            color: #333;
        }
        .btn-danger:not(.grommetux-button) {
            background-color: #85312C !important;
            color: white !important;
        }
        .btn-danger:not(.grommetux-button) .fa,
        .btn-danger:not(.grommetux-button) h1,
        .btn-danger:not(.grommetux-button) h2,
        .btn-danger:not(.grommetux-button) h3,
        .btn-danger:not(.grommetux-button) h4,
        .btn-danger:not(.grommetux-button) h5,
        .btn-danger:not(.grommetux-button) h6 {
            color: white !important;
        }
        .btn-success:not(.grommetux-button) {
            background-color: #13432D !important;
            color: white !important;
        }
        .btn-success:not(.grommetux-button) .fa,
        .btn-success:not(.grommetux-button) h1,
        .btn-success:not(.grommetux-button) h2,
        .btn-success:not(.grommetux-button) h3,
        .btn-success:not(.grommetux-button) h4,
        .btn-success:not(.grommetux-button) h5,
        .btn-success:not(.grommetux-button) h6 {
            color: white !important;
        }
        #page-wrap + div header {
            position: absolute;
            top: -80px;
        }
        
        .modal-backdrop {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.333);
            z-index: 10;
        }
        /* Position and sizing of burger button */

        .bm-burger-button {
            position: fixed;
            width: 36px;
            height: 30px;
            left: 36px;
            top: 17px;
        }
        /* Color/shape of burger icon bars */

        .bm-burger-bars {
            background: #373a47;
        }
        /* Position and sizing of clickable cross button */

        .bm-cross-button {
            height: 24px;
            width: 24px;
        }
        /* Color/shape of close button cross */

        .bm-cross {
            background: #bdc3c7;
        }
        /* General sidebar styles */
        .bm-menu {
            z-index: 50;
            width: 100%;
            box-sizing: content-box;
            overflow-x: hidden !important;
            padding: 0.5em 0 0;
            font-size: 1.15em;
            background: rgba(133, 49, 44, 0.888);
            position: absolute !important; /* Override inline */
        }
        /* Morph shape necessary with bubble or elastic */
        .bm-morph-shape {
            /*fill: rgba(12,12,12,0.777);*/
            fill: rgba(133, 49, 44, 0.888);
            /*border-right: 2px solid white;*/
            z-index: 49;
        }
        
        .bm-overlay {
            background: rgb(12,12,12) !important; /* Override fbwd style */
            opacity: 0.777 !important;
        }
        
        /* Wrapper for item list */
        .bm-item-list {
            color: #b8b7ad;
            padding: 0.8em;
        }
        /* Styling of overlay */

        .bm-overlay {
            background: rgba(0, 0, 0, 0.3);
        }
        
        /* We want to stretch home page content */
        .home #container {
            padding: 0;
        }
        
        #container {
            position: relative;
            z-index: 2;
        }
        
        table.cart thead, 
        table.cart thead th, 
        table.cart thead td {
            background: none;
            color: white;
        } 
        
        #main-menu {
            right: 15px !important; /* Override BM menu inline style for 'right' positioning */
        }
        
        .checkout-parts .cart-buttons button {
            left: auto;
        }
        
        /* TODO: Mobile first yo! */
        @media all and (max-width: 48em) {
            #main-menu {
                width: calc(100% - 15px) !important;
                right: 0 !important; /* Override BM menu inline style for 'right' positioning */
            }
            
            .table > tbody > tr > td, 
            .table > tbody > tr > th,
            .table > thead > tr > td, 
            .table > thead > tr > th {
                border: none;
            }
            
            table.cart {
                table-layout: fixed;
            }
            
            table.cart thead {
                display: none; /* Hide heading, no point we're going list style */
            }
            
            table.cart tbody td {
                text-align: center;
            }
        }
        
        .cart-ui {
            display: flex;
            flex-flow: row wrap;
            align-items: center;
            /*flex-direction: column;*/
            align-items: stretch;
        }
        .cart-ui > * {
            flex: 1.5;
        }
        .checkout {
            flex: 1;
        }
        .checkout-parts {
            display: flex;
            flex-direction: column;
        }
        #outer-container {
            perspective: none !important;
            /* It's killing the height */
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
            width: 100%;
            /* Force fluid */
        }
        .total {
            font-size: 2.3rem !important;
        }
        /* 384px is set as a max-width somewhere, Grommet is usually the culprit */

        .cart-buttons button {
            max-width: none !important;
        }

        .pagination-sm > li > a,
        .pagination-sm > li > span {
            padding: 1.5rem 2rem;
        }
        .card h5 {
            font-weight: bold !important;
        }
        
        .grommetux-meter--bar .grommetux-meter__values .grommetux-meter__slice.grommetux-color-index-graph-1,
        .grommetux-meter--bar .grommetux-meter__values .grommetux-meter__slice.grommetux-color-index-graph-5 {
            stroke: #A91626;
        }
        /* Not clearing for some reason */

        .griddle-footer {
            clear: both;
        }
        .pagination > .active > a,
        .pagination > .active > a:focus,
        .pagination > .active > a:hover,
        .pagination > .active > span,
        .pagination > .active > span:focus,
        .pagination > .active > span:hover {
            border-color: #A91626;
            background-color: #A91626;
            color: white !important;
        }
        .bm-item-list a {
            padding: 0.8em;
            color: #333;
        }
        /* Temporarily override Grommet title color too, we should eventually set up the SASS files */

        .grommetux-title,
        label.control-label {
            color: #333 !important;
        }
        .bm-item-list a span {
            margin-left: 10px;
            font-weight: 700;
        }
        #page-wrap + div > .bm-burger-button {
            right: 13px;
            left: auto;
        }
        #page-wrap + div > .bm-menu-wrap,
        #page-wrap + div > .bm-overlay {
            top: 0;
        }
        #page-wrap + div > .bm-menu {
            padding: 2.5em 1.5em 0;
        }
        #page-wrap + div > .bm-burger-button .bm-icon:before {
            font-family: FontAwesome;
            content: '\f007';
            font-size: 3rem;
            width: 3rem;
            height: 3rem;
            z-index: 9999;
            font-style: normal;
        }
        /*#page-wrap + div > .bm-overlay {
	        display: none;
	    }*/

        .bm-burger-button {
            z-index: 5555 !important;
        }
        .bm-cross-button {
            display: none;
        }
        .dnd-target-wrapper .fa {
            color: #333;
        }
        @media screen and (max-width: 767px) {
            .cart-ui {
                flex-flow: column wrap;
            }
            .card {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .card .thumbnail {
                margin-right: auto;
                margin-left: auto;
                float: none;
            }
            .card h5,
            .cart p {
                margin: 0 !important;
                padding: 0 !important;
                /* Handle with flex */
            }
            .card h5 {
                flex: 1;
            }
            .card p {
                flex: 2;
            }
            /* Fix now that we rearranged cart layout */

            .checkout-parts button {
                display: block;
                float: none !important;
            }
            .griddle-container {
                min-height: 100px;
                display: flex;
            }
            .order-parts-header,
            .account-parts,
            .checkout-parts {
                margin-top: 20px;
            }
        }
        @media screen and (max-width: 979px) { 
            width: auto;
        }
        @media screen and (max-device-width: 1024px) and (orientation: landscape) {
            .griddle-container div[class^="col-"] {
                height: 230px;
            }
        }
        .multi-filter-panel .dropdown > .dropdown-toggle,
        .multi-filter-panel > li {
            font-size: 1.0rem;
            font-size: 1.0rem;
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
        .navbar-default {
            border-width: 1px 0 0 0;
            /* Kill borders */
        }
        .category-filter-bar .navbar {
            border-width: 0 0 1px 0;
        }
        .category-filter-bar .multi-filter-panel > li {
            border: 0;
        }
        .order-parts,
        .order-parts h1,
        .order-parts h2,
        .order-parts h3,
        .order-parts h4,
        .order-parts h5,
        .order-parts h6,
        .order-parts .grommetux-title {
            color: white !important;
        }
        .order-parts {
            width: 100%;
        }
        .order-parts > div {
            position: fixed;
            width: inherit;
            min-height: 80px;
            color: white !important;
            background: black;
            padding-left: 2rem;
            z-index: 9999;
        }
        .order-parts {
            min-height: 80px;
        }
        /* Override previous black color */

        .bm-burger-bars {
            background: white;
        }
        .navbar-default {
            background-color: white;
        }
        .navbar {
            margin-bottom: 0;
            /* Override bundle style */
        }
        .category-filter-bar .navbar {
            margin-bottom: 20px;
        }
        .browser-content .card {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        #dev-tabs > ul[role=tablist] {
            display: none;
        }
        /* Override step colors */

        .StepIndicator {
            margin-top: 10px;
        }
        .StepIndicator .StepIndicator__step.is-active .StepIndicator__info {
            /*border-color: #A91626;*/

            background-color: #A91626;
            border: 1px solid grey !important;
        }
        .StepIndicator .StepIndicator__step.is-active .StepIndicator__label {
            color: inherit;
        }
        .StepIndicator__info {
            width: 3.5rem;
            height: 3.5rem;
            line-height: 3.5rem;
        }
        .StepIndicator__label {
            font-size: 1.0rem !important;
        }
        .card .item-price,
        .card .item-name {
            margin-bottom: 6px !important;
        }
        .card .item-name {
            font-weight: bold !important;
        }
        .card h5.item-brand {
            font-weight: normal !important;
            font-size: 0.86rem;
        }
        .table.cart .thumbnail {
            max-width: 106px;
            /* Add extra pixels to account for padding */
        }
        /* TODO: Use cached thumbs */

        .table.cart .thumbnail img {
            max-width: 50px !important;
            max-height: 50px !important;
        }
    </style>
    <style>
        /* Override styles for ACE site */

        @media all and (max-width: 48em) {
            .browser-menu-container {
                max-width: 75% !important;
                float: right !important;
            }
            .mainContent > .container {
                width: 100%;
                padding: 0 0 0 100px;
                margin: 0;
                /* Override bundle styles from cart */
            }
        }
        @media all and (min-width: 48em) {
            .mainContent > .container {
                width: 100%;
                padding: 0 0 0 170px;
                margin: 0;
                /* Override bundle styles from cart */
            }
        }
        body {
            height: auto !important;
            /* Override app bundle style */
        }
        .tileBg {
            /*filter: grayscale(80%) brightness(110%);*/
            border: 1px solid #85312C; /* ACE Red */
        }
        .articleImage {
            /*filter: grayscale(60%) invert(10%) brightness(110%);*/
            border: 1px solid #85312C; /* ACE Red */
        }
        html {
            font-size: 100%;
        }
        /* Override icons we pasted in */
        /* Simple transition from circle to square on hover */

        .icon_box .icon_wrapper {
            transition-property: border-radius, transform;
            transition-duration: 0.25s, 0.5s;
            transition-delay: 0.125s, 0s;
            transform: rotate(0deg);
        }
        .icon_box:hover .icon_wrapper,
        .icon_box:hover .icon_wrapper:before {
            border-radius: 0%;
            /* Square */

            transform: rotate(360deg)
        }
        /* Circular product / category tiles */

        .thumbnail {
            border: none;
            background: transparent; /* Override bundle */
        }
        .thumbnail img {
            border-radius: 100%;
            border: 1px solid #231F20; /* ACE Black */
            box-sizing: content-box;
            background-color: #fff;
        }
        
        .browser-content .thumbnail img {
            border-radius: 100%;
            border: 3px solid #111;
            box-sizing: content-box;
        }
        
        .dark .browser-content .thumbnail img {
            border-color: #fff;
        }
        
        .dark .card h1, 
        .dark .card h2, 
        .dark .card h3, 
        .dark .card h4, 
        .dark .card h5, 
        .dark .card h6,
        .dark .card {
            color: #fff;
        }
        
        .icon_box .icon_wrapper {
            background-color: rgba(255,255,255,0.777);
            -webkit-box-shadow: inset 0 0 7px 0 rgba(0, 0, 0, .08);
            box-shadow: inset 0 0 7px 0 rgba(0, 0, 0, .08);
        }
        
        /* Hide unnecessary UI components from cart system */

        .StepIndicator {
            display: none;
        }
        .griddle .top-section {
            display: none;
        }
        
        .checkout-parts .well {
            background: none;
        }
        
        .checkout-parts .drop-target-icon {
            display: none;
        }
        
        .actionButtons {
            height: 250px;
        }
        .actionButtons li {
            text-align: center;
            margin-top: 0;
            margin-bottom: 0;
        }
        .actionButtons li i.fa {
            font-size: 1.5rem;
            margin: 0 auto;
            padding: 1.5rem 0;
        }
        
        .actionButtons .btn.no-bs-style {
            width: 100%;
            text-align: center;
            text-transform: uppercase;
            border: none !important;
            margin-top: 0;
            margin-bottom: 0;
            left: 0;
        }
        
        .actionButtons .btn.no-bs-style .buttonText {
            color: #2B2B2B;
            display: block;
            margin-bottom: 1.5rem;
        }
        
        .actionButtons li a.btn:hover,
        .actionButtons li a.btn:active {
            background: #85312C; /* ACE Red */
        }
        
        .actionButtons li a.btn:focus {
            background: #85312C; /* ACE Red */
        }
        
        .actionButtons li a.btn:hover .buttonText,
        .actionButtons li a.btn:active .buttonText,
        .actionButtons li a.btn:focus  .buttonText,
        .actionButtons li a.btn:hover i,
        .actionButtons li a.btn:active i,
        .actionButtons li a.btn:focus i {
            color: #fff;
        }
        
        .actionButtons i.fa {
            font-size: 1.3rem;
            line-height: 2.3rem;
        }

		.header--navIsActive .actionButtons {
			display: none;
		}

		/* Override more bundle styles */
		.h1, .h2, .h3, h1, h2, h3 {
		    margin-top: 30px;
		    margin-bottom: 10px;
		}

		footer * {
			box-sizing: border-box;
		}

		footer > .container {
			/* Kill bundle styles yet again */
			padding: 0;
			padding-top: 30px;
			max-width: 100%; /* Some stupid style is stretching the footer, not sure where though */
		}

		/* Fix / override footer styles, use flexbox */
		footer .footer__content {
			display: flex;
			flex-direction: column;
			align-items: center;
			margin: 0;
			padding: 0;
		}

		footer .footer__content > * {
			flex: 1;
		}

		footer .footer__content > * > * {
			text-align: center;
		}

		footer .footer__content .footer__brand {
			flex: 0;
			width: 100% !important;
			flex-basis: 100%; /* Force to bottom */
		}
        
        .media-photo-badge {
            position: static;
            left: 0;
        }
        
        .summary-component {
            border-color: transparent;
        }
        
        .page {
            border: none;
            background: transparent;
        }
        
        @media all and (min-width: 25.875em) {
            
        }
        @media all and (min-width: 48em) {
            .media-photo-badge {
                position: relative;
            }
        }
        @media all and (min-width: 64em) {
            .media-photo-badge {
                position: absolute;
                left: 5px;
            }
            
            .summary-component {
                border-color: grey;
            }
            
            /*.page {
                border-left: 1px solid grey;
                border-right: 1px solid grey;
            }*/
        }
        @media all and (min-width: 101.5em) {
            .media-photo-badge {
                position: absolute;
            }
        }

		@media (min-width: 48em) {
			footer .footer__content {
				flex-flow: row wrap;
                flex-direction: row;
				justify-content: space-around;
				align-items: baseline;
				flex-wrap: wrap;
			}
		}

		.copyright {
			white-space: normal;
			padding: 10px 10px 50px;
		}

		/* Kill highlighting on button styles in bundle */
		.btn.active.focus,
		.btn.active:focus,
		.btn.focus,
		.btn:active.focus,
		.btn:active:focus,
		.btn:focus {
			outline: none;
			box-shadow: none;
		}
        
        /* Fuck off stupid goddamn journal header */
        header[class^=journal-] {
            display: none;
        }
        
        #ui-tabs > [role=tablist] {
            display: none;
        }
        
        .footer-post {
            padding: 6px 2rem;
            border: none;
        }
        
        ul, ol {
            margin: 0;
            padding: 0;
        }
        
        /* Overrride versla */
        .g-infolist .g-infolist-item-icon {
            color: #85312C; /* ACE Red */
        }
        
        /* Missing versla style */
        .g-block.center {
            text-align: center;
        }
        
        /* Override bundle style (flex) */
        #footer {
            display: block !important;
        }
        
        footer .column-text-wrap,
        footer .post-wrapper {
            margin: 0;
            padding-left: 1em;
            padding-right: 2em; /* Fix something with owl carousel, equalize padding */
        }
        
        footer .row {
            margin-left: 0;
            margin-right: 0;
        }
        
        /* Center footer stuff */
        footer .column {
            padding: 0;
            text-align: center;
        }
        
        .footer-post-title, .footer-post .comment-date {
            text-align: inherit;
        }
        
        footer .box-heading,
        footer .post-item-details {
            text-align: center !important; /* Override inline style on blog posts in footer */
        }
        
        footer .box-heading {
            text-align: center !important;
            font-size: 1.5rem;
            line-height: 5.85rem;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        footer .column > h3, aside .column > h3 {
            font-family: inherit;
        }
        
        .h1 .small, .h1 small, .h2 .small, .h2 small, .h3 .small, .h3 small, .h4 .small, .h4 small, .h5 .small, .h5 small, .h6 .small, .h6 small, h1 .small, h1 small, h2 .small, h2 small, h3 .small, h3 small, h4 .small, h4 small, h5 .small, h5 small, h6 .small, h6 small {
            color: inherit; /* Fix grommet (?) style */
        }
        
        /* ACE Gold: #564F22 */
        
        /* Fix homepage spacing */
        .entryModule__article {
            padding-bottom: 1rem;
        }
        
        .mcb-wrap .column {
            float: left;
        }
        
        .mcb-wrap-inner h1, 
        .mcb-wrap-inner h2, 
        .mcb-wrap-inner h3, 
        .mcb-wrap-inner h4, 
        .mcb-wrap-inner h5, 
        .mcb-wrap-inner h6 { 
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        
        .dark .heading-with-border {
            border-color: #fff;
        }
        
        /* The map */
        .fullwidth-footer {
            border: 1px solid #cfa670;
            border-width: 1px 0;
        }
        
        /* Rounded button backgrounds for ACE */
        .btn.animateInView .btn__bg {
            height: 1.75rem;
            width: 1.75rem;
            border-radius: 50%;
            top: 0px;
            left: 3px;
        }
        
        .post-image {
            display: block;
            
        }
        
        .post-image img {
            width: 100%;
            float: none;
            border: 1px solid #fff;
            border-radius: 50%;
            max-width: 175px;
            height: 175px;
            margin: 0 auto;
            filter: grayscale(60%) invert(10%) brightness(110%);
            -webkit-filter: grayscale(60%) invert(10%) brightness(110%);
        }
        
        .journal-carousel {
            border: none;
        }
        
        /* Fix more grommet stuff */
        .grommetux-table-row .grommetux-heading {
            font-size: 0.85rem;
        }
        
        table.table {
            border: none;
        }
        
        table tr:first-child td {
            border-top: none;
            background: none;
        }
        
        #listing_name {
            margin-top: 3.5rem !important;
            font-size: 1.90078rem !important;
            line-height: 8.2rem !important;
            padding-bottom: 0.54rem !important;
            text-align: center;
        }
        
        .pinned .col-pinned-9 {
            width: 78%;
        }
        
        .heading-with-border {
            border: 4px solid #332F30;
            display: inline-block;
            padding: 10px;
            margin-bottom: 25px;
            transform: rotate(-1.3deg);
        }
        
        .category-form *, .category-extended *, 
        .product-form *, .product-extended * {
            color: white;
        }
        
        /* Hide wishlist it sucks right now */
        .product-extended {
            visibility: hidden;
            display: none;
        }
        
        /* Kill inline styles in content */
        .product-form-component p,
        .product-form-component span,
        .product-form-component button,
        .product-card * {
            background-color: transparent !important;
        }
        
        .dark .product-form-component p,
        .dark .product-form-component span,
        .dark .product-form-component button,
        .dark .product-card * {
            color: white !important;
        }
        
        .category-form-component, .product-form-component {
            top: 0;
            right: 0;
            z-index: 9998;
        }
        
        .product-card h1,
        .product-card h2,
        .product-card h3,
        .product-card h4,
        .product-card h5,
        .product-card h6 {
            font-size: inherit;
        }
        
        input[name=react-star-rating] {
            display: none;
        }
        
        /* Checkout product icon (griddle) flex arrangement */
        .browser-content .griddle-container > div > div {
            display: flex;
            flex-flow: row wrap;
            align-items: center;            
        }
        
        /* Center icons in item container */
        .browser-content .griddle-container > div > div > div {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .browser-content .griddle-container .thumbnail {
            padding: 1.35rem;
        }
        
        .checkout {
            /*display: none*/ /* Hide by default */
        }
        
        .checkout.inactive {
            display: none;
        }
        
        .checkout.dark tbody td {
            color: white;
        }
        
        .checkout > .container {
            width: auto;
        }
        
        .carousel-inner > .item>a>img, .carousel-inner > .item > img, 
        .img-responsive, .thumbnail a > img, .thumbnail > img {
            display: block;
            height: auto;
        }
        
        
        .carousel-inner > .item > a > img, .carousel-inner > .item > img, 
        .img-responsive, .thumbnail a > img, .thumbnail > img {
            display: block;
            height: auto;
            max-width: 100%;
        }
        
        /* Override bundle */
        .card .thumbnail {
            max-width: 100%;
            width: auto;
        }
        
        .browser-content .griddle-container > div > div > div {
            flex: 0 1 100%;
        }
        
        /* Product image 2 */
        #product-tabs .entryModule--prodotti .entryModule__tile {
            position: static !important;
            float: none;
            margin-right: auto !important;
            margin-left: auto !important;
            max-width: 100%;
            max-height: 100%;
            width: 10rem;
            height: 10rem;
            overflow: hidden;
            display: none;
        }
        
        #product-tabs .entryModule--prodotti .entryModule__text {
            position: static !important;
            max-width: 100% !important;
            float: none !important;
            margin-right: auto !important;
            margin-left: auto !important;
            display: block;
            text-align: center !important;
            z-index: 3;
            clear: both;
        }
        
        .summary-component {
            box-sizing: content-box; /* Stretch! */
            background: transparent;
            border: none;
        }
        
        /* Override bundle */
        /* Override product page styles */
        /* We're going transparent */
        .category-form, .category-extended, 
        .product-form, .product-extended {
            padding: 2.7rem 1.5rem;
            border: 1px solid #85312C; /* ACE Red */
            border-bottom: none;
            background: rgba(46,46,46,1);
            color: white;
            text-align: center;
        }
        
        .customer-profile {
            display: flex;
            flex-flow: row wrap;
        }
        
        .customer-profile > .customer-profile-block {
            flex: 1 1 100%;
            padding: 1rem;
        }
        
        .customer-profile-block:first-child {
            flex: 0 0 100%;
        }
        
        #product-form input[type=number] {
            text-align: center;
            font-size: 1.35rem;
            font-weight: bold;
            width: 8rem;
        }
        
        .page-row-wrapper {
            display: flex;
            flex-direction: column;
        }
        
        .page-row-wrapper .product-form-component {
            order: 1;
        }
        
        .page-row-wrapper .page-wrapper {
            order: 2;
        }
        
        .page-wrapper .summary-component {
            display: none;
        }
        
        .subscription-wrapper > .subscription-component {
            min-height: 138vh;
        }
        
        /* Kill bottom margin on product page's product description */
        .entryModule.product-card,
        .entryModule.product-card .entryModule__text {
            margin-bottom: 0;
        }
        
        @media all and (min-width: 25.875em) {
            .browser-content .griddle-container > div > div > div {
                flex: 0 1 33.3%;
            }
            
            /* Product image 2 */
            #product-tabs .entryModule--prodotti .entryModule__tile {
                position: static !important;
                max-width: 100%;
                max-height: 100%;
                width: 15rem;
                height: 15rem;
            }
            
            .subscription-wrapper > .subscription-component {
                min-height: 118vh;
            }
        }
        
        @media all and (min-width: 48em) {
            .carousel-inner > .item > a >img, .carousel-inner > .item > img, 
            .img-responsive, .thumbnail a > img, .thumbnail > img {
                display: block;
                height: auto;
                max-width: 100%;
            }
            
            .browser-content .griddle-container > div > div > div {
                flex: 0 1 33.3%;
            }
            
            /* Product image 2 */
            #product-tabs .entryModule--prodotti .entryModule__tile {
                position: static !important;
                max-width: 100%;
                max-height: 100%;
                width: 20rem;
                height: 20rem;
            }
            
            .entryModule--prodotti .entryModule__tile .tileBg {
                background-position: center center;
            }
            
            /* Override bundle */
            /* Override product page styles */
            /* We're going transparent */
            .category-form, .category-extended, 
            .product-form, .product-extended {
                border: 1px solid #85312C; /* ACE Red */
            }
            
            .product-form, .category-form {
	        	border-top: none;
	        }
            
            .subscription-wrapper > .subscription-component {
                min-height: 96vh;
            }
            
	        .footer-post {
	            padding: 6px 0;
	            border: none;
	        }
        }
        
        @media all and (min-width: 64em) {            
            .page-wrapper .summary-component {
                display: block;
            }
            
            .summary-component {
                margin-left: -15px;
                padding-left: 15px;
                padding-left: 27px;
            }
            
            .carousel-inner > .item > a >img, .carousel-inner > .item > img, 
            .img-responsive, .thumbnail a > img, .thumbnail > img {
                display: block;
                height: auto;
                max-width: 75%;
            }
            
            /* Browser / checkout items */
            .browser-content .griddle-container > div > div > div {
                flex: 0 1 33.3%;
            }
            
            /* Product image 2 */
            #product-tabs .entryModule--prodotti .entryModule__tile {
                display: block !important;
                position: absolute !important;
                max-width: 300px;
                max-height: 300px;
                width: 147px;
                height: 147px;
                left: 0% !important;
                top: -60px !important;
            }
    
            #product-tabs .entryModule--prodotti .entryModule__text {
                max-width: 58% !important;
                transform: translate(0%, -3.4692%) translate3d(0px, -2.00535px, 0px);
                position: relative !important;
                right: -30%;
                margin-right: inherit !important;
                margin-left: inherit !important;
            }
        }
        
        /* Ems are getting crazy in here, they break on the threshold in width where we  
        switch flex for block display mode. Set flex point in pixels and forget about it */
        @media all and (min-width: 985px) {
            .page-row-wrapper {
                display: block; /* Kill flexbox to restore desktop layout */
            }
        }
        
        @media all and (min-width: 74em) {            
            .carousel-inner > .item > a >img, .carousel-inner > .item > img, 
            .img-responsive, .thumbnail a > img, .thumbnail > img {
                display: block;
                height: auto;
                max-width: 66.66%;
            }
            
            /* Center icons in item container */
            .browser-content .griddle-container > div > div > div {
                flex: 0 1 33.33%;
            }
            
            /* Product image 2 */
            #product-tabs .entryModule--prodotti .entryModule__tile {
                position: absolute !important;
                max-width: 500px;
                max-height: 500px;
                width: 300px;
                height: 300px;
                left: -20% !important;
                top: 0 !important;
            }
            
            .category-form-component, .product-form-component {
                right: 52px;
            }
            
            .customer-profile > .customer-profile-block {
	            flex: 1 1 50%;
	            padding: 1rem;
	        }
	        
	        .customer-profile-block:first-child {
	            flex: 0 0 100%;
	        }
            
            .subscription-wrapper > .subscription-component {
                min-height: 74vh;
            }
        }
        
        @media all and (min-width: 101.5em) {
            /* Product image 2 */
            #product-tabs .entryModule--prodotti .entryModule__tile {
                position: absolute !important;
                max-width: 500px;
                max-height: 500px;
                width: 350px;
                height: 350px;
                left: -20% !important;
                top: 0 !important;
            }
            
            /* Browser / checkout items */
            .browser-content .griddle-container > div > div > div {
                flex: 0 1 33.33%;
            }
            
        }
        
        .griddle-container div[class^="col-"] {
            height: auto !important; /* Override bundle */
        }
        
        .cart-ui > .section_wrapper {
            flex: 1;
            align-self: stretch;
            padding-top:5%;
        }
        
        .product-form .form-group > .btn {
            margin-left: auto;
            margin-right: auto;
            display: inline-block !important; /* Override inline style */
        }
        
        /* Override grommet style */
        .grommetux-number-input svg path {
            fill: white;
            stroke: white;
        }
        
        .changePageLoader {
            display: none;
        }
        
        .cart-ui.dark .form-group label,
        .cart-ui.dark .form-group label.control-label {
            color: white !important; /* Override previous important flag */
        }
        
        /* Override input styling */
        input[type="date"], input[type="email"], 
        input[type="number"], input[type="password"], 
        input[type="search"], input[type="tel"], 
        input[type="text"], input[type="url"],
        select.form-control, 
        textarea.form-control,
        select, textarea {
		    color: #000 !important;
		    /*background-color: rgba(34, 34, 34, 1)!important;*/
		    border-color: #222 !important;
		}
		
        /* Override input styling */
        .dark input[type="date"], .dark input[type="email"], 
        .dark input[type="number"], .dark input[type="password"], 
        .dark input[type="search"], .dark input[type="tel"], 
        .dark input[type="text"], .dark input[type="url"],
        .dark select.form-control, 
        .dark textarea.form-control,
        .dark select, .dark textarea {
		    color: #fff !important;
		    background-color: rgba(34, 34, 34, 1)!important;
		    border-color: #fff !important;
		}
        
		/* Outlines suck */
		:focus {
		    outline: none !important;
		    outline-color: transparent !important;
		    outline-style: none !important;
		    outline-width: 0 !important;
		}
		
		/* Fix betheme style */
		.one-fourth.column, .four.columns {
		    width: 25%;
		}
        
        .media-photo-badge img {
            background: #fff;
            /*border-radius: 50%;*/
            border: 1px grey solid;
            padding: 1rem;
        }
        
        #product-tabs .entryModule--prodotti .entryModule__tile {
            display: none !important; /* Temp */
        }
        
        .subscription-product-page .entryModule__text,
        .product-card .entryModule__text {
            right: auto !important;
            left: auto !important;
            max-width: 85% !important;
            text-align: center !important;
            margin: 2.5rem auto;
            font-size: 1.15rem !important; /* Override any inline styles */
        }
        
        #product-tabs {
            font-size: 1.15rem;
        }
        
        .product-page .griddle-footer,
        .subscription-product-page .griddle-footer {
            /* Quick force hide... */
            display: none !important;
        }
        
        .bg-ace-red {
            background: rgba(133, 49, 44, 0.888);
        }
        
        .form-builder {
            margin-top: 0;
            padding-bottom: 2rem;
        }
        
        .form-builder h1,
        .form-builder h2,
        .form-builder h3,
        .form-builder h4,
        .form-builder h5,
        .form-builder h6 {
            border: none;
        }
        
        .form-builder textarea, 
        .form-builder input[type="text"], 
        .form-builder input[type="password"], 
        .form-builder label, 
        .form-builder select {
            width: 90% !important;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-builder textarea {
            height: 90% !important;
        }
        
        .btn.btn-primary {
            background: #564F22; /* ACE Gold */
        }
        
        .btn.btn-primary:hover {
            border: 1px solid white;
        }
        
        #content {
            min-height: 0 !important;
        }
        
        .browser-content .card:hover .thumbnail img,
        .browser-content .card:active .thumbnail img,
        .browser-content .card:focus .thumbnail img {
            border: 3px solid #c9302c;
            transition: border 0.666s;
        }
        
        .pagination {
            margin-left: auto;
            margin-right: auto;
        }
        
        .customer-info {
            display: flex;
            justify-content: space-around;
            flex-flow: row wrap;
        }
        
        .customer-info > div:first-child {
            flex: 1 2 33%;
        }
        .customer-info > div:last-child {
            flex: 2 1 66%;
        }
        
        .customer-profile-block.customer-info .signin-form {
            flex: 1;
        }
        
        @media all and (min-width: 64em) {            
            .customer-profile-block.customer-info .signin-form {
                flex: 5;
            }
        }
        
        .customer-info .form-group {
            text-align: center; /* Center Labels */
        }
        
        .customer-info .form-group input,
        .customer-info .form-group input,
        .customer-info .form-group select,
        .customer-info .form-group textarea {
            margin-left: auto;
            margin-right: auto;
        }
        
        .customer-full-info, 
        .mailing-address, 
        .billing-address, 
        .shipping-address {
            max-width: 75%;
            text-align: center;
            margin: 0 auto;
        }
        .modal-content {
            border-radius: 0 !important;
        }
        
        .userMenu .actionButtons {
            height: auto;
        }
        
        .userMenu .actionButtons > ul {
            display: flex;
            justify-content: flex-end;
        }
        
        .userMenu .actionButtons > ul > li {
            width: 11.5em;
        }
        
        .userMenu .actionButtons .btn.no-bs-style .buttonText {
            display: inline-block;
            position: relative;
            margin-left: 1rem;
            top: -0.25rem;
        }
        
        .userMenu .actionButtons > ul > li br {
            display: none;
        }
        
        @media all and (min-width: 64em) {  
            .userMenu .actionButtons .btn.no-bs-style .buttonText {
                display: block;
            }
            
            .userMenu .actionButtons > ul > li br {
                display: block;
            }
        }
        
        .entryModule__text span.small > div > span {
            transform: rotate(-1.34deg);
            font-size: 1.3rem;
        }
    </style>
    
    <style>
    /* Clear betheme */
    input[type="date"], input[type="email"], input[type="number"], input[type="password"], 
    input[type="search"], input[type="tel"], input[type="text"], input[type="url"], 
    select, textarea {
        width: auto;
    }
    
    /* Horizontal forms, pivot layout using flex */
    .employer-profile > .employer-profile-block,
    .customer-profile > .customer-profile-block {
        flex: 1 0 100%;
    }
    
    @media all and (min-width: 74em) { 
        .customer-profile > .customer-profile-block.address-block {
            flex: 1 1 50%;
        }
    }
    
    .employer-profile-block.employer-info > div,
    .customer-profile-block.customer-info > div {
        display: flex;
        flex-direction: column;
    }
    
    .employer-profile-block.employer-info > div > form > div,
    .customer-profile-block.customer-info > div > form > div {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .mailing-address > div > div,
    .billing-address > div > div,
    .shipping-address > div > div {
        display: flex;
        flex-direction: column;
    }
    
    .mailing-address > div > div > form > div,
    .billing-address > div > div > form > div,
    .shipping-address > div > div > form > div {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
    }
    /* Done pivot */
    
    /* Fix bootstrap col spacing */
    form .col-lg-1, form .col-lg-10, form .col-lg-11, form .col-lg-12, form .col-lg-2, form .col-lg-3, form .col-lg-4, form .col-lg-5, form .col-lg-6, form .col-lg-7, form .col-lg-8, form .col-lg-9, 
    form .col-md-1, form .col-md-10, form .col-md-11, form .col-md-12, form .col-md-2, form .col-md-3, form .col-md-4, form .col-md-5, form .col-md-6, form .col-md-7, form .col-md-8, form .col-md-9, 
    form .col-sm-1, form .col-sm-10, form .col-sm-11, form .col-sm-12, form .col-sm-2, form .col-sm-3, form .col-sm-4, form .col-sm-5, form .col-sm-6, form .col-sm-7, form .col-sm-8, form .col-sm-9, 
    form .col-xs-1, form .col-xs-10, form .col-xs-11, form .col-xs-12, form .col-xs-2, form .col-xs-3, form .col-xs-4, form .col-xs-5, form .col-xs-6, form .col-xs-7, form .col-xs-8, form .col-xs-9 {
        padding-left: 5px;
        padding-right: 5px;
    }
    
    .full-width-inputs .form-group input, 
    .full-width-inputs .form-group select, 
    .full-width-inputs .form-group textarea {
        width: 100%;
    }
    
    .display-block {
        display: block !important;
    }
    </style>
    
    <!--<link href="site/js/jqvmap/jqvmap.css" media="screen, projection" rel="stylesheet" type="text/css">-->
    <link href="site/js/jqvmap/jqvmap.min.css" media="screen, projection" rel="stylesheet" type="text/css">
    
    <script src="catalog/view/javascript/common.js" type="text/javascript"></script>
    <script src="site/js/jqvmap/jquery.vmap.js"></script>
    <!--<script src="site/js/jqvmap/jquery.vmap.min.js"></script>-->
    <script src="site/js/jqvmap/maps/jquery.vmap.world.js"></script>
    
    <style>
    
    #background {
        -webkit-perspective: 1000; 
        -moz-perspective: 1000px; 
        -o-perspective: 1000; 
        perspective: 1000px;
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        /*background-image: linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
        background-image: -o-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
        background-image: -moz-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
        background-image: -webkit-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
        background-image: -ms-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);

        background-image: -webkit-gradient(
            linear,
            left bottom,
            left top,
            color-stop(0.28, rgb(69,132,180)),
            color-stop(0.64, rgb(31,71,120))
        );*/
    }

    #clouds {
        position: absolute;
        left: 100%;
        top: 50%;
        margin-left: -512px;
        margin-top: -256px;
        /*margin-left: -256px;
        margin-top: -256px;*/
        height: 512px;
        width: 1280px;
        width: 100%;
        height: 100%;
        
        //border: 1px solid rgb( 255, 0, 0 );
        -webkit-transform-style: preserve-3d;
        -moz-transform-style: preserve-3d;
        -o-transform-style: preserve-3d;
        transform-style: preserve-3d;
        pointer-events: none;
    }


    #clouds div {
        -webkit-transform-style: preserve-3d;
        -moz-transform-style: preserve-3d;	
        -o-transform-style: preserve-3d;	
        transform-style: preserve-3d;
    }

    .cloudBase {
        //border: 1px solid #ff00ff;
        position: absolute;
        left: 256px;
        top: 256px;
        width: 20px;
        height: 20px;
        margin-left: -10px;
        margin-top: -10px;
    }

    .cloudLayer {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 256px;
        height: 256px;
        margin-left: -128px;
        margin-top: -128px;
        -webkit-transition: opacity .5s ease-out;
        -moz-transition: opacity .5s ease-out;
        -o-transition: opacity .5s ease-out;
        transition: opacity .5s ease-out;
    }

    #options {
        position: fixed;
        left: 0;
        top: 0;
        margin: 0;
        padding: 50px 20px 20px;
        width: 300px;
        background-color: rgba( 0, 0, 0, .4 );
        border-radius: 5px;
        z-index: 20;
        display: none !important;
    }

    #options,
    #options h1,
    #options h2,
    #options h3,
    #options h4,
    #options h5,
    #options h6 {
        color: white;
    }

    #optionsContent{
        margin-top: 20px;
        -webkit-transition: all 1s ease-out;
        -moz-transition: all 1s ease-out;
        -o-transition: all 1s ease-out;
        transition: all 1s ease-out;
    }

    p {
        margin-bottom: 20px;
    }

    .actions {
        margin-bottom: 20px;
    }

    #textureList li {
        clear: both;
        list-style-type: none;
        position: relative;
        height: 35px;
        padding-top: 10px;
    }

    #textureList li span {
        text-transform: capitalize;
    }

    #textureList div {
        position: absolute;
        right: 0;
        top: 0;
        display: flex;
    }

    #textureList .button {
        padding: 0.25rem 0.5rem
    }

    #textureList li a{
        float: left;
    }

    a {
        color: inherit;
    }

    #textureControls {
        display: none;
    }

    #closeBtn {
        position: absolute; 
        left: 15px;
        top: 10px;
    }

    .nope{
        text-decoration: line-through;
    }

    :-moz-full-screen #options{ display: none }
    :-webkit-full-screen #options{ display: none }
    :full-screen #options{ display: none }
    
    .header__contentTop {
        opacity: 0.777;
    }
    </style>
    <style>
        /*#background {
            bottom: 0;
            left: 0;
            overflow: hidden;
            perspective: 400; 
            position: absolute;
            right: 0;
            top: 0;
        }*/

        #clouds {
            position: absolute;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            opacity: 0.666;
        }
        
        .cloudBase {
            height: 20px;
            left: 256px;
            margin-left: -10px;
            margin-top: -10px;
            position: absolute;
            top: 256px;
            width: 20px;
        }
        
        .cloudLayer {
            height: 256px;
            left: 50%;
            margin-left: -128px;
            margin-top: -128px;
            position: absolute;
            top: 50%;
            width: 256px;
        }
        
        @media screen and (min-width: 922px) {
            #logo {
                margin: 0 0 10px 0;
                position: fixed;
                top: 100px;
                width: 100%;
                left: 0;
                text-align: center;
                z-index: 1;
                opacity: 0.333;
            }
            
            #logo a {
                margin: 0 auto;
            }
            
            #logo img {
                max-width: 50%;
                margin: 0 auto;
                height: 150px;
            }
            
            .motion-menu {
                position: fixed;
                top: 60%;
                right: 54%;
                white-space: nowrap;
                cursor: pointer;
            }
            
            .motion-menu i {
                margin-right: 0.5rem;
            }
            
            .taco-main--content {
                /* Kill background on large devices */
                background-image: none;
                background-color: transparent;
                background: rgba(255,255,255,0.747);
            }
            
            .cart > div {
                background: transparent !important; /* Temp override inline styles */
            }
        }
        
        #category-menu,
        .taco-main--content {
            position: relative;
            z-index: 100;
        }
        
        #world {
            z-index: 2;
            position: absolute;
            width: 100%;
            height: 100%;
        }
        #background {
            z-index: 2;
        }
        
        
        .button, .g-pricingtable .button {
            display: inline-block;
            font-family: "sourcesanspro", "Helvetica", "Tahoma", "Geneva", "Arial", sans-serif;
            font-weight: 700;
            background: #85312c;
            border: 1px solid #111;
            color: #fff;
            font-size: 1.125rem;
            line-height: 1.225;
            margin: 0 0 0.5rem 0;
            padding: 0.75rem 1.4rem;
            border-radius: 5px;
            vertical-align: middle;
            text-shadow: none;
            -webkit-transition: background 0.2s;
            -moz-transition: background 0.2s;
            transition: background 0.2s;
        }
        
        .grommetux-background-color-index-neutral-1, 
        .grommetux-background-color-index-neutral-5 {
            background-color: #85312c !important;
        }
    </style>
    <style>
        .rccs__demo__content {
            margin-top: 2rem;
        }
        /** ISSUERS **/
        /** Images **/
        .rccs {
          margin: 0 auto;
          perspective: 1000px;
          width: 290px;
        }
        .rccs__card {
          height: 182.87299786px;
          margin: 0 auto;
          position: relative;
          transform-style: preserve-3d;
          transition: all 0.4s linear;
          width: 290px;
        }
        .rccs__card--front, .rccs__card--back {
          backface-visibility: hidden;
          background: linear-gradient(25deg, #939393, #717171);
          border-radius: 14.5px;
          color: #fff;
          height: 100%;
          left: 0;
          overflow: hidden;
          position: absolute;
          top: 0;
          transform-style: preserve-3d;
          width: 100%;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        .rccs__card--front {
          z-index: 20;
        }
        .rccs__card--back {
          transform: rotateY(180deg);
        }
        .rccs__card--back .rccs__issuer {
          background-position: bottom center;
          bottom: 5%;
          left: 50%;
          opacity: 0.6;
          right: auto;
          top: auto;
          transform: translateX(-50%);
        }
        .rccs__card__background {
          height: 200%;
          left: -170%;
          position: absolute;
          top: -60%;
          transform: rotate(25deg);
          transition: all 0.5s ease-out;
          width: 150%;
          left: -170%;
        }
        .rccs__card--flipped {
          transform: rotateY(180deg);
        }
        .rccs__card--flipped .rccs__card--front {
          z-index: 10;
        }
        .rccs__card--flipped .rccs__card--back {
          z-index: 20;
        }
        .rccs__card--unknown > div {
          background: linear-gradient(25deg, #999, #999);
          box-shadow: none;
        }
        .rccs__card--unknown .rccs__issuer {
          visibility: hidden;
        }
        .rccs__card:not(.rccs__card--unknown) .rccs__card__background {
          left: -22%;
        }
        .rccs__card--amex .rccs__card__background {
          background: linear-gradient(25deg, #308c67, #a3f2cf);
        }
        .rccs__card--amex .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxkZWZzPjxyYWRpYWxHcmFkaWVudCBjeD0iMTcuNTQxJSIgY3k9IjE3LjQ2NiUiIGZ4PSIxNy41NDElIiBmeT0iMTcuNDY2JSIgcj0iOTEuMjM3JSIgaWQ9ImEiPjxzdG9wIHN0b3AtY29sb3I9IiM2NUJDRjEiIG9mZnNldD0iMCUiLz48c3RvcCBzdG9wLWNvbG9yPSIjMjNBREUzIiBvZmZzZXQ9IjQ1LjQ2JSIvPjxzdG9wIHN0b3AtY29sb3I9IiMwREE2RTAiIG9mZnNldD0iNTAlIi8+PHN0b3Agc3RvcC1jb2xvcj0iIzA1NTFDMyIgb2Zmc2V0PSIxMDAlIi8+PC9yYWRpYWxHcmFkaWVudD48L2RlZnM+PHBhdGggZmlsbD0idXJsKCNhKSIgZD0iTTAgMGg1MTJ2NTEyaC01MTJ6Ii8+PHBhdGggZD0iTTQ1Ljc5MSAyMjAuOTM1bC05Ljc3My0yMy44MTMtOS43MTcgMjMuODEzaDE5LjQ4OXptMjE1LjI4OS05LjQ4M2MtMS45NjIgMS4xOTEtNC4yODMgMS4yMzEtNy4wNjMgMS4yMzFoLTE3LjM0NXYtMTMuMjY4aDE3LjU4MWMyLjQ4OCAwIDUuMDg0LjExMiA2Ljc3MSAxLjA3NyAxLjg1Mi44NyAyLjk5OCAyLjcyMiAyLjk5OCA1LjI4MSAwIDIuNjExLTEuMDkgNC43MTItMi45NDIgNS42Nzl6bTEyMy43MzkgOS40ODNsLTkuODgxLTIzLjgxMy05LjgyNyAyMy44MTNoMTkuNzA3em0tMjMwLjY1OCAyNS43NzZoLTE0LjYzN2wtLjA1NC00Ni43ODQtMjAuNzA0IDQ2Ljc4NGgtMTIuNTM2bC0yMC43NTgtNDYuODI1djQ2LjgyNWgtMjkuMDRsLTUuNDg2LTEzLjMyNGgtMjkuNzI5bC01LjU0MiAxMy4zMjRoLTE1LjUwN2wyNS41NjgtNTkuNzM1aDIxLjIxNGwyNC4yODQgNTYuNTU2di01Ni41NTZoMjMuMzA0bDE4LjY4NiA0MC41MjMgMTcuMTY1LTQwLjUyM2gyMy43NzJ2NTkuNzM1aC4wMDJ6bTU4LjMzOCAwaC00Ny42OTd2LTU5LjczNWg0Ny42OTd2MTIuNDM5aC0zMy40MTl2MTAuNzY3aDMyLjYxN3YxMi4yNDVoLTMyLjYxN3YxMS45MjloMzMuNDE5djEyLjM1NHptNjcuMjUxLTQzLjY0N2MwIDkuNTI0LTYuMzU3IDE0LjQ0NC0xMC4wNjEgMTUuOTIyIDMuMTI0IDEuMTg5IDUuNzkzIDMuMjkgNy4wNjMgNS4wMyAyLjAxNiAyLjk3MSAyLjM2NCA1LjYyNSAyLjM2NCAxMC45NnYxMS43MzVoLTE0LjQwMWwtLjA1NC03LjUzM2MwLTMuNTk0LjM0NC04Ljc2NC0yLjI1NC0xMS42MzctMi4wODYtMi4xMDEtNS4yNjYtMi41NTctMTAuNDA3LTIuNTU3aC0xNS4zMjd2MjEuNzI3aC0xNC4yNzd2LTU5LjczNWgzMi44NGM3LjI5NyAwIDEyLjY3My4xOTMgMTcuMjg5IDIuODYxIDQuNTE3IDIuNjY4IDcuMjI1IDYuNTY0IDcuMjI1IDEzLjIyN3ptMjIuODUgNDMuNjQ3aC0xNC41Njl2LTU5LjczNWgxNC41Njl2NTkuNzM1em0xNjkuMDE3IDBoLTIwLjIzM2wtMjcuMDY0LTQ0LjgzNHY0NC44MzRoLTI5LjA3OGwtNS41NTctMTMuMzI0aC0yOS42NmwtNS4zOTEgMTMuMzI0aC0xNi43MDdjLTYuOTQgMC0xNS43MjctMS41MzUtMjAuNzA0LTYuNjA3LTUuMDE4LTUuMDcyLTcuNjI5LTExLjk0Mi03LjYyOS0yMi44MDUgMC04Ljg1OSAxLjU2LTE2Ljk1OCA3LjY5Ny0yMy4zNTggNC42MTYtNC43NjcgMTEuODQ1LTYuOTY1IDIxLjY4NC02Ljk2NWgxMy44MjN2MTIuNzk5aC0xMy41MzNjLTUuMjExIDAtOC4xNTMuNzc1LTEwLjk4NyAzLjUzOS0yLjQzNCAyLjUxNS00LjEwNCA3LjI3LTQuMTA0IDEzLjUzMSAwIDYuNCAxLjI3MiAxMS4wMTQgMy45MjYgMTQuMDI4IDIuMTk4IDIuMzY0IDYuMTkzIDMuMDgxIDkuOTUxIDMuMDgxaDYuNDEybDIwLjEyNC00Ni45NzdoMjEuMzk0bDI0LjE3NCA1Ni41di01Ni41aDIxLjc0bDI1LjA5OCA0MS42MDJ2LTQxLjYwMmgxNC42MjV2NTkuNzMzem0tNDcxLjYxNiAxMS43MzNoMjQuMzk1bDUuNTAxLTEzLjI2OGgxMi4zMTVsNS40ODYgMTMuMjY4aDQ4di0xMC4xNDRsNC4yODUgMTAuMTg3aDI0LjkxOGw0LjI4NS0xMC4zMzh2MTAuMjk1aDExOS4yODlsLS4wNTYtMjEuNzc5aDIuMzA4YzEuNjE2LjA1NiAyLjA4OC4yMDUgMi4wODggMi44NzR2MTguOTA2aDYxLjY5N3YtNS4wN2M0Ljk3NiAyLjY2NyAxMi43MTcgNS4wNyAyMi45MDIgNS4wN2gyNS45NTZsNS41NTUtMTMuMjY4aDEyLjMxNWw1LjQzMiAxMy4yNjhoNTAuMDE4di0xMi42MDNsNy41NzQgMTIuNjAzaDQwLjA4MXYtODMuMzEyaC0zOS42Njd2OS44MzlsLTUuNTU1LTkuODM5aC00MC43MDN2OS44MzlsLTUuMTAxLTkuODM5aC01NC45OGMtOS4yMDMgMC0xNy4yOTMgMS4yODUtMjMuODI4IDQuODY1di00Ljg2NWgtMzcuOTQxdjQuODY1Yy00LjE1OC0zLjY5LTkuODI1LTQuODY1LTE2LjEyNS00Ljg2NWgtMTM4LjYxM2wtOS4zMDEgMjEuNTE4LTkuNTUxLTIxLjUxOGgtNDMuNjZ2OS44MzlsLTQuNzk2LTkuODM5aC0zNy4yMzVsLTE3LjI5MSAzOS42MTF2NDMuNzAxaC4wMDJ6TTUxMiAzMDIuMDE0aC0yNi4wMzljLTIuNiAwLTQuMzI3LjA5Ny01Ljc4MiAxLjA4LTEuNTA3Ljk2OC0yLjA4OCAyLjQwNS0yLjA4OCA0LjMwMiAwIDIuMjU1IDEuMjczIDMuNzkgMy4xMjQgNC40NTMgMS41MDcuNTI1IDMuMTI2LjY3OCA1LjUwNi42NzhsNy43NDMuMjA3YzcuODE0LjE5MyAxMy4wMjkgMS41MzYgMTYuMjA5IDQuODEyLjU3OS40NTYuOTI3Ljk2OCAxLjMyNSAxLjQ4di0xNy4wMTJ6bTAgMzkuNDE2Yy0zLjQ3IDUuMDc1LTEwLjIzMyA3LjY0OC0xOS4zODggNy42NDhoLTI3LjU5MXYtMTIuODJoMjcuNDc5YzIuNzI2IDAgNC42MzMtLjM1OSA1Ljc4Mi0xLjQ4Ljk5NS0uOTI1IDEuNjg5LTIuMjY4IDEuNjg5LTMuOSAwLTEuNzQyLS42OTQtMy4xMjQtMS43NDUtMy45NTQtMS4wMzctLjkxMi0yLjU0Ni0xLjMyNy01LjAzNC0xLjMyNy0xMy40MTUtLjQ1Ni0zMC4xNTEuNDE1LTMwLjE1MS0xOC41MDQgMC04LjY3MiA1LjUwNi0xNy44IDIwLjQ5OC0xNy44aDI4LjQ1OHYtMTEuODk1aC0yNi40NDFjLTcuOTc5IDAtMTMuNzc2IDEuOTExLTE3Ljg4MSA0Ljg4MnYtNC44ODJoLTM5LjEwOWMtNi4yNTQgMC0xMy41OTUgMS41NS0xNy4wNjggNC44ODJ2LTQuODgyaC02OS44Mzl2NC44ODJjLTUuNTU4LTQuMDEtMTQuOTM3LTQuODgyLTE5LjI2NS00Ljg4MmgtNDYuMDY2djQuODgyYy00LjM5Ny00LjI1OC0xNC4xNzYtNC44ODItMjAuMTM2LTQuODgyaC01MS41NTZsLTExLjc5OCAxMi43NjgtMTEuMDUtMTIuNzY4aC03Ny4wMTR2ODMuNDIxaDc1LjU2NWwxMi4xNTctMTIuOTcgMTEuNDUyIDEyLjk3IDQ2LjU3OC4wNDF2LTE5LjYyNGg0LjU3OWM2LjE4LjA5NiAxMy40NjktLjE1MyAxOS45LTIuOTMzdjIyLjUxNGgzOC40MTl2LTIxLjc0MmgxLjg1M2MyLjM2NSAwIDIuNTk4LjA5NyAyLjU5OCAyLjQ2MXYxOS4yOGgxMTYuNzA5YzcuNDEgMCAxNS4xNTUtMS44OTcgMTkuNDQ0LTUuMzM4djUuMzM4aDM3LjAyYzcuNzA0IDAgMTUuMjI3LTEuMDggMjAuOTUxLTMuODQ1di0xNS41NDF6bS01Ni45OS0yMy44ODRjMi43ODIgMi44NzkgNC4yNzMgNi41MTQgNC4yNzMgMTIuNjY3IDAgMTIuODYyLTguMDM1IDE4Ljg2NS0yMi40NDQgMTguODY1aC0yNy44Mjd2LTEyLjgyaDI3LjcxNWMyLjcxIDAgNC42MzItLjM1OSA1LjgzNi0xLjQ4Ljk4My0uOTI1IDEuNjg3LTIuMjY4IDEuNjg3LTMuOSAwLTEuNzQyLS43NjMtMy4xMjQtMS43NDMtMy45NTQtMS4wOTMtLjkxMi0yLjYtMS4zMjctNS4wODgtMS4zMjctMTMuMzYxLS40NTYtMzAuMDkzLjQxNS0zMC4wOTMtMTguNTA0IDAtOC42NzIgNS40NDgtMTcuOCAyMC40MjYtMTcuOGgyOC42NDJ2MTIuNzI1aC0yNi4yMDhjLTIuNTk4IDAtNC4yODcuMDk3LTUuNzI0IDEuMDgtMS41NjUuOTY4LTIuMTQ1IDIuNDA1LTIuMTQ1IDQuMzAyIDAgMi4yNTUgMS4zMjkgMy43OSAzLjEyNiA0LjQ1MyAxLjUwNy41MjUgMy4xMjYuNjc4IDUuNTYuNjc4bDcuNjkxLjIwN2M3Ljc1Ni4xODkgMTMuMDggMS41MzEgMTYuMzE2IDQuODA4em0tMTI4LjkxOC0zLjY5MmMtMS45MTEgMS4xMzQtNC4yNzUgMS4yMzEtNy4wNTUgMS4yMzFoLTE3LjM1NnYtMTMuNDI4aDE3LjU5MmMyLjU0NCAwIDUuMDg5LjA1NCA2LjgxOCAxLjA4IDEuODUyLjk2OCAyLjk1OCAyLjgyIDIuOTU4IDUuMzc4IDAgMi41NTgtMS4xMDcgNC42MTktMi45NTggNS43Mzh6bTguNjI4IDcuNDRjMy4xOCAxLjE3NCA1Ljc4IDMuMjc4IDYuOTk5IDUuMDE5IDIuMDE3IDIuOTE5IDIuMzA5IDUuNjQzIDIuMzY3IDEwLjkxM3YxMS44NTJoLTE0LjM0M3YtNy40OGMwLTMuNTk3LjM0Ni04LjkyMi0yLjMwOS0xMS43MDItMi4wODgtMi4xNDItNS4yNy0yLjY1NC0xMC40ODItMi42NTRoLTE1LjI2OHYyMS44MzZoLTE0LjM1NnYtNTkuNzg2aDMyLjk4NWM3LjIzMyAwIDEyLjUwMS4zMTkgMTcuMTkgMi44MjEgNC41MDkgMi43MjQgNy4zNDUgNi40NTYgNy4zNDUgMTMuMjc2LS4wMDIgOS41NDItNi4zNjYgMTQuNDEyLTEwLjEyNyAxNS45MDV6bTE4LjA0OC0zMi4wMDJoNDcuNjg0djEyLjM2NGgtMzMuNDU1djEwLjg2OWgzMi42Mzl2MTIuMTk4aC0zMi42Mzl2MTEuODk1bDMzLjQ1NS4wNTR2MTIuNDA1aC00Ny42ODR2LTU5Ljc4NnptLTk2LjM5MyAyNy41OTFoLTE4LjQ2M3YtMTUuMjI1aDE4LjYyOWM1LjE1OCAwIDguNzM4IDIuMTAyIDguNzM4IDcuMzMgMCA1LjE3MS0zLjQxNSA3Ljg5NS04LjkwNCA3Ljg5NXptLTMyLjY5MyAyNi43NThsLTIxLjkzNS0yNC4zNTMgMjEuOTM1LTIzLjU3OXY0Ny45MzJ6bS01Ni42NDctNy4wMjJoLTM1LjEyN3YtMTEuODk1aDMxLjM2NnYtMTIuMTk4aC0zMS4zNjZ2LTEwLjg2OWgzNS44MTlsMTUuNjI3IDE3LjQyMy0xNi4zMTkgMTcuNTR6bTExMy41ODMtMjcuNjNjMCAxNi42MDgtMTIuMzkxIDIwLjAzNy0yNC44NzkgMjAuMDM3aC0xNy44Mjd2MjAuMDUzaC0yNy43NjlsLTE3LjU5Mi0xOS43OTItMTguMjgzIDE5Ljc5MmgtNTYuNTkxdi01OS43ODZoNTcuNDYybDE3LjU3OCAxOS41OTcgMTguMTczLTE5LjU5N2g0NS42NTJjMTEuMzM4IDAgMjQuMDc3IDMuMTM5IDI0LjA3NyAxOS42OTZ6IiBmaWxsPSIjZmZmIi8+PC9zdmc+");
        }
        .rccs__card--amex .rccs__cvc__front {
          opacity: 0.5;
          visibility: visible;
        }
        .rccs__card--dankort .rccs__card__background {
          background: linear-gradient(25deg, #ccc, #999);
        }
        .rccs__card--dankort .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjMwOCIgdmlld0JveD0iMCAwIDUxMiAzMDgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxkZWZzPjxsaW5lYXJHcmFkaWVudCB4MT0iNTAlIiB5MT0iMCUiIHgyPSI1MCUiIHkyPSIxMDAlIiBpZD0iYSI+PHN0b3Agc3RvcC1jb2xvcj0iI0Y1MDkxQSIgb2Zmc2V0PSIwJSIvPjxzdG9wIHN0b3AtY29sb3I9IiM5RTBDMTciIG9mZnNldD0iMTAwJSIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxwYXRoIGQ9Ik0zNTguNDA3IDBoLTIwNC43OTVjLTg0Ljg0MiAwLTE1My42MTIgNjguNzcxLTE1My42MTIgMTUzLjU5MyAwIDg0Ljg1MSA2OC43NyAxNTMuNjA2IDE1My42MTIgMTUzLjYwNmgyMDQuNzk1Yzg0LjgyMiAwIDE1My41OTMtNjguNzU1IDE1My41OTMtMTUzLjYwNiAwLTg0LjgyMS02OC43Ny0xNTMuNTkzLTE1My41OTMtMTUzLjU5MyIgZmlsbD0iI0ZFRkVGRSIvPjxwYXRoIGQ9Ik0zOTUuNTkxIDE0NC40ODZsNjguMzI4IDgxLjI2MWMxNC4xNTEtMjAuNDUxIDIyLjQ2Mi00NS4yNDIgMjIuNDYyLTcxLjk0NSAwLTI4LjE4My05LjI1OS01NC4yNDgtMjQuODg0LTc1LjMxNWwtNjUuOTA2IDY1Ljk5OXptLTI0MS4zOTctMTE3LjM4NmMtMzguNTQ3IDAtNzMuMTIxIDE3LjI5My05Ni4zODMgNDQuNTI4aDE0NC4xNTljNTEuMDI3IDAgODkuNDc0IDEyLjk4MyA5My40MzEgNTUuOTc0bDUzLjcwMi01NS45ODFoMTA2LjkzMWMtMjMuMjYzLTI3LjIyNy01Ny44MzEtNDQuNTItOTYuMzYxLTQ0LjUyaC0yMDUuNDc5em0yOS4yMzYgMjA1LjQ3OWgtMTI4LjQyOWMyMy4yMzMgMjkuMTkzIDU5LjA2MiA0Ny45NDYgOTkuMTk0IDQ3Ljk0NmgyMDUuNDc5YzQwLjExNyAwIDc1LjkzNy0xOC43NDUgOTkuMTcxLTQ3LjkzOWgtMTA5LjI5NWwtNTYuNzk2LTcxLjc3NmMtMTAuNTA2IDQ4LjkzOS00NC44ODEgNzEuNzY5LTEwOS4zMjMgNzEuNzY5ek0xMTguNDQgMTI1Ljk5N2wtMTguMjIyIDUwLjFoNzMuNTE3YzIxLjczMyAwIDI4LjMxLTguOTk4IDMzLjE3My0yNi4zMTkgNC44MTUtMTcuMTMxLTcuMjc3LTIzLjc4LTIyLjEyOS0yMy43OGgtNjYuMzM5eiIgZmlsbD0idXJsKCNhKSIvPjwvc3ZnPg==");
        }
        .rccs__card--dinersclub > div {
          color: #555;
        }
        .rccs__card--dinersclub .rccs__card__background {
          background: linear-gradient(25deg, #fff, #eee);
        }
        .rccs__card--dinersclub .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjEzNCIgdmlld0JveD0iMCAwIDUxMiAxMzQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxwYXRoIGQ9Ik05OS4yODUgMTMzLjg2YzM2LjQ0Ni4xNzcgNjkuNzE1LTI5LjY1OSA2OS43MTUtNjUuOTU1IDAtMzkuNjg5LTMzLjI2OS02Ny4xMjItNjkuNzE1LTY3LjExMWgtMzEuMzY1Yy0zNi44ODItLjAxMS02Ny4yNDEgMjcuNDI5LTY3LjI0MSA2Ny4xMTEgMCAzNi4zMDUgMzAuMzU4IDY2LjEzMyA2Ny4yNDEgNjUuOTU1aDMxLjM2NSIgZmlsbD0iIzAwNjA5NSIvPjxwYXRoIGQ9Ik04MS45MDkgMTAzLjI0N3YtNzIuMDcyYzE0LjUxNyA1LjU1NyAyNC44MjMgMTkuNTgzIDI0Ljg0NyAzNi4wMzMtLjAyNCAxNi40NTQtMTAuMzMgMzAuNDcxLTI0Ljg0NyAzNi4wMzhtLTUyLjUyMi0zNi4wMzhjLjAzMy0xNi40NDEgMTAuMzIyLTMwLjQ1OCAyNC44MzEtMzYuMDMydjcyLjA1NWMtMTQuNTA5LTUuNTY5LTI0Ljc5OC0xOS41NzgtMjQuODMxLTM2LjAyNG0zOC42NzktNjAuOTE1Yy0zMy43MDIuMDExLTYxLjAxMSAyNy4yNzMtNjEuMDIgNjAuOTE1LjAwOCAzMy42MzkgMjcuMzE4IDYwLjg5NSA2MS4wMiA2MC45MDUgMzMuNzEzLS4wMSA2MS4wMjgtMjcuMjY2IDYxLjAzMy02MC45MDUtLjAwNS0zMy42NDItMjcuMzE5LTYwLjkwNC02MS4wMzMtNjAuOTE1IiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTE5MC4zNzMgMjkuNDIxYzAtNi4yOTQtMy4yOTEtNS44ODEtNi40NDQtNS45NDd2LTEuODE5YzIuNzMzLjEzMyA1LjUzNi4xMzMgOC4yNzUuMTMzIDIuOTQzIDAgNi45NDEtLjEzMyAxMi4xMzMtLjEzMyAxOC4xNTcgMCAyOC4wNDIgMTIuMTAzIDI4LjA0MiAyNC40OTYgMCA2LjkyOS00LjA2MyAyNC4zNTMtMjguODg4IDI0LjM1My0zLjU3MyAwLTYuODc0LS4xMzgtMTAuMTY3LS4xMzgtMy4xNTMgMC02LjI0Mi4wNjctOS4zOTUuMTM4di0xLjgyYzQuMjAzLS40MjEgNi4yNDItLjU2MSA2LjQ0NC01LjMydi0zMy45NDN6bTYuODczIDMyLjgyNWMwIDUuMzg4IDMuODYxIDYuMDE3IDcuMjk0IDYuMDE3IDE1LjE0OSAwIDIwLjEyNC0xMS40MDcgMjAuMTI0LTIxLjgzNiAwLTEzLjA4My04LjQxNS0yMi41My0yMS45NDctMjIuNTMtMi44NzkgMC00LjIwMy4yMDUtNS40NzIuMjh2MzguMDY4em0zNy41OTIgNi40NGgxLjMzMWMxLjk2MSAwIDMuMzY1IDAgMy4zNjUtMi4zMTV2LTE4Ljk2MmMwLTMuMDc3LTEuMDUtMy41MDQtMy42NDgtNC44OTZ2LTEuMTIyYzMuMjk2LS45ODUgNy4yMjYtMi4zMDggNy41MDMtMi41MTguNDkzLS4yOC45MTItLjM1NCAxLjI2NC0uMzU0LjM0NyAwIC40OTIuNDIxLjQ5Mi45ODV2MjYuODY2YzAgMi4zMTUgMS41NDIgMi4zMTUgMy41MDggMi4zMTVoMS4xODl2MS44MmMtMi4zODYgMC00Ljg0Mi0uMTQtNy4zNi0uMTQtMi41MjYgMC01LjA1Mi4wNjgtNy42NDMuMTR2LTEuODJ6bTcuNTAzLTQwLjk0Yy0xLjgyNyAwLTMuNDM2LTEuNjc5LTMuNDM2LTMuNDk5IDAtMS43NTIgMS42ODYtMy4zNjYgMy40MzYtMy4zNjYgMS44MTcgMCAzLjQzNSAxLjQ3NiAzLjQzNSAzLjM2NiAwIDEuODkyLTEuNTQ2IDMuNDk5LTMuNDM1IDMuNDk5em0xNC4xNjIgMjAuMDgyYzAtMi41ODgtLjc3NS0zLjI4NS00LjA2My00LjYxNXYtMS4zM2MzLjAxMS0uOTc4IDUuODg0LTEuODkyIDkuMjU0LTMuMzYzLjIwOCAwIC40MTQuMTQzLjQxNC42OTl2NC41NTFjNC4wMDMtMi44NjYgNy40NDEtNS4yNSAxMi4xNDQtNS4yNSA1Ljk1NiAwIDguMDU4IDQuMzQgOC4wNTggOS44djE4LjA1MmMwIDIuMzE1IDEuNTQxIDIuMzE1IDMuNTA2IDIuMzE1aDEuMjYydjEuODJjLTIuNDU5IDAtNC45MTMtLjE0LTcuNDMxLS4xNC0yLjUyOCAwLTUuMDUzLjA2OC03LjU3Ni4xNHYtMS44MmgxLjI2MWMxLjk2NyAwIDMuMzYzIDAgMy4zNjMtMi4zMTV2LTE4LjEyYzAtMy45OTQtMi40NDMtNS45NDktNi40NDYtNS45NDktMi4yNDMgMC01LjgxOSAxLjgxNy04LjE0MSAzLjM1OHYyMC43MTFjMCAyLjMxNSAxLjU1MiAyLjMxNSAzLjUxNiAyLjMxNWgxLjI2MXYxLjgyYy0yLjQ1NCAwLTQuOTEyLS4xNC03LjQzNi0uMTQtMi41MjEgMC01LjA1LjA2OC03LjU3MS4xNHYtMS44MmgxLjI2NWMxLjk2MiAwIDMuMzYxIDAgMy4zNjEtMi4zMTV2LTE4LjU0NHptMzUuOTA3IDMuMzY1Yy0uMTQ1LjYzMS0uMTQ1IDEuNjc3IDAgNC4wNTkuNDEyIDYuNjQ2IDQuNzAyIDEyLjEwMyAxMC4zMDIgMTIuMTAzIDMuODYzIDAgNi44ODItMi4xIDkuNDctNC42ODZsLjk4Ljk4Yy0zLjIyNiA0LjI3LTcuMjI0IDcuOTA4LTEyLjk2OCA3LjkwOC0xMS4xNTQgMC0xMy4zOTUtMTAuNzgtMTMuMzk1LTE1LjI1NiAwLTEzLjcxOCA5LjI1Mi0xNy43NzkgMTQuMTU3LTE3Ljc3OSA1LjY4NSAwIDExLjc5MSAzLjU2OCAxMS44NTMgMTAuOTg2IDAgLjQyNiAwIC44NC0uMDYyIDEuMjY1bC0uNjM3LjQyMWgtMTkuNzAxem0xMi40MTMtMi4yNDFjMS43NTIgMCAxLjk1Ni0uOTEyIDEuOTU2LTEuNzUxIDAtMy41NjgtMi4xNzEtNi40MzctNi4xMDEtNi40MzctNC4yNzUgMC03LjIxOSAzLjE0Mi04LjA2IDguMTg3aDEyLjIwNXptOS42MDQgMTkuNzM2aDEuODk0YzEuOTU2IDAgMy4zNiAwIDMuMzYtMi4zMTV2LTE5LjY2MWMwLTIuMTY4LTIuNTkxLTIuNTkxLTMuNjQ1LTMuMTV2LTEuMDQ3YzUuMTE5LTIuMTcyIDcuOTI1LTMuOTk0IDguNTY1LTMuOTk0LjQxMiAwIC42Mi4yMS42Mi45MTN2Ni4yOTdoLjE1YzEuNzQ2LTIuNzI5IDQuNjk3LTcuMjEgOC45NzItNy4yMSAxLjc1NCAwIDMuOTk1IDEuMTg4IDMuOTk1IDMuNzA5IDAgMS44OS0xLjMyOSAzLjU3NC0zLjI5MSAzLjU3NC0yLjE4MiAwLTIuMTgyLTEuNjg0LTQuNjMzLTEuNjg0LTEuMTk0IDAtNS4xMTkgMS42MTEtNS4xMTkgNS44MTJ2MTYuNDM5YzAgMi4zMTUgMS4zOTkgMi4zMTUgMy4zNjUgMi4zMTVoMy45MjV2MS44MmMtMy44NTgtLjA3Mi02Ljc5My0uMTQtOS44MTItLjE0LTIuODc0IDAtNS44MjEuMDY4LTguMzQ1LjE0di0xLjgyem0yNi45OTUtNy45MTFjLjkxNCA0LjYxOCAzLjcxNiA4LjU0IDguODM4IDguNTQgNC4xMyAwIDUuNjcyLTIuNTE5IDUuNjcyLTQuOTY3IDAtOC4yNi0xNS4yODEtNS42MDMtMTUuMjgxLTE2Ljg2NyAwLTMuOTIyIDMuMTU5LTguOTYxIDEwLjg2OS04Ljk2MSAyLjI0MSAwIDUuMjU1LjYzMiA3Ljk4OCAyLjAzM2wuNDk1IDcuMTMyaC0xLjYxNGMtLjctNC40MDUtMy4xNTMtNi45MjQtNy42NDMtNi45MjQtMi44MDggMC01LjQ2OSAxLjYwOS01LjQ2OSA0LjYxOCAwIDguMTk0IDE2LjI2OCA1LjY2OSAxNi4yNjggMTYuNjU1IDAgNC42MTgtMy43MTYgOS41MjItMTIuMDYzIDkuNTIyLTIuODA0IDAtNi4xMDEtLjk4Mi04LjU1Mi0yLjM4bC0uNzc0LTguMDQ5IDEuMjY0LS4zNTN6bTgzLjQ0Mi0yNi40NTVoLTEuNzQ5Yy0xLjMzNS04LjE4Mi03LjE1Ni0xMS40NzUtMTUuMDA2LTExLjQ3NS04LjA2OCAwLTE5Ljc4MSA1LjM4Ny0xOS43ODEgMjIuMTgyIDAgMTQuMTQ0IDEwLjEwNSAyNC4yODggMjAuOSAyNC4yODggNi45MzkgMCAxMi42OTUtNC43NTggMTQuMDk5LTEyLjEwOWwxLjYwOS40MTktMS42MDkgMTAuMjE3Yy0yLjk0NiAxLjgyNS0xMC44NzEgMy43MTItMTUuNTAyIDMuNzEyLTE2LjQwMyAwLTI2Ljc3OS0xMC41NjgtMjYuNzc5LTI2LjMxNSAwLTE0LjM0NyAxMi44MjgtMjQuNjM4IDI2LjU3Mi0yNC42MzggNS42NzcgMCAxMS4xNDkgMS44MjUgMTYuNTQ2IDMuNzE2bC43IDEwLjAwM3ptMi41MjkgMzQuMzY2aDEuMzI2YzEuOTY5IDAgMy4zNzMgMCAzLjM3My0yLjMxNXYtMzguOTc0YzAtNC41NTMtMS4wNTItNC42OTMtMy43MTYtNS40NjF2LTEuMTJjMi44MDMtLjkwOSA1Ljc0OS0yLjE2OCA3LjIyMS0zLjAxMi43NjUtLjQxNiAxLjMzMS0uNzcyIDEuNTM3LS43NzIuNDI3IDAgLjU2OC40MjQuNTY4Ljk4NXY0OC4zNTRjMCAyLjMxNSAxLjU0MSAyLjMxNSAzLjUwNiAyLjMxNWgxLjE4NHYxLjgyYy0yLjM3NiAwLTQuODMyLS4xNC03LjM1Ni0uMTQtMi41MjMgMC01LjA0Ny4wNjgtNy42NDMuMTR2LTEuODJ6bTQ1LjAyMi0yLjAzM2MwIDEuMjY1Ljc2OSAxLjMzMSAxLjk1OSAxLjMzMWwyLjgwNi0uMDY3djEuNDczYy0zLjAxOS4yNzgtOC43NjcgMS43NDctMTAuMSAyLjE2N2wtLjM1My0uMjExdi01LjY2NmMtNC4yIDMuNDI4LTcuNDMxIDUuODc3LTEyLjQxNSA1Ljg3Ny0zLjc4MyAwLTcuNzEtMi40NDktNy43MS04LjMyNXYtMTcuOTJjMC0xLjgyLS4yNzgtMy41NzEtNC4yMDItMy45MTl2LTEuMzMxYzIuNTI4LS4wNjggOC4xMjgtLjQ4OCA5LjA0My0uNDg4Ljc3NyAwIC43NzcuNDg4Ljc3NyAyLjAyOHYxOC4wNTJjMCAyLjEwMyAwIDguMTIgNi4wOTYgOC4xMiAyLjM4NCAwIDUuNTM5LTEuODE5IDguNDgtNC4yNjN2LTE4LjgzM2MwLTEuMzk2LTMuMzYzLTIuMTYzLTUuODgyLTIuODY0di0xLjI2YzYuMzA0LS40MjMgMTAuMjM3LS45OCAxMC45MzQtLjk4LjU2NyAwIC41NjcuNDg4LjU2NyAxLjI2MXYyNS44MTh6bTEzLjk1Mi0yMy4wOTJjMi44MDEtMi4zOCA2LjU4OS01LjA0IDEwLjQ0OS01LjA0IDguMTM1IDAgMTMuMDM3IDcuMDc0IDEzLjAzNyAxNC42OTggMCA5LjE2Ny02LjcyOCAxOC4zMzgtMTYuNzYxIDE4LjMzOC01LjE4NCAwLTcuOTE4LTEuNjgyLTkuNzQ0LTIuNDQ5bC0yLjEwMSAxLjYwNi0xLjQ2Ny0uNzY3Yy42Mi00LjEyOS45NzktOC4xODkuOTc5LTEyLjQ1N3YtMzAuMDkxYzAtNC41NTMtMS4wNTctNC42OTMtMy43Mi01LjQ2MXYtMS4xMmMyLjgxMS0uOTA5IDUuNzQ5LTIuMTY4IDcuMjI0LTMuMDEyLjc3LS40MTYgMS4zMjctLjc3MiAxLjU0Ni0uNzcyLjQyIDAgLjU1OS40MjQuNTU5Ljk4NXYyNS41NDN6bTAgMTkuMDMzYzAgMi42NTkgMi41MjMgNy4xNDQgNy4yMjEgNy4xNDQgNy41MDQgMCAxMC42NTctNy4zNTIgMTAuNjU3LTEzLjU4MyAwLTcuNTU2LTUuNzQ0LTEzLjg1Mi0xMS4yMTYtMTMuODUyLTIuNjAxIDAtNC43NyAxLjY4MS02LjY2MyAzLjI5MXYxN3ptLTMwMi41MTggNDguNjAxaC41MzdjMS4zNzEgMCAyLjgyMS0uMTg1IDIuODIxLTIuMTY4di0xOS45NDhjMC0xLjk4Ni0xLjQ0OS0yLjE3NS0yLjgyMS0yLjE3NWgtLjUzN3YtMS4xNDNjMS40ODkgMCAzLjc3OC4xNSA1LjY1My4xNSAxLjkwNSAwIDQuMTkyLS4xNSA1Ljk4NS0uMTV2MS4xNDNoLS41MzZjLTEuMzY4IDAtMi44MjEuMTg5LTIuODIxIDIuMTc1djE5Ljk0OGMwIDEuOTgzIDEuNDUzIDIuMTY4IDIuODIxIDIuMTY4aC41MzZ2MS4xNDhjLTEuODMxIDAtNC4xMjUtLjE1LTYuMDI2LS4xNS0xLjg3MSAwLTQuMTI0LjE1LTUuNjEyLjE1di0xLjE0OHptMzcuODUxLTYuMzU5bC4wNzctLjA3NXYtMTQuMjc3YzAtMy4xMjQtMi4xNzctMy41ODEtMy4zMjEtMy41ODFoLS44NHYtMS4xNDNsNS4zNDIuMTUxIDQuNjk0LS4xNTF2MS4xNDNoLS41NjhjLTEuNjA3IDAtMy40LjMwNi0zLjQgNC44MzZ2MTcuMzNjMCAxLjMzMS4wMzkgMi42NjIuMjI2IDMuODQyaC0xLjQ0OWwtMTkuNjQ3LTIxLjg2djE1LjY5MmMwIDMuMzEzLjY0MyA0LjQ1MiAzLjU4NSA0LjQ1MmguNjQ3djEuMTQ4bC00LjkyLS4xNS01LjE4Ny4xNXYtMS4xNDhoLjUzNGMyLjYzMiAwIDMuNDMxLTEuNzg2IDMuNDMxLTQuODI5di0xNi4wMzljMC0yLjEyOS0xLjc1OS0zLjQyMy0zLjQ2OS0zLjQyM2gtLjQ5N3YtMS4xNDNsNC4zODMuMTUxIDMuMzk5LS4xNTEgMTYuOTgzIDE5LjA3NnptMTEuNjY2LTE3LjE3MmMtMi44NjYgMC0yLjk3NC42ODYtMy41NDcgMy40NjJoLTEuMTQzYy4xNDgtMS4wNjUuMzQyLTIuMTI5LjQ1OC0zLjIzNi4xNTUtMS4wNjguMjI4LTIuMTI5LjIyOC0zLjIzM2guOTE1Yy4zMDggMS4xNDQgMS4yNjEgMS4xMDQgMi4yOTYgMS4xMDRoMTkuNjgxYzEuMDMzIDAgMS45ODQtLjAzNyAyLjA2My0xLjE4MmwuOTEuMTUzYy0uMTQ4IDEuMDI5LS4zMDMgMi4wNTUtLjQxOSAzLjA4NS0uMDcgMS4wMjktLjA3IDIuMDU4LS4wNyAzLjA4M2wtMS4xNDcuNDIzYy0uMDc4LTEuNDA4LS4yNy0zLjY1OS0yLjgyNC0zLjY1OWgtNi4yNTd2MjAuMjU5YzAgMi45MzcgMS4zMzcgMy4yNzIgMy4xNjQgMy4yNzJoLjcyNXYxLjE0OGMtMS40ODcgMC00LjE1OS0uMTUtNi4yMTYtLjE1LTIuMjkgMC00Ljk2Mi4xNS02LjQ1Mi4xNXYtMS4xNDhoLjcyNWMyLjEwNiAwIDMuMTY4LS4xODcgMy4xNjgtMy4xOTJ2LTIwLjMzOWgtNi4yNTl6bTIzLjA4MiAyMy41MzFoLjUzNmMxLjM3MyAwIDIuODI0LS4xODUgMi44MjQtMi4xNjh2LTE5Ljk0OGMwLTEuOTg2LTEuNDUxLTIuMTc1LTIuODI0LTIuMTc1aC0uNTM2di0xLjE0M2MyLjMyMiAwIDYuMjk5LjE1IDkuNDk4LjE1IDMuMjEgMCA3LjE3Mi0uMTUgOS43NjUtLjE1LS4wNjUgMS42MzUtLjAyOCA0LjE1My4wODMgNS44MjdsLTEuMTUuMzA2Yy0uMTg0LTIuNDc0LS42NC00LjQ1NC00LjY1LTQuNDU0aC01LjI5OHY5Ljk3M2g0LjUzNGMyLjI4OCAwIDIuNzg4LTEuMjkxIDMuMDE0LTMuMzUyaDEuMTQyYy0uMDc1IDEuNDg5LS4xMTQgMi45NzMtLjExNCA0LjQ1NSAwIDEuNDUxLjAzOSAyLjg5Ni4xMTQgNC4zNDNsLTEuMTQyLjIyNmMtLjIyNi0yLjI4Mi0uMzQyLTMuNzY4LTIuOTc5LTMuNzY4aC00LjU3djguODcxYzAgMi40NzUgMi4yMDMgMi40NzUgNC42NDYgMi40NzUgNC41ODEgMCA2LjYwMS0uMzA3IDcuNzQ3LTQuNjQybDEuMDY3LjI2MmMtLjQ5OCAyLjAyNC0uOTU0IDQuMDM2LTEuMjkzIDYuMDU4LTIuNDQ2IDAtNi44MzQtLjE1LTEwLjI2My0uMTUtMy40MzggMC03Ljk3OC4xNS0xMC4xNTMuMTV2LTEuMTQ4em0yNi44NTktMjEuNGMwLTIuNzc4LTEuNTI2LTIuODkyLTIuNzEyLTIuODkyaC0uNjg3di0xLjE0NGMxLjIyMSAwIDMuNTg2LjE1MyA1LjkxNS4xNTMgMi4yODcgMCA0LjEyLS4xNTMgNi4xNDEtLjE1MyA0LjgwNCAwIDkuMDgzIDEuMjk0IDkuMDgzIDYuNzA2IDAgMy40MjEtMi4yODggNS41MTctNS4zMDMgNi43MDNsNi41MjIgOS43NDFjMS4wNyAxLjYxMSAxLjgyOSAyLjA2MyAzLjcwMiAyLjI4NHYxLjE0OWwtMy43MzktLjE1LTMuNTg0LjE1Yy0yLjkzOC0zLjg0Ni01LjQ2Mi03Ljk1OC03LjkzOS0xMi4zNDNoLTIuNTEzdjguMTUzYzAgMi45MzUgMS4zNyAzLjA0MSAzLjEyMiAzLjA0MWguNjg5djEuMTQ5bC02LjU2Mi0uMTVjLTEuODM0IDAtMy42MjcuMTUtNS41MzQuMTV2LTEuMTQ5aC42ODdjMS40MTcgMCAyLjcxMi0uNjM5IDIuNzEyLTIuMDV2LTE5LjM0OXptNC44ODYgOC44MzJoMS44NjNjMy44MTkgMCA1Ljg3NC0xLjQ0MyA1Ljg3NC01LjkzOSAwLTMuMzg0LTIuMTczLTUuNTU2LTUuNTcxLTUuNTU2LTEuMTQ3IDAtMS42MzUuMTE3LTIuMTY2LjE1MXYxMS4zNDR6bTQzLjY4IDYuMjA5bC4wNjgtLjA3NXYtMTQuMjc3YzAtMy4xMjQtMi4xNzEtMy41ODEtMy4zMTQtMy41ODFoLS44Mzd2LTEuMTQzbDUuMzQuMTUxIDQuNy0uMTUxdjEuMTQzaC0uNTc3Yy0xLjYwMSAwLTMuMzk2LjMwNi0zLjM5NiA0LjgzNnYxNy4zM2MwIDEuMzMxLjAzNiAyLjY2Mi4yMjUgMy44NDJoLTEuNDQ2bC0xOS42NDktMjEuODZ2MTUuNjkyYzAgMy4zMTMuNjQ3IDQuNDUyIDMuNTg0IDQuNDUyaC42NDh2MS4xNDhsLTQuOTE4LS4xNS01LjE5Mi4xNXYtMS4xNDhoLjUyOWMyLjYzNyAwIDMuNDM4LTEuNzg2IDMuNDM4LTQuODI5di0xNi4wMzljMC0yLjEyOS0xLjc1Ni0zLjQyMy0zLjQ3LTMuNDIzaC0uNDk3di0xLjE0M2w0LjM4OS4xNTEgMy4zOTQtLjE1MSAxNi45OCAxOS4wNzZ6bTEyLjA5MiAyLjA2MWMtLjM4OCAxLjI5Mi0uODQ3IDIuMjg5LS44NDcgMi45NjggMCAxLjE0NCAxLjYwNCAxLjMzIDIuODYgMS4zM2guNDI3djEuMTQ4Yy0xLjUyOS0uMDgzLTMuMDg4LS4xNDgtNC42MjUtLjE0OC0xLjM3MSAwLTIuNzM2LjA2NS00LjExNS4xNDh2LTEuMTQ4aC4yMzFjMS40ODcgMCAyLjc0OS0uODc0IDMuMzEzLTIuNDc3bDYuMTEyLTE3LjQ3MWMuNDktMS40MDkgMS4xODQtMy4zMTQgMS40MTQtNC43MjggMS4yMTUtLjQxMyAyLjc0NC0xLjE3NCAzLjQ2OS0xLjYzMi4xMTctLjA0MS4xODQtLjA4LjMwNS0uMDguMTE0IDAgLjE4NCAwIC4yNy4xMTcuMTE0LjMwNC4yMjEuNjQ3LjM0NS45NTFsNy4wMTkgMTkuOTE4Yy40NTYgMS4zMzEuOTEgMi43MzkgMS40MDQgMy44ODYuNDYzIDEuMDY2IDEuMjY0IDEuNTE2IDIuNTI0IDEuNTE2aC4yMjh2MS4xNDhjLTEuNzE3LS4wODMtMy40MzYtLjE0OC01LjI2NC0uMTQ4LTEuODY4IDAtMy43ODIuMDY1LTUuNzI4LjE0OHYtMS4xNDhoLjQyM2MuODczIDAgMi4zNjYtLjE0OCAyLjM2Ni0xLjEwMiAwLS40OTItLjM0NC0xLjUyLS43Ny0yLjc0MmwtMS40ODQtNC40MTZoLTguNjYxbC0xLjIxNyAzLjk2M3ptNS41NjgtMTYuODcyaC0uMDc4bC0zLjU0OSAxMC43NzhoNy4xM2wtMy41MDMtMTAuNzc4em0xNi40NDQtMi4zNjJjLTIuODYzIDAtMi45NzcuNjg2LTMuNTUgMy40NjJoLTEuMTQ3Yy4xNS0xLjA2NS4zNDQtMi4xMjkuNDYzLTMuMjM2LjE1MS0xLjA2OC4yMjMtMi4xMjkuMjIzLTMuMjMzaC45MmMuMzAzIDEuMTQ0IDEuMjYxIDEuMTA0IDIuMjg3IDEuMTA0aDE5LjY5MWMxLjAyNiAwIDEuOTc5LS4wMzcgMi4wNTctMS4xODJsLjkxNC4xNTNjLS4xNDcgMS4wMjktLjMgMi4wNTUtLjQxNCAzLjA4NS0uMDg1IDEuMDI5LS4wODUgMi4wNTgtLjA4NSAzLjA4M2wtMS4xNDIuNDIzYy0uMDctMS40MDgtLjI2Mi0zLjY1OS0yLjgxOS0zLjY1OWgtNi4yNTl2MjAuMjU5YzAgMi45MzcgMS4zMzkgMy4yNzIgMy4xNjYgMy4yNzJoLjcyNnYxLjE0OGMtMS40ODkgMC00LjE1OC0uMTUtNi4yMi0uMTUtMi4yODUgMC00Ljk2Mi4xNS02LjQ0OS4xNXYtMS4xNDhoLjcyNWMyLjEwMiAwIDMuMTY5LS4xODcgMy4xNjktMy4xOTJ2LTIwLjMzOWgtNi4yNTd6bTIzLjI2NCAyMy41MzFoLjUzNGMxLjM3OCAwIDIuODIxLS4xODUgMi44MjEtMi4xNjh2LTE5Ljk0OGMwLTEuOTg2LTEuNDQzLTIuMTc1LTIuODIxLTIuMTc1aC0uNTM0di0xLjE0M2MxLjQ5MiAwIDMuNzc3LjE1IDUuNjQzLjE1IDEuOTE0IDAgNC4yMDItLjE1IDUuOTk4LS4xNXYxLjE0M2gtLjUzNGMtMS4zNzYgMC0yLjgyOS4xODktMi44MjkgMi4xNzV2MTkuOTQ4YzAgMS45ODMgMS40NTMgMi4xNjggMi44MjkgMi4xNjhoLjUzNHYxLjE0OGMtMS44MzQgMC00LjEyNC0uMTUtNi4wMjktLjE1LTEuODczIDAtNC4xMi4xNS01LjYxMi4xNXYtMS4xNDh6bTI2Ljg1Ny0yNi4wMDNjOC4xMzUgMCAxNC42MTYgNS4wMjkgMTQuNjE2IDEzLjE0IDAgOC43NTktNi4yOTYgMTQuNTgxLTE0LjQxOSAxNC41ODEtOC4wOTYgMC0xNC4yNzMtNS40ODItMTQuMjczLTEzLjY3IDAtNy45MTkgNi4xNDEtMTQuMDUyIDE0LjA3Ni0xNC4wNTJ6bS41NzcgMjYuMDQ1YzcuNDA0IDAgOC42OTItNi41MTYgOC42OTItMTIuMDY3IDAtNS41NjktMy4wMDUtMTIuMzA2LTkuMzQyLTEyLjMwNi02LjY3OSAwLTguNjYxIDUuOTQyLTguNjYxIDExLjA0IDAgNi44MTcgMy4xMjggMTMuMzMzIDkuMzExIDEzLjMzM3ptMzkuNzUtNi40MDFsLjA3OC0uMDc1di0xNC4yNzdjMC0zLjEyNC0yLjE4MS0zLjU4MS0zLjMyNi0zLjU4MWgtLjgyN3YtMS4xNDNsNS4zMy4xNTEgNC43MDItLjE1MXYxLjE0M2gtLjU3N2MtMS41OTkgMC0zLjM5Ni4zMDYtMy4zOTYgNC44MzZ2MTcuMzNjMCAxLjMzMS4wMzYgMi42NjIuMjMgMy44NDJoLTEuNDQ4bC0xOS42NTQtMjEuODZ2MTUuNjkyYzAgMy4zMTMuNjUgNC40NTIgMy41ODQgNC40NTJoLjY1MXYxLjE0OGwtNC45Mi0uMTUtNS4xOTUuMTV2LTEuMTQ4aC41MzljMi42MzcgMCAzLjQzLTEuNzg2IDMuNDMtNC44Mjl2LTE2LjAzOWMwLTIuMTI5LTEuNzQ2LTMuNDIzLTMuNDctMy40MjNoLS40OTh2LTEuMTQzbDQuMzg5LjE1MSAzLjM5Ni0uMTUxIDE2Ljk4MSAxOS4wNzZ6bTEyLjA4NyAyLjA2MWMtLjM3NSAxLjI5Mi0uODM3IDIuMjg5LS44MzcgMi45NjggMCAxLjE0NCAxLjYwNyAxLjMzIDIuODU4IDEuMzNoLjQyN3YxLjE0OGMtMS41MjgtLjA4My0zLjA5NC0uMTQ4LTQuNjItLjE0OC0xLjM3OCAwLTIuNzQ2LjA2NS00LjExNC4xNDh2LTEuMTQ4aC4yMTdjMS40OTMgMCAyLjc1Ny0uODc0IDMuMzIxLTIuNDc3bDYuMTE0LTE3LjQ3MWMuNDkzLTEuNDA5IDEuMTg0LTMuMzE0IDEuNDA1LTQuNzI4IDEuMjIzLS40MTMgMi43NDctMS4xNzQgMy40NzktMS42MzIuMTA5LS4wNDEuMTg2LS4wOC4zMDEtLjA4LjExNCAwIC4xODcgMCAuMjY0LjExN2wuMzQ5Ljk1MSA3LjAxNiAxOS45MThjLjQ1OCAxLjMzMS45MTQgMi43MzkgMS40MTUgMy44ODYuNDU4IDEuMDY2IDEuMjYyIDEuNTE2IDIuNTE4IDEuNTE2aC4yMzN2MS4xNDhjLTEuNzItLjA4My0zLjQzOC0uMTQ4LTUuMjcyLS4xNDgtMS44NjUgMC0zLjc3Ny4wNjUtNS43MjMuMTQ4di0xLjE0OGguNDIyYy44NzEgMCAyLjM3LS4xNDggMi4zNy0xLjEwMiAwLS40OTItLjM0Ny0xLjUyLS43NzItMi43NDJsLTEuNDgyLTQuNDE2aC04LjY2MmwtMS4yMjUgMy45NjN6bTUuNTc2LTE2Ljg3MmgtLjA3N2wtMy41NTQgMTAuNzc4aDcuMTQxbC0zLjUxMS0xMC43Nzh6bTI0LjM4MSAxOC41ODNjMCAxLjUyNiAxLjA2IDEuOTggMi4yODIgMi4xMzYgMS41NjUuMTE1IDMuMjgyLjExNSA1LjA0Mi0uMDc4IDEuNTk5LS4xOTIgMi45NzItMS4xMDQgMy42NTgtMi4wNTguNjA5LS44MzkuOTUxLTEuOTAzIDEuMTg0LTIuNzQyaDEuMTA2Yy0uNDE5IDIuMTctLjk1NCA0LjMwNy0xLjQxNSA2LjQ3NmwtMTAuMDY2LS4xNDgtMTAuMDc0LjE0OHYtMS4xNDhoLjUyNmMxLjM3OSAwIDIuODcxLS4xODUgMi44NzEtMi41NDl2LTE5LjU3YzAtMS45ODUtMS40OTItMi4xNzMtMi44NzEtMi4xNzNoLS41MjZ2LTEuMTQzbDYuMDI0LjE1MSA1LjgwNC0uMTUxdjEuMTQzaC0uOTU5Yy0xLjQ0NiAwLTIuNTg1LjA0Mi0yLjU4NSAyLjA1NnYxOS42NDl6IiBmaWxsPSIjMUExOTE4Ii8+PC9zdmc+");
        }
        .rccs__card--discover > div {
          color: #555;
        }
        .rccs__card--discover .rccs__card__background {
          background: linear-gradient(25deg, #fff, #eee);
        }
        .rccs__card--discover .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9Ijg2IiB2aWV3Qm94PSIwIDAgNTEyIDg2IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCI+PGRlZnM+PGxpbmVhckdyYWRpZW50IHgxPSIyMC40NDIlIiB5MT0iMTAuNTk5JSIgeDI9Ijg5LjI0NSUiIHkyPSI4My41MyUiIGlkPSJhIj48c3RvcCBzdG9wLWNvbG9yPSIjRTI1NDI5IiBvZmZzZXQ9IjAlIi8+PHN0b3Agc3RvcC1jb2xvcj0iI0Y5OUQzRSIgb2Zmc2V0PSIxMDAlIi8+PC9saW5lYXJHcmFkaWVudD48cGF0aCBkPSJNMjcwLjM1Ni4zNjVjLTIzLjk4MiAwLTQzLjQ0IDE4LjczNS00My40NCA0MS44NTggMCAyNC41ODMgMTguNjEyIDQyLjk2IDQzLjQ0IDQyLjk2IDI0LjIwOCAwIDQzLjMyMS0xOC42MiA0My4zMjEtNDIuNDc4IDAtMjMuNzE2LTE4Ljk4Ni00Mi4zNC00My4zMjEtNDIuMzR6IiBpZD0iYiIvPjxmaWx0ZXIgeD0iLTUwJSIgeT0iLTUwJSIgd2lkdGg9IjIwMCUiIGhlaWdodD0iMjAwJSIgZmlsdGVyVW5pdHM9Im9iamVjdEJvdW5kaW5nQm94IiBpZD0iYyI+PGZlTW9ycGhvbG9neSByYWRpdXM9IjIiIGluPSJTb3VyY2VBbHBoYSIgcmVzdWx0PSJzaGFkb3dTcHJlYWRJbm5lcjEiLz48ZmVHYXVzc2lhbkJsdXIgc3RkRGV2aWF0aW9uPSIyLjUiIGluPSJzaGFkb3dTcHJlYWRJbm5lcjEiIHJlc3VsdD0ic2hhZG93Qmx1cklubmVyMSIvPjxmZU9mZnNldCBkeD0iMiIgZHk9IjIiIGluPSJzaGFkb3dCbHVySW5uZXIxIiByZXN1bHQ9InNoYWRvd09mZnNldElubmVyMSIvPjxmZUNvbXBvc2l0ZSBpbj0ic2hhZG93T2Zmc2V0SW5uZXIxIiBpbjI9IlNvdXJjZUFscGhhIiBvcGVyYXRvcj0iYXJpdGhtZXRpYyIgazI9Ii0xIiBrMz0iMSIgcmVzdWx0PSJzaGFkb3dJbm5lcklubmVyMSIvPjxmZUNvbG9yTWF0cml4IHZhbHVlcz0iMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMC4zMDE2NTg3NDEgMCIgaW49InNoYWRvd0lubmVySW5uZXIxIi8+PC9maWx0ZXI+PC9kZWZzPjx1c2UgZmlsbD0idXJsKCNhKSIgZmlsbC1ydWxlPSJldmVub2RkIiB4bGluazpocmVmPSIjYiIvPjx1c2UgZmlsdGVyPSJ1cmwoI2MpIiB4bGluazpocmVmPSIjYiIvPjxwYXRoIGQ9Ik0yMy43NDYgMS44OTFoLTIzLjM1M3Y4MS40NTRoMjMuMjMyYzEyLjMyNSAwIDIxLjI0LTIuOTIxIDI5LjA1OS05LjM5OCA5LjI3OC03LjY5NSAxNC43ODEtMTkuMjk4IDE0Ljc4MS0zMS4yODkgMC0yNC4wNDgtMTcuOTY1LTQwLjc2Ni00My43MTktNDAuNzY2em0xOC41NzMgNjEuMTc2Yy01LjAyMiA0LjUzMS0xMS40ODYgNi40ODgtMjEuNzYgNi40ODhoLTQuMjY4di01My44NzNoNC4yNjhjMTAuMjc0IDAgMTYuNDkxIDEuODM0IDIxLjc2IDYuNTkzIDUuNDk1IDQuODg2IDguNzcyIDEyLjQ1MiA4Ljc3MiAyMC4yNjUgMCA3LjgyOS0zLjI3NyAxNS42Ni04Ljc3MiAyMC41Mjd6bTMyLjQ4IDIwLjI3OGgxNS44NzF2LTgxLjQ1NGgtMTUuODcxdjgxLjQ1NHptNTQuNzI3LTUwLjIwOWMtOS41MzktMy41MzQtMTIuMzQ2LTUuODY1LTEyLjM0Ni0xMC4yNDcgMC01LjEzNCA0Ljk5OC05LjAzOSAxMS44NDktOS4wMzkgNC43NjMgMCA4LjY3MSAxLjk1MyAxMi44MzYgNi41OGw4LjI5NC0xMC44NTJjLTYuODM5LTUuOTk4LTE1LjAyMS05LjA0Ny0yMy45NDYtOS4wNDctMTQuMzk4IDAtMjUuMzk5IDEwLjAyLTI1LjM5OSAyMy4zMiAwIDExLjI0NyA1LjEyNiAxNi45ODEgMjAuMDMxIDIyLjM2OSA2LjIzMyAyLjE4OCA5LjQwMSAzLjY0NiAxMC45OTMgNC42NDMgMy4xNzUgMi4wNzcgNC43NjkgNC45OTggNC43NjkgOC40MTYgMCA2LjYwNS01LjI1NyAxMS40ODMtMTIuMzUxIDExLjQ4My03LjU3NCAwLTEzLjY3NC0zLjc4Mi0xNy4zNDEtMTAuODY1bC0xMC4yNDcgOS45MDVjNy4zMTMgMTAuNzMzIDE2LjEwOSAxNS41MTEgMjguMjE0IDE1LjUxMSAxNi40ODggMCAyOC4wODQtMTEuMDA3IDI4LjA4NC0yNi43NTggMC0xMi45NDgtNS4zNjEtMTguODE1LTIzLjQ0My0yNS40MTl6bTI4LjQ0OSA5LjUyMWMwIDIzLjk2NSAxOC44MTUgNDIuNTI1IDQzLjAwNiA0Mi41MjUgNi44MzkgMCAxMi43MDEtMS4zNTIgMTkuOTE1LTQuNzU4di0xOC42OTdjLTYuMzYxIDYuMzU4LTExLjk4IDguOTE2LTE5LjE4OSA4LjkxNi0xNS45OTcgMC0yNy4zNjQtMTEuNjA2LTI3LjM2NC0yOC4xMDIgMC0xNS42MjYgMTEuNzIxLTI3Ljk2NSAyNi42MzgtMjcuOTY1IDcuNTYxIDAgMTMuMzExIDIuNjg2IDE5LjkxNSA5LjE1OXYtMTguNjk2Yy02Ljk2Mi0zLjUzMS0xMi43MTItNC45ODUtMTkuNTUyLTQuOTg1LTI0LjA2NyAwLTQzLjM2OSAxOC45MzUtNDMuMzY5IDQyLjYwNHptMTkxLjY1MSAxMy45NDhsLTIxLjc0My01NC43MTVoLTE3LjM0NmwzNC41NzkgODMuNTM0aDguNTQzbDM1LjE4My04My41MzRoLTE3LjIxOGwtMjIgNTQuNzE1em00Ni40MzkgMjYuNzM5aDQ1LjA2NnYtMTMuNzg5aC0yOS4xODh2LTIyLjAwMWgyOC4wNzJ2LTEzLjc5MmgtMjguMDcydi0xOC4wNzloMjkuMTg4di0xMy43OTJoLTQ1LjA2NnY4MS40NTR6bTEwNy45NTUtNTcuNDE1YzAtMTUuMjU5LTEwLjQ5LTI0LjAzOS0yOC44MjMtMjQuMDM5aC0yMy41Nzd2ODEuNDU0aDE1Ljg5NXYtMzIuNzM3aDIuMDhsMjEuOTc1IDMyLjczN2gxOS41NDRsLTI1LjY2Ny0zNC4zMTFjMTEuOTg4LTIuNDUxIDE4LjU3My0xMC42MzggMTguNTczLTIzLjEwNHptLTMxLjg4MiAxMy40NTJoLTQuNjIzdi0yNC42ODNoNC44NzdjOS45MTYgMCAxNS4yODcgNC4xNjUgMTUuMjg3IDEyLjA5MiAwIDguMTc4LTUuMzcyIDEyLjU5LTE1LjU0MSAxMi41OXoiIGZpbGw9IiMwQjEwMTUiLz48L3N2Zz4=");
        }
        .rccs__card--elo .rccs__card__background {
          background: linear-gradient(25deg, #211c18, #aaa7a2);
        }
        .rccs__card--elo .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbC1ydWxlPSJldmVub2RkIj48cGF0aCBkPSJNMjU2IDBjMTQxLjM4NSAwIDI1NiAxMTQuNjE1IDI1NiAyNTYgMCAxNDEuMzg2LTExNC42MTUgMjU2LTI1NiAyNTZzLTI1Ni0xMTQuNjE0LTI1Ni0yNTZjMC0xNDEuMzg1IDExNC42MTUtMjU2IDI1Ni0yNTYiIGZpbGw9IiMwRTBFMTEiLz48cGF0aCBkPSJNMTgwLjA0MiAyMzcuNTgzbC03OC41MjQgMzMuODM3Yy0uMTIyLTEuMzUzLS4xODktMi43MjEtLjE4OS00LjEwNiAwLTI0LjgzOSAyMC4xMzUtNDQuOTc0IDQ0Ljk3NC00NC45NzQgMTMuNDM4IDAgMjUuNDk5IDUuODk4IDMzLjczOSAxNS4yNDN6bS0zMy43MzktNDguODc2YzM3LjA1MyAwIDY4LjExMiAyNS42MzggNzYuNDIgNjAuMTQzbC0zMS42ODIgMTMuODUzLS4wMDctLjA2Ni0zMi40MTMgMTQuMjQxLTc3Ljc1NSAzMy45OTdjLTguMzE4LTEyLjQ3LTEzLjE3LTI3LjQ0OC0xMy4xNy00My41NjEgMC00My40MTQgMzUuMTkzLTc4LjYwNyA3OC42MDctNzguNjA3em01NC45MjggMTM0LjgzOGMtMjguMTQ1IDI2LjcxMy02NS4zNzkgMjkuMzM1LTk4LjA5NiA5LjQ3M2wxOC40ODUtMjguMTA0YzE4LjYxNyAxMS4xMjggMzcuMzE5IDkuMzIzIDU2LjEwNy01LjQyOGwyMy41MDQgMjQuMDZ6bTMyLjAwNy0xOS40NTdsLS4xOTgtMTQ4LjY1MmgyOC4xNjN2MTQ0LjYzOGMwIDEuMzg4LjE3MiAyLjYxIDEuOTkyIDMuMzE3bDI0LjUgOS41MjgtMTEuMDM4IDI4LjctMjguNy0xMi4xNDNjLTEwLjg4My00LjYwNS0xNC43LTExLjI3Ny0xNC43MTktMjUuMzg5eiIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik0zMzkuMjggMzAxLjU4N2MtMTAuNTU3LTguMjA4LTE3LjM1NC0yMS4wMTgtMTcuMzU0LTM1LjQyNiAwLTEyLjgwOSA1LjM3Ny0yNC4zNTQgMTMuOTg1LTMyLjUyOWwtMTguMTktMjkuNDhjLTE4LjI4NSAxNC40ODctMzAuMDI4IDM2Ljg3LTMwLjAyOCA2Mi4wMDkgMCAyNy4wOTYgMTMuNjMyIDUxLjAwMiAzNC40MDcgNjUuMjU0bDE3LjE3OS0yOS44MjgiIGZpbGw9IiMyMDkxQzMiLz48cGF0aCBkPSJNMzUxLjYxIDIyMy45MzhjNC43NC0xLjcwNSA5Ljg1LTIuNjM5IDE1LjE3OC0yLjYzOSAxOS4yMDggMCAzNS41ODggMTIuMDc3IDQxLjk4NSAyOS4wNDhsMzQuODU2LTIuOTEyYy04LjQxNC0zNC42NC0zOS42MTMtNjAuMzY2LTc2Ljg0MS02MC4zNjYtMTAuNTE5IDAtMjAuNTQ5IDIuMDcxLTI5LjczIDUuNzk5bDE0LjU1MiAzMS4wNzEiIGZpbGw9IiNGQUVDMzIiLz48cGF0aCBkPSJNNDExLjU5NSAyNjguMzM1Yy0xLjEzNSAyMy43NjYtMjAuNzYgNDIuNjg4LTQ0LjgwNyA0Mi42ODgtMy43NyAwLTcuNDI4LS40NzItMTAuOTI1LTEuMzQ3bC0xMy42NzMgMzEuNjU3YzcuNzQ1IDIuNTMxIDE2LjAwNyAzLjkyIDI0LjU5OCAzLjkyIDQyLjU5MSAwIDc3LjMwMi0zMy42NyA3OS4wMDktNzUuODQybC0zNC4yMDMtMS4wNzciIGZpbGw9IiNEMDM1MkEiLz48L2c+PC9zdmc+");
        }
        .rccs__card--hipercard .rccs__card__background {
          background: linear-gradient(25deg, #8b181b, #de1f27);
        }
        .rccs__card--hipercard .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjEyMyIgdmlld0JveD0iMCAwIDUxMiAxMjMiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxwYXRoIGQ9Ik0zNzQuMTE4IDgwLjg0MmMtNi45NDMgNi43OTctMjYuNDM0IDguNzI4LTI0LjQ0LTcuNTIgMS42NTYtMTMuNDk1IDE2LjM0OC0xNi4zNjMgMzIuMjczLTE0LjQxNC0xLjE4NCA3LjM4MS0yLjU0MiAxNi43NTUtNy44MzMgMjEuOTM0em0tMjEuOTM0LTUyLjY0MWMtLjY2MyAzLjcyMy0xLjY4MSA3LjA5Mi0yLjUwNiAxMC42NTMgNy45NTEtMS45OTMgMzIuNzA5LTguMTA1IDM1LjA5NSAyLjUwNi43OTIgMy41MjYtLjU3NCA3LjI3Mi0xLjU2NyAxMC4wMjgtMjIuMzg5LTIuMTIxLTQwLjYzNiAxLjU5OS00NS40MzUgMTcuNTQ3LTMuMjE0IDEwLjY4Mi4zNiAyMS4xOTQgNy4yMDcgMjQuMTI5IDEzLjE4NyA1LjY0OSAyOS4yMjgtLjgyMiAzNC43ODEtOS43MTQtLjU3NyAzLjA3OC0xLjEyMyA2LjE4Ny0uOTQgMTAuMDI2aDExLjU5NGMuMTIzLTExLjEwMyAxLjczOS0yMC4wOTEgMy40NDctMzAuMDgyIDEuNDU2LTguNTA4IDQuMTk1LTE2LjkzMSAzLjc1OS0yNC40NC0uOTk1LTE3LjE5Ny0yOS40ODYtMTEuMTE0LTQ1LjQzNC0xMC42NTN6bTExNi4yNDkgNTcuOTY4Yy05LjA3LjE5NS0xMy41OC01LjQxOS0xMy43ODctMTQuNzI2LS4zNjEtMTYuMzAxIDYuNzg5LTM0LjQxMSAyMS4zMDctMzYuMDM1IDYuNzYtLjc1NiAxMS42NTQuODE2IDE2LjYwNyAyLjUwOC00LjU0NyAxOC4zMS0yLjkwNSA0Ny43OTktMjQuMTI4IDQ4LjI1NHptMzAuMDgxLTg2LjE2OWMtMS4xNTUgMTAuMTI1LTIuNjk5IDE5Ljg2Mi00LjY5OSAyOS4xNC0zMy4wNTEtMTAuNDYxLTUzLjMyIDEzLjg1NS01Mi45NTYgNDMuODY4LjA3MiA1LjgwNSAxLjA3MSAxMS41NjEgNC43MDEgMTUuNjY3IDYuMjU2IDcuMDc2IDI0LjE3IDguNzY4IDMzLjIxNCAyLjgyMSAxLjc1Mi0xLjE1MSAzLjU0LTMuMjQ2IDQuNzAxLTQuNy44NzEtMS4wOTUgMi4yNTYtMy45NTkgMi41MDUtMy4xMzUtLjQ3NCAzLjE4Mi0xLjE4NSA2LjEyNy0xLjI1MiA5LjcxNWgxMi4yMmMyLjM1NC0zMy43ODQgOS42MzctNjIuNjQxIDE1LjA0LTkzLjM3NmgtMTMuNDc1em0tMzQ0Ljk4OSA4MS4xNTZjLTcuMjA5IDcuNjM1LTI0LjkzMSA3LjUxOC0yNi4zMi01LjMyOC0uNjA2LTUuNTg5IDEuNDc3LTExLjQ1IDIuNTA2LTE3LjIzMyAxLjA0MS01Ljg1MyAxLjc5MS0xMS40NjkgMi44MTktMTYuNjA5IDcuMS04LjY3IDI3Ljk2NS05LjcxNiAzMC4wODIgNC43MDEgMS44MzcgMTIuNTE3LTMuMTE3IDI4LjE0NS05LjA4NyAzNC40Njh6bTEwLjY1NC01Mi45NTVjLTExLjQwNC00LjI4NC0yNS4zMDMuODMtMzEuMzI4IDUuNjk0LjAyMS4yMTQtLjE0NC4yNDMtLjMyMS4yNTlsLjMyMS0uMjU5LS4wMDctLjA1NC45NC01LjY0aC0xMS41OTRjLTQuODMxIDMyLjE0My0xMC41NjEgNjMuMzg3LTE2LjYwNyA5NC4zMTZoMTMuNDczYzEuOTUtMTIuMDQ2IDMuMjM5LTI0Ljc1MyA1Ljk1NC0zNi4wMzQgMy4wNzcgMTEuODY1IDIzLjE2OSA5LjU5NyAzMS42NDcgNS4wMTQgMTcuNDk0LTkuNDYgMzAuOTg3LTU0LjQ4MSA3LjUyMS02My4yOTZ6bTYzLjkyMSAyMi41NjFoLTMxLjY0N2MxLjAwMS03LjI3NiA3LjU0Ny0xNS4yNzMgMTcuODU5LTE1LjY2NyA5LjcwNy0uMzcxIDE2LjY1NiAzLjU2NCAxMy43ODcgMTUuNjY3em0tMTIuODQ3LTIzLjgxNGMtOS43MzYuNzQ5LTE3Ljk2NSAzLjU1MS0yMy44MTQgOS43MTMtNy4xNzMgNy41NTgtMTIuOTgzIDI0LjI2Ni0xMS4yODEgMzkuNDgyIDIuNDI4IDIxLjcwOCAyOS40NTcgMjAuOTI5IDUxLjA3NSAxNS42NjcuMzY3LTMuODA5IDEuMjktNy4wNjYgMS44OC0xMC42NTMtOC45MDYgMy4zMzItMjQuMzY4IDcuOTg0LTMzLjUyOCAyLjE5NC02LjkxNS00LjM3My02Ljk1Ni0xNS40NDYtNC43LTI1LjA2OCAxNC41MjktLjQ2MiAyOS42MzEtLjM3NCA0NC4xODEgMCAuOTIzLTYuODIxIDMuNTY1LTE0LjI1NyAxLjI1My0yMC45OTMtMy4wNDktOC44OTQtMTMuOTYxLTExLjE5Ni0yNS4wNjctMTAuMzR6bS0xMTguNDQzIDEuMjUzYy0uMzY1LjA1Mi0uMzQxLjQ5NS0uMzE0LjkzOS0yLjk2OSAyMi4yMDMtNi45ODYgNDMuMzU4LTExLjI4IDY0LjIzNmgxMy40NzNjMy4yMzgtMjIuMzUyIDYuOTQ2LTQ0LjIzMyAxMS41OTQtNjUuMTc1aC0xMy40NzN6bTM1MS41Ny4zMTNjLTExLjkwOS01Ljk1My0yMS44MjIgNC4wMzctMjUuNjkzIDEwLjAyNyAxLjEwMi0zLjA3MyAxLjE2Ni03LjE4OCAyLjE5Mi0xMC4zMzloLTExLjkwN2MtMy4yMSAyMi4zNzktNy4wODEgNDQuMDk4LTExLjU5NCA2NS4xNzVoMTMuNzg3Yy4wODgtOC42MzggMS43ODQtMTUuMDI4IDMuMTMzLTIzLjUwMSAyLjg4Mi0xOC4wODggNy4xMTEtMzcuOTI2IDI4LjIwMi0zMS45Ni43LTMuMDYuOTktNi41MyAxLjg4LTkuNDAxem0tMTUxLjM0NSA0OS41MDhjLTEuMjQxLTMuMjA4LTEuNTYzLTguNTA3LTEuMjUzLTEyLjUzNC42OTctOS4wNTQgMy45OTYtMjAuMDc3IDkuMDg3LTI1LjA2NyA3LjAyNi02Ljg4OCAyMC44OTctNS43NDggMzEuOTYtMS44ODEuMzQzLTMuNzI5IDEuMDkzLTcuMDU0IDEuNTY3LTEwLjY1Mi0xOC4xNDUtMi45NjQtMzUuMzY1LTEuMTIxLTQ0LjQ5NCA4LjQ1OS04LjkzNiA5LjM3OC0xNC43OTYgMzAuOTMyLTEwLjY1NCA0NC40OTQgNC44NDggMTUuODcgMjYuNTgzIDE2LjczMyA0NC4xODMgMTAuNjU0Ljc3Ny0zLjE5IDEuMTktNi43NDYgMS44OC0xMC4wMjgtOS42MTYgNC45OTctMjguMDAxIDcuNTkzLTMyLjI3Ni0zLjQ0N3ptLTcuNTE5LTQ5LjgyMWMtMTEuOTQ4LTQuODI1LTIxLjMzOSAzLjMyOS0yNS42OTMgMTAuOTY3Ljk4Ny0zLjM5OCAxLjM5NC03LjM3OCAyLjE5Mi0xMC45NjdoLTExLjkwN2MtMi45MSAyMi41NzUtNy4yMDEgNDMuNzY5LTExLjI4IDY1LjE3NWgxMy40NzVjMS44ODgtMTIuNzA3IDIuNzAzLTI5LjgzNCA2Ljg5Mi00MS45ODggMy4zNDgtOS43MTMgMTIuMTExLTE3Ljk4NyAyNC43NTQtMTMuNDczLjE3OC0zLjU4MyAxLjIwMy02LjMxOCAxLjU2Ny05LjcxNHptLTIxNy4xNDYtMjYuMzIxYy0xLjkyNSAxMi40ODktNC4wOTMgMjQuNzM1LTYuMjY3IDM2Ljk3NC0xMy45NTkuMTQ3LTI4LjIxMS42OS00MS42NzQtLjMxNCAyLjU0My0xMS45NzMgNC4zNjctMjQuNjY4IDYuODkyLTM2LjY2aC0xNS4wMzljLTUuMzg1IDMwLjU0Ny0xMC4yODIgNjEuNTc5LTE2LjI5NCA5MS40OTdoMTUuMzUzYzIuNDA5LTE1LjM0OCA0LjY2MS0zMC44NTIgNy44MzMtNDUuNDM1IDEzLjA5MS0uMzIyIDI4Ljc4MS0uODg1IDQxLjM2Mi4zMTQtMi41OTQgMTUuMTYyLTUuNzI4IDI5Ljc4My04LjE0NyA0NS4xMjFoMTUuMzU0YzQuOTMyLTMwLjk5NyAxMC4xMzEtNjEuNzI4IDE2LjI5NC05MS40OTdoLTE1LjY2N3ptMzguMjI3IDEzLjc4N2MyLjY4Ni0xLjg0OSA2LjE0NS0xMC4yNzggMi4xOTQtMTMuNzg3LTEuMjUyLTEuMTExLTMuMzQ3LTEuNDMzLTYuMjY3LS45MzktMi43MS40NTctNC4yNjggMS4zNzctNS4zMjcgMi44MTktMS43MDMgMi4zMTktMy4yNjMgOS4zMS0uNjI4IDExLjkwNyAyLjU2NyAyLjUyOSA4LjMxOCAxLjE3NyAxMC4wMjggMHoiIGZpbGw9IiNmZmYiLz48L3N2Zz4=");
        }
        .rccs__card--jcb .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjM5NSIgdmlld0JveD0iMCAwIDUxMiAzOTUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxkZWZzPjxsaW5lYXJHcmFkaWVudCB4MT0iLTU3LjUyNyUiIHkxPSI1MC4xMjQlIiB4Mj0iMjMyLjM5MSUiIHkyPSI1MC4xMjQlIiBpZD0iYSI+PHN0b3Agc3RvcC1jb2xvcj0iIzAwNzk0MCIgb2Zmc2V0PSIwJSIvPjxzdG9wIHN0b3AtY29sb3I9IiMwMDg3M0YiIG9mZnNldD0iMjIuODUlIi8+PHN0b3Agc3RvcC1jb2xvcj0iIzQwQTczNyIgb2Zmc2V0PSI3NC4zMyUiLz48c3RvcCBzdG9wLWNvbG9yPSIjNUNCNTMxIiBvZmZzZXQ9IjEwMCUiLz48L2xpbmVhckdyYWRpZW50PjxsaW5lYXJHcmFkaWVudCB4MT0iLjE4MyUiIHkxPSI0OS45NiUiIHgyPSIxMDAuMjczJSIgeTI9IjQ5Ljk2JSIgaWQ9ImIiPjxzdG9wIHN0b3AtY29sb3I9IiMwMDc5NDAiIG9mZnNldD0iMCUiLz48c3RvcCBzdG9wLWNvbG9yPSIjMDA4NzNGIiBvZmZzZXQ9IjIyLjg1JSIvPjxzdG9wIHN0b3AtY29sb3I9IiM0MEE3MzciIG9mZnNldD0iNzQuMzMlIi8+PHN0b3Agc3RvcC1jb2xvcj0iIzVDQjUzMSIgb2Zmc2V0PSIxMDAlIi8+PC9saW5lYXJHcmFkaWVudD48bGluZWFyR3JhZGllbnQgeDE9Ii02Mi44MDIlIiB5MT0iNDkuODU4JSIgeDI9IjI1My42NzElIiB5Mj0iNDkuODU4JSIgaWQ9ImMiPjxzdG9wIHN0b3AtY29sb3I9IiMwMDc5NDAiIG9mZnNldD0iMCUiLz48c3RvcCBzdG9wLWNvbG9yPSIjMDA4NzNGIiBvZmZzZXQ9IjIyLjg1JSIvPjxzdG9wIHN0b3AtY29sb3I9IiM0MEE3MzciIG9mZnNldD0iNzQuMzMlIi8+PHN0b3Agc3RvcC1jb2xvcj0iIzVDQjUzMSIgb2Zmc2V0PSIxMDAlIi8+PC9saW5lYXJHcmFkaWVudD48bGluZWFyR3JhZGllbnQgeDE9Ii4xNzYlIiB5MT0iNTAuMDA2JSIgeDI9IjEwMS44MDglIiB5Mj0iNTAuMDA2JSIgaWQ9ImQiPjxzdG9wIHN0b3AtY29sb3I9IiMxRjI4NkYiIG9mZnNldD0iMCUiLz48c3RvcCBzdG9wLWNvbG9yPSIjMDA0RTk0IiBvZmZzZXQ9IjQ3LjUxJSIvPjxzdG9wIHN0b3AtY29sb3I9IiMwMDY2QjEiIG9mZnNldD0iODIuNjElIi8+PHN0b3Agc3RvcC1jb2xvcj0iIzAwNkZCQyIgb2Zmc2V0PSIxMDAlIi8+PC9saW5lYXJHcmFkaWVudD48bGluZWFyR3JhZGllbnQgeDE9Ii0uNTc2JSIgeTE9IjQ5LjkxNCUiIHgyPSI5OC4xMzMlIiB5Mj0iNDkuOTE0JSIgaWQ9ImUiPjxzdG9wIHN0b3AtY29sb3I9IiM2QzJDMkYiIG9mZnNldD0iMCUiLz48c3RvcCBzdG9wLWNvbG9yPSIjODgyNzMwIiBvZmZzZXQ9IjE3LjM1JSIvPjxzdG9wIHN0b3AtY29sb3I9IiNCRTE4MzMiIG9mZnNldD0iNTcuMzElIi8+PHN0b3Agc3RvcC1jb2xvcj0iI0RDMDQzNiIgb2Zmc2V0PSI4NS44NSUiLz48c3RvcCBzdG9wLWNvbG9yPSIjRTYwMDM5IiBvZmZzZXQ9IjEwMCUiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cGF0aCBkPSJNNTEyIDMxNC44MzZjMCA0My44MTQtMzUuNjc3IDc5LjQ5MS03OS40OTEgNzkuNDkxaC00MzIuNTA5di0zMTQuODM2YzAtNDMuODE0IDM1LjY3Ny03OS40OTEgNzkuNDkxLTc5LjQ5MWg0MzIuNTA5djMxNC44MzZ6IiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTM3MS4xNjkgMjM0LjA5M2gzMi44NjFsNC4wNjgtLjMxM2M2LjI1OS0xLjI1MiAxMS41NzktNi44ODUgMTEuNTc5LTE0LjcwOSAwLTcuNTExLTUuMzItMTMuMTQ0LTExLjU3OS0xNC43MDktLjkzOS0uMzEzLTIuODE3LS4zMTMtNC4wNjgtLjMxM2gtMzIuODYxdjMwLjA0NHoiIGZpbGw9InVybCgjYSkiLz48cGF0aCBkPSJNNDAwLjI3NCAyNi42MDFjLTMxLjI5NiAwLTU2Ljk1OCAyNS4zNS01Ni45NTggNTYuOTU4djU5LjE0OWg4MC40M2MxLjg3OCAwIDQuMDY4IDAgNS42MzMuMzEzIDE4LjE1Mi45MzkgMzEuNjA5IDEwLjMyOCAzMS42MDkgMjYuNjAxIDAgMTIuODMxLTkuMDc2IDIzLjc4NS0yNS45NzYgMjUuOTc2di42MjZjMTguNDY1IDEuMjUyIDMyLjU0OCAxMS41NzkgMzIuNTQ4IDI3LjU0IDAgMTcuMjEzLTE1LjY0OCAyOC40NzktMzYuMzAzIDI4LjQ3OWgtODguMjU0djExNS43OTVoODMuNTZjMzEuMjk2IDAgNTYuOTU4LTI1LjM1IDU2Ljk1OC01Ni45NTh2LTI4NC40NzloLTgzLjI0N3oiIGZpbGw9InVybCgjYikiLz48cGF0aCBkPSJNNDE1LjYwOSAxNzMuMzc5YzAtNy41MTEtNS4zMi0xMi41MTgtMTEuNTc5LTEzLjQ1Ny0uNjI2IDAtMi4xOTEtLjMxMy0zLjEzLS4zMTNoLTI5LjczMXYyNy41NGgyOS43MzFjLjkzOSAwIDIuODE3IDAgMy4xMy0uMzEzIDYuMjU5LS45MzkgMTEuNTc5LTUuOTQ2IDExLjU3OS0xMy40NTd6IiBmaWxsPSJ1cmwoI2MpIi8+PHBhdGggZD0iTTg1LjQzOCAyNi42MDFjLTMxLjI5NiAwLTU2Ljk1OCAyNS4zNS01Ni45NTggNTYuOTU4djE0MC41MThjMTUuOTYxIDcuODI0IDMyLjU0OCAxMi44MzEgNDkuMTM0IDEyLjgzMSAxOS43MTYgMCAzMC4zNTctMTEuODkyIDMwLjM1Ny0yOC4xNjZ2LTY2LjM0N2g0OC44MjJ2NjYuMDM0YzAgMjUuNjYzLTE1Ljk2MSA0Ni42MzEtNzAuMTAzIDQ2LjYzMS0zMi44NjEgMC01OC41MjMtNy4xOTgtNTguNTIzLTcuMTk4djExOS44NjNoODMuNTZjMzEuMjk2IDAgNTYuOTU4LTI1LjM1IDU2Ljk1OC01Ni45NTh2LTI4NC4xNjZoLTgzLjI0N3oiIGZpbGw9InVybCgjZCkiLz48cGF0aCBkPSJNMjQyLjg1NiAyNi42MDFjLTMxLjI5NiAwLTU2Ljk1OCAyNS4zNS01Ni45NTggNTYuOTU4djc0LjQ4NGMxNC4zOTYtMTIuMjA1IDM5LjQzMy0yMC4wMjkgNzkuODA0LTE4LjE1MiAyMS41OTQuOTM5IDQ0Ljc1MyA2Ljg4NSA0NC43NTMgNi44ODV2MjQuMDk4Yy0xMS41NzktNS45NDYtMjUuMzUtMTEuMjY3LTQzLjE4OC0xMi41MTgtMzAuNjctMi4xOTEtNDkuMTM0IDEyLjgzMS00OS4xMzQgMzkuMTIgMCAyNi42MDEgMTguNDY1IDQxLjYyMyA0OS4xMzQgMzkuMTIgMTcuODM5LTEuMjUyIDMxLjYwOS02Ljg4NSA0My4xODgtMTIuNTE4djI0LjA5OHMtMjIuODQ2IDUuOTQ2LTQ0Ljc1MyA2Ljg4NWMtNDAuMzcyIDEuODc4LTY1LjQwOC01Ljk0Ni03OS44MDQtMTguMTUydjEzMS40NDNoODMuNTZjMzEuMjk2IDAgNTYuOTU4LTI1LjM1IDU2Ljk1OC01Ni45NTh2LTI4NC43OTJoLTgzLjU2eiIgZmlsbD0idXJsKCNlKSIvPjwvc3ZnPg==");
        }
        .rccs__card--laser .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjM2OCIgdmlld0JveD0iMCAwIDUxMiAzNjgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxwYXRoIGZpbGw9IiMyODM0N0QiIGQ9Ik0wIDM2Ny4yMTJoNTEydi0zNjcuMjExaC01MTJ6Ii8+PHBhdGggZD0iTTI1My45MSAyOTEuMDQzaC0yNTMuOTF2Mi4wOTZoMjUyLjY5bC4xMTYgNy42MjIgNC43NjQtNy42MjJoMjU0LjQzdi0yLjA5NmgtMjUzLjI3MWwzLjY2My02LjExMi04LjQ4MiA2LjExMnptMi41NTYtMjAuMDg0aC0yNTYuNDY2djIuMDk2aDI1NS40Mmw5LjE0NyA4LjM5OCA1LjExMi04LjM5OGgyNDIuMzIxdi0yLjA5NmgtMjQxLjA0NWw0LjY3Ny03LjY4NC0xOS4xNjYgNy42ODR6bTIuMTUtMjAuMDgzaC0yNTguNjE2djIuMDk2aDI1NS45OTlsMjAuODgyIDguMjUyIDUuMDIyLTguMjUyaDIzMC4wOTd2LTIuMDk2aC0yMjguODJsNC4xMS02Ljc1Mi0yOC42NzQgNi43NTJ6bS02Ljk3Mi0yMC4wODNoLTI1MS42NDR2Mi4wOTZoMjUwLjAxN2wzOS4zNDIgNy44MzYgNC43Ny03LjgzNmgyMTcuODcxdi0yLjA5NmgtMjE2LjU5NGw0LjEzNC03LjI1Mi00Ny44OTYgNy4yNTJ6bS0yMi43NzUtMTcuMDU3aC0yMjguODY4djIuMDk2aDIzNy43bDYzLjEgNS45OTYgMy43MTEtNS45OTZoMjA3LjQ4OXYtMi4wOTZoLTIwNi4yMTRsNS4xMy04LjMyNC04Mi4wNDggOC4zMjR6bS0zLjMxLTIwLjA4M2gtMjI1LjU1OHYyLjA5NmgyMjYuMDgxbDg2Ljg4OSA2LjExMiAzLjc2Ni02LjExMmgxOTUuMjY0di0yLjA5NmgtMTkzLjk4OGw0LjUzNS03LjQ1MS05Ni45ODkgNy40NTF6bS0zLjEzOC0yMC4wODNoLTIyMi40MnYyLjA5NmgyMjEuOTU1bDEwMy4xMjEgNi4zNDQgMy44ODYtNi4zNDRoMTgzLjAzOHYtMi4wOTZoLTE4MS43NjNsNC40MzQtNy4yMTgtMTEyLjI1MSA3LjIxOHptLTguNzcyLTIwLjA4M2gtMjEzLjY0N3YyLjA5NWgyMTMuMjk4bDEyMy40ODIgNy42NjcgNC42MDUtNy42NjdoMTcwLjYxNXYtMi4wOTVoLTE2OS4zNTZsNC4wNzgtNi43ODgtMTMzLjA3NSA2Ljc4OHptLTE5LjA1Ny0xNy4yODloLTE5NC41OTF2Mi4wOTZoMTk0LjQxNmwxNTMuNjY3IDYuMTM4IDMuNjg5LTYuMTM4aDE2MC4yMjh2LTIuMDk2aC0xNTguOTdsNC42MzEtNy43MDktMTYzLjA3IDcuNzA5em00Ljc2NS0yMC4wODRoLTE5OS4zNTV2Mi4wOTZoMTk4LjMwOGwxNjEuNDQzIDYuODEgNC4wODQtNi44MWgxNDguMTY1di0yLjA5NmgtMTQ2LjkwNmw0LjAxNS02LjY4NC0xNjkuNzU0IDYuNjg0em0tMTEuMzMtMjAuMDgzaC0xODguMDI1djIuMDk2aDE4Ni45NzlsMTg0LjY3NiA3LjEwMiA0LjI0NC03LjEwMmgxMzYuMTAxdi0yLjA5NmgtMTM0Ljg0MmwzLjY3Ny02LjE3MS0xOTIuODEgNi4xNzF6bS04Ljg4OS0yMC4wODNoLTE3OS4xMzZ2Mi4wOTZoMTY0Ljg0NGwyMTkuMDg2IDYuNzEzIDQuMDMzLTYuNzEzaDEyNC4wMzd2LTIuMDk2aC0xMjIuNzc4bDMuNjc4LTYuMTIxLTIxMy43NjQgNi4xMjF6bS0xLjIyLTIwLjA4M2gtMTc3LjkxNnYyLjA5NmgxNzUuNjVsMjE5Ljc0OCA3LjcwNiA0LjYzLTcuNzA2aDExMS45NzJ2LTIuMDk2aC0xMTAuNzE0bDMuOTIyLTYuNTItMjI3LjI5MiA2LjUyem0tMTIuMzc1LTIwLjA4M2gtMTY1LjU0MXYyLjA5NWgxMzIuNTQybDI3NS4yOTkgNy4wNzggNC4yNTEtNy4wNzhoOTkuOTA4di0yLjA5NWgtOTguNjQ5bDMuODE3LTYuMzU2LTI1MS42MjcgNi4zNTZ6bS04Mi4yMjctMTYuNjQ5aC04My4zMTR2Mi4wOTZoMjAwLjA1MmwyMTkuNTU0IDQuMTM4IDIuNDg3LTQuMTM4aDg5LjkwN3YtMi4wOTZoLTg4LjY0OGw1LjU4OC05LjMwMy0zNDUuNjI1IDkuMzAzeiIgZmlsbD0iI0VCMEQ3RiIvPjxwYXRoIGQ9Ik0xMzQuNzEgMzQ5LjE0MXYtMzcuNjE4aDcuNjQzdjMxLjIyN2gxOS4wMDV2Ni4zOTFoLTI2LjY0OHptODMuMTU1IDBoLTguMzE0bC0zLjMwNi04LjYxNWgtMTUuMTMybC0zLjEyNCA4LjYxNWgtOC4xMDhsMTQuNzQ0LTM3LjkyOWg4LjA4MmwxNS4xNTcgMzcuOTI5em0tMTQuMDczLTE1LjAwNmwtNS4yMTYtMTQuMDc1LTUuMTEzIDE0LjA3NWgxMC4zMjl6bTMzLjExMiAyLjY2NWw3LjQzNi0uNzI1Yy40NDcgMi41MDIgMS4zNTYgNC4zMzkgMi43MjUgNS41MTEgMS4zNjggMS4xNzQgMy4yMTUgMS43NTkgNS41MzkgMS43NTkgMi40NiAwIDQuMzE3LS41MjEgNS41NjQtMS41NjYgMS4yNDgtMS4wNDIgMS44NzItMi4yNjMgMS44NzItMy42NiAwLS44OTYtLjI2My0xLjY2LS43ODctMi4yOS0uNTI1LS42MjktMS40NDQtMS4xNzYtMi43NTItMS42NDItLjg5NC0uMzExLTIuOTM1LS44NjItNi4xMTktMS42NTYtNC4wOTctMS4wMTctNi45NzEtMi4yNjctOC42MjQtMy43NTItMi4zMjMtMi4wODYtMy40ODUtNC42MzEtMy40ODUtNy42MzIgMC0xLjkzMS41NDYtMy43MzkgMS42MzktNS40MTkgMS4wOTMtMS42ODMgMi42NjgtMi45NjQgNC43MjYtMy44NDMgMi4wNTctLjg4IDQuNTM5LTEuMzE5IDcuNDUtMS4zMTkgNC43NTEgMCA4LjMyOCAxLjA0NCAxMC43MjggMy4xMyAyLjQwMyAyLjA4NyAzLjY2MSA0Ljg3NCAzLjc4NCA4LjM1NmwtNy42NDMuMzM3Yy0uMzI5LTEuOTQ4LTEuMDMxLTMuMzUtMi4xMDQtNC4yMDUtMS4wNzctLjg1Mi0yLjY5MS0xLjI4LTQuODQ0LTEuMjgtMi4yMjEgMC0zLjk1OS40NTgtNS4yMTYgMS4zNzEtLjgwOS41ODctMS4yMTQgMS4zNzItMS4yMTQgMi4zNTUgMCAuODk3LjM3OCAxLjY2NSAxLjEzNyAyLjMwMi45NjMuODEyIDMuMzA1IDEuNjU2IDcuMDI0IDIuNTM2IDMuNzE4Ljg3OSA2LjQ2OCAxLjc4OSA4LjI0OSAyLjczIDEuNzgzLjkzOSAzLjE3OCAyLjIyNSA0LjE4MyAzLjg1MyAxLjAwOCAxLjYzMiAxLjUxMSAzLjY0NiAxLjUxMSA2LjA0MiAwIDIuMTc0LS42MDMgNC4yMDktMS44MDcgNi4xMDYtMS4yMDcgMS44OTgtMi45MSAzLjMwOC01LjExMiA0LjIzMS0yLjIwNS45MjItNC45NSAxLjM4NC04LjIzOSAxLjM4NC00Ljc4NiAwLTguNDYtMS4xMDktMTEuMDI1LTMuMzI2LTIuNTY1LTIuMjE1LTQuMDk3LTUuNDQ1LTQuNTk2LTkuNjg4em01NC4xMzIgMTIuMzQxdi0zNy45MjloMjguMDY5djYuNDE3aC0yMC40MjZ2OC40MDhoMTkuMDA1djYuMzkxaC0xOS4wMDV2MTAuMzIyaDIxLjE0OXY2LjM5MWgtMjguNzkyem01Mi4yMiAwdi0zNy45MjloMTYuMDg3YzQuMDQ1IDAgNi45ODQuMzQgOC44MTkgMS4wMjEgMS44MzIuNjgzIDMuMzAxIDEuODk1IDQuNDAyIDMuNjM2IDEuMTAxIDEuNzQzIDEuNjUzIDMuNzM1IDEuNjUzIDUuOTc3IDAgMi44NDUtLjgzNiA1LjE5Ni0yLjUwNSA3LjA0OS0xLjY3IDEuODU1LTQuMTY3IDMuMDI1LTcuNDg4IDMuNTA2IDEuNjUzLjk2NyAzLjAxNyAyLjAyOCA0LjA5NCAzLjE4MyAxLjA3NSAxLjE1NiAyLjUyNCAzLjIwOCA0LjM1MSA2LjE1N2w0LjYyMSA3LjRoLTkuMTRsLTUuNTI2LTguMjU0Yy0xLjk2Mi0yLjk0OS0zLjMwNS00LjgwOC00LjAyOC01LjU3Ni0uNzIzLS43NjYtMS40ODktMS4yOTMtMi4yOTktMS41NzctLjgwOS0uMjg2LTIuMDkxLS40MjctMy44NDYtLjQyN2gtMS41NXYxNS44MzRoLTcuNjQzem03LjY0My0yMS44ODhoNS42NTVjMy42NjcgMCA1Ljk1NS0uMTU1IDYuODY5LS40NjYuOTEyLS4zMSAxLjYyNy0uODQ0IDIuMTQ0LTEuNjA0LjUxNi0uNzU5Ljc3NC0xLjcwNy43NzQtMi44NDYgMC0xLjI3Ni0uMzQtMi4zMDctMS4wMTktMy4wOTEtLjY4Mi0uNzg1LTEuNjQxLTEuMjgxLTIuODgtMS40ODgtLjYyLS4wODYtMi40NzgtLjEzLTUuNTc3LS4xM2gtNS45NjV2OS42MjV6IiBmaWxsPSIjZmZmIi8+PC9zdmc+");
        }
        .rccs__card--maestro .rccs__card__background, .rccs__card--mastercard .rccs__card__background {
          background: linear-gradient(25deg, #f37b26, #fdb731);
        }
        .rccs__card--maestro .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjM5OCIgdmlld0JveD0iMCAwIDUxMiAzOTgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxwYXRoIGZpbGw9IiM2QzZCQkQiIGQ9Ik0zMjUuMjIzIDI4Mi42MjloLTEzOC40NDV2LTI0OC44MDNoMTM4LjQ0NXoiLz48cGF0aCBkPSJNMTk1LjU2NSAxNTguMjMzYzAtNTAuNDcxIDIzLjYzMS05NS40MjkgNjAuNDMtMTI0LjQwMS0yNi45MS0yMS4xODYtNjAuODczLTMzLjgzMi05Ny43ODMtMzMuODMyLTg3LjM4MSAwLTE1OC4yMTMgNzAuODQyLTE1OC4yMTMgMTU4LjIzM3M3MC44MzIgMTU4LjIzMyAxNTguMjEzIDE1OC4yMzNjMzYuOTEgMCA3MC44NzItMTIuNjQ1IDk3Ljc4My0zMy44MzItMzYuNzk5LTI4Ljk3My02MC40My03My45MzEtNjAuNDMtMTI0LjQwMSIgZmlsbD0iI0QzMjAxMSIvPjxwYXRoIGQ9Ik01MTIgMTU4LjIzM2MwIDg3LjM5MS03MC44MzIgMTU4LjIzMy0xNTguMjEzIDE1OC4yMzMtMzYuOTEgMC03MC44NzItMTIuNjQ1LTk3Ljc5My0zMy44MzIgMzYuODA5LTI4Ljk3MyA2MC40NC03My45MzEgNjAuNDQtMTI0LjQwMSAwLTUwLjQ3MS0yMy42MzEtOTUuNDI5LTYwLjQ0LTEyNC40MDEgMjYuOTItMjEuMTg2IDYwLjg4My0zMy44MzIgOTcuNzkzLTMzLjgzMiA4Ny4zODEgMCAxNTguMjEzIDcwLjg0MiAxNTguMjEzIDE1OC4yMzMiIGZpbGw9IiMwMDk5REYiLz48cGF0aCBkPSJNMzcyLjA2NSAzNTIuOTM0YzEuODQxIDAgNC40ODcuMzUyIDYuNTA5IDEuMTQ3bC0yLjgxNyA4LjYxMWMtMS45MzItLjc5NS0zLjg2My0xLjA1Ni01LjcxNC0xLjA1Ni01Ljk3NiAwLTguOTYzIDMuODYzLTguOTYzIDEwLjgwNHYyMy41NmgtOS4xNDR2LTQyLjAxaDkuMDU0djUuMWMyLjM3NC0zLjY5MiA1LjgwNS02LjE1NyAxMS4wNzYtNi4xNTd6bS0zMy43ODEgOS40MDZoLTE0LjkzOXYxOC45ODNjMCA0LjIxNSAxLjQ4OSA3LjAzMiA2LjA2NiA3LjAzMiAyLjM3NCAwIDUuMzYyLS43OTUgOC4wNzgtMi4zNzRsMi42MzYgNy44MTdjLTIuODk3IDIuMDIyLTcuNDY0IDMuMjU5LTExLjQxOCAzLjI1OS0xMC44MTQgMC0xNC41ODctNS44MDUtMTQuNTg3LTE1LjU2M3YtMTkuMTU0aC04LjUzMXYtOC4zNWg4LjUzMXYtMTIuNzQ2aDkuMjI1djEyLjc0NmgxNC45Mzl2OC4zNXptLTExNi45ODcgOC45NjNjLjk3Ni02LjA2NiA0LjY1OC0xMC4yMDEgMTEuMTY3LTEwLjIwMSA1Ljg4NSAwIDkuNjY4IDMuNjkyIDEwLjYzMyAxMC4yMDFoLTIxLjh6bTMxLjI5NyAzLjY5MmMtLjA5MS0xMy4wOTgtOC4xNzktMjIuMDYxLTE5Ljk1OS0yMi4wNjEtMTIuMzAzIDAtMjAuOTE1IDguOTYzLTIwLjkxNSAyMi4wNjEgMCAxMy4zNSA4Ljk2MyAyMi4wNTEgMjEuNTM4IDIyLjA1MSA2LjMyOCAwIDEyLjEyMi0xLjU3OSAxNy4yMjMtNS44ODVsLTQuNDg3LTYuNzdjLTMuNTExIDIuODE3LTcuOTk4IDQuMzk2LTEyLjIxMyA0LjM5Ni01Ljg4NSAwLTExLjI0Ny0yLjcyNi0xMi41NjUtMTAuMjgxaDMxLjE5NmMuMDkxLTEuMTQ3LjE4MS0yLjI4NC4xODEtMy41MTF6bTQwLjE1OS0xMC4yODFjLTIuNTQ1LTEuNTg5LTcuNzI2LTMuNjEyLTEzLjA4OC0zLjYxMi01LjAxIDAtNy45OTggMS44NTEtNy45OTggNC45MjkgMCAyLjgwNyAzLjE1OSAzLjYwMSA3LjExMiA0LjEyNWw0LjMwNi42MTRjOS4xNDQgMS4zMjggMTQuNjc3IDUuMTkxIDE0LjY3NyAxMi41NzUgMCA3Ljk5OC03LjAzMiAxMy43MTItMTkuMTU0IDEzLjcxMi02Ljg2MSAwLTEzLjE4OS0xLjc2LTE4LjE5OC01LjQ1Mmw0LjMwNi03LjEyMmMzLjA3OCAyLjM3NCA3LjY1NiA0LjM5NiAxMy45ODMgNC4zOTYgNi4yMzcgMCA5LjU3Ny0xLjg0MSA5LjU3Ny01LjEgMC0yLjM2NC0yLjM3NC0zLjY5Mi03LjM4NC00LjM4NmwtNC4zMDYtLjYxNGMtOS40MDYtMS4zMjgtMTQuNTA2LTUuNTQzLTE0LjUwNi0xMi4zOTQgMC04LjM1IDYuODYxLTEzLjQ1IDE3LjQ5NC0xMy40NSA2LjY4IDAgMTIuNzQ2IDEuNDk5IDE3LjEzMiA0LjM5NmwtMy45NTQgNy4zODR6bTExMi43MjItMy4wOThjLTEuODgxIDAtMy42MjIuMzMyLTUuMjQxLjk4Ni0xLjYxLjY2NC0zLjAwOCAxLjU4OS00LjE4NSAyLjc3Ny0xLjE3NyAxLjE4Ny0yLjEwMyAyLjYxNi0yLjc3NyA0LjI3NS0uNjc0IDEuNjYtMS4wMDYgMy40OTEtMS4wMDYgNS40ODMgMCAyLjAwMi4zMzIgMy44MjMgMS4wMDYgNS40ODMuNjc0IDEuNjYgMS42IDMuMDg4IDIuNzc3IDQuMjc1IDEuMTc3IDEuMTg3IDIuNTc1IDIuMTEzIDQuMTg1IDIuNzc3IDEuNjIuNjY0IDMuMzYuOTg2IDUuMjQxLjk4NiAxLjg4MSAwIDMuNjMyLS4zMjIgNS4yNDEtLjk4NiAxLjYyLS42NjQgMy4wMjgtMS41ODkgNC4yMDUtMi43NzcgMS4xOTctMS4xODcgMi4xMjMtMi42MTYgMi44MDctNC4yNzUuNjc0LTEuNjYgMS4wMDYtMy40ODEgMS4wMDYtNS40ODMgMC0xLjk5Mi0uMzMyLTMuODIzLTEuMDA2LTUuNDgzLS42ODQtMS42Ni0xLjYxLTMuMDg4LTIuODA3LTQuMjc1LTEuMTc3LTEuMTg3LTIuNTg1LTIuMTEzLTQuMjA1LTIuNzc3LTEuNjEtLjY1NC0zLjM2LS45ODYtNS4yNDEtLjk4NnptMC04LjY4MmMzLjI1OSAwIDYuMjc3LjU2MyA5LjA1NCAxLjcgMi43NzcgMS4xMjcgNS4xODEgMi42ODYgNy4yMDMgNC42NjggMi4wMzIgMS45ODIgMy42MTIgNC4zMjYgNC43NTggNy4wMjIgMS4xNDcgMi43MDYgMS43MiA1LjY0NCAxLjcyIDguODEzIDAgMy4xNjktLjU3MyA2LjEwNi0xLjcyIDguODEzLTEuMTQ3IDIuNjk2LTIuNzI2IDUuMDUtNC43NTggNy4wMzItMi4wMjIgMS45ODItNC40MjYgMy41MzEtNy4yMDMgNC42NjgtMi43NzcgMS4xMjctNS43OTUgMS42OS05LjA1NCAxLjY5LTMuMjU5IDAtNi4yNzctLjU2My05LjA1NC0xLjY5LTIuNzc3LTEuMTM3LTUuMTYxLTIuNjg2LTcuMTczLTQuNjY4LTIuMDEyLTEuOTgyLTMuNTkxLTQuMzM2LTQuNzM4LTcuMDMyLTEuMTQ3LTIuNzA2LTEuNzItNS42NDQtMS43Mi04LjgxMyAwLTMuMTY5LjU3My02LjEwNiAxLjcyLTguODEzIDEuMTQ3LTIuNjk2IDIuNzI2LTUuMDQgNC43MzgtNy4wMjIgMi4wMTItMS45ODIgNC4zOTYtMy41NDEgNy4xNzMtNC42NjggMi43NzctMS4xMzcgNS43OTUtMS43IDkuMDU0LTEuN3ptLTIzNy41NzYgMjIuMDYxYzAtNy4zODQgNC44MzktMTMuNDUgMTIuNzQ2LTEzLjQ1IDcuNTU1IDAgMTIuNjU1IDUuODA1IDEyLjY1NSAxMy40NSAwIDcuNjQ2LTUuMSAxMy40NC0xMi42NTUgMTMuNDQtNy45MDcgMC0xMi43NDYtNi4wNTYtMTIuNzQ2LTEzLjQ0em0zNC4wMTMgMHYtMjEuMDA1aC05LjEzNHY1LjFjLTIuOTA3LTMuNzgzLTcuMjkzLTYuMTU3LTEzLjI2OS02LjE1Ny0xMS43OCAwLTIxLjAwNSA5LjIyNS0yMS4wMDUgMjIuMDYxIDAgMTIuODI2IDkuMjI1IDIyLjA2MSAyMS4wMDUgMjIuMDYxIDUuOTc2IDAgMTAuMzYyLTIuMzc0IDEzLjI2OS02LjE1N3Y1LjFoOS4xMzR2LTIxLjAwNXptLTUxLjQ5NyAyMS4wMDV2LTI2LjM2N2MwLTkuOTI5LTYuMzI4LTE2LjYwOS0xNi41MTgtMTYuNy01LjM2Mi0uMDkxLTEwLjkwNSAxLjU3OS0xNC43NjggNy40NzUtMi44OTctNC42NTgtNy40NzUtNy40NzUtMTMuODkzLTcuNDc1LTQuNDc3IDAtOC44NzMgMS4zMTgtMTIuMzAzIDYuMjM3di01LjE4MWgtOS4xMzR2NDIuMDFoOS4yMjV2LTIzLjI4OWMwLTcuMjkzIDQuMDQ0LTExLjE2NyAxMC4yODEtMTEuMTY3IDYuMDY2IDAgOS4xNDQgMy45NTQgOS4xNDQgMTEuMDc2djIzLjM3OWg5LjIyNXYtMjMuMjg5YzAtNy4yOTMgNC4yMjUtMTEuMTY3IDEwLjI4MS0xMS4xNjcgNi4yNDcgMCA5LjIzNSAzLjk1NCA5LjIzNSAxMS4wNzZ2MjMuMzc5aDkuMjI1eiIgZmlsbD0iIzExMEYwRCIvPjwvc3ZnPg==");
        }
        .rccs__card--mastercard .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjM5NyIgdmlld0JveD0iMCAwIDUxMiAzOTciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxwYXRoIGQ9Ik05My4wNzkgMzk2LjAyM3YtMjYuMzQzYzAtMTAuMDk4LTYuMTQ3LTE2LjY4NC0xNi42ODQtMTYuNjg0LTUuMjY5IDAtMTAuOTc2IDEuNzU2LTE0LjkyOCA3LjQ2NC0zLjA3My00LjgzLTcuNDY0LTcuNDY0LTE0LjA1LTcuNDY0LTQuMzkgMC04Ljc4MSAxLjMxNy0xMi4yOTMgNi4xNDd2LTUuMjY5aC05LjIydjQyLjE0OWg5LjIydi0yMy4yN2MwLTcuNDY0IDMuOTUxLTEwLjk3NiAxMC4wOTgtMTAuOTc2czkuMjIgMy45NTEgOS4yMiAxMC45NzZ2MjMuMjdoOS4yMnYtMjMuMjdjMC03LjQ2NCA0LjM5LTEwLjk3NiAxMC4wOTgtMTAuOTc2IDYuMTQ3IDAgOS4yMiAzLjk1MSA5LjIyIDEwLjk3NnYyMy4yN2gxMC4wOTh6bTEzNi41NDQtNDIuMTQ5aC0xNC45Mjh2LTEyLjczMmgtOS4yMnYxMi43MzJoLTguMzQydjguMzQyaDguMzQydjE5LjMxOGMwIDkuNjU5IDMuOTUxIDE1LjM2NyAxNC40ODkgMTUuMzY3IDMuOTUxIDAgOC4zNDItMS4zMTcgMTEuNDE1LTMuMDczbC0yLjYzNC03LjkwM2MtMi42MzQgMS43NTYtNS43MDggMi4xOTUtNy45MDMgMi4xOTUtNC4zOSAwLTYuMTQ3LTIuNjM0LTYuMTQ3LTcuMDI1di0xOC44NzloMTQuOTI4di04LjM0MnptNzguMTUxLS44NzhjLTUuMjY5IDAtOC43ODEgMi42MzQtMTAuOTc2IDYuMTQ3di01LjI2OWgtOS4yMnY0Mi4xNDloOS4yMnYtMjMuNzA5YzAtNy4wMjUgMy4wNzMtMTAuOTc2IDguNzgxLTEwLjk3NiAxLjc1NiAwIDMuOTUxLjQzOSA1LjcwOC44NzhsMi42MzQtOC43ODFjLTEuNzU2LS40MzktNC4zOS0uNDM5LTYuMTQ3LS40Mzl6bS0xMTguMTA0IDQuMzljLTQuMzktMy4wNzMtMTAuNTM3LTQuMzktMTcuMTIzLTQuMzktMTAuNTM3IDAtMTcuNTYyIDUuMjY5LTE3LjU2MiAxMy42MTEgMCA3LjAyNSA1LjI2OSAxMC45NzYgMTQuNDg5IDEyLjI5M2w0LjM5LjQzOWM0LjgzLjg3OCA3LjQ2NCAyLjE5NSA3LjQ2NCA0LjM5IDAgMy4wNzMtMy41MTIgNS4yNjktOS42NTkgNS4yNjlzLTEwLjk3Ni0yLjE5NS0xNC4wNS00LjM5bC00LjM5IDcuMDI1YzQuODMgMy41MTIgMTEuNDE1IDUuMjY5IDE4LjAwMSA1LjI2OSAxMi4yOTMgMCAxOS4zMTgtNS43MDggMTkuMzE4LTEzLjYxMSAwLTcuNDY0LTUuNzA4LTExLjQxNS0xNC40ODktMTIuNzMybC00LjM5LS40MzljLTMuOTUxLS40MzktNy4wMjUtMS4zMTctNy4wMjUtMy45NTEgMC0zLjA3MyAzLjA3My00LjgzIDcuOTAzLTQuODMgNS4yNjkgMCAxMC41MzcgMi4xOTUgMTMuMTcxIDMuNTEybDMuOTUxLTcuNDY0em0yNDQuOTktNC4zOWMtNS4yNjkgMC04Ljc4MSAyLjYzNC0xMC45NzYgNi4xNDd2LTUuMjY5aC05LjIydjQyLjE0OWg5LjIydi0yMy43MDljMC03LjAyNSAzLjA3My0xMC45NzYgOC43ODEtMTAuOTc2IDEuNzU2IDAgMy45NTEuNDM5IDUuNzA4Ljg3OGwyLjYzNC04Ljc4MWMtMS43NTYtLjQzOS00LjM5LS40MzktNi4xNDctLjQzOXptLTExNy42NjUgMjEuOTUyYzAgMTIuNzMyIDguNzgxIDIxLjk1MiAyMi4zOTIgMjEuOTUyIDYuMTQ3IDAgMTAuNTM3LTEuMzE3IDE0LjkyOC00LjgzbC00LjM5LTcuNDY0Yy0zLjUxMiAyLjYzNC03LjAyNSAzLjk1MS0xMC45NzYgMy45NTEtNy40NjQgMC0xMi43MzItNS4yNjktMTIuNzMyLTEzLjYxMSAwLTcuOTAzIDUuMjY5LTEzLjE3MSAxMi43MzItMTMuNjExIDMuOTUxIDAgNy40NjQgMS4zMTcgMTAuOTc2IDMuOTUxbDQuMzktNy40NjRjLTQuMzktMy41MTItOC43ODEtNC44My0xNC45MjgtNC44My0xMy42MTEgMC0yMi4zOTIgOS4yMi0yMi4zOTIgMjEuOTUyem04NS4xNzYgMHYtMjEuMDc0aC05LjIydjUuMjY5Yy0zLjA3My0zLjk1MS03LjQ2NC02LjE0Ny0xMy4xNzEtNi4xNDctMTEuODU0IDAtMjEuMDc0IDkuMjItMjEuMDc0IDIxLjk1MiAwIDEyLjczMiA5LjIyIDIxLjk1MiAyMS4wNzQgMjEuOTUyIDYuMTQ3IDAgMTAuNTM3LTIuMTk1IDEzLjE3MS02LjE0N3Y1LjI2OWg5LjIydi0yMS4wNzR6bS0zMy44MDcgMGMwLTcuNDY0IDQuODMtMTMuNjExIDEyLjczMi0xMy42MTEgNy40NjQgMCAxMi43MzIgNS43MDggMTIuNzMyIDEzLjYxMSAwIDcuNDY0LTUuMjY5IDEzLjYxMS0xMi43MzIgMTMuNjExLTcuOTAzLS40MzktMTIuNzMyLTYuMTQ3LTEyLjczMi0xMy42MTF6bS0xMTAuMjAxLTIxLjk1MmMtMTIuMjkzIDAtMjEuMDc0IDguNzgxLTIxLjA3NCAyMS45NTIgMCAxMy4xNzEgOC43ODEgMjEuOTUyIDIxLjUxMyAyMS45NTIgNi4xNDcgMCAxMi4yOTMtMS43NTYgMTcuMTIzLTUuNzA4bC00LjM5LTYuNTg2Yy0zLjUxMiAyLjYzNC03LjkwMyA0LjM5LTEyLjI5MyA0LjM5LTUuNzA4IDAtMTEuNDE1LTIuNjM0LTEyLjczMi0xMC4wOThoMzEuMTczdi0zLjUxMmMuNDM5LTEzLjYxMS03LjQ2NC0yMi4zOTItMTkuMzE4LTIyLjM5MnptMCA3LjkwM2M1LjcwOCAwIDkuNjU5IDMuNTEyIDEwLjUzNyAxMC4wOThoLTIxLjk1MmMuODc4LTUuNzA4IDQuODMtMTAuMDk4IDExLjQxNS0xMC4wOTh6bTIyOC43NDUgMTQuMDV2LTM3Ljc1OGgtOS4yMnYyMS45NTJjLTMuMDczLTMuOTUxLTcuNDY0LTYuMTQ3LTEzLjE3MS02LjE0Ny0xMS44NTQgMC0yMS4wNzQgOS4yMi0yMS4wNzQgMjEuOTUyIDAgMTIuNzMyIDkuMjIgMjEuOTUyIDIxLjA3NCAyMS45NTIgNi4xNDcgMCAxMC41MzctMi4xOTUgMTMuMTcxLTYuMTQ3djUuMjY5aDkuMjJ2LTIxLjA3NHptLTMzLjgwNyAwYzAtNy40NjQgNC44My0xMy42MTEgMTIuNzMyLTEzLjYxMSA3LjQ2NCAwIDEyLjczMiA1LjcwOCAxMi43MzIgMTMuNjExIDAgNy40NjQtNS4yNjkgMTMuNjExLTEyLjczMiAxMy42MTEtNy45MDMtLjQzOS0xMi43MzItNi4xNDctMTIuNzMyLTEzLjYxMXptLTMwOC4yMTMgMHYtMjEuMDc0aC05LjIydjUuMjY5Yy0zLjA3My0zLjk1MS03LjQ2NC02LjE0Ny0xMy4xNzEtNi4xNDctMTEuODU0IDAtMjEuMDc0IDkuMjItMjEuMDc0IDIxLjk1MiAwIDEyLjczMiA5LjIyIDIxLjk1MiAyMS4wNzQgMjEuOTUyIDYuMTQ3IDAgMTAuNTM3LTIuMTk1IDEzLjE3MS02LjE0N3Y1LjI2OWg5LjIydi0yMS4wNzR6bS0zNC4yNDYgMGMwLTcuNDY0IDQuODMtMTMuNjExIDEyLjczMi0xMy42MTEgNy40NjQgMCAxMi43MzIgNS43MDggMTIuNzMyIDEzLjYxMSAwIDcuNDY0LTUuMjY5IDEzLjYxMS0xMi43MzIgMTMuNjExLTcuOTAzLS40MzktMTIuNzMyLTYuMTQ3LTEyLjczMi0xMy42MTF6Ii8+PHBhdGggZmlsbD0iI0ZGNUYwMCIgZD0iTTE4Ni41OTYgMzMuODA3aDEzOC4zMDF2MjQ4LjUwMmgtMTM4LjMwMXoiLz48cGF0aCBkPSJNMTk1LjM3NyAxNTguMDU4YzAtNTAuNDkxIDIzLjcwOS05NS4yNzQgNjAuMTUtMTI0LjI1MS0yNi43ODItMjEuMDc0LTYwLjU4OS0zMy44MDctOTcuNDY5LTMzLjgwNy04Ny4zNzEgMC0xNTguMDU4IDcwLjY4Ny0xNTguMDU4IDE1OC4wNThzNzAuNjg3IDE1OC4wNTggMTU4LjA1OCAxNTguMDU4YzM2Ljg4IDAgNzAuNjg3LTEyLjczMiA5Ny40NjktMzMuODA3LTM2LjQ0MS0yOC41MzgtNjAuMTUtNzMuNzYtNjAuMTUtMTI0LjI1MXoiIGZpbGw9IiNFQjAwMUIiLz48cGF0aCBkPSJNNTExLjQ5MyAxNTguMDU4YzAgODcuMzcxLTcwLjY4NyAxNTguMDU4LTE1OC4wNTggMTU4LjA1OC0zNi44OCAwLTcwLjY4Ny0xMi43MzItOTcuNDY5LTMzLjgwNyAzNi44OC0yOC45NzcgNjAuMTUtNzMuNzYgNjAuMTUtMTI0LjI1MXMtMjMuNzA5LTk1LjI3NC02MC4xNS0xMjQuMjUxYzI2Ljc4Mi0yMS4wNzQgNjAuNTg5LTMzLjgwNyA5Ny40NjktMzMuODA3IDg3LjM3MSAwIDE1OC4wNTggNzEuMTI2IDE1OC4wNTggMTU4LjA1OHoiIGZpbGw9IiNGNzlFMUIiLz48L3N2Zz4=");
        }
        .rccs__card--unionpay .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTEyIiBoZWlnaHQ9IjMyMCIgdmlld0JveD0iMCAwIDUxMiAzMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPjxwYXRoIGQ9Ik0xMDAuMDgzLjAwMWgxMjcuNTU3YzE3LjgwNiAwIDI4Ljg4IDE0LjUxMyAyNC43MjcgMzIuMzc4bC01OS4zODcgMjU0Ljk3MWMtNC4xOTEgMTcuODAzLTIyLjAxOSAzMi4zMjgtMzkuODM3IDMyLjMyOGgtMTI3LjU0NWMtMTcuNzgxIDAtMjguODgtMTQuNTI2LTI0LjcyNy0zMi4zMjhsNTkuNDEyLTI1NC45NzFjNC4xNTQtMTcuODY1IDIxLjk3Mi0zMi4zNzggMzkuOC0zMi4zNzgiIGZpbGw9IiNFMjE4MzYiLz48cGF0aCBkPSJNMjE3LjAyNy4wMDFoMTQ2LjY4N2MxNy44MDMgMCA5Ljc3NSAxNC41MTMgNS41ODcgMzIuMzc4bC01OS4zNzggMjU0Ljk3MWMtNC4xNjYgMTcuODAzLTIuODY3IDMyLjMyOC0yMC43MDcgMzIuMzI4aC0xNDYuNjg3Yy0xNy44NCAwLTI4Ljg4LTE0LjUyNi0yNC42ODktMzIuMzI4bDU5LjM3NS0yNTQuOTcxYzQuMjE2LTE3Ljg2NSAyMS45OTctMzIuMzc4IDM5LjgxMi0zMi4zNzgiIGZpbGw9IiMwMDQ0N0MiLz48cGF0aCBkPSJNMzU3Ljg5Ni4wMDFoMTI3LjU1N2MxNy44MzEgMCAyOC45MDUgMTQuNTEzIDI0LjcxNyAzMi4zNzhsLTU5LjM3OCAyNTQuOTcxYy00LjE5MSAxNy44MDMtMjIuMDMxIDMyLjMyOC0zOS44NTkgMzIuMzI4aC0xMjcuNDk4Yy0xNy44NCAwLTI4LjkwNS0xNC41MjYtMjQuNzI3LTMyLjMyOGw1OS4zODctMjU0Ljk3MWM0LjE1NC0xNy44NjUgMjEuOTYtMzIuMzc4IDM5LjgtMzIuMzc4IiBmaWxsPSIjMDA3Qjg0Ii8+PHBhdGggZD0iTTEzMy4zOTcgODEuNzEyYy0xMy4xMTcuMTM0LTE2Ljk5MSAwLTE4LjIyOS0uMjkyLS40NzYgMi4yNi05LjMyNyA0My4wOTItOS4zNTIgNDMuMTI2LTEuOTA2IDguMjYxLTMuMjkzIDE0LjE1LTguMDAzIDE3Ljk1Mi0yLjY3NCAyLjIxMS01Ljc5NSAzLjI3Ny05LjQxNCAzLjI3Ny01LjgxNyAwLTkuMjA2LTIuODg4LTkuNzc1LTguMzY3bC0uMTA5LTEuODgxczEuNzcyLTExLjA2NSAxLjc3Mi0xMS4xMjdjMCAwIDkuMjktMzcuMjEgMTAuOTUzLTQyLjEyOC4wODctLjI4LjExMi0uNDI2LjEzNC0uNTYtMTguMDgzLjE1OS0yMS4yODggMC0yMS41MDktLjI5Mi0uMTIxLjQwMS0uNTY5IDIuNzA4LS41NjkgMi43MDhsLTkuNDg2IDQxLjkzOS0uODE1IDMuNTU3LTEuNTc2IDExLjYzNGMwIDMuNDUxLjY3OCA2LjI2OCAyLjAyNyA4LjY1IDQuMzIyIDcuNTUyIDE2LjY0OSA4LjY4NCAyMy42MjMgOC42ODQgOC45ODUgMCAxNy40MTQtMS45MDkgMjMuMTEtNS4zOTQgOS44ODctNS44NDIgMTIuNDc0LTE0Ljk3MyAxNC43ODEtMjMuMDg4bDEuMDctNC4xNjNzOS41Ny0zOC42NDYgMTEuMTk2LTQzLjY3NGMuMDYyLS4yOC4wODctLjQyNi4xNzEtLjU2em0zMi41NjEgMzEuMTc2Yy0yLjMwNyAwLTYuNTIzLjU2LTEwLjMxIDIuNDE2LTEuMzc0LjcwNi0yLjY3NCAxLjUyLTQuMDQ1IDIuMzMybDEuMjM3LTQuNDY4LS42NzgtLjc1MmMtOC4wMzEgMS42MjYtOS44MjggMS44NDQtMTcuMjQ2IDIuODg4bC0uNjIyLjQxNGMtLjg2MSA3LjE0Mi0xLjYyNiAxMi41MTEtNC44MTkgMjYuNTQ5LTEuMjE2IDUuMTc0LTIuNDc4IDEwLjM5Ny0zLjc0MyAxNS41NThsLjM0Mi42NTZjNy42MDItLjQwMSA5LjkwOS0uNDAxIDE2LjUxNi0uMjkybC41MzUtLjU4MWMuODM5LTQuMy45NDgtNS4zMDcgMi44MDgtMTQuMDE2Ljg3NC00LjEyOSAyLjY5Ni0xMy4yMDEgMy41OTQtMTYuNDMyIDEuNjUxLS43NjUgMy4yOC0xLjUxNyA0LjgzNS0xLjUxNyAzLjcwMyAwIDMuMjUyIDMuMjMgMy4xMDkgNC41MTgtLjE1OSAyLjE2MS0xLjUwOCA5LjIxOC0yLjg5MSAxNS4yNzhsLS45MjMgMy45MTFjLS42NDQgMi44ODgtMS4zNDkgNS42OTYtMS45OTMgOC41NTlsLjI4LjU3MmM3LjQ5My0uNDAxIDkuNzc4LS40MDEgMTYuMTc3LS4yOTJsLjc1Mi0uNTgxYzEuMTU3LTYuNzE2IDEuNDk1LTguNTEzIDMuNTQ3LTE4LjI5MWwxLjAzMi00LjQ5M2MyLjAwNS04Ljc5MyAzLjAxMy0xMy4yNTEgMS40OTUtMTYuODgyLTEuNjA0LTQuMDctNS40NTMtNS4wNTItOC45ODgtNS4wNTJ6bTM2LjM3NCA5LjIwNmMtMy45ODMuNzY1LTYuNTIzIDEuMjc1LTkuMDQ3IDEuNjA0LTIuNTAzLjQwMS00Ljk0My43NjUtOC43OTMgMS4zbC0uMzA1LjI3Ny0uMjguMjIxYy0uNDAxIDIuODY3LS42ODEgNS4zNDUtMS4yMTMgOC4yNTgtLjQ1MSAzLjAxMy0xLjE0NCA2LjQzNi0yLjI3MyAxMS4zNTQtLjg3NCAzLjc2NS0xLjMyNCA1LjA3Ny0xLjgyMiA2LjQwMi0uNDg1IDEuMzI0LTEuMDIgMi42MTItMi4wMDIgNi4zMTVsLjIzLjM0Mi4xOTMuMzE0YzMuNTk3LS4xNzEgNS45NTEtLjI5MiA4LjM3LS4zMTQgMi40MTYtLjA4NyA0LjkxOSAwIDguNzkzLjAyMmwuMzM5LS4yNzcuMzY0LS4zMDVjLjU2LTMuMzM5LjY0NC00LjIzOC45ODYtNS44NjcuMzM5LTEuNzQ3LjkyMy00LjE2NiAyLjM1Ny0xMC42MjcuNjc4LTMuMDM0IDEuNDMzLTYuMDYgMi4xMzYtOS4xNTYuNzMxLTMuMDg0IDEuNDk1LTYuMTIyIDIuMjIzLTkuMTU2bC0uMTA5LS4zNjctLjE0Ni0uMzM5em0uMDg1LTEyLjQxMmMtMy42MTktMi4xMzYtOS45NzEtMS40NTgtMTQuMjQ2IDEuNDkyLTQuMjYzIDIuODkxLTQuNzQ4IDYuOTk1LTEuMTQxIDkuMTU5IDMuNTU3IDIuMDc3IDkuOTM0IDEuNDU4IDE0LjE3MS0xLjUxNyA0LjI1My0yLjk1NCA0Ljc4NS03LjAyIDEuMjE2LTkuMTM1em0yMS44ODcgNDkuNDY3YzcuMzIyIDAgMTQuODI3LTIuMDE4IDIwLjQ3Ny04LjAwNiA0LjM0Ny00Ljg1NiA2LjMzOS0xMi4wODIgNy4wMy0xNS4wNTcgMi4yNDgtOS44NjIuNDk3LTE0LjQ2Ny0xLjcwMS0xNy4yNzEtMy4zMzktNC4yNzUtOS4yNC01LjY0Ni0xNS4zNjItNS42NDYtMy42ODEgMC0xMi40NDkuMzY0LTE5LjI5OCA2LjY3OC00LjkxOSA0LjU1NS03LjE5MSAxMC43MzYtOC41NjIgMTYuNjYyLTEuMzg0IDYuMDM4LTIuOTc1IDE2LjkwNyA3LjAyIDIwLjk1MiAzLjA4NCAxLjMyNCA3LjUzIDEuNjg4IDEwLjM5NyAxLjY4OHptLS41NzItMjIuMTljMS42ODgtNy40NjggMy42ODEtMTMuNzM2IDguNzY4LTEzLjczNiAzLjk4NiAwIDQuMjc1IDQuNjY0IDIuNTAzIDEyLjE1Ny0uMzE3IDEuNjYzLTEuNzcyIDcuODQ3LTMuNzQgMTAuNDgxLTEuMzc0IDEuOTQzLTMgMy4xMjItNC43OTcgMy4xMjItLjUzNSAwLTMuNzE1IDAtMy43NjUtNC43MjMtLjAyNS0yLjMzMi40NTEtNC43MTMgMS4wMzItNy4zem00Ni4zODIgMjEuMjI5bC41NzItLjU4MWMuODExLTQuMy45NDUtNS4zMSAyLjc0Mi0xNC4wMTYuODk5LTQuMTI5IDIuNzU4LTEzLjIwMSAzLjYzMS0xNi40MzIgMS42NTQtLjc2OCAzLjI1NS0xLjUyIDQuODYtMS41MiAzLjY3OCAwIDMuMjMgMy4yMyAzLjA4NCA0LjUxOC0uMTM0IDIuMTY0LTEuNDgzIDkuMjE4LTIuODkxIDE1LjI3OGwtLjg3NCAzLjkxMWMtLjY2OCAyLjg5MS0xLjM5NiA1LjY5Ni0yLjA0IDguNTYybC4yOC41NzJjNy41MTgtLjQwMSA5LjcxNi0uNDAxIDE2LjE1Mi0uMjkybC43NzctLjU4MWMxLjEyOS02LjcxOSAxLjQzMy04LjUxNiAzLjU0Ny0xOC4yOTFsMS4wMDctNC40OTZjMi4wMTUtOC43OTMgMy4wMzQtMTMuMjQ4IDEuNTQyLTE2Ljg3OS0xLjY1MS00LjA3LTUuNTI1LTUuMDUyLTkuMDEtNS4wNTItMi4zMSAwLTYuNTQ4LjU1Ny0xMC4zMTMgMi40MTYtMS4zNDYuNzA2LTIuNjk2IDEuNTE3LTQuMDIgMi4zMzJsMS4xNTMtNC40NjgtLjYxOS0uNzU2Yy04LjAyOCAxLjYyOS05Ljg2MiAxLjg0Ny0xNy4yNzEgMi44OTFsLS41NjkuNDE0Yy0uODk5IDcuMTQyLTEuNjI5IDEyLjUwOC00LjgyMiAyNi41NDktMS4yMTYgNS4xNzQtMi40NzggMTAuMzk3LTMuNzQgMTUuNTU4bC4zMzkuNjU2YzcuNjE0LS40MDEgOS44ODctLjQwMSAxNi40ODEtLjI5MnptNTUuMjM1LjI5MWMuNDczLTIuMzA3IDMuMjgtMTUuOTgxIDMuMzA1LTE1Ljk4MSAwIDAgMi4zOTEtMTAuMDMzIDIuNTM3LTEwLjM5NyAwIDAgLjc1Mi0xLjA0NSAxLjUwNS0xLjQ1OGgxLjEwN2MxMC40NDMgMCAyMi4yMzYgMCAzMS40OC02LjggNi4yOS00LjY2NCAxMC41OS0xMS41NSAxMi41MDgtMTkuOTIuNDk3LTIuMDUyLjg2NC00LjQ5My44NjQtNi45MzMgMC0zLjIwNS0uNjQ0LTYuMzc3LTIuNTAzLTguODU1LTQuNzEzLTYuNTk0LTE0LjEtNi43MTYtMjQuOTM1LTYuNzY1bC01LjM0MS4wNWMtMTMuODcuMTcxLTE5LjQzMi4xMjEtMjEuNzE3LS4xNTktLjE5MyAxLjAxLS41NTcgMi44MDgtLjU1NyAyLjgwOHMtNC45NjggMjMuMDI2LTQuOTY4IDIzLjA2M2MwIDAtMTEuODg5IDQ4Ljk1Ni0xMi40NDkgNTEuMjYzIDEyLjExLS4xNDYgMTcuMDc1LS4xNDYgMTkuMTY1LjA4NHptOS4yMDYtNDAuOTAzczUuMjgyLTIyLjk3OSA1LjI1Ny0yMi44OTJsLjE3MS0xLjE3OC4wNzUtLjg5OSAyLjExMS4yMThzMTAuODk0LjkzNiAxMS4xNDkuOTYxYzQuMyAxLjY2MyA2LjA3MiA1Ljk1MSA0LjgzNSAxMS41NDctMS4xMjkgNS4xMTQtNC40NDYgOS40MTQtOC43MDkgMTEuNDkxLTMuNTEgMS43Ni03LjgxIDEuOTA2LTEyLjI0MSAxLjkwNmgtMi44NjdsLjIxOC0xLjE1M3ptMzIuODg5IDE5LjgwOWMtMS4zOTYgNS45NTEtMyAxNi44MiA2Ljk0NiAyMC42OTQgMy4xNzEgMS4zNDkgNi4wMTMgMS43NSA4LjkwMSAxLjYwNCAzLjA1LS4xNjUgNS44NzYtMS42OTQgOC40OTQtMy44OTZsLS43MDkgMi43MTcuNDUxLjU4MWM3LjE1NC0uMzAyIDkuMzc0LS4zMDIgMTcuMTI1LS4yNDNsLjcwMy0uNTM1YzEuMTMyLTYuNjUzIDIuMTk4LTEzLjExNCA1LjEzOS0yNS44NDMgMS40MzMtNi4wOTcgMi44NjMtMTIuMTM1IDQuMzM0LTE4LjIwN2wtLjIzLS42NjhjLTguMDAzIDEuNDgzLTEwLjE0MiAxLjgtMTcuODQgMi44OTFsLS41ODUuNDc2LS4yMzMgMS44MDZjLTEuMTk3LTEuOTM0LTIuOTMyLTMuNTg1LTUuNjA5LTQuNjE0LTMuNDIzLTEuMzQ2LTExLjQ2My4zODktMTguMzc1IDYuNjgxLTQuODU2IDQuNDkzLTcuMTg4IDEwLjY0OS04LjUxMyAxNi41NTN6bTE2LjgwOC4zNjRjMS43MTMtNy4zMzQgMy42ODEtMTMuNTQgOC43OC0xMy41NCAzLjIyNCAwIDQuOTIyIDIuOTc1IDQuNTc3IDguMDQ5LS4yNzQgMS4yNjUtLjU2OSAyLjU5OS0uOTIgNC4xMDctLjUxIDIuMTc5LTEuMDYzIDQuMzQtMS42MDEgNi41MDQtLjU0NyAxLjQ4LTEuMTg1IDIuODc2LTEuODg0IDMuODA2LTEuMzEyIDEuODU5LTQuNDM0IDMuMDEzLTYuMjMxIDMuMDEzLS41MSAwLTMuNjU2IDAtMy43NjUtNC42MzktLjAyNS0yLjMxLjQ1MS00LjY4OSAxLjA0NS03LjN6bTg3Ljc3Mi0yNC4yMTdsLS42MTktLjcwNmMtNy45MTkgMS42MDQtOS4zNTIgMS44NTktMTYuNjI3IDIuODQybC0uNTM1LjUzNS0uMDg0LjM0Mi0uMDI1LS4xMjFjLTUuNDE2IDEyLjQ5NS01LjI1NyA5LjgtOS42NjYgMTkuNjM3bC0uMDUtMS4yMDMtMS4xMDQtMjEuMzI1LS42OTMtLjcwNmMtOC4yOTUgMS42MDQtOC40OTEgMS44NTktMTYuMTUyIDIuODQybC0uNTk3LjUzNWMtLjA4NC4yNTUtLjA4NC41MzUtLjEzNC44MzlsLjA1LjEwOWMuOTU4IDQuODk0LjcyOCAzLjgwMiAxLjY4OCAxMS41MjUuNDQ4IDMuNzkgMS4wNDUgNy42MDIgMS40OTIgMTEuMzQ1Ljc1NiA2LjI2NSAxLjE3OCA5LjM0OSAyLjEwMiAxOC45MS01LjE3NCA4LjUzOC02LjM5OSAxMS43NjgtMTEuMzc5IDE5LjI2MWwuMDM0LjA3NS0zLjUwNyA1LjU0N2MtLjQwMS41ODUtLjc2NS45ODYtMS4yNzUgMS4xNTctLjU2LjI3Ny0xLjI4Ny4zMjYtMi4yOTguMzI2aC0xLjk0M2wtMi44ODggOS42MDcgOS45MDkuMTcxYzUuODE3LS4wMjUgOS40NzMtMi43NDUgMTEuNDQxLTYuNDAybDYuMjMxLTEwLjY3N2gtLjA5OWwuNjU2LS43NTJjNC4xOTEtOS4wMjMgMzYuMDcyLTYzLjcxMiAzNi4wNzItNjMuNzEyem0tMTA0LjU4IDEyNi4xNzVoLTQuMjA0bDE1LjU1OC01MS40NTloNS4xNjFsMS42MzgtNS4zMDEuMTU5IDUuODk1Yy0uMTkzIDMuNjQ0IDIuNjc0IDYuODc0IDEwLjIwNCA2LjMzOWg4LjcwOWwyLjk5Ny05LjkwOWgtMy4yNzdjLTEuODg0IDAtMi43NTgtLjQ3Ni0yLjY0OS0xLjQ5NWwtLjE1OS01Ljk5N2gtMTYuMTI3di4wMzFjLTUuMjE0LjEwOS0yMC43ODQuNTAxLTIzLjkzNyAxLjM0LTMuODE1Ljk4Mi03LjgzNSAzLjg3NC03LjgzNSAzLjg3NGwxLjU3OS01LjMwN2gtMTUuMDg1bC0zLjE0MyAxMC41MzEtMTUuNzY2IDUyLjI0NWgtMy4wNTlsLTMgOS44MzdoMzAuMDQ2bC0xLjAwNyAzLjI4aDE0LjgwNmwuOTgyLTMuMjhoNC4xNTRsMy4yNTUtMTAuNjI0em0tMTIuMzI4LTQxLjAwM2MtMi40MTYuNjY4LTYuOTEyIDIuNjk2LTYuOTEyIDIuNjk2bDMuOTk4LTEzLjE1MmgxMS45ODZsLTIuODkxIDkuNTgycy0zLjcwMy4yMTgtNi4xODEuODc0em0uMjMgMTguNzg4cy0zLjc2NS40NzMtNi4yNDMgMS4wMzJjLTIuNDQxLjc0LTcuMDE3IDMuMDcyLTcuMDE3IDMuMDcybDQuMTI5LTEzLjY4NmgxMi4wNDhsLTIuOTE2IDkuNTgyem0tNi43MTYgMjIuMzM2aC0xMi4wMjNsMy40ODUtMTEuNTVoMTEuOTg2bC0zLjQ0OCAxMS41NXptMjguOTU0LTMxLjkxOGgxNy4zM2wtMi40OSA4LjA2NWgtMTcuNTZsLTIuNjM3IDguODE3aDE1LjM2NWwtMTEuNjM0IDE2LjM4MmMtLjgxNSAxLjIwMy0xLjU0NSAxLjYyOS0yLjM1NyAxLjk2OC0uODE1LjQxNC0xLjg4NC44OTktMy4xMjIuODk5aC00LjI2M2wtMi45MjkgOS42NTdoMTEuMTQ5YzUuNzk1IDAgOS4yMTgtMi42MzcgMTEuNzQ2LTYuMDk3bDcuOTc4LTEwLjkxOSAxLjcxMyAxMS4wODdjLjM2NCAyLjA3NyAxLjg1NiAzLjI5MyAyLjg2NyAzLjc2NSAxLjExNi41NiAyLjI3IDEuNTIgMy44OTkgMS42NjMgMS43NDcuMDc1IDMuMDEuMTM0IDMuODQ5LjEzNGg1LjQ3OGwzLjI4OS0xMC44MDdoLTIuMTYxYy0xLjI0MSAwLTMuMzc2LS4yMDgtMy43NC0uNTk3LS4zNjQtLjQ3My0uMzY0LTEuMi0uNTYtMi4zMDdsLTEuNzM4LTExLjExMmgtNy4xMTdsMy4xMjItMy43MTVoMTcuNTI2bDIuNjk2LTguODE3aC0xNi4yMjZsMi41MjgtOC4wNjVoMTYuMTc3bDMtOS45NDZoLTQ4LjIyOGwtMi45NTEgOS45NDZ6bS0xNDYuMzcxIDM0LjE2NGw0LjA0NS0xMy40NTZoMTYuNjI0bDMuMDM4LTEwLjAwOGgtMTYuNjRsMi41NC04LjI4M2gxNi4yNjFsMy4wMTMtOS42OTFoLTQwLjY4NmwtMi45NTEgOS42OTFoOS4yNDNsLTIuNDY2IDguMjgzaC05LjI2OGwtMy4wNzIgMTAuMTc5aDkuMjRsLTUuMzkxIDE3LjgwM2MtLjcyOCAyLjM1Ny4zNDIgMy4yNTUgMS4wMiA0LjM1LjY5MyAxLjA2NiAxLjM5NiAxLjc3MiAyLjk3NSAyLjE3MyAxLjYyOS4zNjQgMi43NDUuNTgxIDQuMjYzLjU4MWgxOC43NDJsMy4zMzktMTEuMDg3LTguMzA4IDEuMTQxYy0xLjYwNCAwLTYuMDQ3LS4xOTMtNS41NjItMS42NzZ6bTEuOTA3LTY0LjQxN2wtNC4yMTMgNy42MTRjLS45MDIgMS42NjMtMS43MTMgMi42OTYtMi40NDQgMy4xNzEtLjY0NC40MDEtMS45MTguNTY5LTMuNzY1LjU2OWgtMi4xOThsLTIuOTM4IDkuNzQxaDcuM2MzLjUxIDAgNi4yMDYtMS4yODcgNy40OTMtMS45MzEgMS4zODQtLjc0IDEuNzQ3LS4zMTcgMi44MTctMS4zNDlsMi40NjYtMi4xMzZoMjIuNzk2bDMuMDI1LTEwLjE0MmgtMTYuNjg3bDIuOTEzLTUuNTM3aC0xNi41NjV6bTMzLjY1NCA2NC42MTJjLS4zODktLjU2LS4xMDktMS41NDUuNDg1LTMuNTk3bDYuMjMxLTIwLjYyM2gyMi4xNjVjMy4yMy0uMDQ3IDUuNTYyLS4wODQgNy4wNzktLjE5MyAxLjYyOS0uMTcxIDMuNDAxLS43NTIgNS4zMzItMS43OTcgMS45OTMtMS4wOTQgMy4wMTMtMi4yNDggMy44NzQtMy41NzIuOTYxLTEuMzIxIDIuNTAzLTQuMjEzIDMuODI3LTguNjcxbDcuODMyLTI2LjA5OC0yMy4wMDEuMTM0cy03LjA4MyAxLjA0NS0xMC4yMDEgMi4xOThjLTMuMTQ2IDEuMjg3LTcuNjQyIDQuODgxLTcuNjQyIDQuODgxbDIuMDc3LTcuMTU0aC0xNC4yMDlsLTE5Ljg5MiA2NS45NzJjLS43MDYgMi41NjItMS4xNzggNC40MjEtMS4yODcgNS41MzctLjAzNyAxLjIwMyAxLjUxNyAyLjM5NCAyLjUyNSAzLjI5MyAxLjE5MS44OTkgMi45NTEuNzUyIDQuNjM5Ljg5OSAxLjc3NS4xMzQgNC4zLjIxOCA3Ljc4NS4yMThoMTAuOTE5bDMuMzUyLTExLjMxNy05Ljc3NS45MjNjLTEuMDQ1IDAtMS44LS41Ni0yLjExNC0xLjAzMnptMTAuNzM2LTM4LjE0OWgyMy4yODFsLTEuNDggNC42MzljLS4yMDguMTA5LS43MDYtLjIzLTMuMDc1LjA1aC0yMC4xNTlsMS40MzMtNC42ODl6bTQuNjY0LTE1LjU1OGgyMy40NzdsLTEuNjg4IDUuNTg3cy0xMS4wNjUtLjEwOS0xMi44MzcuMjE4Yy03Ljc5OCAxLjM0OS0xMi4zNTIgNS41MTYtMTIuMzUyIDUuNTE2bDMuNDAxLTExLjMyem0xNy42NTggMzUuNzMxYy0uMTkzLjY5My0uNDk3IDEuMTE2LS45MjMgMS40MzMtLjQ3My4zMDUtMS4yMzcuNDE0LTIuMzc4LjQxNGgtMy4zMTdsLjE5Ni01LjY0OWgtMTMuNzk4bC0uNTYgMjcuNjE4Yy0uMDIyIDEuOTkzLjE3MSAzLjE0NiAxLjYyOSA0LjA3IDEuNDU4IDEuMTUzIDUuOTUxIDEuMyAxMS45OTggMS4zaDguNjQ2bDMuMTIyLTEwLjMzOC03LjUyNy40MTQtMi41MDMuMTQ2Yy0uMzQyLS4xNDYtLjY2OC0uMjgtMS4wMzItLjY0NC0uMzE3LS4zMTQtLjg1Mi0uMTIxLS43NjUtMi4xMTRsLjA1OS03LjA3OSA3Ljg5NC0uMzI2YzQuMjYzIDAgNi4wODUtMS4zODcgNy42MzktMi43MDggMS40ODMtMS4yNjUgMS45NjgtMi43MiAyLjUyOC00LjY4OWwxLjMyNC02LjI2OGgtMTAuODQ4bC0xLjM4NCA0LjQyMXoiIGZpbGw9IiNGRUZFRkUiLz48L3N2Zz4=");
        }
        .rccs__card--visa .rccs__card__background, .rccs__card--visaelectron .rccs__card__background {
          background: linear-gradient(25deg, #0f509e, #1399cd);
        }
        .rccs__card--visa .rccs__issuer, .rccs__card--visaelectron .rccs__issuer {
          background-size: 75%;
        }
        .rccs__card--visa .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSIxNjZweCIgdmlld0JveD0iMCAwIDUxMiAxNjYiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPiAgICA8Zz4gICAgICAgIDxwYXRoIGQ9Ik0yNjQuNzk0MTg3LDExMi40Nzk0OTEgQzI2NC41MDIwNzIsODkuNDQ4NTYxNiAyODUuMzE5MDgsNzYuNTk1NTE5OCAzMDEuMDAxMDIxLDY4Ljk1NDQxNzIgQzMxNy4xMTM0NDcsNjEuMTEzNDQ2NiAzMjIuNTI1MjU0LDU2LjA4NjAwMDggMzIyLjQ2Mzc1Niw0OS4wNzUyNTA3IEMzMjIuMzQwNzYsMzguMzQzODgzMyAzMDkuNjEwNzE0LDMzLjYwODU1MiAyOTcuNjk1NTE0LDMzLjQyNDA1ODYgQzI3Ni45MDkyNTUsMzMuMTAxMTk1MSAyNjQuODI0OTM1LDM5LjAzNTczMzYgMjU1LjIxNTkwMyw0My41MjUwNzM2IEwyNDcuNzI4NTQ1LDguNDg2Njk3NSBDMjU3LjM2ODMyNiw0LjA0MzQ4MDg3IDI3NS4yMTgwNjUsMC4xNjkxMTg5NzIgMjkzLjcyODkwNSwtMS40MjEwODU0N2UtMTQgQzMzNy4xNzcxMDYsLTEuNDIxMDg1NDdlLTE0IDM2NS42MDQ0NjgsMjEuNDQ3MzYwNSAzNjUuNzU4MjEzLDU0LjcwMjMwMDIgQzM2NS45MjczMzIsOTYuOTA1MTcwOSAzMDcuMzgxNDE5LDk5LjI0MjA4NzYgMzA3Ljc4MTE1NCwxMTguMTA2NTQgQzMwNy45MTk1MjQsMTIzLjgyNTgzNiAzMTMuMzc3NDU1LDEyOS45Mjk0OTQgMzI1LjMzODc3OCwxMzEuNDgyMzEzIEMzMzEuMjU3OTQyLDEzMi4yNjY0MSAzNDcuNjAwOTg1LDEzMi44NjYwMTQgMzY2LjEyNzIsMTI0LjMzMzE5MyBMMzczLjM5OTMxNSwxNTguMjMzODYgQzM2My40MzY2NywxNjEuODYyMjMgMzUwLjYyOTc1MiwxNjUuMzM2ODU3IDMzNC42ODY0NDUsMTY1LjMzNjg1NyBDMjkzLjc5MDQwMywxNjUuMzM2ODU3IDI2NS4wMjQ4MDMsMTQzLjU5NzM4MiAyNjQuNzk0MTg3LDExMi40Nzk0OTEgTTQ0My4yNzYyLDE2Mi40MTU3MTEgQzQzNS4zNDI5ODIsMTYyLjQxNTcxMSA0MjguNjU1MDk2LDE1Ny43ODgwMDEgNDI1LjY3MjQ1MiwxNTAuNjg1MDA0IEwzNjMuNjA1Nzg5LDIuNDkwNjYxMjIgTDQwNy4wMjMyNDIsMi40OTA2NjEyMiBMNDE1LjY2MzY4NCwyNi4zNjcxODUyIEw0NjguNzIwOTE4LDI2LjM2NzE4NTIgTDQ3My43MzI5ODksMi40OTA2NjEyMiBMNTEyLDIuNDkwNjYxMjIgTDQ3OC42MDY2OSwxNjIuNDE1NzExIEw0NDMuMjc2MiwxNjIuNDE1NzExIE00NDkuMzQ5MTA4LDExOS4yMTM1MDEgTDQ2MS44NzkyODcsNTkuMTYwODkxMiBMNDI3LjU2MzUxLDU5LjE2MDg5MTIgTDQ0OS4zNDkxMDgsMTE5LjIxMzUwMSBNMjEyLjE1MjA2MywxNjIuNDE1NzExIEwxNzcuOTI4NTMzLDIuNDkwNjYxMjIgTDIxOS4zMDExODMsMi40OTA2NjEyMiBMMjUzLjUwOTMzOSwxNjIuNDE1NzExIEwyMTIuMTUyMDYzLDE2Mi40MTU3MTEgTTE1MC45NDYzNywxNjIuNDE1NzExIEwxMDcuODgyNTMsNTMuNTY0NTkwNyBMOTAuNDYzMjc1NSwxNDYuMTE4NzkyIEM4OC40MTg0NzM0LDE1Ni40NTA0MjMgODAuMzQ2ODg2MSwxNjIuNDE1NzExIDcxLjM4MzU4MDYsMTYyLjQxNTcxMSBMMC45ODM5NjQ5MjcsMTYyLjQxNTcxMSBMMCwxNTcuNzcyNjI2IEMxNC40NTE5ODQ5LDE1NC42MzYyMzggMzAuODcxODk5NiwxNDkuNTc4MDQzIDQwLjgxOTE3LDE0NC4xNjYyMzYgQzQ2LjkwNzQ1MywxNDAuODYwNzI5IDQ4LjY0NDc2NjEsMTM3Ljk3MDMzMiA1MC42NDM0NDQ4LDEzMC4xMTM5ODcgTDgzLjYzNzAxODgsMi40OTA2NjEyMiBMMTI3LjM2MTk2LDIuNDkwNjYxMjIgTDE5NC4zOTQ1NzEsMTYyLjQxNTcxMSBMMTUwLjk0NjM3LDE2Mi40MTU3MTEiIGZpbGw9IiNGRkZGRkYiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI1Ni4wMDAwMDAsIDgyLjY2ODQyOCkgc2NhbGUoMSwgLTEpIHRyYW5zbGF0ZSgtMjU2LjAwMDAwMCwgLTgyLjY2ODQyOCkgIj48L3BhdGg+ICAgIDwvZz48L3N2Zz4=");
        }
        .rccs__card--visaelectron .rccs__issuer {
          background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSIyMjhweCIgdmlld0JveD0iMCAwIDUxMiAyMjgiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQiPiAgICA8Zz4gICAgICAgIDxwYXRoIGQ9Ik0xOTQuMzkzMDEyLDIuOTE5MzQ0MTMgTDEyNy4zNjI1MTcsMTYyLjgzNTk4MSBMODMuNjI3OTMwNywxNjIuODM1OTgxIEw1MC42MzkwMTUyLDM1LjIwODY2MzIgQzQ4LjY0MTU2OTIsMjcuMzY1OTkwNyA0Ni45MDIzODU1LDI0LjQ3OTMzOCA0MC44MTUyNDI0LDIxLjE2NDQyNzYgQzMwLjg2NzI0MjIsMTUuNzYwNTM1MyAxNC40Mzk4MDE4LDEwLjcwNjQ0MTIgMCw3LjU2MTUyNjI4IEwwLjk3NDIwNDQyNCwyLjkxOTM0NDEzIEw3MS4zODE3MjM1LDIuOTE5MzQ0MTMgQzgwLjM0MjQ0MjcsMi45MTkzNDQxMyA4OC40MTA2ODYsOC44ODU1Mjg5NCA5MC40NjA0MzgzLDE5LjIxNjAxODggTDEwNy44OTE1MDUsMTExLjc2MjE3IEwxNTAuOTIzMjI2LDIuOTE5MzQ0MTMgTDE5NC4zOTMwMTIsMi45MTkzNDQxMyBaIE0yNTMuNTE1NDUyLDIuOTE5MzQ0MTMgTDIxOS4zMDcxNDYsMTYyLjgzNTk4MSBMMTc3LjkzMjg4LDE2Mi44MzU5ODEgTDIxMi4xNDExODYsMi45MTkzNDQxMyBMMjUzLjUxNTQ1MiwyLjkxOTM0NDEzIFogTTMwNy43Nzk5NDYsNDcuMjI5Mjk5NyBDMzA3LjkyMDUxOSw0MS40OTg0OTMxIDMxMy4zNzk5ODcsMzUuMzk1MDA0MyAzMjUuMzMxOTcxLDMzLjgzNTYyMzQgQzMzMS4yNTIzODgsMzMuMDcwNjQ0MSAzNDcuNjA3OTA3LDMyLjQ1OTMxNDUgMzY2LjEzNDEzNyw0MC45OTUwNDUyIEwzNzMuMzgxODI2LDcuMDY0NjE2NjQgQzM2My40MjcyODcsMy40NjUyOTA5IDM1MC42MTg3ODcsMCAzMzQuNjg0OTg3LDAgQzI5My43ODQ3NDcsMCAyNjUuMDEzMDI1LDIxLjcyMzQ1MSAyNjQuNzg0MTg2LDUyLjg1NTQ5MzcgQzI2NC41MTYxMTYsNzUuODgzMzMyNSAyODUuMzM0MDE0LDg4LjcxNzk4NTQgMzAwLjk4OTkzNyw5Ni4zODczOTM0IEMzMTcuMTE2NjE2LDEwNC4yMjAyNTggMzIyLjUyMzc3OCwxMDkuMjU0NzM4IDMyMi40NDg1ODgsMTE2LjI1Mzk3MSBDMzIyLjMzNDE2OCwxMjYuOTgzMjk3IDMwOS41NzE0MzYsMTMxLjczMzM2MSAyOTcuNzA0NDQ5LDEzMS45MDk4OTQgQzI3Ni44OTYzNTgsMTMyLjI0MDA3OCAyNjQuODMzMjIzLDEyNi4yODY5NjkgMjU1LjIxMjEzNywxMjEuODA0OTc1IEwyNDcuNzAyOTE3LDE1Ni44NTk5ODkgQzI1Ny4zODI4NDcsMTYxLjI5NjIxNSAyNzUuMjIyNTU3LDE2NS4xNTA1MzQgMjkzLjY5OTc1LDE2NS4zNDY2ODIgQzMzNy4xNzkzNDMsMTY1LjM0NjY4MiAzNjUuNjE3NjEzLDE0My44NzE2ODYgMzY1Ljc0NTEwOSwxMTAuNjI0NTA4IEMzNjUuOTE4Mzc0LDY4LjQyNjQxODggMzA3LjM5NzQ1Niw2Ni4wODU3MTI4IDMwNy43Nzk5NDYsNDcuMjI5Mjk5NyBaIE00NzMuNzQ3NzY1LDE2Mi44MzU5ODEgTDUxMiwxNjIuODM1OTgxIEw0NzguNTc5NTU4LDIuOTE5MzQ0MTMgTDQ0My4yODU4OTcsMi45MTkzNDQxMyBDNDM1LjMyNTUzNSwyLjkxOTM0NDEzIDQyOC42MzY4Nyw3LjUzODY0MjI4IDQyNS42Njg0ODksMTQuNjQyNDg4NiBMMzYzLjU4NzQ3NSwxNjIuODM1OTgxIEw0MDcuMDM0Mzc3LDE2Mi44MzU5ODEgTDQxNS42NTE4MzYsMTM4Ljk0ODM1OCBMNDY4LjczMjkwMSwxMzguOTQ4MzU4IEw0NzMuNzQ3NzY1LDE2Mi44MzU5ODEgWiBNNDI3LjU2NDU5MSwxMDYuMTc4NDc1IEw0NDkuMzUwMTU2LDQ2LjEyNDMyOTYgTDQ2MS44NjQ0MzMsMTA2LjE3ODQ3NSBMNDI3LjU2NDU5MSwxMDYuMTc4NDc1IFogTTMwNi40NjQ3NywyMDYuODU0MzI5IEwyODkuNjU0ODQsMjA2Ljg1NDMyOSBMMjg5LjY1NDg0LDIyMi41MDA0NDQgTDMwOC40NTg5NDcsMjIyLjUwMDQ0NCBMMzA4LjQ1ODk0NywyMjcuMjA0NzQgTDI4My45ODk0MTYsMjI3LjIwNDc0IEwyODMuOTg5NDE2LDE4My43OTcwNjggTDMwNy40OTQ1NSwxODMuNzk3MDY4IEwzMDcuNDk0NTUsMTg4LjUwMTM2NCBMMjg5LjY1NDg0LDE4OC41MDEzNjQgTDI4OS42NTQ4NCwyMDIuMjE1NDE2IEwzMDYuNDY0NzcsMjAyLjIxNTQxNiBMMzA2LjQ2NDc3LDIwNi44NTQzMjkgWiBNMzE3LjE0MzQyMywxODEuNDgxMjA4IEwzMjIuODA4ODQ3LDE4MS40ODEyMDggTDMyMi44MDg4NDcsMjI3LjIwMzQzMyBMMzE3LjE0MzQyMywyMjcuMjAzNDMzIEwzMTcuMTQzNDIzLDE4MS40ODEyMDggWiBNMzQ3Ljc4NzM4MywyMjMuNDY5MDkxIEMzNTEuODQ3NjU4LDIyMy40NjkwOTEgMzU0LjM1ODM1OSwyMjIuNzU5Njg3IDM1Ni40MjExODgsMjIxLjg2MDY3MyBMMzU3LjQ1MDk2OCwyMjUuOTE3Njc5IEMzNTUuNDUzNTIyLDIyNi44MTY2OTMgMzUxLjk3ODQyNCwyMjcuOTExODU2IDM0Ny4wMTU4NjYsMjI3LjkxMTg1NiBDMzM3LjQyNDIwMiwyMjcuOTExODU2IDMzMS42OTMzOTUsMjIxLjUzNzAyOCAzMzEuNjkzMzk1LDIxMi4xMzQ5NzUgQzMzMS42OTMzOTUsMjAyLjczMjkyMSAzMzcuMjI4MDUzLDE5NS4zOTA0MjggMzQ2LjMwOTczMSwxOTUuMzkwNDI4IEMzNTYuNTQ4Njg1LDE5NS4zOTA0MjggMzU5LjE5MDE1MiwyMDQuMjc5MjI2IDM1OS4xOTAxNTIsMjEwLjAxMDAzMiBDMzU5LjE5MDE1MiwyMTEuMTY3MzA5IDM1OS4xMjQ3NjksMjEyLjAwNDIwOSAzNTguOTk0MDAzLDIxMi42NDgyMyBMMzM3LjEwMDU1NywyMTIuNjQ4MjMgQzMzNy4yMjgwNTMsMjIwLjMxNDM2OSAzNDIuMDU5ODQ2LDIyMy40NjkwOTEgMzQ3Ljc4NzM4MywyMjMuNDY5MDkxIFogTTM1My43MTQzMzgsMjA4LjU5MTIyNCBDMzUzLjc3OTcyMSwyMDUuMDUwNzQzIDM1Mi4yMzM0MTcsMTk5LjQ0NzQzMyAzNDUuODU4NTg5LDE5OS40NDc0MzMgQzM0MC4wNjI0LDE5OS40NDc0MzMgMzM3LjYxMzgxMiwyMDQuNjY0OTg0IDMzNy4xNjI2NywyMDguNTkxMjI0IEwzNTMuNzE0MzM4LDIwOC41OTEyMjQgWiBNMzkwLjAyMjc0MSwyMjYuMTA4NTk3IEMzODguNTQxODE5LDIyNi44MTgwMDEgMzg1LjI1NjMzMSwyMjcuOTEzMTYzIDM4MS4wNzE4MjksMjI3LjkxMzE2MyBDMzcxLjY2OTc3NiwyMjcuOTEzMTYzIDM2NS41NTMyMSwyMjEuNTM4MzM2IDM2NS41NTMyMSwyMTIuMDA1NTE3IEMzNjUuNTUzMjEsMjAyLjQxMDU4NCAzNzIuMTE3NjQ4LDE5NS4zOTE3MzUgMzgyLjI5NDQ4OCwxOTUuMzkxNzM1IEMzODUuNjQyMDksMTk1LjM5MTczNSAzODguNjAzOTMzLDE5Ni4yMjUzNjcgMzkwLjE1MDIzOCwxOTcuMDY1NTM2IEwzODguODY1NDY1LDIwMS4zODA4MDQgQzM4Ny41MDg3NywyMDAuNjcxNCAzODUuMzgzODI4LDE5OS44OTY2MTMgMzgyLjI5NDQ4OCwxOTkuODk2NjEzIEMzNzUuMTQ0ODc0LDE5OS44OTY2MTMgMzcxLjI4NDAxNywyMDUuMjQxNjYxIDM3MS4yODQwMTcsMjExLjY4NTE0MSBDMzcxLjI4NDAxNywyMTguODk2ODY5IDM3NS45MTk2NjEsMjIzLjMzOTYzMyAzODIuMTAxNjA5LDIyMy4zMzk2MzMgQzM4NS4zMjE3MTQsMjIzLjMzOTYzMyAzODcuNDQ2NjU3LDIyMi41NjgxMTYgMzg5LjA1NTA3NSwyMjEuODU4NzEyIEwzOTAuMDIyNzQxLDIyNi4xMDg1OTcgWiBNNDA1LjkxODI5MiwxODguNTY0NDU4IEw0MDUuOTE4MjkyLDE5Ni4wMzQ0NDkgTDQxNC4wMjkwMzQsMTk2LjAzNDQ0OSBMNDE0LjAyOTAzNCwyMDAuMzQ2NDQ3IEw0MDUuOTE4MjkyLDIwMC4zNDY0NDcgTDQwNS45MTgyOTIsMjE3LjE1NjM3NyBDNDA1LjkxODI5MiwyMjEuMDIzNzczIDQwNy4wMTM0NTUsMjIzLjIxMDgyOSA0MTAuMTY4MTc3LDIyMy4yMTA4MjkgQzQxMS43MTEyMTIsMjIzLjIxMDgyOSA0MTIuNjE2NzY1LDIyMy4wODMzMzIgNDEzLjQ1MzY2NSwyMjIuODIxODAxIEw0MTMuNzExOTI3LDIyNy4xNDAzMzggQzQxMi42MTY3NjUsMjI3LjUyNjA5NyA0MTAuODc3NTgxLDIyNy45MTE4NTYgNDA4LjY4NzI1NSwyMjcuOTExODU2IEM0MDYuMDQ1Nzg4LDIyNy45MTE4NTYgNDAzLjkyMDg0NiwyMjcuMDA5NTcyIDQwMi41NzA2OSwyMjUuNTMxOTIgQzQwMS4wMjQzODYsMjIzLjc4OTQ2NyA0MDAuMzgwMzY1LDIyMS4wMjM3NzMgNDAwLjM4MDM2NSwyMTcuMzUyNTI2IEw0MDAuMzgwMzY1LDIwMC4zNDY0NDcgTDM5NS41NDg1NzIsMjAwLjM0NjQ0NyBMMzk1LjU0ODU3MiwxOTYuMDM0NDQ5IEw0MDAuMzgwMzY1LDE5Ni4wMzQ0NDkgTDQwMC4zODAzNjUsMTkwLjIzODI1OSBMNDA1LjkxODI5MiwxODguNTY0NDU4IFogTTQyMS45NDQ5MzUsMjA1Ljc1OTQ5MyBDNDIxLjk0NDkzNSwyMDIuMDg4MjQ2IDQyMS44NzYyODMsMTk4LjkzMzUyNCA0MjEuNjg2NjczLDE5Ni4wMzM3OTUgTDQyNi42NDU5NjIsMTk2LjAzMzc5NSBMNDI2LjkwNDIyNCwyMDIuMjE1NzQzIEw0MjcuMDkzODM1LDIwMi4yMTU3NDMgQzQyOC41MTI2NDIsMTk4LjAzMTI0MSA0MzEuOTg3NzQxLDE5NS4zODk3NzQgNDM1Ljc4NjQ4NCwxOTUuMzg5Nzc0IEM0MzYuMzY4MzkxLDE5NS4zODk3NzQgNDM2LjgxOTUzMywxOTUuNDU1MTU3IDQzNy4zMzYwNTgsMTk1LjUxNzI3IEw0MzcuMzM2MDU4LDIwMC44NjU1ODcgQzQzNi43NTQxNSwyMDAuNzM0ODIxIDQzNi4xNzg3ODEsMjAwLjczNDgyMSA0MzUuNDAwNzI1LDIwMC43MzQ4MjEgQzQzMS40MTIzNzIsMjAwLjczNDgyMSA0MjguNTc0NzU2LDIwMy42OTY2NjQgNDI3LjgwMzIzOCwyMDcuOTQ5ODE5IEM0MjcuNjc1NzQyLDIwOC43MjEzMzYgNDI3LjYxMDM1OSwyMDkuNjg1NzMzIDQyNy42MTAzNTksMjEwLjU5MTI4NiBMNDI3LjYxMDM1OSwyMjcuMjA1MDY3IEw0MjEuOTQ0OTM1LDIyNy4yMDUwNjcgTDQyMS45NDQ5MzUsMjA1Ljc1OTQ5MyBaIE00NzIuMTY2NDgxLDIxMS4zNjExNjkgQzQ3Mi4xNjY0ODEsMjIyLjg4ODE2NSA0NjQuMTE0NTgzLDIyNy45MTI4MzYgNDU2LjY0NDU5MywyMjcuOTEyODM2IEM0NDguMjcyMzIsMjI3LjkxMjgzNiA0NDEuNzA3ODgyLDIyMS43Mjc2MTkgNDQxLjcwNzg4MiwyMTEuODc3NjkzIEM0NDEuNzA3ODgyLDIwMS41MDc5NzQgNDQ4LjU5NTk2NSwxOTUuMzkxNDA4IDQ1Ny4xNjExMTgsMTk1LjM5MTQwOCBDNDY2LjExMjAyOSwxOTUuMzkxNDA4IDQ3Mi4xNjY0ODEsMjAxLjg5MzczMiA0NzIuMTY2NDgxLDIxMS4zNjExNjkgWiBNNDQ3LjUwMDgwMiwyMTEuNjg0ODE0IEM0NDcuNTAwODAyLDIxOC41MTA3ODMgNDUxLjM2NDkyOCwyMjMuNjYyOTUxIDQ1Ni45MDI4NTUsMjIzLjY2Mjk1MSBDNDYyLjMxMzI4NiwyMjMuNjYyOTUxIDQ2Ni4zNjcwMjMsMjE4LjU3Mjg5NyA0NjYuMzY3MDIzLDIxMS41NTQwNDggQzQ2Ni4zNjcwMjMsMjA2LjI3NDM4MyA0NjMuNzI4ODI1LDE5OS42MzgwMjQgNDU3LjAzMDM1MiwxOTkuNjM4MDI0IEM0NTAuNDAwNTMxLDE5OS42MzgwMjQgNDQ3LjUwMDgwMiwyMDUuODIzMjQyIDQ0Ny41MDA4MDIsMjExLjY4NDgxNCBaIE00ODAuODQ5OTc3LDIwNC40Njk0OSBDNDgwLjg0OTk3NywyMDEuMTg3MjcxIDQ4MC43ODEzMjUsMTk4LjYxMTE4NyA0ODAuNTkxNzE1LDE5Ni4wMzUxMDIgTDQ4NS42MTMxMTcsMTk2LjAzNTEwMiBMNDg1LjkzNjc2MywyMDEuMTg3MjcxIEw0ODYuMDY0MjU5LDIwMS4xODcyNzEgQzQ4Ny42MTA1NjMsMTk4LjI4NzU0MiA0OTEuMjE2NDI3LDE5NS4zOTEwODEgNDk2LjM2ODU5NiwxOTUuMzkxMDgxIEM1MDAuNjgwNTk1LDE5NS4zOTEwODEgNTA3LjM3OTA2NywxOTcuOTY3MTY2IDUwNy4zNzkwNjcsMjA4LjY1Mzk5MiBMNTA3LjM3OTA2NywyMjcuMjAzMTA2IEw1MDEuNzEzNjQ0LDIyNy4yMDMxMDYgTDUwMS43MTM2NDQsMjA5LjIzNTg5OSBDNTAxLjcxMzY0NCwyMDQuMjE0NDk3IDQ5OS44NDY5NjMsMjAwLjAyNjcyNSA0OTQuNTAxOTE2LDIwMC4wMjY3MjUgQzQ5MC44MzA2NjksMjAwLjAyNjcyNSA0ODcuOTMwOTM5LDIwMi42NjgxOTIgNDg2LjkwNDQyOSwyMDUuODIyOTE1IEM0ODYuNjQyODk3LDIwNi41MzIzMTkgNDg2LjUxNTQwMSwyMDcuNDk2NzE2IDQ4Ni41MTU0MDEsMjA4LjQ2NDM4MiBMNDg2LjUxNTQwMSwyMjcuMjAzMTA2IEw0ODAuODQ5OTc3LDIyNy4yMDMxMDYgTDQ4MC44NDk5NzcsMjA0LjQ2OTQ5IFoiIGZpbGw9IiNGRkZGRkYiPjwvcGF0aD4gICAgPC9nPjwvc3ZnPg==");
        }
        .rccs__number {
          clear: both;
          font-family: Consolas, Courier, monospace;
          font-size: 20px;
          left: 10%;
          position: absolute;
          top: 45%;
        }
        .rccs__number.rccs__number--large {
          font-size: 17px;
        }
        .rccs__name {
          bottom: 15%;
          font-family: Consolas, Courier, monospace;
          font-size: 17px;
          left: 10%;
          line-height: 1;
          overflow: hidden;
          position: absolute;
          text-align: left;
          text-overflow: ellipsis;
          text-transform: uppercase;
          width: 60%;
        }
        .rccs__expiry {
          bottom: 15%;
          font-size: 0;
          line-height: 1;
          position: absolute;
          right: 10%;
        }
        .rccs__expiry > * {
          vertical-align: middle;
        }
        .rccs__expiry__valid {
          font-size: 10px;
          margin-bottom: 5px;
        }
        .rccs__expiry__value {
          font-family: Consolas, Courier, monospace;
          font-size: 16px;
        }
        .rccs__number, .rccs__name, .rccs__expiry, .rccs__cvc {
          opacity: 0.5;
          transition: opacity 0.3s;
        }
        .rccs__chip {
          background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9Ijc2IiB2aWV3Qm94PSIwIDAgMTAwIDc2IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIj48ZGVmcz48bGluZWFyR3JhZGllbnQgeDE9IjEwMCUiIHkxPSIwJSIgeDI9IjAlIiB5Mj0iMTAwJSIgaWQ9ImEiPjxzdG9wIHN0b3AtY29sb3I9IiNGM0QwOEYiIG9mZnNldD0iMCUiLz48c3RvcCBzdG9wLWNvbG9yPSIjRkFENzY2IiBvZmZzZXQ9IjEwMCUiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cGF0aCBkPSJNOTIuNzI3IDc1LjQ1NWgtODUuNDU1Yy00IDAtNy4yNzMtMy4yNzMtNy4yNzMtNy4yNzN2LTYwLjkwOWMwLTQgMy4yNzMtNy4yNzMgNy4yNzMtNy4yNzNoODUuNDU1YzQgMCA3LjI3MyAzLjI3MyA3LjI3MyA3LjI3M3Y2MC45MDljMCA0LTMuMjczIDcuMjczLTcuMjczIDcuMjczIiBmaWxsPSJ1cmwoI2EpIi8+PHBhdGggZD0iTTcyLjEyMyAyOC40ODVoMjcuODc4di0xLjgxOGgtMjkuNjQ4Yy0uOTY1IDAtMS44MzIuNjAxLTIuMTcyIDEuNTA0LTIuMjg3IDYuMDcyLTIuNDMzIDEyLjU5NC0uNDM4IDE5Ljg0Mi40NTUgMS42NTQuNDM1IDMuNC0uMSA1LjAzLTIuMDM2IDYuMTk1LTcuNzc5IDE5Ljk4OC0xOC41NTEgMTkuOTg4LTExLjAwOCAwLTE2LjA5Ni0xNS42OTktMTcuMzM0LTIxLjk1Mi0uMTU1LS43ODQtLjEyMi0xLjU5Mi4xMDctMi4zNTcgMS42OTUtNS42NDggMi4wOTQtMTAuNjQtLjAxNi0xOS41OS0uMjA1LS44Ny0uMTgyLTEuNzgzLjA0OC0yLjY0NiA0LjQ4LTE2Ljc1NSAxMi44ODItMjAuMTQ3IDEyLjk2NS0yMC4xNzkuMzU2LS4xMzIuNTkzLS40NzIuNTkzLS44NTJ2LTUuNDU1aC0xLjgxOHYzLjc3NmMwIC42NS0uMzMyIDEuMjUyLS44ODQgMS41OTYtMi44MDMgMS43NDItOC45MDQgNi45MzYtMTIuNTU3IDIwLjQ1Ni0uMTguNjY4LS43ODEgMS4xMzYtMS40NzMgMS4xMzNsLTI4LjcyMi0uMTM5djEuODE4bDI3LjQxNi4xMzNjMS40NjguMDA3IDIuNzM1IDEuMDQxIDMuMDM3IDIuNDc4IDEuNDE2IDYuNzQxIDEuMjE5IDExLjAzOS4wODIgMTUuNDU4LS4zMTYgMS4yMy0xLjQyIDIuMDk2LTIuNjkgMi4xMDlsLTI3Ljg0NC4yN3YxLjgxOWwyOC42MDUtLjI3OGMuNjkzLS4wMDcgMS4yOTYuNDczIDEuNDM1IDEuMTUyIDEuNDQyIDcuMDQxIDYuODg3IDIzLjA3IDE5LjA1IDIzLjA3IDYuMzY4IDAgMTIuMDYyLTQuMjUgMTYuNDY3LTEyLjI5IDIuNjQ0LTQuODI4IDQuMDY3LTkuNTkxIDQuNTQxLTExLjM0NmgyOS45MDF2LTEuODE4aC0yOC4wMTZjLTEuMTU4IDAtMi4xODMtLjc3Mi0yLjQ4OS0xLjg4OS0xLjY5Mi02LjE2NC0xLjc2MS0xMS43NTUtLjItMTYuOTU5LjM3MS0xLjIzNSAxLjUzOC0yLjA2MSAyLjgyNy0yLjA2MXptLTE3LjE1LTIxLjkxNGMuMDQ1LjAyMiA0LjUxOSAyLjMyMiA5LjI1MyAxMC4wNDEuMTcyLjI4LjQ3LjQzNC43NzYuNDM0LjE5OCAwIC4zOTktLjA2NC41NzEtLjIwMi4zNjUtLjI5Mi40MTYtLjgzNy4xNzItMS4yMzUtMy41Ny01LjgwNS03LjAyNC04LjcxLTguNzc1LTkuOTMxLS40My0uMjk5LS42OC0uNzkyLS42OC0xLjMxNXYtNC4zNjNoLTEuODE4djUuNzU4YzAgLjM0NS4xOTUuNjU5LjUwMi44MTN6IiBmaWxsPSIjMEMwMjAwIi8+PC9zdmc+");
          background-repeat: no-repeat;
          background-size: contain;
          height: 26.36363636px;
          left: 10%;
          position: absolute;
          top: 10%;
          width: 41.42857143px;
        }
        .rccs__issuer {
          background-position: top right;
          background-repeat: no-repeat;
          background-size: contain;
          height: 23%;
          position: absolute;
          right: 10%;
          top: 10%;
          width: 40%;
        }
        .rccs__stripe {
          background-color: #2a1d16;
          height: 22%;
          left: 0;
          position: absolute;
          top: 9%;
          width: 100%;
        }
        .rccs__signature {
          background: repeating-linear-gradient(0.1deg, #fff 20%, #fff 40%, #fea 40%, #fea 44%, #fff 44%);
          height: 18%;
          left: 5%;
          position: absolute;
          top: 35%;
          width: 75%;
        }
        .rccs__cvc {
          color: #222;
          font-family: Consolas, Courier, monospace;
          font-size: 14px;
          left: 67%;
          line-height: 1;
          position: absolute;
          top: 42%;
        }
        .rccs__cvc__front {
          font-family: Consolas, Courier, monospace;
          font-size: 11.9px;
          opacity: 0;
          position: absolute;
          right: 10%;
          top: 38%;
          visibility: hidden;
        }
        .rccs--filled {
          opacity: 0.8 !important;
        }
        .rccs--focused {
          font-weight: 700;
          opacity: 1 !important;
        }
    </style>
</head>
<body>

<div id='showcase' class="grommetux-box grommetux-box--direction-column grommetux-box--responsive grommetux-box--pad-none">
    <div class="grommetux-box grommetux-box--direction-column grommetux-box--responsive grommetux-box--pad-none">
        <div class="grommetux-box grommetux-box--direction-column grommetux-box--responsive grommetux-box--pad-none grommetux-hero grommetux-hero--large grommetux-hero--bg-center">
            <style>                
                /* End */

                #content {
                    min-height: 200px !important;
                }
                
                #map-wrap {
                    position: fixed;
                    overflow: hidden;
                    width: 100%; 
                    padding: 0;
                    left: 0;
                    top: 0;
                }
                
                .world-bg {
                    /*background-image: linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
                    background-image: -o-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
                    background-image: -moz-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
                    background-image: -webkit-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);
                    background-image: -ms-linear-gradient(bottom, rgb(69,132,180) 28%, rgb(31,71,120) 64%);

                    background-image: -webkit-gradient(
                        linear,
                        left bottom,
                        left top,
                        color-stop(0.28, rgb(69,132,180)),
                        color-stop(0.64, rgb(31,71,120))
                    );*/
                    
                    /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#1e5799+0,7db9e8+100&0.5+0,0+100 */
                    background: -moz-linear-gradient(top, rgba(30,87,153,0.5) 0%, rgba(125,185,232,0) 100%); /* FF3.6-15 */
                    background: -webkit-linear-gradient(top, rgba(30,87,153,0.5) 0%,rgba(125,185,232,0) 100%); /* Chrome10-25,Safari5.1-6 */
                    background: linear-gradient(to bottom, rgba(30,87,153,0.5) 0%,rgba(125,185,232,0) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#801e5799', endColorstr='#007db9e8',GradientType=0 ); /* IE6-9 */
                    background-size: cover;
                    background-repeat: repeat;
                    background-position-y: -160px;
                }
                
                #map-wrap,
                #vmap { 
                    width: 100%;
                }
                
                #category-menu {
                    margin-top: 100px !important; /* Override */
                }
                
                #hero {
                    position: absolute;
                }
                
                @media screen and (min-width: 768px) {
                    .griddle-container div[class^="col-"] {
                        height: 310px;
                        overflow: hidden;
                    }
                }
                
                @media screen and (min-width: 1200px) {
                    body > .container {
                        /*width: 1280px;*/ /* Shop Danesi */
                        width: 100%;
                    }
                    
                    #map-wrap,
                    #vmap { 
                        width: 100%; 
                        height: 1080px;
                    }
                    
                    #category-menu {
                        margin-top: 1080px !important; /* Override */
                    }
                    
                    #menu {
                        position: relative;
                        top: 40px;
                    }
                }
                
                @media screen and (max-width: 1200px) {
                    #menu {
                        position: relative;
                        top: 100px;
                    }
                }
                
                @media screen and (max-width: 1024px) {
                    #menu {
                        position: relative;
                        top: 100px;
                    }
                }
                
                @media screen and (max-device-width: 1024px) and (orientation: landscape) {
                    .griddle-container div[class^="col-"] {
                        height: 130px;
                    }
                }
                
                @media screen and (max-width: 992px) {
                    #logo img {
                        margin-left: auto;
                        margin-right: auto;
                    }
                    
                    #footer .column {
                        width: '100%';
                    }
                    
                    .pinned {
                        position: static;
                    }
                }
                
                @media screen and (max-width: 768px) {
                    #menu {
                        position: relative;
                        top: 80px;
                    }
                    
                    #logo img {
                        max-height: 60px;
                    }
                    
                    /* Set to 310px for standard grid view, need to adjust now that we're displaying as a list */
                    .griddle-container div[class^="col-"] {
                        height: auto;
                    }
                }
                
                @media only screen and (max-width: 480px) {
                    #footer {
                        display: flex;
                    }

                    footer .row, footer .column {
                        display: flex;
                        flex-direction: column;
                    }
                }
                
                @media screen and (min-width: 768px) and (max-width: 1200px) {
                    #map-wrap,
                    #vmap { 
                        width: 100%; 
                        height: 1390px;
                    }
                    
                    #category-menu {
                        /*margin-top: 540px !important;*/ /* Override */
                        margin-top: 1390px !important; /* Override */
                    }
                    
                    #hero img {
                        position: relative;
                        top: 120px;
                    }
                    
                    .taco-main--header--links {
                        margin-top: 0;
                    }
                }
                
                #hero-background {
                    background-position: 164% 60% !important;
                    background-repeat: repeat !important;
                }
                
                #hero-content-container {
                    position: relative;
                }
                
                #hero-content {
                    position: relative;
                }
                
                #logo img {
                    padding: 1rem 4rem;
                }
                
                .g-infolist-item {
                    text-align: center;
                }
                
                .g-infolist-item .fa {
                    font-size: 4rem;
                }
                
                input[name=react-star-rating] {
                    display: none;
                }
                
                #search input {
                    border: 1px solid grey;
                }
                
                /* Grommet styles are killing my clouds */
                #clouds img {
                    max-width: none !important;
                    font-style: normal !important;
                    vertical-align: baseline !important;
                }
            </style>
            <!--<div id="map-wrap" class='world-bg container-fluid'>
                <div class='row'>
                  <div id='vmap' class='col-sm-12'></div>
                  <div id="hero" class='col-sm-12'>
                      <img src="spa/media/banners/ace-hero.png" />
                  </div>
                </div>
            </div>-->
            
            <div id="background">
                <div id="clouds"></div>
            </div>
            <div class="world" id="world"></div>
            <!--<nav class="meta">
                <a class="demo-link demo-link--current" href="part1.html">Part 1</a>
                <a class="demo-link" href="part2.html">Part 2</a>
                <a class="demo-link" href="index.html">Game</a>
            </nav>-->
            <!--<script src="http://threejs.org/build/three.min.js"></script>
            <script src="http://threejs.org/examples/js/loaders/MTLLoader.js"></script>
            <script src="http://threejs.org/examples/js/loaders/OBJLoader.js"></script>
            <script type="text/javascript" src="spa/js/main_step1.js"/></script>-->
            
            <div id="options">
                <a href="#" id="closeBtn" class="button" >X</a>
                <div id="optionsContent">
                    <p>Move the <b>mouse to rotate</b> around and <b>mouse wheel to zoom</b> in and out. Hit <b>space to generate</b> a new cloud. Works on <b>Firefox</b> (faster if Nightly), <b>Chrome</b> and <b>Safari</b>.</p>
                    <div class="actions" >
                        <a class="button" href="#" id="generateBtn" >Generate clouds</a> <a class="button" href="#" id="showTextureControlsBtn" >Show texture controls</a> <a class="button" href="#" id="fullscreenBtn" >Fullscreen</a> <a class="button" href="#" id="resetBtn" >Exit Flight</a>
                    </div>
                    <div id="textureControls" >
                        <h2>Texture list</h2>
                        <p>Select one or more textures to create clouds. The more the merrier!</p>
                        <ul id="textureList" >
                        </ul>
                    </div>
                    <h2>Presets</h2>
                    <div class="presets" >
                        <a href="#" class="left button" id="cloudsPreset" >Clouds</a>  
                        <a href="#" class="middle button" id="stormPreset" >Storm</a>  
                        <a href="#" class="middle button" id="boomPreset" >Boom</a> 
                        <a href="#" class="right button" id="bayPreset" >Michael Bay</a>
                    </div>
                </div>
            </div>
            
            <script>
            
            function getParameterByName(name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                    results = regex.exec(location.search);
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            var isKosher = getParameterByName( 'metadata' ).indexOf( 'Player' ) === -1;

            (function() {
                var lastTime = 0;
                var vendors = ['ms', 'moz', 'webkit', 'o'];
                for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
                    window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
                    window.cancelRequestAnimationFrame = window[vendors[x]+
                      'CancelRequestAnimationFrame'];
                }

                if (!window.requestAnimationFrame)
                    window.requestAnimationFrame = function(callback, element) {
                        var currTime = new Date().getTime();
                        var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                        var id = window.setTimeout(function() { callback(currTime + timeToCall); }, 
                          timeToCall);
                        lastTime = currTime + timeToCall;
                        return id;
                    };

                if (!window.cancelAnimationFrame)
                    window.cancelAnimationFrame = function(id) {
                        clearTimeout(id);
                    };
            }())

            var layers = [],
                objects = [],
                textures = [],
                
                world = document.getElementById( 'clouds' ),
                viewport = document.getElementById( 'background' ),
                
                d = 0,
                p = 1920 + 960, // Viewport width
                worldXAngle = 0,
                worldYAngle = 0,
                computedWeights = [];
            
            var links = document.querySelectorAll( 'a[rel=external]' );
            for( var j = 0; j < links.length; j++ ) {
                var a = links[ j ];
                a.addEventListener( 'click', function( e ) {
                    window.open( this.href, '_blank' );
                    e.preventDefault();
                }, false );
            }
            
            viewport.style.webkitPerspective = p;
            viewport.style.MozPerspective = p + 'px';
            viewport.style.oPerspective = p;
            viewport.style.perspective = p;
            
            textures = [
                { name: 'white cloud', 	file: 'https://www.clicktorelease.com/code/css3dclouds/cloud.png'		, opacity: 1, weight: 0 },
                { name: 'dark cloud', 	file: 'https://www.clicktorelease.com/code/css3dclouds/darkCloud.png'	, opacity: 1, weight: 0 },
                { name: 'smoke cloud', 	file: 'https://www.clicktorelease.com/code/css3dclouds/smoke.png'		, opacity: 1, weight: 0 },
                { name: 'explosion', 	file: 'https://www.clicktorelease.com/code/css3dclouds/explosion.png'	, opacity: 1, weight: 0 },
                { name: 'explosion 2', 	file: 'https://www.clicktorelease.com/code/css3dclouds/explosion2.png'	, opacity: 1, weight: 0 },
                { name: 'box', 			file: 'https://www.clicktorelease.com/code/css3dclouds/box.png'			, opacity: 1, weight: 0 }
            ];
            
            var el = document.getElementById( 'textureList' );
            for( var j = 0; j < textures.length; j++ ) {
                var li = document.createElement( 'li' );
                var span = document.createElement( 'span' );
                span.textContent = textures[ j ].name;
                var div = document.createElement( 'div' );
                div.className = 'buttons';
                var btnNone = document.createElement( 'a' );
                btnNone.setAttribute( 'id', 'btnNone' + j );
                btnNone.setAttribute( 'href', '#' );
                btnNone.textContent = 'None';
                btnNone.className = 'left button';
                var btnFew = document.createElement( 'a' );
                btnFew.setAttribute( 'id', 'btnFew' + j );
                btnFew.setAttribute( 'href', '#' );
                btnFew.textContent = 'A few';
                btnFew.className = 'middle button';
                var btnNormal = document.createElement( 'a' );
                btnNormal.setAttribute( 'id', 'btnNormal' + j );
                btnNormal.setAttribute( 'href', '#' );
                btnNormal.textContent = 'Some';
                btnNormal.className = 'middle button';
                var btnLot = document.createElement( 'a' );
                btnLot.setAttribute( 'id', 'btnLot' + j );
                btnLot.setAttribute( 'href', '#' );
                btnLot.textContent = 'A lot';
                btnLot.className = 'right button';
                
                ( function( id ) {
                    btnNone.addEventListener( 'click', function() { setTextureUsage( id, 'None' ); } );
                    btnFew.addEventListener( 'click', function() { setTextureUsage( id, 'Few' ); } );
                    btnNormal.addEventListener( 'click', function() { setTextureUsage( id, 'Normal' ); } );
                    btnLot.addEventListener( 'click', function() { setTextureUsage( id, 'Lot' ); } );
                } )( j );
                
                li.appendChild( span );
                div.appendChild( btnNone );
                div.appendChild( btnFew );
                div.appendChild( btnNormal );
                div.appendChild( btnLot );
                li.appendChild( div );
                el.appendChild( li );
                
                setTextureUsage( j, 'None' );
            }
            
            setTextureUsage( 0, 'Lot' );
            generate();
            
            document.getElementById( 'cloudsPreset' ).addEventListener( 'click', function( e ) {
                setTextureUsage( 0, 'Lot' );
                setTextureUsage( 1, 'None' );
                setTextureUsage( 2, 'None' );
                setTextureUsage( 3, 'None' );
                setTextureUsage( 4, 'None' );
                setTextureUsage( 5, 'None' );
                generate();
                e.preventDefault();
            } );
            
            document.getElementById( 'stormPreset' ).addEventListener( 'click', function( e ) {
                setTextureUsage( 0, 'None' );
                setTextureUsage( 1, 'None' );
                setTextureUsage( 2, 'Lot' );
                setTextureUsage( 3, 'None' );
                setTextureUsage( 4, 'None' );
                setTextureUsage( 5, 'None' );
                generate();
                e.preventDefault();
            } );
            
            document.getElementById( 'boomPreset' ).addEventListener( 'click', function( e ) {
                setTextureUsage( 0, 'None' );
                setTextureUsage( 1, 'None' );
                setTextureUsage( 2, 'Lot' );
                setTextureUsage( 3, 'Few' );
                setTextureUsage( 4, 'None' );
                setTextureUsage( 5, 'None' );
                generate();
                e.preventDefault();
            } );
            
            document.getElementById( 'bayPreset' ).addEventListener( 'click', function( e ) {
                setTextureUsage( 0, 'None' );
                setTextureUsage( 1, 'None' );
                setTextureUsage( 2, 'Normal' );
                setTextureUsage( 3, 'Lot' );
                setTextureUsage( 4, 'Lot' );
                setTextureUsage( 5, 'None' );
                generate();
                e.preventDefault();
            } );
            
            function setTextureUsage( id, mode ) {
                var modes = [ 'None', 'Few', 'Normal', 'Lot' ];
                var weights = { 'None': 0, 'Few': .3, 'Normal': .7, 'Lot': 1 };
                for( var j = 0; j < modes.length; j++ ) {
                    var el = document.getElementById( 'btn' + modes[ j ] + id );
                    el.className = el.className.replace( ' active', '' );
                    if( modes[ j ] == mode ) {
                        el.className += ' active';
                        textures[ id ].weight = weights[ mode ];
                    }
                }
            }
            
            var optionsContent = document.getElementById( 'optionsContent' );
            var el = document.getElementById( 'closeBtn' ).addEventListener( 'click', function( e ) {
                if( optionsContent.style.display != 'block' ) {
                    optionsContent.style.display = 'block';
                } else {
                    optionsContent.style.display = 'none';
                }
                e.preventDefault();
            } );
            
            var textureControls = document.getElementById( 'textureControls' );
            var el = document.getElementById( 'showTextureControlsBtn' ).addEventListener( 'click', function( e ) {
                if( textureControls.style.display != 'block' ) {
                    textureControls.style.display = 'block';
                } else {
                    textureControls.style.display = 'none';
                }
                e.preventDefault();
            } );
            
            var el = document.getElementById( 'fullscreenBtn' );
            if( el ) {
                var options = document.getElementById( 'options' );
                el.addEventListener( 'click', function( e ) {
                    if( document.body.webkitRequestFullScreen ) {
                        document.body.onwebkitfullscreenchange = function(e) {
                        //	options.style.display = 'none';
                            document.body.style.width = window.innerWidth + 'px';
                            document.body.style.height = window.innerHeight + 'px';
                            document.body.onwebkitfullscreenchange = function() {
                        //		options.style.display = 'block';
                            };
                        };
                        document.body.webkitRequestFullScreen();
                    }
                    if( document.body.mozRequestFullScreen ) {
                        /*document.body.onmozfullscreenchange = function( e ) {
                            options.style.display = 'none';
                            document.body.onmozfullscreenchange = function( e ) {
                                options.style.display = 'block';
                            };
                        };*/
                        document.body.mozRequestFullScreen();
                    }
                    e.preventDefault();
                }, false );
            }
            
            function createCloud() {
                var textureWidth = 1440;
                var div = document.createElement( 'div' );
                div.className = 'cloudBase';
                var x = 256 - ( Math.random() * textureWidth );
                var y = 256 - ( Math.random() * textureWidth );
                var z = 256 - ( Math.random() * textureWidth );
                var z = 256 - ( Math.random() * textureWidth );
                var t = 'translateX( ' + x + 'px ) translateY( ' + y + 'px ) translateZ( ' + z + 'px )';
                div.style.webkitTransform = 
                div.style.MozTransform = 
                div.style.oTransform =
                div.style.transform = t;
                world.appendChild( div );
                
                for( var j = 0; j < 5 + Math.round( Math.random() * 10 ); j++ ) {
                    var cloud = document.createElement( 'img' );
                    cloud.style.opacity = 0;
                    var r = Math.random();
                    var src = 'troll.png';
                    for( var k = 0; k < computedWeights.length; k++ ) {
                        if( r >= computedWeights[ k ].min && r <= computedWeights[ k ].max ) {
                            ( function( img ) { img.addEventListener( 'load', function() {
                                img.style.opacity = .8;
                            } ) } )( cloud );
                            src = computedWeights[ k ].src;
                        }
                    }
                    if( !isKosher ) src = 'troll.png';
                    cloud.setAttribute( 'src', src );
                    cloud.className = 'cloudLayer';
                    
                    var x = 256 - ( Math.random() * textureWidth );
                    var y = 256 - ( Math.random() * textureWidth );
                    var z = 500 - ( Math.random() * 200 );
                    var a = Math.random() * 360;
                    var s = .25 + Math.random();
                    x *= .2; y *= .2;
                    cloud.data = { 
                        x: x,
                        y: y,
                        z: z,
                        a: a,
                        s: s,
                        speed: .1 * Math.random()
                    };
                    var t = 'translateX( ' + x + 'px ) translateY( ' + y + 'px ) translateZ( ' + z + 'px ) rotateZ( ' + a + 'deg ) scale( ' + s + ' )';
                    cloud.style.webkitTransform = 
                    cloud.style.MozTransform = 
                    cloud.style.oTransform = 
                    cloud.style.transform = t;
                
                    div.appendChild( cloud );
                    layers.push( cloud );
                }
                
                return div;
            }
            
            window.addEventListener( 'mousewheel', onContainerMouseWheel );
            window.addEventListener( 'DOMMouseScroll', onContainerMouseWheel ); 
            //window.addEventListener( 'deviceorientation', orientationhandler, false );
            //window.addEventListener( 'MozOrientation', orientationhandler, false );
            
            document.getElementById( 'generateBtn' ).addEventListener( 'click', function( e ) {
                generate();
                e.preventDefault();
            } );
            
            window.addEventListener( 'keydown', function( e ) {
                if( e.keyCode == 32 ) generate();
            } );
            
            window.addEventListener( 'mousemove', function( e ) {
                //worldYAngle = -( .1 - ( e.clientX / window.innerWidth ) ) * 180;
                //worldXAngle = ( .1 - ( e.clientY / window.innerHeight ) ) * 180;
                //worldXAngle = .1 * ( e.clientY - .5 * window.innerHeight );
                //worldYAngle = .1 * ( e.clientX - .5 * window.innerWidth );
                //updateView();
            } );
            
            window.addEventListener( 'touchmove', function( e ) {
                /*var ptr = e.changedTouches.length;
                while( ptr-- ) {
                    var touch = e.changedTouches[ ptr ];
                    worldYAngle = -( .5 - ( touch.pageX / window.innerWidth ) ) * 180;
                    worldXAngle = ( .5 - ( touch.pageY / window.innerHeight ) ) * 180;
                    updateView();
                }
                e.preventDefault();*/
            } );
            
            function generate() {
                objects = [];
                if ( world.hasChildNodes() ) {
                    while ( world.childNodes.length >= 1 ) {
                        world.removeChild( world.firstChild );       
                    } 
                }
                computedWeights = [];
                var total = 0;
                for( var j = 0; j < textures.length; j++ ) {
                    if( textures[ j ].weight > 0 ) {
                        total += textures[ j ].weight;
                    }
                }
                var accum = 0;
                for( var j = 0; j < textures.length; j++ ) {
                    if( textures[ j ].weight > 0 ) {
                        var w = textures[ j ].weight / total;
                        computedWeights.push( {
                            src: textures[ j ].file,
                            min: accum,
                            max: accum + w
                        } );
                        accum += w;
                    }
                }
                for( var j = 0; j < 20; j++ ) {
                    objects.push( createCloud() );
                }
            }
            
            function updateView() {
                var t = 'translateZ( ' + d + 'px ) rotateX( ' + worldXAngle + 'deg) rotateY( ' + worldYAngle + 'deg)';
                world.style.webkitTransform =
                world.style.MozTransform =
                world.style.oTransform = 
                world.style.transform = t;
            }
            
            function onContainerMouseWheel( event ) {
                //event = event ? event : window.event;
                //d = d - ( event.detail ? event.detail * -5 : event.wheelDelta / 8 );
                //updateView();
            }
            
            function orientationhandler( e ){
                  
                if( !e.gamma && !e.beta ) {
                    e.gamma = -( e.x * ( 180 / Math.PI ) );
                    e.beta = -( e.y * ( 180 / Math.PI ) );
                }
                
                var x = e.gamma;
                var y = e.beta;
                
                worldXAngle = y;
                worldYAngle = x;
                updateView();
            }
            
            function update (){
                
                for( var j = 0; j < layers.length; j++ ) {
                    var layer = layers[ j ];
                    layer.data.a += layer.data.speed;
                    var t = 'translateX( ' + layer.data.x + 'px ) translateY( ' + layer.data.y + 'px ) translateZ( ' + layer.data.z + 'px ) rotateY( ' + ( - worldYAngle ) + 'deg ) rotateX( ' + ( - worldXAngle ) + 'deg ) rotateZ( ' + layer.data.a + 'deg ) scale( ' + layer.data.s + ')';
                    layer.style.webkitTransform =
                    layer.style.MozTransform =
                    layer.style.oTransform =
                    layer.style.transform = t;
                    //layer.style.webkitFilter = 'blur(5px)';
                }
                
                requestAnimationFrame( update );
                
            }
            
            update();

            </script>
              
            <script>
                /*var sample_data = [],
                    vmap = jQuery('#vmap');
                    
                vmap.bind('load.jqvmap', function (event, map) {
                    console.log(event);
                    console.log(map);
                });

                vmap.vectorMap({
                    map: 'world_en',
                    backgroundColor: null,
                    color: 'transparent',
                    hoverOpacity: 0.7,
                    borderColor: '#cccccc',
                    color: 'rgba(14,69,48, 0.07)',
                    borderWidth: 0.3,
                    selectedColor: '#A01826',
                    enableZoom: true,
                    showTooltip: true,
                    scaleColors: ['#C8EEFF', '#006491'],
                    normalizeFunction: 'polynomial',
                    multiSelectRegion: true,
                    selectedRegions: [ // TODO: use ISO codes?
                        //'ca', // Canada - initial stop at ACE store
                        //'cn', // China
                        //'co', // Colombia
                        //'br', // Brazil
                        //'ve', // Venezuela
                        //'et', // Ethiopia
                        'cr', // Costa Rica
                        'gt', // Guatemala
                        //'ke', // Kenya
                        'mx', // Mexico
                        'sv', // El Salvador
                        'id', // Indonesia
                        'pg' // Papua New Guinea
                    ],
                    onRegionSelect: function (element, code, region) {
                        var message = 'You clicked "'
                        + region
                        + '" which has the code: '
                        + code.toUpperCase();

                        alert(message);
                    }
                });
                
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');
                vmap.vectorMap('zoomIn');*/
            </script>
        </div>
    </div>

<!--[if lt IE 9]>
<div class="old-browser"><?php echo $this->journal2->settings->get('old_browser_message', 'You are using an old browser. Please <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">upgrade to a newer version</a> or <a href="http://browsehappy.com/">try a different browser</a>.'); ?></div>
<![endif]-->
<?php if ($this->journal2->settings->get('config_header_modules')):  ?>
<?php echo $this->journal2->settings->get('config_header_modules'); ?>
<?php endif; ?>
<?php
    $header_type = $this->journal2->settings->get('header_type', 'default');
    if ($header_type === 'center') {
        if (!$this->journal2->settings->get('config_secondary_menu')) {
            $header_type = 'center.nosecond';
        } else {
            if (!$currency && !$language) {
                $header_type = 'center.nolang-nocurr';
            } else if (!$currency) {
                $header_type = 'center.nocurr';
            } else if (!$language) {
                $header_type = 'center.nolang';
            }
        }
    }

    if ($header_type === 'mega') {
        if (!$this->journal2->settings->get('config_secondary_menu')) {
            $header_type = 'mega.nosecond';
        } else {
            if (!$currency && !$language) {
                $header_type = 'mega.nolang-nocurr';
            } else if (!$currency) {
                $header_type = 'mega.nocurr';
            } else if (!$language) {
                $header_type = 'mega.nolang';
            }
        }
    }

    if ($header_type === 'default' || $header_type === 'extended') {
        $no_cart = $this->journal2->settings->get('catalog_header_cart', 'block') === 'none';
        $no_search = $this->journal2->settings->get('catalog_header_search', 'block') === 'none';
        if ($no_cart && $no_search) {
            $header_type = $header_type . '.nocart-nosearch';
        } else if ($no_cart) {
            $header_type = $header_type . '.nocart';
        } else if ($no_search) {
            $header_type = $header_type . '.nosearch';
        }
    }
    if (class_exists('VQMod')) {
        global $vqmod;
        if ($vqmod !== null) {
            require $vqmod->modCheck(DIR_TEMPLATE . $this->config->get('config_template') . "/template/journal2/headers/{$header_type}.tpl");
        } else {
            require VQMod::modCheck(DIR_TEMPLATE . $this->config->get('config_template') . "/template/journal2/headers/{$header_type}.tpl");
        }
    } else {
        require DIR_TEMPLATE . $this->config->get('config_template') . "/template/journal2/headers/{$header_type}.tpl";
    }
?>
<?php if ($this->journal2->settings->get('config_top_modules')): ?>
<div id="top-modules">
   <?php echo $this->journal2->settings->get('config_top_modules'); ?>
</div>
<?php endif; ?>
<div id="site" class="wrapper" style="max-width: 100%">
    <div class="loadingScreen">
        <div class="loadingScreen__content">
            <div class="bl_loading_logo">
            </div>
            <!-- bl_loading_logo -->
        </div>
        <!-- loadingScreen__content -->
    </div>
    <!-- loadingScreen -->
    <div class="canvasGrid"></div>
    <header id="header" role="banner" class="" style="">
        <div class="header__contentBottom" style="">
            <div class="borderLine" style="transform-origin: 0% 100% 0px; transform: matrix(1, 0, 0, 1, 0, 0);"></div>
            <div class="burgerMenu">
                <button class="burgerMenu__button burgerMenu__button--mobile">
                    <div class="burgerPatty burgerPatty--former" style="top: 0px; transform-origin: 50% 50% 0px; transform: matrix(1, 0, 0, 1, 0, 0); width: 100%;"></div>
                    <div class="burgerPatty burgerPatty--latter" style="bottom: 0px; transform-origin: 50% 50% 0px; transform: matrix(1, 0, 0, 1, 0, 0); width: 100%;"></div>
                </button>
                <!-- burgerMenu__button -->
                <button class="burgerMenu__button burgerMenu__button--desktop">
                    <div class="burgerPatty burgerPatty--former" style="top: 0px; transform-origin: 50% 50% 0px; transform: matrix(1, 0, 0, 1, 0, 0); width: 100%;"></div>
                    <div class="burgerPatty burgerPatty--latter" style="bottom: 0px; transform-origin: 50% 50% 0px; transform: matrix(1, 0, 0, 1, 0, 0); width: 100%;"></div>
                </button>
                <!-- burgerMenu__button -->
            </div>
            <div class="actionButtons">
                <ul>
                    <!--<li><button id="account-button"><i class="fa fa-user"></i></button></li>-->
                    <li tabindex='1'><a href="#/category" class="btn no-bs-style" id="shop-button"><i class="fa fa-tags"></i><br/><span class="buttonText">Catalog<span></a></li>
                    <li tabindex='2'><a href="#/checkout" class="btn no-bs-style" id="cart-button"><i class="fa fa-shopping-cart"></i><br/><span class="buttonText">Order</span></a></li>
                    <li tabindex='3'><a href="#/contact-form" class="btn no-bs-style" id="contact-button"><i class="fa fa-comment"></i><br/><span class="buttonText">Contact</span></a></li>
                </ul>
            </div>
            <!-- burgerMenu -->
            <!--<div class="socialBadges socialBadges--front">
                    <ul class="socialBadges-ul">
                        <li class="socialBadges-li socialBadges-li--facebook" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 0, -3);">
                            <a href="https://www.facebook.com/acecoffeeroasters" target="_blank">
                                <i class="fa fa-facebook"></i>
                            </a>
                        </li>

                        <li class="socialBadges-li socialBadges-li--twitter" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 0, -3);">
                            <a href="https://twitter.com/acecoffeeroasters" target="_blank">
                                <i class="fa fa-twitter"></i>
                            </a>
                        </li>

                        <li class="socialBadges-li socialBadges-li--instagram" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 0, -3);">
                            <a href="https://www.instagram.com/acecoffeeroasters" target="_blank">
                                <i class="fa fa-instagram"></i>
                            </a>
                        </li>

                        <li class="socialBadges-li socialBadges-li--youtube" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 0, -3);">
                            <a href="https://www.youtube.com/user/acecoffeeroasters" target="_blank">
                                <i class="fa fa-youtube"></i>
                            </a>
                        </li>

                    </ul>

                    <div class="socialBadges__trigger">
                        <span class="socialTriggerText">Socialize</span>
                        <span class="socialTriggerLine"></span>
                        <span class="socialTriggerIcon" style="transform: matrix(1, 0, 0, 1, 0, 0);">
                            <i class="fa fa-plus"></i>
                        </span>
                    </div>

                    <div class="socialBadges__title">
                        <span class="socialTriggerText">SOCIALIZE</span>
                        <span class="socialTriggerLine"></span>
                    </div>
                </div>-->
        </div>
        <!-- header__contentBottom -->
        <div class="header__contentTop" style="transform: matrix(1, 0, 0, 1, 0, 0);">
            <h1>
                <span>ACE COFFEE ROASTERS</span>
                <a data-type="page-transition" class="mainLogo" href="#/" rel="home">
                    <!--<span class="logoType">-->
                    <img src="site/assets/images/logo/ACElogo.png" style="max-width: 100%" />
                    <!--</span>-->
                </a>
                <p class="site-description"></p>
            </h1>
            <!-- h1 -->
        </div>
        <!-- header__contentTop -->
        <nav class="mainNav" role="navigation">
            <div class="container">
                <div class="mainNav__contentEntries">
                    <div class="relatedImage">
                        <div class="relatedImage__shadow" style="width: 0px;"></div>
                        <div class="relatedImage__bg
                    relatedImage__bg--home			      	" style="width: 0px;"></div>
                        <div class="relatedImage__glide" style="visibility: hidden; opacity: 0;"></div>
                    </div>
                    <ul class="listMenu-ul">
                        <li class="listMenu-li listMenu-li--home listMenu-li--current" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">HOME</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--hair-trends" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/category">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">ONLINE OUTLET</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--saloni" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/about">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">OUR STORY</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--prodotti" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/community">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">COMMUNITY</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--news" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/news">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">NEWS &amp; OFFERINGS</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--about" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/about">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">COMPANY</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--social-wall" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="#/social">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">SOCIAL WALL</span>
                            </a>
                        </li>
                    </ul>
                    <!-- listMenu -->
                </div>
                <!-- mainNav__contentEntries -->
            </div>
            <!-- container -->
        </nav>
        <!-- mainNav -->
    </header>
    <!-- header -->