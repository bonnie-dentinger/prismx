<?php
    echo Html::doctype('html5');
    echo Html::meta('viewport', 'width=device-width, initial-scale=1.0');
    echo Asset::css('bootstrap.min.css');
    echo Asset::css('m1.css');
    echo Asset::js('https://code.jquery.com/jquery-3.6.3.min.js');
    echo Asset::js('https://kit.fontawesome.com/f34a3ea12f.js', array('crossorigin' => 'anonymous'));
?>
<title>PrismX</title>
<body>
    <nav>
        <?php echo Asset::img('PrismXLogo.svg', array('class' => 'logo', 'alt' => 'PrismX Logo')); ?>
        <?php echo Html::anchor('index.php/prismx/index', 'Index', array('class' => 'nav-link')); ?>
        <?php echo Html::anchor('index.php/prismx/colors', 'Colors', array('class' => 'nav-link')); ?>
        <?php echo Html::anchor('index.php/prismx/about', 'About', array('class' => 'nav-link')); ?>
        <p class="nav-title" style="display: none;">PrismX</p>
    </nav>