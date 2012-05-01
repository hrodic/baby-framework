<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="#"><?php echo _('Feudal-Online'); ?></a>
			<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-user"></i><?php echo \helper\Account::userName(); ?>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a href="/?c=account&m=profile"><?php echo _('Profile'); ?></a></li>
					<li class="divider"></li>
					<li><a href="/?c=account&m=signout"><?php echo _('Sign out'); ?></a></li>
				</ul>
			</div>
			<div class="nav-collapse">
				<ul class="nav">
					<li class="active"><a href="#">Realm</a></li>
					<li><a href="#kinship">Kinship</a></li>
					<li><a href="#search">Search</a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>