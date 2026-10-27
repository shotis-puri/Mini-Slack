<?php
const DB_PATH = __DIR__ . '/../data/app.sqlite';

// Returns the shared PDO connection, creating the schema and default channels on first use.
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY, name TEXT UNIQUE, pass TEXT);
                CREATE TABLE IF NOT EXISTS channels(id INTEGER PRIMARY KEY, name TEXT UNIQUE);
                CREATE TABLE IF NOT EXISTS messages(id INTEGER PRIMARY KEY, channel_id INT, user_id INT, body TEXT, created_at INT)');
    if (!$pdo->query('SELECT 1 FROM channels')->fetch()) {
        foreach (['general', 'random'] as $c) $pdo->prepare('INSERT INTO channels(name) VALUES(?)')->execute([$c]);
    }
    return $pdo;
}
