<?php
$route = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Remove folder name if exists
$basePath = 'Jsonproj';
if (strpos($route, $basePath) === 0) {
    $route = substr($route, strlen($basePath));
    $route = trim($route, '/');
}

// echo "ROUTE AFTER FIX: $route<br>"; // Debug

if ($route === '') {
    $route = 'dashboard';  
}

$routes = [
    'login' => 'login.php',
    'logout' => 'logout.php',
    'portfolio' => 'portfolio.php',
    'portfolio_details' => 'portfolio_details.php',
    'subject' => 'subject.php',
    'dashboard' => 'dashboard.php',
    'error' => 'error.php',
];
global $route; 
if (array_key_exists($route, $routes)) {
    include $routes[$route];
} else {
    echo "Route not found, including error.php";
    include 'error.php';
}
