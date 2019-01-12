<?php

use App\Controller\BaseControllerClass;

/** @var array $config */
/** @var array $currentUser */
/** @var array $input */
/** @var array $lang */

$errors = BaseControllerClass::getFlash('errors');
$messages = BaseControllerClass::getFlash('messages');

?>
<div class="row">
    <div class="col-md-6">

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

        <p>
            <a href="<?php echo $config['base_url'] . $config['home_url']; ?>?action=users">
                &larr; <?php echo $lang['back']; ?>
            </a>
        </p>

        <form action="<?php echo $config['base_url'] . $config['home_url']; ?>?action=edit_user&user_id=<?php echo $input['id']; ?>" method="post">
            <div class="row form-group">
                <div class="col-md-5">
                    <label for="formFieldName"><?php echo $lang['name']; ?>:</label>
                </div>
                <div class="col-md-7">
                    <input class="form-control" name="name" value="<?php echo $input['name']; ?>" id="formFieldName">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-5">
                    <label for="formFieldEmail"><?php echo $lang['email']; ?>:</label>
                </div>
                <div class="col-md-7">
                    <input class="form-control" name="email" value="<?php echo $input['email']; ?>" id="formFieldEmail">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-5">
                    <label for="formFieldRole"><?php echo $lang['role']; ?>:</label>
                </div>
                <div class="col-md-7">
                    <input class="form-control" name="role" value="<?php echo $input['role']; ?>" id="formFieldRole">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-5">
                    <label for="formFieldType"><?php echo $lang['type']; ?>:</label>
                </div>
                <div class="col-md-7">
                    <select class="form-control" name="type" id="formFieldType">
                        <option value="basic"<?php if (isset($input['type']) && $input['type'] == 'basic'): ?> selected="selected"<?php endif; ?>>Basic</option>
                        <option value="advanced"<?php if (isset($input['type']) && $input['type'] == 'advanced'): ?> selected="selected"<?php endif; ?>>Advanced</option>
                    </select>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-md-7 offset-md-5">
                    <label>
                        <input type="checkbox" name="blocked" value="1"<?php if( !empty( $input['blocked'] ) ): ?> checked<?php endif; ?>>
                        <?php echo $lang['blocked']; ?>
                    </label>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-7 offset-md-5">
                    <label>
                        <input type="checkbox" name="confirmed" value="1"<?php if( !empty( $input['confirmed'] ) ): ?> checked<?php endif; ?>>
                        <?php echo $lang['confirmed']; ?>
                    </label>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $lang['submit']; ?>
                    </button>
                </div>
            </div>
        </form>

    </div>
    <div class="col-md-6">

        <?php if( !empty( $input['input_data'] ) && !empty( $input['input_data']['data'] ) ): ?>
        <div class="card card-primary mb-3">
            <div class="card-header card-inverse text-center"><?php echo $lang['input_files']; ?></div>
            <div class="max-height300">
                <ul class="list-group list-group-flush">
                    <?php foreach($input['input_data']['data'] as $item): ?>
                    <li class="list-group-item text-ellipsis">
                        <a href="<?php echo $item['url']; ?>" title="<?php echo $item['title']; ?>">
                            <span class="badge badge-warning">
                                <?php echo $item['ext']; ?>
                            </span>
                            <?php echo $item['title']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if( !empty( $input['output_data'] ) && !empty( $input['output_data']['data'] ) ): ?>
        <div class="card card-primary">
            <div class="card-header card-inverse text-center"><?php echo $lang['output_files']; ?></div>
            <div class="max-height300">
                <ul class="list-group list-group-flush">
                    <?php foreach($input['output_data']['data'] as $item): ?>
                        <li class="list-group-item text-ellipsis">
                            <div class="float-right">
                                <?php if(empty($item['allowed'])): ?>
                                    <a href="<?php echo $config['base_url'] . $config['home_url']; ?>?action=content_status_toggle&itemId=<?php echo $item['id']; ?>&userId=<?php echo $input['id']; ?>&type=output" data-toggle="tooltip" title="Approva">
                                        <i class="icon-cross"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo $config['base_url'] . $config['home_url']; ?>?action=content_status_toggle&itemId=<?php echo $item['id']; ?>&userId=<?php echo $input['id']; ?>&type=output" data-toggle="tooltip" title="Disapprova">
                                        <i class="icon-checkmark"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <a href="<?php echo $item['url']; ?>" title="<?php echo $item['title']; ?>">
                                <span class="badge badge-warning">
                                    <?php echo $item['ext']; ?>
                                </span>
                                <?php echo $item['title']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
