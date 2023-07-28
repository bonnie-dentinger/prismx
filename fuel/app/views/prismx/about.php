<?php
    echo View::forge('prismx/header');
    echo "<div class='content'>";
    echo "<section class='left'>";
    echo Asset::img('BonnieDentinger.jpg', array('class' => 'img-fluid', 'alt' => 'Bonnie Dentinger'));
    echo "</section>";
    echo "<section class='right'>";
    echo "<h1><strong>About the Developer</strong></h1><br>";
    echo "<p>My name is Bonnie Dentinger a senior at Colorado State University studying Computer Science. I have been a professional full-stack web developer for almost a year, and I am working on pursuing Software Engineering or
    continuing with web development. I have a passion for learning and I am always looking for new opportunities to grow and improve my skills. I have experience with HTML, CSS, JavaScript, PHP, Python, C++,
    and Java to name a few languages, various frameworks, as well as relational and non-relational databases. Challenges and design are my favorite parts of programming, and I love to work with others to create
    something beautiful and functional. I am looking forward to the future and the opportunities that await me!<br> <i class='fas fa-heart'></i> Bonnie</p>";
    echo "</section>";
    echo "</div>";
    echo View::forge('prismx/footer');
?>