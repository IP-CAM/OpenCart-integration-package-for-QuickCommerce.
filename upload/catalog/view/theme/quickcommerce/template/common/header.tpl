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
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="">
<head>
    <meta charset="UTF-8" />
    
    <!-- TODO: We need to add this to bower/npm -->
    <!-- For animations and off canvas burger menu -->
    <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/velocity/1.4.3/velocity.min.js"></script>

    <script type="text/javascript" src="site/js/react-bundle.js"></script>
    <!--<script type="text/javascript" src="site/js/bower-bundle.js"></script>-->
    <script type="text/javascript" src="site/js/libs.js"></script>
    <script src="./index_files/jquery.min.js"></script>
    <script src="./index_files/TweenMax.min.js"></script>
    <script src="./index_files/bluebird.min.js"></script>
    <!--<script async="" defer="" src="./index_files/js" type="text/javascript"></script>-->
    <script src="./index_files/all.js"></script>
    <script src="./index_files/site.js"></script>
   
	<!--<link href="spa/imported/owl.css" media="screen, projection" rel="stylesheet" type="text/css">-->
    <link media="all" href="app/build/css/grommet.min.css" rel="stylesheet" type="text/css">
    <link href="./site/styles/lib/versla.css" rel="stylesheet" type="text/css">
    <!--<link href="app/build/css/fonts.css" media="screen, projection" rel="stylesheet" type="text/css">-->
    <link media="all" rel="stylesheet" type="text/css" href="app/build/css/owl.css">
    <link media="all" rel="stylesheet" type="text/css" href="site/fonts/font-awesome/css/font-awesome.css">  
    <!--<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">-->
    
    <!-- Journal Styles -->
    <link media="all" rel="stylesheet" type="text/css" href="catalog/view/theme/quickcommerce/css/fbwd-theme-sassed.css" />
    
    <link rel="stylesheet" type="text/css" href="./site/styles/betheme/betheme.css" />
    <link rel="stylesheet" type="text/css" href="catalog/view/javascript/quickcommerce/css/bundle.css" />
    <!--<link media="all" rel="stylesheet" type="text/css" href="site/css/bundle.css" />-->
    
    
    <link rel="stylesheet" type="text/css" href="./site/styles/style.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/heading.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/input.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/forms.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/button.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/action-buttons.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/cards.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/menus.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/paging.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/tables.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/thumbnails.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/footer.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/entry-modules.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/catalog-forms.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/checkout-parts.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/order-parts.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/clouds.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/animate.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/animateview.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/loader.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/spinner.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/stepper.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/burger.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/social.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/socialbadges.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/address.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/autocomplete.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/browser.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/cart.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/credit-cards.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/customer.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/filter.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/smartloader.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/social.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/share.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/socialstream.css" />
    <link rel="stylesheet" type="text/css" href="./site/styles/components/summary.css" />

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
        
        #container {
            padding: 0;
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
        .shipping-address,
        .credit-card-info {
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
    
    .align-center {
        text-align: center;
    }
    </style>
    
    <!--<link href="site/js/jqvmap/jqvmap.css" media="screen, projection" rel="stylesheet" type="text/css">-->
    <link href="site/js/jqvmap/jqvmap.min.css" media="screen, projection" rel="stylesheet" type="text/css">
    
    <script src="catalog/view/javascript/common.js" type="text/javascript"></script>
    <script src="site/js/jqvmap/jquery.vmap.js"></script>
    <!--<script src="site/js/jqvmap/jquery.vmap.min.js"></script>-->
    <script src="site/js/jqvmap/maps/jquery.vmap.world.js"></script>
    
    <style type="text/css">
        /* Hide ACE Stuff */
        /*#header,*/ /*.header__contentTop {
            display: none !important;
        }
        
        .fullwidth-footer, footer#footer {
            display: none !important;
        }
        
        .mcb-wrap-inner h1,
        .mcb-wrap-inner h2,
        .mcb-wrap-inner h3,
        .mcb-wrap-inner h4,
        .mcb-wrap-inner h5,
        .mcb-wrap-inner h6 {
            display: none;
        }
        
        #background, #world, .g-grid {
            display: none;
        }*/
    </style>
</head>
<body>

<div id='showcase' class="grommetux-box grommetux-box--direction-column grommetux-box--responsive grommetux-box--pad-none">
    <div class="grommetux-box grommetux-box--direction-column grommetux-box--responsive grommetux-box--pad-none" style="display: none">
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
                
                label {
                    display: block;
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

<div id="top-modules">
</div>
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
                    <li tabindex='1'><a href="http://acecoffeeroasters/#/category" class="btn no-bs-style" id="shop-button"><i class="fa fa-tags"></i><br/><span class="buttonText">Catalog<span></a></li>
                    <li tabindex='2'><a href="http://acecoffeeroasters/#/checkout" class="btn no-bs-style" id="cart-button"><i class="fa fa-shopping-cart"></i><br/><span class="buttonText">Order</span></a></li>
                    <li tabindex='3'><a href="http://acecoffeeroasters/#/contact-form" class="btn no-bs-style" id="contact-button"><i class="fa fa-comment"></i><br/><span class="buttonText">Contact</span></a></li>
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
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters/#/">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">HOME</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--hair-trends" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters/#/category">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">ONLINE OUTLET</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--saloni" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters.com/about">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">OUR STORY</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--prodotti" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters.com/community">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">COMMUNITY</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--news" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters.com/news">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">NEWS &amp; OFFERINGS</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--about" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters.com/about">
                                <span class="lmLinkBg"></span>
                                <span class="lmLinkText">COMPANY</span>
                            </a>
                        </li>
                        <li class="listMenu-li listMenu-li--social-wall" style="visibility: hidden; opacity: 0; transform: matrix(1, 0, 0, 1, 10, -5);">
                            <a class="listMenu-li__link" data-type="page-transition" href="http://acecoffeeroasters.com/social">
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