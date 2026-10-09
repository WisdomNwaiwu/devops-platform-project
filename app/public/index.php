<?php
header('Content-Type: application/json');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function db(): PDO {
    $dsn = sprintf('pgsql:host=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_NAME'));
    return new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

if ($path === '/health') {
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($path === '/users') {
    try {
        echo json_encode(db()->query('SELECT id, name FROM users')->fetchAll(PDO::FETCH_ASSOC));
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'database unavailable']);
    }
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'not found']);