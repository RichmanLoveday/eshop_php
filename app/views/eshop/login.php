<?php $this->view("header", $data) ?>
<section id="form" style="margin-top: 5px; margin-bottom: 5px;">
    <!--form-->
    <div class="container" style="display: flex; justify-content: center;">
        <div class="col-sm-4 col-sm-offset-1">
            <div class="login-form">
                <!--login form-->
                <h2>Login to your account</h2>
                <?= print_error($data, 'email/password') ?>
                <form action="#" method="post">
                    <input name="email" type="email" value="<?= get_var('email') ?>" placeholder="Email Address" />
                    <?= print_error($data, 'email') ?>
                    <input name="password" value="<?= get_var('password') ?>" type="password" placeholder="Password" />
                    <?= print_error($data, 'password') ?>
                    <span>
                        <input type="checkbox" class="checkbox">Keep me signed in
                    </span>
                    <button type="submit" class="btn btn-default">Login</button>
                </form>
                <br>
                <a href="<?= ROOT ?>signup">Don't have an account? Signup here</a>
            </div>
            <!--/login form-->
        </div>
    </div>
</section>
<!--/form-->

<?php $this->view("footer") ?>