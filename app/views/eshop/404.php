<?php $this->view("header", $data) ?>

<body>
    <div class="container text-center" style="margin-bottom: 40px; margin-top: -70px; width: 50%; min-height: 200px;">
        <div class="logo-404">
            <a href="index.html"><img src="<?= IMAGES ?>home/logo.png" alt="" /></a>
        </div>
        <div class="content-404">
            <img style="width: 50%; height: 50%;" src="<?= IMAGES ?>404/404.png" class="img-responsive" alt="" />
            <h1 style="font-size: 25px;"><b>OPPS!</b> We Couldn’t Find this Page</h1>
            <p>Uh... So it looks like you brock something. The page you are looking for has up and Vanished.</p>
            <h2><a style="font-size: 25px;" href="<?= ROOT ?>">Bring me back Home</a></h2>
        </div>
    </div>
    <?php $this->view("footer", $data) ?>