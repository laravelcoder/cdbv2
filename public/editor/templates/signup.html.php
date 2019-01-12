<?php

/** @var array $lang */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
use \App\Controller\AuthControllerClass as AuthController;

$errors = AuthController::getFlash('errors');

?>

<h2 class="logo">
    <img src="<?php echo $config['logo_image']; ?>" alt="<?php echo $config['app_title']; ?>">
</h2>

<hr>

<h1><?php echo $lang['sign_up']; ?></h1>

<?php if( !empty( $errors ) && is_array( $errors ) ): ?>
    <div class="alert alert-danger">
        <?php echo implode( '<br>', $errors ); ?>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">

            <form action="<?php echo $config['base_url'] . $config['home_url']; ?>?action=signup" method="post">
                <div class="form-group">
                    <label for="registerFormEmail"><?php echo $lang['email_address']; ?>:</label>
                    <input type="email" name="email" class="form-control" id="registerFormEmail" value="<?php if (!empty($_POST['email'])) { echo $_POST['email']; } ?>" required>
                </div>
                <div class="form-group">
                    <label for="registerFormPassword"><?php echo $lang['password']; ?>:</label>
                    <input type="password" name="password" class="form-control" id="registerFormPassword" value="" required>
                </div>
                <div class="form-group">
                    <label for="registerFormPasswordConfirm"><?php echo $lang['confirm_password']; ?>:</label>
                    <input type="password" name="password_confirm" class="form-control" id="registerFormPasswordConfirm" value="" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <?php echo $lang['sign_up']; ?>
                </button>
            </form>

            <hr>
            <div class="my-3">
                <a href="<?php echo $config['base_url'] . $config['home_url']; ?>">
                    <?php echo $lang['login']; ?>
                </a>
            </div>

        </div>
    </div>
</div>
