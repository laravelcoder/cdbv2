<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
use \App\Controller\UsersControllerClass as UsersController;

/** @var array $config */
/** @var array $user */
/** @var array $page_content */
/** @var array $lang */

if( empty( $user ) || $user['role'] != 'admin' ){
    UsersController::redirectTo( $config['base_url'] . $config['home_url'] );
}

?>
<h2 class="logo">
    <a href="<?php echo $config['base_url'] . $config['home_url']; ?>">
        <img src="<?php echo $config['logo_image']; ?>" alt="<?php echo $config['app_title']; ?>">
    </a>
</h2>

<hr>

<h1>
    <?php echo $lang['users']; ?>
</h1>

<?php if( !empty( $page_content['data'] ) ): ?>
    <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo $lang['name']; ?></th>
                <th><?php echo $lang['social_url']; ?></th>
                <th><?php echo $lang['email']; ?></th>
                <th><?php echo $lang['role']; ?></th>
                <th><?php echo $lang['type']; ?></th>
                <th><?php echo $lang['confirmed']; ?></th>
                <th><?php echo $lang['actions']; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($page_content['data'] as $index => $user): ?>
            <tr>
                <th scope="row">
                    <?php echo $page_content['pages']['offset'] + $index + 1; ?>
                </th>
                <td>
                    <?php echo $user['name']; ?>
                </td>
                <td class="no-wrap">
                    <?php if( !empty( $user['facebook_id'] ) ): ?>
                        <a href="https://www.facebook.com/app_scoped_user_id/<?php echo $user['facebook_id']; ?>/" target="_blank">
                            <i class="icon-facebook"></i>
                            <?php echo $user['facebook_id']; ?>
                        </a>
                    <?php elseif( !empty( $user['google_id'] ) ): ?>
                        <a href="https://profiles.google.com/<?php echo $user['google_id']; ?>" target="_blank">
                            <i class="icon-google"></i>
                            <?php echo $user['google_id']; ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $user['email']; ?>
                </td>
                <td>
                    <?php if( !empty( $user['blocked'] ) ): ?>
                        <div class="badge badge-default big">Blocked</div>
                    <?php elseif( $user['role'] == 'admin' ): ?>
                        <div class="badge badge-warning badge-pill">
                            <?php echo $lang['admin']; ?>
                        </div>
                    <?php else: ?>
                        <div class="badge badge-default badge-pill">
                            <?php echo $lang['user']; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                        if ($user['role'] !== 'admin'
                            && !empty($user['type'])) {
                                echo ucfirst($user['type']);
                        }
                    ?>
                </td>
                <td class="text-center">
                    <?php if (!empty($user['confirmed'])) : ?>
                        <i class="icon-checkmark"></i>
                    <?php else: ?>
                        <i class="icon-cross"></i>
                    <?php endif; ?>
                </td>
                <td class="text-right no-wrap">
                    <a class="btn btn-secondary btn-sm" href="<?php echo $config['base_url'] . $config['home_url'] . '?action=edit_user&user_id=' . urlencode( $user['id'] ); ?>">
                        <?php echo $lang['edit']; ?>
                    </a>
                    <a class="btn btn-danger btn-sm" href="<?php echo $config['base_url'] . $config['home_url'] . '?action=delete_user&user_id=' . urlencode( $user['id'] ); ?>">
                        <?php echo $lang['delete']; ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php include __DIR__ . '/pagination.html.php'; ?>

<?php endif; ?>
