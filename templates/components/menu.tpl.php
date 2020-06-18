<menu>
	<ul>
		<li><a href="<?php echo reSmushitRouter::getRouteURL('optimize') ?>" class="<?php if(reSmushitRouter::getRoute() == 'optimize') { echo 'active'; } ?> typcn typcn-flash">Optimize</a></li>
		<li><a href="<?php echo reSmushitRouter::getRouteURL('tools') ?>" class="<?php if(reSmushitRouter::getRoute() == 'tools') { echo 'active'; } ?> typcn typcn-spanner">Tools</a></li>
		<li><a href="<?php echo reSmushitRouter::getRouteURL('statistics') ?>" class="<?php if(reSmushitRouter::getRoute() == 'statistics') { echo 'active'; } ?> typcn typcn-chart-bar">Statistics</a></li>
		<li><a href="<?php echo reSmushitRouter::getRouteURL('settings') ?>" class="<?php if(reSmushitRouter::getRoute() == 'settings') { echo 'active'; } ?> typcn typcn-input-checked">Settings</a></li>
		<li><a href="<?php echo reSmushitRouter::getRouteURL('logs') ?>" class="<?php if(reSmushitRouter::getRoute() == 'logs') { echo 'active'; } ?> typcn typcn-clipboard">Logs</a></li>
		<li><a href="<?php echo reSmushitRouter::getRouteURL('support') ?>" class="<?php if(reSmushitRouter::getRoute() == 'support') { echo 'active'; } ?> typcn typcn-info">Support</a></li>
	</ul>
</menu>