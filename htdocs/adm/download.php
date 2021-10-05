<?php
use classes\Db\ManuscriptContentNakalaSeeder;

// Debug F3 params
// var_dump($f3, $params);

// Call Nakala Seeder
$downloaded = (new ManuscriptContentNakalaSeeder(base64_decode($params['id'])))->handle();

// Debug returned Nakala Seeder value
// var_dump($downloaded);
// var_dump(json_encode($downloaded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

// Set response as JSON
header('Content-Type: text/json');

// Testing Nakala Seeder status
if ($downloaded['success'] === true) {
	// Add required client API status
	$downloaded['status'] = 'OK';

	// Add status message
	$downloaded['message'] = 'Manuscript added with success.';

	// Add baseHref
	$downloaded['baseHref'] = $f3->get('MR_PATH_WEB') . 'admin/edit';

	// Look for manuscript id
	/* foreach ($downloaded['data']->metas as $metas) {
		if ($metas->{'propertyUri'} === 'http://purl.org/dc/terms/bibliographicCitation') {
			// Add encoded manuscript id
			$downloaded['encodedId'] = base64_encode(strtoupper(str_replace(' ', '', $metas->value)));
			break;
		}
	} */

	// Display Nakala Download API Schema
	echo json_encode($downloaded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

// Return custom message for now
// TODO: Handle error message from ManuscriptContentNakalaSeeder
else {
	// Custom error message
	$error_content = new stdClass();
	$error_content->success = $downloaded['success'];
	$error_content->status = 'NOK';
	$error_content->error = 500;
	$error_content->message = 'Manuscript content download failed.';
	echo json_encode($error_content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}