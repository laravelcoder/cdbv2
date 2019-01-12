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

    <h1><?php echo $lang['reset_password']; ?></h1>

<?php if( !empty( $errors ) && is_array( $errors ) ): ?>
    <div class="alert alert-danger">
        <?php echo implode( '<br>', $errors ); ?>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">

            <form action="<?php echo $config['base_url'] . $config['home_url']; ?>?action=password_reset" method="post">
                <div class="form-group">
                    <label for="loginFormEmail"><?php echo $lang['email_address']; ?>:</label>
                    <input type="email" name="email" class="form-control" id="loginFormEmail" value="<?php if (!empty($_POST['email'])) { echo $_POST['email']; } ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <?php echo $lang['submit']; ?>
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
