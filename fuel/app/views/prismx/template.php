<?php
    echo Html::doctype('html5');
    echo Html::meta('viewport', 'width=device-width, initial-scale=1.0');
?>
<title>PrismX</title>
<body>
    <?php echo View::forge('prismx/header'); ?>
    <body>
        <section class="content">
            <?php echo $content; ?>
        </section>
    </body>
    <footer>
        <caption><i class="fa fa-copyright"></i> Bonnie Dentinger</caption>
    </footer>
</html>