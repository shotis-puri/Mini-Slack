<?php
function postMessage(int $channel, int $uid, string $body): bool {
    $body = trim($body);
    if ($body === '' || mb_strlen($body) > 2000) return false;
    db()->prepare('INSERT INTO messages(channel_id, user_id, body, created_at) VALUES(?,?,?,?)')
        ->execute([$channel, $uid, $body, time()]);
    return true;
}

// Messages in a channel with id greater than $afterId (used for polling).
function getMessages(int $channel, int $afterId = 0): array {
    $s = db()->prepare('SELECT m.id, u.name AS user, m.body, m.created_at AS ts
                        FROM messages m JOIN users u ON u.id=m.user_id
                        WHERE m.channel_id=? AND m.id>? ORDER BY m.id LIMIT 200');
    $s->execute([$channel, $afterId]);
    return $s->fetchAll();
}
