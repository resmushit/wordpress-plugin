<?php
	$pageCtrler = new stdclass();

	$pageCtrler->spaceSaved = reSmushitHelpers::formatSize(reSmushitStatistics::getSpaceSaved()['totalBytesSaved']);
	$pageCtrler->percentSaved = reSmushitStatistics::getSpaceSaved()['percentSaved'];

	$pageCtrler->attachmentsToOptimize = reSmushitStatistics::getUnoptimizedAttachmentsCount();
	$pageCtrler->percentLibraryOptimized = reSmushitStatistics::getOptimizedAttachmentsPercent();