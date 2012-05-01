<form class="content ajax full" method="post" action="/?c=index&m=signup" autocomplete="off">
    <fieldset>
        <legend><?= _('You can start playing right now for FREE. Create an account and join the Feudal Online world.'); ?></legend>	
        <label class="width1 first field-first">
            <?= _('Username'); ?> (3-16 max)
            <input pattern="<?php echo \model\form\Signup::getRules('username', 'regexp'); ?>" required="required" type="text" class="text" value="" name="username">
        </label>
        <label class="width1 field-email">
            <?= _('Email'); ?>
            <input required="required" type="email" class="text" value="" name="email">
        </label>
        <label class="width1 first field-password">
            <?= _('Password'); ?> (6-16 max)
            <input pattern="<?php echo \model\form\Signup::getRules('password', 'regexp'); ?>" required="required" type="password" class="text" value="" name="password">
        </label>
        <label class="width1 field-password">
            <?= _('Retype password'); ?>
            <input pattern="<?php echo \model\form\Signup::getRules('password', 'regexp'); ?>" required="required" type="password" class="text" value="" name="re-password">
        </label>
        <label class="width1 first field-state">
            <?= _('Language'); ?>
            <select required="required" name="locale">
                <option value=""></option>
                <option value="en_US">English</option>
                <option value="es_ES">Español</option>
            </select>
        </label>
        <label class="width1">
            <input required="required" type="checkbox" name="t&c">
            <a href="/t&c.html" target="_blank"><?= _('I accept the terms and conditions'); ?></a>
        </label>
    </fieldset>
    <fieldset>
        <!-- recaptcha configuration -->
        <script type="text/javascript">
            var RecaptchaOptions = {
               lang : 'en',
               theme: 'white'
            };
        </script>
        <?php helper\Captcha::recaptchaWidget(false); ?>
        <input type="submit" value="<?= _('Registrarse'); ?>"></label>
    </fieldset>
</form>