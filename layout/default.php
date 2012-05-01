<!doctype html>
<html lang="en">

	<head>
		<title><?= _('title') ?></title>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="description" content="<?= _('description') ?>">
		<meta name="keywords" content="<?= _('keywords') ?>">
		<meta name="author" content="author">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<!--<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Gentium+Book+Basic">-->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <?php echo helper\Bundler::getLink('JS') ?>
        <?php echo helper\Bundler::getLink('CSS') ?>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	
	<body>

		<div id="page">
			<?= empty($nav) ? null : $nav ?>
			<div class="container">
				<?= empty($content) ? null : $content ?>
			</div>
			<?= empty($footer) ? null : $footer ?>
		</div>
		
		<script>
		<?= empty($javascript) ? null : $javascript ?>
		</script>
	
	</body>

</html>
