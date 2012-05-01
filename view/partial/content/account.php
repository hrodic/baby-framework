<article class="row-fluid">
    <?php if($noblesCount < 1): ?>
    <section class="span12">
        <h2><?php echo _('Accolade'); ?>&nbsp;<a target="_blank" href="<?php echo _('http://en.wikipedia.org/wiki/Accolade'); ?>" class="btn btn-mini btn-info"><i class="icon-info-sign icon-white"></i> Learn more</a></h2>
        <p>
            <em>
            The accolade or adoubement is a ceremony to confer knighthood that may take many forms including, for example, 
            the tapping of the flat side of a sword on the shoulders of a candidate or an embrace about the neck.
            </em>
        </p><br />
        <p>Now, you remember your boyhood in the castle as a page and all your effort as squire.</p>
        <p>You served well the realm, and we are celebrating the Accolade ceremony in the Chapel of the Castle</p>
        <p>As a knight, you will be a member of the lower nobility.</p>
        <br />
        <p>
            <em>Knights of the medieval era were asked to "Protect the weak, defenseless, helpless, and fight for the general welfare of all".<br />
            They were trained in hunting, fighting, and riding, amongst other things. They were also trained to practise courteous, honorable behaviour.<br />
            A way of demonstrating military chivalry was to own expensive, heavy weaponry and horses.
            </em>
        </p>
    </section>
</article>
<article class="row-fluid">
    <section class="span12">
        <form class="ajax content full" action="/?c=account&m=accolade" method="post" id="form-accolade">
            <fieldset>
                <legend><?php echo _('I dub thee Sir ...'); ?></legend>
                <input required="required" name="name" type="text" placeholder="<?= _('Name'); ?>">
                <input required="required" name="kingdom" type="text" placeholder="<?= _('Kingdom'); ?>">
            </fieldset><br />
            <fieldset>
                <button type="submit"><?= _('Do it!'); ?></button>
            </fieldset>
        </form>
    </section>
    <?php else: ?>
    <section class="span12">
        <h1><a href="/gui">Play</a></h1>
    </section>
    <?php endif; ?>
</article>