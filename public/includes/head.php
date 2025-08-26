<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EventPlanner - Organiseer en beheer evenementen">
    <meta name="author" content="Dave de Visser">
    
    <!-- Dynamic Title -->
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - EventPlanner' : 'EventPlanner'; ?></title>
    
    <!-- CSS Files -->
    <?php
    // Slimme CSS pad detectie
    $currentPath = $_SERVER['REQUEST_URI'];
    if (strpos($currentPath, '/public/pages/') !== false) {
        // We zitten in de pages map (login.php, register.php, etc.)
        $cssPath = '../css/style.css';
    } else {
        // We zitten in de root (index.php)
        $cssPath = 'public/css/style.css';
    }
    ?>
    <link rel="stylesheet" href="<?php echo $cssPath; ?>">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>