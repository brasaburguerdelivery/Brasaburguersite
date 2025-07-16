<?php
header('Content-Type: application/json');

$apiUrl = 'https://api.pushinpay.com.br/api/transactions/';
$apiToken = '38274|APichzF70faIOos8ZIoanIHtCg5wmJuXyAfZq2Sy1963e538';
$pixId = $_GET['id'] ?? '';

if (empty($pixId)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do PIX não informado']);
    exit;
}

try {
    $ch = curl_init($apiUrl . $pixId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiToken,
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode == 404) {
        echo json_encode(['status' => 'not_found']);
        exit;
    }
    
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    
    $data = json_decode($response, true);
    echo json_encode(['status' => $data['status'] ?? 'unknown']);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>