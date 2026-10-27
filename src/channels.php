<?php
function listChannels(): array {
    return db()->query('SELECT id, name FROM channels ORDER BY name')->fetchAll();
}

// Creates the channel if needed; returns its id, or an error string for an invalid name.
function createChannel(string $name): int|string {
    $name = strtolower(trim($name));
    if (!preg_match('/^[a-z0-9_-]{2,20}$/', $name)) return 'Channel: 2-20 chars a-z 0-9 _ -';
    db()->prepare('INSERT OR IGNORE INTO channels(name) VALUES(?)')->execute([$name]);
    $s = db()->prepare('SELECT id FROM channels WHERE name=?');
    $s->execute([$name]);
    return (int)$s->fetchColumn();
}
