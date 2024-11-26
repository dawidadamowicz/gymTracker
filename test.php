<?php

require_once __DIR__ . '/Config.php';

try {
    $config = new Config();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

