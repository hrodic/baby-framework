<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php _('Error'); ?></title>
    </head>
    <body>
        <h1><?php _('An error occurred'); ?></h1>
        <div>
            <p>type: <?=$type;?></p>
            <p>code: <?=$code;?></p>
            <p>message: <?=$message;?></p>
            <p>file: <?=$file;?></p>
            <p>line: <?=$line;?></p>
            <p><pre>trace: <?=print_r($trace, true); ?></pre></p>
        </div>
    </body>
</html>