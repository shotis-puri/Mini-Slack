<?php
require __DIR__ . '/../src/bootstrap.php';
header('Content-Type: application/json');
$u = currentUser();
if (!$u) { http_response_code(401); exit('{"error":"auth"}'); }
$act = $_GET['a'] ?? '';
switch ($act) {
    case 'channels': echo json_encode(listChannels()); break;
    case 'messages': echo json_encode(getMessages((int)$_GET['c'], (int)($_GET['after'] ?? 0))); break;
    case 'send':
        echo json_encode(['ok' => postMessage((int)$_POST['c'], $u['id'], $_POST['body'] ?? '')]); break;
    case 'newchannel':
        $r = createChannel($_POST['name'] ?? '');
        echo json_encode(is_int($r) ? ['id' => $r] : ['error' => $r]); break;
    default: http_response_code(400); echo '{}';
}
