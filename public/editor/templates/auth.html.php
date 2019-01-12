<?php

/** @var array $lang */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
use \App\Controller\AuthControllerClass as AuthController;

$errors = AuthController::getFlash('errors');
$messages = AuthController::getFlash('messages');

?>

<h2 class="logo">
    <img src="<?php echo $config['logo_image']; ?>" alt="<?php echo $config['app_title']; ?>">
</h2>

<hr>

<p>
    <?php echo $config['app_description']; ?>
</p>

<hr>

<div>
    <a class="btn btn-primary my-2" href="<?php echo AuthController::getGoogleAuthUrl($config['facebook_app_id']); ?>">
        <i class="icon-google"></i>
        &nbsp;
        <?php echo $lang['signup_with_google']; ?>
    </a>
    <a class="btn btn-primary my-2" href="<?php echo AuthController::getFacebookAuthUrl($config['facebook_app_id']); ?>">
        <i class="icon-facebook2"></i>
        &nbsp;
        <?php echo $lang['signup_with_facebook']; ?>
    </a>
</div>

<?php if( !empty( $errors ) && is_array( $errors ) ): ?>
    <div class="alert alert-danger my-2">
        <?php echo implode( '<br>', $errors ); ?>
    </div>
<?php endif; ?>

<?php if( !empty( $messages ) && is_array( $messages ) ): ?>
    <div class="alert alert-info my-2">
        <?php echo implode( '<br>', $messages ); ?>
    </div>
<?php endif; ?>

<hr>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">

            <form action="<?php echo $config['base_url'] . $config['home_url']; ?>?action=auth" method="post">
                <div class="form-group">
                    <label for="loginFormEmail"><?php echo $lang['email_address']; ?>:</label>
                    <input type="email" name="email" class="form-control" id="loginFormEmail" value="<?php if(!empty($_POST['email'])){ echo $_POST['email']; } ?>" required>
                </div>
                <div class="form-group">
                    <label for="loginFormPassword"><?php echo $lang['password']; ?>:</label>
                    <input type="password" name="password" class="form-control" id="loginFormPassword" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <?php echo $lang['login']; ?>
                </button>
                &nbsp;
                <a href="<?php echo $config['base_url'] . $config['home_url']; ?>?action=password_reset">
                    <?php echo $lang['forgot_your_password']; ?>
                </a>
            </form>

            <hr>
            <div class="my-3">
                <?php echo $lang['need_an_account']; ?>
                <a href="<?php echo $config['base_url'] . $config['home_url']; ?>?action=signup">
                    <?php echo $lang['sign_up']; ?>
                </a>
            </div>

        </div>
    </div>
</div>