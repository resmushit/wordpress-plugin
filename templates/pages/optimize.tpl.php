<div class="flex flex-start">
	<div class="illustration">
		<img src="<?php echo RESMUSHIT_BASE_URL ?>/images/launch_day.svg" alt="reSmush.it Logo" />
	</div>
	<div class="flex flex-start w100">
		<div class="w50">
			<p class="data flex">
				<span class="figure"><?php echo $pageCtrler->spaceSaved; ?></span>
				<span class="description">saved on your library</span>
			</p>
			<p class="data flex">
				<span class="figure"><?php echo $pageCtrler->percentSaved; ?></span>
				<span class="description">average reduction</span>
			</p>
		</div>
		<div class="w50">
			<p class="data flex">
				<span class="figure"><?php echo $pageCtrler->percentLibraryOptimized; ?></span>
				<span class="description">of library optimized</span>
			</p>
		</div>
	</div>
</div>
<h2>Status</h2>
<div class="brdr-dashed text-center rsmt-status">
	<h3 class="typcn-center typcn typcn-flash-outline"><?php echo $pageCtrler->attachmentsToOptimize ?> pictures need to be optimized !</h3>
	<button class="button-primary" onclick="resmushit_bulk_resize(&quot;bulk_resize_image_list&quot;);">Launch optimization 🚀</button>
	<?php reSmushitTemplate::loadComponent('donut_chart'); ?>
</div>