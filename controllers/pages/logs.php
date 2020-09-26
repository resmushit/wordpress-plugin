<?php
	$pageCtrler = new stdclass();
	$pageCtrler->logs = trim(file_get_contents(ABSPATH . RESMUSHIT_LOGS_PATH));

	$pattern = '/\[[0-9]{2}-[0-9]{2}-[0-9]{4}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]/m';
	$replacement = '<strong>$0</strong>';
	$pageCtrler->logs =  preg_replace($pattern, $replacement, $pageCtrler->logs);