<?php
header('Content-Type: application/json');

$gradio_url = "https://6c53a600e9de935837.gradio.live";
$width = isset($_GET['width']) ? intval($_GET['width']) : 256;
$height = isset($_GET['height']) ? intval($_GET['height']) : 256;

// Erster Request: Event-ID holen mit Dimensionen
$api_url = $gradio_url . "/gradio_api/call/predict";
$data = array(
    "data" => array($width, $height),
    "fn_index" => 0
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    http_response_code(500);
    die(json_encode(['error' => 'First request failed']));
}

$response_data = json_decode($response, true);
if (!isset($response_data['event_id'])) {
    http_response_code(500);
    die(json_encode(['error' => 'No event ID received']));
}

// Zweiter Request: Ergebnis abfragen
$result_url = $gradio_url . "/gradio_api/call/predict/" . $response_data['event_id'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $result_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    http_response_code(500);
    die(json_encode(['error' => 'Second request failed']));
}

// Event-Stream parsen
if (preg_match('/data: \["(data:image\/[^"]+)"\]/', $response, $matches)) {
    $base64_image = $matches[1];
    $image_data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64_image));
    
    header('Content-Type: image/png');
    echo $image_data;
    exit;
}

http_response_code(500);
echo json_encode(['error' => 'No image data found']);
?>
