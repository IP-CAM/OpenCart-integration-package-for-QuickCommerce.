<!DOCTYPE html>
<?php
    if (!defined('JOURNAL_INSTALLED')) {
        echo '
            <h3>Journal Installation Error</h3>
            <p>Make sure you have uploaded all Journal files to your server and successfully replaced <b>system/engine/front.php</b> file.</p>
            <p>You can find more information <a href="http://docs.digital-atelier.com/opencart/journal/#/settings/install" target="_blank">here</a>.</p>
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
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/journal.css');
    //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/features.css');
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/header.css');
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/module.css');
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/pages.css');
    //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/account.css');
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/blog-manager.css');
    //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/side-column.css');
    //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/product.css');
    //$this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/category.css');
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/footer.css');
    $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/icons.css');
    if ($this->journal2->settings->get('responsive_design')) {
        $this->journal2->minifier->addStyle('catalog/view/theme/journal2/css/responsive.css');
    }
?>
<?php echo $this->journal2->minifier->css(); ?>
<?php if ($this->journal2->cache->getDeveloperMode() || !$this->journal2->minifier->getMinifyCss()): ?>
<link rel="stylesheet" href="index.php?route=journal2/assets/css&amp;j2v=<?php echo JOURNAL_VERSION; ?>" />
<?php endif; ?>
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




<!--<link href='app/build/spa/js/src/grommet.min.css' rel='stylesheet' type='text/css'>-->
<link href="app/build/spa/imported/fonts.css" media="screen, projection" rel="stylesheet" type="text/css">
<link href="app/build/spa/imported/owl.css" media="screen, projection" rel="stylesheet" type="text/css">

<!--<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">-->
<link href="app/build/spa/lib/font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet"  type="text/css">

<link rel="stylesheet" type="text/css" href="app/build/css/bundle.css" />


<!-- START Site container (for off canvas menu) -->
<style>
/* Some fuck class is messing up tab display */
.oc2 .tab-content {
    display: block;
}

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
.one_half.parallax_scroll {
    position: absolute;
    top: 0;
    background: rgba(255, 255, 255, 0.77);
    padding: 40px;
    transform: translate3d(0px, 119.645px, 0px);
    display: none;
    z-index: 500;
}
.page_title_intro p {
    color: #424242;
}
.owl-controls {
    position: absolute;
    z-index: 1000;
    height: 40px;
    width: 100%;
    bottom: 0;
}
.owl-pagination {
    margin: 0 auto;
    position: relative;
    display: inline-block;
    left: 8px;
}
.parallax_scroll.scene {
    width: 100%;
    background-size: cover;
    background-repeat: no-repeat !important;
    position: absolute;
    max-width: 1920px;
    left: 0;
}
.parallax_scroll.scene img {
    max-width: 100%;
}
.parallax_title_intro {
    padding-top: 120px;
    text-align: center;
}
#scene-logo {
    left: 50%;
    transform: translateX(-50%) !important;
}
#text {
    left: 50%;
    transform: translateX(-50%) !important;
    display: none;
}
.scene .page_title_intro p {
    margin-left: auto;
    margin-right: auto;
    width: 100%;
}
.store-location {
    background-attachment: scroll;
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.777);
    text-align: center;
    color: white;
    position: relative;
    z-index: 5;
    min-height: 400px;
}
@media screen and (max-width: 1280px) {
    .circle-graphic {
        max-width: 50%;
    }
    svg {
        height: auto;
    }
    .parallax_title_intro {
        padding-top: 4rem;
        padding-bottom: 2rem;
    }
    .parallax_title_intro .cta-buttons {
        display: none;
    }
    .parallax.title {
        height: auto !important;
    }
    .parallax.title:after {
        /* kill the gradient - do it everywhere later too */
        
        background-image: none;
    }
    .parallax .parallax_title.inline {
        max-width: 60%;
        margin: 0 auto;
    }
    .parallax .parallax_title.inline .ppb_title {
        font-size: 23px;
    }
    .parallax .parallax_title.inline .ppb_title_first {
        font-size: 40px;
    }
    .store-location {
        padding: 2rem 0;
    }
}

