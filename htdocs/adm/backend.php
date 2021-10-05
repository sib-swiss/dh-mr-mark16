<?php
if (isset($f3, $params)) {
    $response = new stdClass();

    // Debug info
    if ($f3->get('MR_CONFIG')->debug === true) {
        $response->data = [
            'POST' => $f3->get('POST'),
            'FILES' => $f3->get('FILES')
        ];
        $response->params = $params;
        $response->framework = (array) $f3;
    }

    // Returned data
    if (!empty($f3->get('FILES'))) {
        $response->success = true;
        $response->message = 'Image sent to the server';
    }
    elseif (!empty($f3->get('POST.manuscript_folio_image_content'))) {
        $response->success = true;
        $response->message = 'Image sent to the server';
        $response->details = json_decode($f3->get('POST.manuscript_folio_image_metas'));
    }
    else {
        $response->success = false;
        $response->message = 'Unable to receive file data';
    }

    // Set response as JSON
    header('Content-Type: text/json');

    // Return backend response
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}