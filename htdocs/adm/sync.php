<?php
use classes\Db\ManuscriptContentNakalaSeeder;

// Debug F3 params
// var_dump($f3, $params);

// Call Nakala Seeder
$synced = (new ManuscriptContentNakalaSeeder(base64_decode($params['id'])))->handle();

// Debug returned Nakala Seeder value
// var_dump($synced);
// var_dump(json_encode($synced, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

// Set response as JSON
header('Content-Type: text/json');

// Testing Nakala Seeder status
if ($synced['success'] === true) {
	// Add required client API status
	$synced['status'] = 'OK';

	// Add status message
	$synced['message'] = 'Manuscript synced with success.';

	// Add baseHref
	$synced['baseHref'] = $f3->get('MR_PATH_WEB') . 'admin/edit';

	// Add Nakala revision number
	$synced['revision'] = $synced['data']->version;

	// Look for manuscript id
	/* foreach ($synced['data']->metas as $metas) {
		if ($metas->{'propertyUri'} === 'http://purl.org/dc/terms/bibliographicCitation') {
			// Add encoded manuscript id
			$synced['encodedId'] = base64_encode(strtoupper(str_replace(' ', '', $metas->value)));
			break;
		}
	} */

	// Display Nakala Download API Schema
	echo json_encode($synced, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

// Return custom message for now
// TODO: Handle error message from ManuscriptContentNakalaSeeder
else {
	// Custom error message
	$error_content = new stdClass();
	$error_content->success = $synced['success'];
	$error_content->status = 'NOK';
	$error_content->error = 500;
	$error_content->message = 'Manuscript content sync failed.';
	echo json_encode($error_content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}