.site {
    max-width: 100%;
    z-index: 3;
    position: relative;
    border-top: 1px solid white;
    /*left: 0;*/
}

.off-canvas {
    width: 100%;
    height: 100%;
    z-index: 2;
    /*background-color: rgb(61, 9, 49);*/
    background-color: #333;
    position: absolute;
    right: 0px;
    top: 0;
}

.off-canvas-menu-wrapper {
    /* Use flex to dictate orientation */
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    box-sizing: border-box;
}

.off-canvas-menu-wrapper .menu-element {
    width: 20%;
    flex-basis: 20%;
}

/* Make the last item expand */
.off-canvas-menu-wrapper .menu-element:last-child {
    flex-grow: 2;
}

.off-canvas,
.off-canvas-menu-wrapper {
    height: 100%;
}


/* MENU TRIGGER */
.off-canvas-trigger {
    z-index: 99999;
    position: absolute;
    right: -1px;
    top: 0;
    width: 28px;
    height: 20px;
    cursor: pointer;
    display: inline-block;
    box-sizing: content-box;
    -moz-box-sizing: content-box;
    padding: 10px;
    background-color: white;
    /* Poblanos Purple */
}


.form-builder {
    margin-top: 100px;
}

/* Default thirds */
/*.primary-nav, .secondary-nav, .tertiary-nav {
  width: 33.33%;
  height: 100%;
  float: right;
  position: relative;
}*/

/* Custom changes */

.primary-nav,
.secondary-nav,
.tertiary-nav,
.quaternary-nav {
    /*background-color: rgb(61, 9, 49);*/
}
/*.primary-nav {
    background-color: white;
}
.secondary-nav {
    background-color: white;
}
.tertiary-nav {
    background-color: white;
}
.quaternary-nav {
    background-color: white;
}*/
.nav-content {
    width: 100%;
    display: inline-block;
    padding: 20px;
    box-sizing: border-box;
}
.nav-content a {
    text-decoration: none;
    color: #fff;
}
.nav-content p.top-level {
    font-weight: 600;
}
.nav-content ul {
    padding: 0;
    list-style-type: none;
}
.nav-content ul li a {
    padding-bottom: 8px;
    width: 100%;
    display: inline-block;
}
.primary-nav p.seemore {
    width: 100%;
    display: inline-block;
    padding: 15px;
    margin: 0;
    background-color: #1D1F20;
    color: #fff;
    position: absolute;
    bottom: 5%;
    cursor: pointer;
    text-align: right;
    padding-right: 15px;
    box-sizing: border-box;
}

/* NOT IN SASS - EDIT */
/*#nav-trigger-anim {
  position: absolute;
  top: 20px;
}

#nav-trigger-anim span, #nav-trigger-anim span:before, #nav-trigger-anim span:after {
  border-radius: 2px;
  height: 5px;
  width: 35px;
  background-color: #fff;
  position: absolute;
  display: inline-block;
  content: '';
  z-index: 3;
  cursor: pointer;
}

#nav-trigger-anim span:before {
  top: -10px;
  width: 30px;
  left: 2.5px;
}

#nav-trigger-anim span:after {
  bottom: -10px;
  width: 30px;
  left: 2.5px;
}

#nav-trigger-anim span, #nav-trigger-anim span:before, #nav-trigger-anim span:after {
  -webkit-transition: all 500ms ease-in-out;
  transition: all 500ms ease-in-out;
}

#nav-trigger-anim.active span {
  background-color: transparent;
}

#nav-trigger-anim.active span:before, #nav-trigger-anim.active span:after {
  top: 0;
}

#nav-trigger-anim.active span:before {
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
  transform: rotate(45deg);
}

#nav-trigger-anim.active span:after {
  transform: translateY(-10px) rotate(-45deg);
  -webkit-transform: translateY(-10px) rotate(-45deg);
  transform: translateY(-10px) rotate(-45deg);
  top: 10px;
}*/
/* Burger */

