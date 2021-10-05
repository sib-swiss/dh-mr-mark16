<?php
// Debug F3 params
// var_dump($f3, $params);

// Set response as JSON
header('Content-Type: text/json');

// Running cache removal command
$deletion_report = [];
exec('rm -rf ' . $f3->get('MR_CONFIG')->cache->path . '*', $output, $return_code);

// Testing cache removal status
if ($return_code === 0) {
	// Add required client API status
	$deletion_report['success'] = true;
	$deletion_report['status'] = 'OK';

	// Add status message
	if ($f3->get('MR_CONFIG')->debug === true) {
		$deletion_report['message']  = 'Cleared cache: ' . $f3->get('MR_CONFIG')->cache->path . PHP_EOL;
		$deletion_report['message'] .= 'Output: ' . print_r($output, true) . PHP_EOL;
		$deletion_report['message'] .= 'Return code: ' . $return_code . PHP_EOL;
	}
	else {
		$deletion_report['message'] = 'Cache cleared.';
	}

	// Return deletion report
	echo json_encode($deletion_report, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

// Return custom message for now
else {
	// Custom error message
	$error_content = new stdClass();
	$error_content->success = false;
	$error_content->status = 'NOK';
	$error_content->error = 500;

	// Add detailed status message
	if ($f3->get('MR_CONFIG')->debug === true) {
		$error_content->message  = 'Cleared cache: ' . $f3->get('MR_CONFIG')->cache->path . PHP_EOL;
		$error_content->message .= 'Output: ' . print_r($output, true) . PHP_EOL;
		$error_content->message .= 'Return code: ' . $return_code . PHP_EOL;
	}
	else {
		$error_content->message = 'Cache not cleared.';
	}

	// Return error message
	echo json_encode($error_content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}