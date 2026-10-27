<?php
require __DIR__ . '/../src/bootstrap.php';
if (isset($_GET['logout'])) { session_destroy(); header('Location: /'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $r = loginOrRegister($_POST['name'] ?? '', $_POST['pass'] ?? '');
    if ($r === true) { header('Location: /'); exit; }
    $err = $r;
}
$u = currentUser();
?><!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mini Slack</title>
<link rel="stylesheet" href="assets/app.css">
</head><body>
<?php if (!$u): ?>
<form class="login" method="post">
  <h1>Mini Slack</h1>
  <input name="name" placeholder="Username" required autofocus>
  <input name="pass" type="password" placeholder="Password (new names auto-register)" required>
  <?php if ($err): ?><div class="err"><?= htmlspecialchars($err) ?></div><?php endif ?>
  <button>Sign in / Sign up</button>
</form>
<?php else: ?>
<div id="side"><h2>Mini Slack</h2><div id="chs"></div>
  <form id="nc"><input placeholder="+ new channel" name="name"></form>
  <div id="me">@<?= htmlspecialchars($u['name']) ?> · <a href="?logout">log out</a></div></div>
<div id="main"><div id="head"></div><div id="msgs"></div>
  <form id="send"><input name="body" placeholder="Message…" autocomplete="off"><button>Send</button></form></div>
<script src="assets/app.js"></script>
<?php endif ?>
</body></html>
