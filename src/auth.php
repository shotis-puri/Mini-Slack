<?php
function currentUser(): ?array {
    if (empty($_SESSION['uid'])) return null;
    $s = db()->prepare('SELECT id, name FROM users WHERE id=?');
    $s->execute([$_SESSION['uid']]);
    return $s->fetch() ?: null;
}

// Logs in an existing user, or registers them if the name is free.
function loginOrRegister(string $name, string $pass): string|true {
    $name = trim($name);
    if (!preg_match('/^[\w.-]{2,20}$/', $name)) return 'Username: 2-20 letters, digits, _ . -';
    if (strlen($pass) < 4) return 'Password must be at least 4 characters';
    $s = db()->prepare('SELECT * FROM users WHERE name=?');
    $s->execute([$name]);
    if ($u = $s->fetch()) {
        if (!password_verify($pass, $u['pass'])) return 'Wrong password';
        $_SESSION['uid'] = $u['id'];
    } else {
        db()->prepare('INSERT INTO users(name, pass) VALUES(?,?)')->execute([$name, password_hash($pass, PASSWORD_DEFAULT)]);
        $_SESSION['uid'] = db()->lastInsertId();
    }
    return true;
}
