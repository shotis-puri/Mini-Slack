<?php
// Loaded by every entry point: starts the session and pulls in the app functions.
session_start();

require __DIR__ . '/db.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/channels.php';
require __DIR__ . '/messages.php';