.burger {
    display: inline-block;
    border: 0;
    background: none;
    outline: 0;
    padding: 0;
    cursor: pointer;
    border-bottom: 4px solid currentColor;
    width: 28px;
    transition: border-bottom 1s ease-in-out;
    -webkit-transition: border-bottom 1s ease-in-out;
}
.burger::-moz-focus-inner {
    border: 0;
    padding: 0;
}
.burger:before {
    content: "";
    display: block;
    border-bottom: 4px solid currentColor;
    width: 100%;
    margin-bottom: 5px;
    transition: -webkit-transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out, -webkit-transform 0.5s ease-in-out;
    -webkit-transition: -webkit-transform 0.5s ease-in-out;
}
.burger:after {
    content: "";
    display: block;
    border-bottom: 4px solid currentColor;
    width: 100%;
    margin-bottom: 5px;
    transition: -webkit-transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out, -webkit-transform 0.5s ease-in-out;
    -webkit-transition: -webkit-transform 0.5s ease-in-out;
}
.burger-check {
    display: none;
}
.burger-check:checked ~ .burger {
    /*border-bottom: 4px solid transparent;*/
    border-bottom: none;
    transition: border-bottom 0.8s ease-in-out;
    -webkit-transition: border-bottom 0.8s ease-in-out;
}
.burger-check:checked ~ .burger:before {
    transform: rotate(-405deg) translateY(1px) translateX(-3px);
    -webkit-transform: rotate(-405deg) translateY(1px) translateX(-3px);
    transition: -webkit-transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out, -webkit-transform 0.5s ease-in-out;
    -webkit-transition: -webkit-transform 0.5s ease-in-out;
}
.burger-check:checked ~ .burger:after {
    transform: rotate(405deg) translateY(-4px) translateX(-5px);
    -webkit-transform: rotate(405deg) translateY(-4px) translateX(-5px);
    transition: -webkit-transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out;
    transition: transform 0.5s ease-in-out, -webkit-transform 0.5s ease-in-out;
    -webkit-transition: -webkit-transform 0.5s ease-in-out;
}
/*.navigation {
  overflow: hidden;
  max-height: 0;
  -webkit-transition: max-height 0.5s ease-in-out;
  transition: max-height 0.5s ease-in-out;
}

.burger-check:checked ~ .navigation {
  max-height: 500px;
  -webkit-transition: max-height 0.5s ease-in-out;
  transition: max-height 0.5s ease-in-out;
}*/

.top-level,
.top-level a {
    text-align: center;
}
/* Make sticky-menu area bigger */
/* Never mind just destroy the stupid thing, it's blocking our goddamn menu */
#sticky-wrapper, .j-min {
    display: none;
}

body {
    padding-top: 10px;
}

/* WTF Journal Styles to KILL */
/* Temp kill header background */
header, .journal-header-center {
    background: transparent;
}
/* Kill OC column styles */
/* Let containing element set width */

.extended-layout #column-right,
.extended-layout #column-left {
    width: auto;
}
/* Stupid padding */

#content {
    padding: 0 !important;
}
/* Adjust menu list items top margin to make room for top bar spacing */

.off-canvas .nav-content ul {
    margin-top: 17px;
}
.ppb_wrapper {
    /* HACK Job! Quick mask transparent areas in scene */
    
    background-color: #F5F1D7;
}
.copyright {
    max-width: 70%;
    white-space: normal;
    line-height: auto;
    margin: 0 auto;
}

.fullwidth-footer {
    border-top: 1px solid white;
}

footer {
    padding-bottom: 0;
    padding-top: 0;
}

.fullwidth-footer.location {
    border-top: 1px solid white; 
    border-bottom: 1px solid white; 
    position: relative;
    z-index: 5;
}

.footer {
    margin: 0; 
    padding: 0; 
    display: table; 
    width: 100%; 
    position: relative; 
    z-index: 5;
}

#embedded-map-wrapper {
    position: absolute;
    display: flex; /* Expand map */
    background: black;
    overflow: hidden; 
    height: 100%; 
    width: 100%; 
}

#embedded-map-display {
    width: 100%;
    max-width: 100%;
}

/* Kill padding, add real border */
.product-wrapper {
    overflow: hidden;
    margin: 0;
}

