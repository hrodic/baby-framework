<form class="ajax content full" action="/?c=index&m=signin" method="post" id="form-signin">
    <input pattern="<?php echo \model\form\Signin::getRules('username', 'regexp'); ?>" required="required" name="username" type="username" placeholder="<?= _('Username'); ?>">
    <input pattern="<?php echo \model\form\Signin::getRules('password', 'regexp'); ?>" required="required" name="password" type="password" placeholder="<?= _('Password'); ?>">
    <button type="submit"><?= _('Log in'); ?></button>
    <a href=""><?= _('Recover password'); ?></a>
</form>