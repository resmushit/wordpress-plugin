<div class="rsmt-app">
	<div class="rsmt-header">
		<div class="rsmt-logo">
			<img src="<?php echo RESMUSHIT_BASE_URL ?>/images/logo-white.png" alt="reSmush.it Logo" />
			<span>!</span>
		</div>
		<nav>
			<ul>
				<li><a href="https://ko-fi.com/resmushit" target="_blank" title="__('Support us by paying us a coffee')" class="active typcn typcn-heart-half-outline">Support us !</a></li>
				<li><a href="https://resmush.it" target="_blank" class="typcn typcn-code">Editor's website</a></li>
			</ul>
		</nav>
	</div>
	<div class="rsmt-main-wrapper">
		<?php reSmushitTemplate::loadComponent('menu'); ?>
		<div class="rsmt-page-<?php echo reSmushitRouter::getRoute(); ?>"><?php reSmushitTemplate::loadPage(reSmushitRouter::getRoute()); ?></div>

				
	</div>
</div>