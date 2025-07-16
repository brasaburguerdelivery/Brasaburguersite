<?php
header('Content-Type: application/json');

// Configurações básicas
$apiUrl = 'https://api.pushinpay.com.br/api/pix/cashIn';
$apiKey = '38274|APichzF70faIOos8ZIoanIHtCg5wmJuXyAfZq2Sy1963e538'; // Substitua pela sua chave de API real

// Obter dados do corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);
$value = isset($input['value']) ? intval($input['value']) : 0;

// Validar valor
if ($value <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valor inválido']);
    exit;
}

// Preparar dados para a API
$data = [
    'value' => $value,
    'webhook_url' => $input['webhook_url'] ?? '',
    'split_rules' => $input['split_rules'] ?? []
];

// Inicializar cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Accept: application/json',
    'Content-Type: application/json'
]);

// Executar a requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Verificar resposta
if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    echo json_encode([
        'success' => true,
        'data' => [
            'value' => $value,
            'qr_code_image' => $responseData['qr_code_image'] ?? '',
            'qr_code' => $responseData['qr_code'] ?? '',
            'transaction_id' => $responseData['transaction_id'] ?? ''
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao gerar PIX: ' . ($response ?: 'Sem resposta da API')
    ]);
}
?>