.copyright {
    font-size: small;
}

/* Override stupid Journal style */

.fullwidth-footer .columns {
    padding: 0 !important;
}

footer .column {
    padding: 0; /* Reset padding Journal columns are retarded and only pad in one direction */
}

/* Grid system fixes */

footer *,
#footer * {
    box-sizing: border-box;
}

.content-plus,
#content > .box {
    background-color: white;
}

/* This content-plus thing sucks */

.content-plus {
    position: relative;
    z-index: 5;
}
.product-grid-item .image > a {
    cursor: default;
}

/* Quick fix for double border */
.box {
    padding-top: 0;
}

/* Adjust height to cover SFA schedule widget footer */
.fullwidth-footer .bottom-footer {
    position: relative;
    top: -60px;
}

.off-canvas-trigger {
    border-style: solid;
    border-color: rgba(255,255,255,0.75);
    border-width: 0 0 1px 1px;
    border-radius: 0 0 0 5px;
}

/* Override Journal carousel styles */
.journal-carousel {
    border-top: 1px solid white;
}

.journal-carousel .owl-wrapper-outer {
    padding: 0;
}

/* Override background shading on product labels (normally black) */
.journal-carousel .product-details {
    border-top: 1px solid white;
    background: rgba(61, 9, 49, 0.777);
    padding: 1.25rem 0;
}

.journal-carousel .product-details, 
.journal-carousel .product-grid-item .name a {
    font-size: 1.25rem;
}

/* Override button styles that I added in custom CSS */
input[type=submit], input[type=button], a.button, .button {
    border: 1px solid white;
    border-radius: 5px;
    text-transform: none;
    font-size: 1.25rem;
}

/* Override footer header styles that I either customized or added in custom CSS */
footer .column > h3, aside .column > h3 {
    font-size: 1.25rem;
    text-transform: none;
}

.google-play-column img,
.app-store-column img {
    border: 1px solid white;
    border-radius: 7px;
    opacity: 0.666;
}

/* Colors */
/* rgba(199, 216, 100, 1) Jimmy Neon Green */
/* rgba(61, 9, 49, 1) Jimmy Dark Purple */
/* rgba(181, 102, 136, 1) Jimmy Light Purple */
/* rgba(254, 133, 109, 1) Jimmy Sun Orange */

.cta-buttons .button.cta {
    background-color: white; /*rgba(199, 216, 100, 0.777);*/
    transition: background-color 1s;
    -webkit-transition: background-color 0.6s;
}

.cta-buttons .button.cta:hover {
    background-color: rgba(254, 133, 109, 0.777);
}

/* Darker shadows */
.parallax_title_intro {
    padding: 150px 0;
    background: -moz-radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.777) 0%, rgba(0, 0, 0, 0.666) 55%, rgba(0, 0, 0, 0) 76%, rgba(0, 0, 0, 0) 100%);
    background: -webkit-radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.777) 0%, rgba(0, 0, 0, 0.66) 55%, rgba(0, 0, 0, 0) 76%, rgba(0, 0, 0, 0) 100%);
    background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.777) 0%, rgba(0, 0, 0, 0.666) 55%, rgba(0, 0, 0, 0) 76%, rgba(0, 0, 0, 0) 100%);
    filter: progid: DXImageTransform.Microsoft.gradient( startColorstr='#a8000000', endColorstr='#00000000', GradientType=1);
}

.site, .menu-wrapper, .content-plus > .parallax {
    box-shadow: 0 2px 20px #000;
    -moz-box-shadow: 0 2px 20px #000;
    -webkit-box-shadow: 0 2px 20px #000;
}

/* Image zoom fx */
.product-grid-item .image {
    overflow: hidden;
}

.product-grid-item .image img {
    max-width: none; /* I have set max-width elsewhere in the styles to clear up */
    transition: width 0.6s, height 0.6s;
}

.product-grid-item .image:hover img {
    max-width: none; /* I have set max-width elsewhere in the styles to clear up */
    width: 102.5%;
    height: 102.5%;
}

