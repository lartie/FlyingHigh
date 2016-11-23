<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

Route::get('', ['as' => 'admin.dashboard', function () {

	$parameters = [];

	$loads=sys_getloadavg();
	$core_nums=trim(shell_exec("grep -P '^physical id' /proc/cpuinfo|wc -l"));
	$load=$loads[0]/$core_nums;

	$parameters['cpu'] = $load;

	$fh = fopen('/proc/meminfo','r');

	$total = 0;
	$free = 0;

	while ($line = fgets($fh)) {
		$pieces = array();
		if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
			$total = $pieces[1];
		} else if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
			$free = $pieces[1];
		}
	}
	fclose($fh);

	$parameters['memory'] =  floor($free/1024) . '/' . floor($total/1024);

	return AdminSection::view(view('admin.dashboard', $parameters), 'Dashboard');
}]);

Route::get('information', ['as' => 'admin.information', function () {
	$content = 'Define your information here.';
	return AdminSection::view($content, 'Information');
}]);