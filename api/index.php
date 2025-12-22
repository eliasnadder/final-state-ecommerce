<?php

// Fix for Vercel serverless functions - ensure proper request handling
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

// Change to public directory for proper asset resolution
chdir(__DIR__ . '/../public');

require __DIR__ . '/../public/index.php';