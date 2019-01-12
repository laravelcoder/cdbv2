<?php

/** @var array $lang */

?>
<p>
    <?php echo $lang['your_new_password']; ?>:
    <br>
    <b><?php echo $input['password_new']; ?></b>
</p>
<p>
    <?php echo $lang['to_confirm_click_here']; ?>:
    <br>
    <a href="<?php echo $input['site_url']; ?>?action=password_confirm&code=<?php echo $input['confirm_code']; ?>">
        <?php echo $input['site_url']; ?>?action=password_confirm&code=<?php echo $input['confirm_code']; ?>
    </a>
</p>