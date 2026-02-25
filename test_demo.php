<?php

$baseUrl = 'http://nginx'; // El servicio de nginx en docker

function makeRequest($method, $url, $data = null, $token = null) {
    global $baseUrl;
    $options = [
        'http' => [
            'method'  => $method,
            'ignore_errors' => true,
            'header'  => "Content-Type: application/json\r\n"
        ]
    ];
    
    if ($token) {
        $options['http']['header'] .= "Authorization: Bearer $token\r\n";
    }
    
    if ($data) {
        $options['http']['content'] = json_encode($data);
    }
    
    $context  = stream_context_create($options);
    $response = file_get_contents($baseUrl . $url, false, $context);
    
    $statusLine = $http_response_header[0];
    preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
    $statusCode = $match[1];
    
    return [
        'status' => $statusCode,
        'body' => $response
    ];
}

echo "=== INICIANDO DEMO JWT ===\n\n";

// 1. GET /api/products sin token ® 401.
echo "1. GET /api/products sin token...\n";
$res1 = makeRequest('GET', '/api/products');
echo "   -> Codigo HTTP: " . $res1['status'] . "\n\n";

// 2. POST /api/login ® 200 + token.
echo "2. POST /api/login (Obtener Token de ROLE_USER)...\n";
$res2 = makeRequest('POST', '/api/login', ['username' => 'user@test.com', 'password' => '123456']);
echo "   -> Codigo HTTP: " . $res2['status'] . "\n";
echo "   -> Respuesta: " . $res2['body'] . "\n\n";
$userToken = json_decode($res2['body'], true)['token'] ?? null;

// 3. GET /api/products con token ® 200.
echo "3. GET /api/products con token ROLE_USER...\n";
$res3 = makeRequest('GET', '/api/products', null, $userToken);
echo "   -> Codigo HTTP: " . $res3['status'] . "\n\n";

// 4. POST /api/products con token ROLE_USER ® 403.
echo "4. POST /api/products con token ROLE_USER...\n";
$res4 = makeRequest('POST', '/api/products', ['name' => 'Demo User', 'price' => 10, 'stock' => 5], $userToken);
echo "   -> Codigo HTTP: " . $res4['status'] . "\n\n";

// 5. POST /api/products con token ROLE_ADMIN ® 201.
echo "5. POST /api/login (Obtener Token de ROLE_ADMIN)...\n";
$res5 = makeRequest('POST', '/api/login', ['username' => 'admin@test.com', 'password' => '123456']);
$adminToken = json_decode($res5['body'], true)['token'] ?? null;
echo "   -> Codigo HTTP: " . $res5['status'] . "\n\n";

echo "6. POST /api/products con token ROLE_ADMIN...\n";
$res6 = makeRequest('POST', '/api/products', ['name' => 'Demo Admin', 'price' => 20, 'stock' => 10], $adminToken);
echo "   -> Codigo HTTP: " . $res6['status'] . "\n";
echo "\n=== FIN EXITOSO ===\n";
