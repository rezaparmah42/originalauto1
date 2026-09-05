<?php

// Ensure the admin directory entry uses the application's real MVC bootstrap.
// This prevents Apache from serving a standalone stub that bypasses the router,
// session handling, and auth flow used by /admin and /admin/login.
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../index.php';
exit;
