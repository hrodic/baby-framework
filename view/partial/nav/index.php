<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
        <div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<div class="nav-collapse">
				<ul class="nav">
					<li><a data-toggle="modal" href="#modal_terms_and_conditions"><?= _('Terms and conditions') ?></a></li>
					<li><a href="#"><?= _('Support') ?></a></li>
					<li><a data-toggle="modal" href="#modal_about"><?= _('About') ?></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<aside>
	<?= $modal_terms_and_conditions ?>
	<?= $modal_about ?>
</aside>