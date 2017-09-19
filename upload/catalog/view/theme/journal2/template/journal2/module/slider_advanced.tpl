<?php //$width = 'width: 813px'; ?>
<?php //$height = 310; ?>
<!-- Set width and height -->
<div style="" class="tp-banner-container box <?php echo $js_options['hideThumbs'] ? 'nav-on-hover' : '' ?> <?php echo $slider_class; ?> <?php echo $js_options['thumbAmount'] === '' ? 'full-thumbs' : ''; ?> <?php echo implode(' ', $disable_on_classes); ?> <?php echo Journal2Utils::getProperty($js_options, 'navigationType') === 'none' ? 'hide-navigation' : ''; ?>">
    <div class="tp-banner" id="journal-slider-<?php echo $module; ?>" style="display: none;">
        <ul>
            <?php foreach ($slides as $slide): ?>
            <li <?php echo $slide['data']; ?> >
            <?php if ($preload_images): ?>
            <img src="<?php echo $dummy_image; ?>" data-lazyload="<?php echo $slide['image']; ?>" alt="<?php echo $slide['name']; ?>" />
            <?php else: ?>
            <img src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['name']; ?>" />
            <?php endif; ?>
            <?php foreach ($slide['captions'] as $caption): ?>
            <?php if ($caption['link']): ?>
            <a id="jcaption-<?php echo $caption['id']; ?>" href="<?php echo $caption['link']; ?>" <?php echo $caption['target']; ?> class="tp-caption <?php echo $caption['classes']; ?>" style="<?php echo $caption['css']; ?>" <?php echo $caption['data']; ?>>
            <?php echo $caption['content']; ?>
            </a>
            <?php else: ?>
            <div id="jcaption-<?php echo $caption['id']; ?>" class="tp-caption <?php echo $caption['classes']; ?>" style="<?php echo $caption['css']; ?>" <?php echo $caption['data']; ?>>
            <?php echo $caption['content']; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            </li>
            <?php endforeach; ?>
         </ul>
        <?php if ($timer === 'top'): ?>
        <div class="tp-bannertimer"></div>
        <?php elseif ($timer === 'bottom'): ?>
        <div class="tp-bannertimer tp-bottom"></div>
        <?php endif; ?>
    </div>
</div>
<script>
    (function () {
        $('<style><?php echo implode(" ", $global_style); ?></style>').appendTo($('head'));
        var module_id = 'journal-slider-<?php echo $module; ?>',
            module = $('#' + module_id),
            wrapper = module.parent('.journal-slider'),
            container_width = wrapper.width(),
            container_height = <?php echo $height; ?>;

        $(function() {
            var opts = $.parseJSON('<?php echo json_encode($js_options); ?>');
            opts.hideThumbs = 0;
            opts.startwidth = container_width;
            opts.startheight = container_height;
            //console.log(opts);
            $('#journal-slider-<?php echo $module; ?>').show().revolution(opts);
            <?php if ($timer !== 'top' && $timer !== 'bottom'): ?>
            $('#journal-slider-<?php echo $module; ?> .tp-bannertimer').hide();
            <?php endif; ?>
        });
    })();
</script>
