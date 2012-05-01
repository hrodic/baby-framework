<section class="column width4">
    <h2><?= _('You completed the signup form successfully!'); ?></h2>
    <p>
        <?php echo _('Congratulations'); ?> <strong><?= $signupForm['username']; ?> (<?=$signupForm['email']; ?>)</strong>
        <?php echo _('Shortly, you will receive an email with the instructions required to active your account and start playing.'); ?>
    </p>
</section>