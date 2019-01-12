<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';
use \App\Controller\BaseControllerClass as BaseController;

/** @var array $config */
/** @var array $user */
/** @var array $page_content */

?>

<h2 class="logo">
    <a href="<?php echo $config['base_url'] . $config['home_url']; ?>">
        <img src="<?php echo $config['logo_image']; ?>" alt="<?php echo $config['app_title']; ?>">
    </a>
</h2>

<hr>

<?php
if(!empty($page_content['content'])){
    echo $page_content['content'];
}
?>