@media screen and (max-width: 1220px) {
    #scene-clouds-3 {
        display: none !important;
    }
}
@media screen and (max-width: 992px) {
    header,
    .journal-header-center {
        margin-top: 0;
    }
    
    .off-canvas {
        width: calc(100% - 48px);
    }
    
    #sticky-wrapper, .j-min {
        min-height: auto;
        height: 0;
    }
    .off-canvas,
    .off-canvas-trigger {
        top: 0;
    }
    
    /* Make sticky-menu area bigger */
    body {
        padding-top: 0;
    }
    
    .site {
        border-top: none;
    }
    
    .off-canvas-menu-wrapper {
        flex-direction: column;
        align-items: flex-end;
    }
    
    .off-canvas-menu-wrapper .menu-element {
        flex-basis: auto;
        width: 50%;
    }
}
@media screen and (max-width: 768px) {
    /* Adjust height to cover SFA schedule widget footer */
    .fullwidth-footer .bottom-footer {
        position: relative;
        top: -90px;
    }

    #footer .footer-location {
        padding-top: 2rem;
    }
    
    .off-canvas-menu-wrapper {
        flex-direction: column;
        align-items: flex-end;
    }
    
    .off-canvas-menu-wrapper .menu-element {
        flex-basis: auto;
        width: 75%;
    }
    
    #sticky-wrapper, .j-min {
        min-height: auto;
    }
    
    p,
    body,
    .journal-carousel .product-details,
    .journal-carousel .product-grid-item .name a {
        font-size: 15px;
    }
    
    .store-location > .column:not(.hidden-phone) {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .google-play-column,
    .app-store-column {
        max-height: 80px !important;
    }
    .google-play-column img,
    .app-store-column img {
        max-height: 60px !important;
    }
}

/* Custom layout fix for Jimmy P's on iPad - there's a 7px range where the footer doesn't line up on iPad */
@media only screen and (min-width: 761px) and (max-width: 768px) {
    /* We previously adjusted height to cover SFA schedule widget footer */
    #footer .footer-location {
        padding-top: 0;
    }
}

@media only screen and (max-width: 470px) {
    .off-canvas-menu-wrapper {
        flex-direction: column;
        align-items: stretch;
    }
    
    .off-canvas-menu-wrapper .menu-element {
        flex-basis: auto;
        width: auto;
    }
    
    .off-canvas {
        width: calc(100% - 48px);
    }
    
    #footer {
        flex-direction: column;
    }
    /* Fix & center footer cols for mobile */
    
    footer .column {
        padding-right: 0;
    }
    .fullwidth-footer .row.columns {
        padding: 0;
        margin: 0 auto;
    }
}

@media screen and (max-width: 980px) {
    #text {
        display: none;
    }
}
@media screen and (max-width: 768px) {
    .parallax_title_intro .parallax_title {
        display: none;
    }
    .page_title_intro p,
    .parallax.title .parallax_title_intro p {
        margin-left: auto;
        margin-right: auto;
        width: 90%;
    }
    .name {
        font-size: 11px;
    }
    /*.owl-controls {
        position: relative;
    }*/
    #header {
        padding-top: 0;
    }
    .parallax_title_intro {
        padding-top: 1.5rem;
    }
    .hidden-phone {
        display: none;
    }
    .display-phone {
        display: block;
    }
}
.journal-carousel .product-grid-item .price,
.product-grid-item .price {
    display: none;
}
/* TODO: Not sure if these queries are that great - at some point should switch grid system to bootstrap or something so I don't have to f*** around */

@media screen and (min-width: 768px) {
    .hidden-tablet {
        display: none;
    }
    .display-tablet {
        display: block;
    }
    /* Fix for inline elements */
    
    p.display-tablet,
    span.display-tablet,
    a.display-tablet,
    img.display-tablet {
        display: inline-block;
    }
}
@media screen and (min-width: 992px) {
    .hidden-lg-tablet {
        display: none;
    }
    .display-lg-tablet {
        display: block;
    }
    /* Fix for inline elements */
    
    p.display-lg-tablet,
    span.display-lg-tablet,
    a.display-lg-tablet,
    img.display-lg-tablet {
        display: inline-block;
    }
    .store-location {
        display: flex;
        align-items: center;
    }
    .store-location svg {
        align-self: baseline;
    }
}
/* The built in Journal grid system blows, we should replace it at some point */

