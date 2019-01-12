<?php

/** @var array $config */
require_once dirname(__DIR__) . '/config/config.php';

/** @var array $lang */
if (file_exists($config['root_path'] . "language/{$config['lang']}.php")) {
    require_once $config['root_path'] . "language/{$config['lang']}.php";
}

if( $config['debug'] ){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

session_start();

require_once $config['root_path'] . 'vendor/autoload.php';
use \App\Controller\BaseControllerClass as BaseController;

$controller = new BaseController($config, $lang);
$page_content = $controller->handleRequest();
$user = $controller->getUser(true);

$action = BaseController::getRequestAction('main');

if ($config['authentication']) {
    if (empty($user['id']) && !in_array($action, array('auth', 'signup', 'password_reset'))) {
        $action = 'auth';
    }
    if ($action == 'auth' && empty($user)) {
        // BaseController::redirectTo($config['base_url']);
    }
}

?>
<html>
<head>
    <title><?php echo $config['app_title']; ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="<?php echo $config['app_description']; ?>">
    <meta name="keywords" content="video edit, online editor, cut, video">

    <meta property="og:url" content="<?php echo BaseController::getCurrentBaseUrl(); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $config['app_title']; ?>">
    <meta property="og:description" content="<?php echo $config['app_description']; ?>">
    <meta property="og:image" content="<?php echo $config['logo_image']; ?>">

    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $config['base_url']; ?>assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['base_url']; ?>assets/img/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="<?php echo $config['base_url']; ?>favicon.ico">

    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>lib/bootstrap/dist/css/bootstrap.css?<?php echo $config['version']; ?>">
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>lib/jquery-ui/themes/smoothness/jquery-ui.min.css?<?php echo $config['version']; ?>">
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>assets/css/icomoon/style.css?<?php echo $config['version']; ?>">
    <link rel="stylesheet" href="<?php echo $config['base_url']; ?>assets/css/styles.css?<?php echo $config['version']; ?>">

    <script src="<?php echo $config['base_url']; ?>index.php?action=lang_script"></script>

    <?php if( $config['environment'] == 'dev' ): ?>
        <script src="<?php echo $config['base_url']; ?>lib/jquery/dist/jquery.min.js?<?php echo $config['version']; ?>"></script>
        <script src="<?php echo $config['base_url']; ?>lib/jquery-ui/jquery-ui.min.js?<?php echo $config['version']; ?>"></script>
        <script src="<?php echo $config['base_url']; ?>lib/tether/dist/js/tether.min.js?<?php echo $config['version']; ?>"></script>
        <script src="<?php echo $config['base_url']; ?>lib/bootstrap/dist/js/bootstrap.min.js?<?php echo $config['version']; ?>"></script>
        <script src="<?php echo $config['base_url']; ?>lib/underscore/underscore-min.js?<?php echo $config['version']; ?>"></script>

        <script src="<?php echo $config['base_url']; ?>assets/js/webvideoedit.js?<?php echo $config['version']; ?>"></script>
    <?php else: ?>
        <script src="<?php echo $config['base_url']; ?>assets/js/app.min.js?<?php echo $config['version']; ?>"></script>
    <?php endif; ?>

    <script>
        var webVideoEditor = new WebVideoEditor({
            baseUrl: '<?php echo $config['base_url']; ?>',
            requestHandler: '<?php echo $config['home_url']; ?>index.php'
        });
    </script>

</head>
<body>

<div class="container">

    <div class="card card-default">
        <div class="card-block">

            <?php
            if(file_exists( $config['root_path'] . "templates/{$action}.html.php")) {
                include $config['root_path'] . "templates/{$action}.html.php";
            } else {
                include $config['root_path'] . "templates/default.html.php";
            }
            ?>

        </div>
    </div>

</div>

<?php
if( file_exists( $config['root_path'] . "templates/{$action}_templates.html.php" ) ) {
    include $config['root_path'] . "templates/{$action}_templates.html.php";
}
?>

</body>
</html>