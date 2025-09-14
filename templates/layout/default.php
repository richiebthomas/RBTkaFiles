<?php
/**
 * File Manager Layout
 */

$appTitle = 'RBTkaFiles';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        <?= $this->fetch('title') ?: $appTitle ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <?= $this->Html->css(['file-manager.min']) ?>

    <style>
    /* Navigation Bar Styles */
    .custom-navbar {
        background: rgba(0, 0, 0, 0.9);
        padding: 15px 30px;
        box-shadow: 0 4px 10px rgba(0, 255, 255, 0.2);
        transition: background 0.3s ease-in-out;
    }
    .custom-navbar:hover {
        background: rgba(0, 0, 0, 1);
    }

    .glitch-text {
        font-family: 'Orbitron', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #0ff;
        position: relative;
    }

    @keyframes glitch {
        0% { text-shadow: -2px -2px 0px rgba(255, 0, 0, 0.8), 2px 2px 0px rgba(0, 255, 0, 0.8); }
        50% { text-shadow: 2px -2px 0px rgba(255, 0, 0, 0.8), -2px 2px 0px rgba(0, 255, 0, 0.8); }
        100% { text-shadow: -2px 2px 0px rgba(255, 0, 0, 0.8), 2px -2px 0px rgba(0, 255, 0, 0.8); }
    }

    .glitch-text:hover {
        color: #ff4b2b;
        text-shadow: 0 0 12px rgba(255, 75, 75, 0.9);
        transition: color 0.3s ease-in-out;
        animation: glitch 0.3s infinite;
        text-decoration: none;
    }

    .navbar { 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    }

    /* Button styles in navbar */
    .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-1px);
    }
    </style>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand glitch-text" href="<?= $this->Url->build('/') ?>">📁 RBTkaFiles</a>
            <div class="ms-auto">
                <a href="<?= $this->Url->build('/marks') ?>" class="btn btn-outline-light me-2">
                    <i class="fas fa-graduation-cap"></i> Marks
                </a>
                <a href="<?= $this->Url->build('/pages/about') ?>" class="btn btn-outline-light">
                    <i class="fas fa-info-circle"></i> About
                </a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>

    <footer class="bg-dark text-light text-center py-3 mt-5">
        <div class="container">
            <small>
                &copy; <?= date('Y') ?> RBTkaFiles. All Right Reserved. Probably
            </small>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- Firebase SDK -->
    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getDatabase } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";
        
        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyAXY4HzCTejsyelLa5kuW3FEg2kjquw6zE",
            authDomain: "rbtpad-4221e.firebaseapp.com",
            databaseURL: "https://rbtpad-4221e-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "rbtpad-4221e",
            storageBucket: "rbtpad-4221e.firebasestorage.app",
            messagingSenderId: "678212231875",
            appId: "1:678212231875:web:b1bcc7fef0906abcd1f876"
        };
        
        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);
        
        // Make Firebase available globally
        window.firebaseApp = app;
        window.firebaseDatabase = database;
    </script>
    
    <!-- Firepad CSS -->
    <link rel="stylesheet" href="https://cdn.firepad.io/releases/v1.5.9/firepad.css" />
    
    <!-- Firepad JS -->
    <script src="https://cdn.firepad.io/releases/v1.5.9/firepad.min.js"></script>
    
    <!-- Custom JS -->
    <?= $this->fetch('script') ?>
</body>
</html>