@media screen and (min-width: 992px) and (max-width: 1280px) {
    .lg-11 {
        width: 11.11111111111111%;
    }
    .lg-12 {
        width: 12.5%;
    }
    .lg-14 {
        width: 14.28571428571429%;
    }
    .lg-16 {
        width: 16.66666666666666%;
    }
    .lg-33 {
        width: 33.33333333333333%;
    }
    .lg-66 {
        width: 66.66666666666666%;
    }
}
@media screen and (min-width: 1200px) {
    .hidden-desktop {
        display: none;
    }
    .display-desktop {
        display: block;
    }
    /* Fix for inline elements */
    
    p.display-desktop,
    span.display-desktop,
    a.display-desktop,
    img.display-desktop {
        display: inline-block;
    }
}

/* TODO: What form? be specific */
.form-builder h3 {
    margin-top: 1.1rem;
    border-top: 1px solid white;
    padding-top: 2rem;
    font-weight: 600;
    color: white;
    text-align: center;
}

.menu-element {
    box-shadow: inset 1px 0px 1px white;
}

.form-builder h4 {
    color: white;
}

.form-builder .form-field {
    text-align: center;
}

.form-builder .btn-primary {
    border: 1px solid white;
    border-radius: 5px;
}

@keyframes site-block-animation {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

.site.disabled::after {
    animation-name: site-block-animation;
    animation-duration: 1s;
    content: '';
    position: absolute;
    height: 100%;
    width: 100%;
    background: rgba(0,0,0,0.777);
    z-index: 9999;
    top: 0;
    left: 0;
}
</style>
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
    
    .btn-danger:not(.grommetux-button) {
        background-color: #A91626 !important;
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
    
    .bm-menu {
        box-sizing: content-box;
        overflow-x: hidden !important;
    }
    
    .modal-backdrop {
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.333);
        z-index: 10;
    }
    
    /* Position and sizing of burger button */
    .bm-burger-button {
      position: absolute;
      width: 36px;
      height: 30px;
      left: 36px;
      top: 36px;
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
      background: white;
      padding: 7.5em 1.5em 0;
      font-size: 1.15em;
    }

    /* Morph shape necessary with bubble or elastic */
    .bm-morph-shape {
      fill: #373a47;
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
        background: white;
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
    
    .pagination-sm > li > a, 
    .pagination-sm > li > span {
        padding: 1.5rem 2rem;
    }
    
    .card h5 {
        font-weight: bold !important;
    }
    
    .grommetux-meter--bar .grommetux-meter__values .grommetux-meter__slice.grommetux-color-index-graph-1, .grommetux-meter--bar .grommetux-meter__values .grommetux-meter__slice.grommetux-color-index-graph-5 {
        stroke: #A91626;
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
    .grommetux-title, label.control-label {
        color: #333 !important;
    }
    
    
    .bm-item-list a span {
        margin-left: 10px;
        font-weight: 700;
    }
    
    #page-wrap + div > .bm-burger-button {
        right: 30px;
        left: auto;
    }
    
    #page-wrap + div > .bm-menu-wrap,
    #page-wrap + div > .bm-overlay {
        top: 0;
    }
    
    #page-wrap + div > .bm-menu {
        padding: 2.5em 1.5em 0;
    }
    
    #page-wrap + div >  .bm-burger-button .bm-icon:before {
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
        
        .thumbnail {
            width: 50px !important;
        }
        
        .card {
            display: flex;
        }
        
        .card h5, .cart p {
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
            padding: 3rem 1rem 0.5rem;
            border: 1px solid #e3e3e3;
            border-radius: 5px;
            background: #f5f5f5;
            min-height: 100px;
            display: flex;
        }
        
        .checkout-parts {
            margin-top: 20px;
        }
    }
    
    @media screen and (max-device-width: 1024px) and (orientation: landscape) {
        .griddle-container div[class^="col-"] {
            height: 230px;
        }
    }
    </style>

</head>
<body>


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