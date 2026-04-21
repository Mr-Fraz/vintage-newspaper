<?php
// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vintage Newspaper</title>
    <link rel="stylesheet" href="/assets/css/vintage.css">
</head>
<body>

<header>
    <h1>📰 Vintage Daily</h1>
    <p>Breaking News from Around the World</p>
</header>
