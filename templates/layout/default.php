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
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">

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

    /* Broadcast Ticker Styles */
    .broadcast-ticker {
        flex: 1;
        height: 40px;
        overflow: hidden;
        background: transparent;
        border: 2px solid transparent;
        border-radius: 8px;
        margin: 0 20px;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .broadcast-ticker:hover,
    .broadcast-ticker.active {
        background: #1a1a1a;
        border-color: #333;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.5),
            0 1px 2px rgba(255, 204, 0, 0.1);
    }

    .broadcast-ticker.active:hover {
        background: #252525;
        border-color: #444;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.5),
            0 1px 4px rgba(255, 204, 0, 0.3);
    }

    .broadcast-ticker-text {
        white-space: nowrap;
        color: #ffcc00;
        font-size: 20px;
        font-family: 'VT323', monospace;
        font-weight: 400;
        letter-spacing: 2px;
        animation: scroll-left 20s linear infinite;
        padding-left: 100%;
        display: inline-block;
        text-shadow: 
            0 0 2px #ffcc00,
            0 0 4px rgba(255, 204, 0, 0.8),
            0 0 1px rgba(255, 204, 0, 0.5);
        text-transform: uppercase;
        filter: contrast(1.2);
    }

    @keyframes scroll-left {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-100%);
        }
    }

    
    </style>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand glitch-text" href="<?= $this->Url->build('/') ?>">📁 RBTkaFiles</a>
            
            <!-- Broadcast Ticker -->
            <div class="broadcast-ticker" id="broadcastTicker" title="Click to send a broadcast message"></div>
            
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
        <?php 
        $firebaseConfig = $this->get('firebaseConfig');
        if (!$firebaseConfig) {
            // Fallback: Load directly from config
            $firebaseConfig = \Cake\Core\Configure::read('Firebase');
        }
        ?>
        const firebaseConfig = {
            apiKey: "<?= h($firebaseConfig['apiKey'] ?? '') ?>",
            authDomain: "<?= h($firebaseConfig['authDomain'] ?? '') ?>",
            databaseURL: "<?= h($firebaseConfig['databaseURL'] ?? '') ?>",
            projectId: "<?= h($firebaseConfig['projectId'] ?? '') ?>",
            storageBucket: "<?= h($firebaseConfig['storageBucket'] ?? '') ?>",
            messagingSenderId: "<?= h($firebaseConfig['messagingSenderId'] ?? '') ?>",
            appId: "<?= h($firebaseConfig['appId'] ?? '') ?>"
        };
        
        
        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);
        
        // Make Firebase available globally
        window.firebaseApp = app;
        window.firebaseDatabase = database;
    </script>

    <!-- Firebase SDK v8 for Realtime Database (for broadcast) -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

    <script>
        // Initialize Firebase v8 for broadcast (using v8 for simpler API)
        if (!firebase.apps.length) {
            firebase.initializeApp({
                apiKey: "<?= h($firebaseConfig['apiKey'] ?? '') ?>",
                authDomain: "<?= h($firebaseConfig['authDomain'] ?? '') ?>",
                databaseURL: "<?= h($firebaseConfig['databaseURL'] ?? '') ?>",
                projectId: "<?= h($firebaseConfig['projectId'] ?? '') ?>",
                storageBucket: "<?= h($firebaseConfig['storageBucket'] ?? '') ?>",
                messagingSenderId: "<?= h($firebaseConfig['messagingSenderId'] ?? '') ?>",
                appId: "<?= h($firebaseConfig['appId'] ?? '') ?>"
            });
        }

        // Broadcast Ticker Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const ticker = document.getElementById('broadcastTicker');
            const broadcastQueueRef = firebase.database().ref('broadcastQueue');
            
            let isPlaying = false;
            let currentBroadcastKey = null;
            
            // Listen for broadcasts in the queue
            broadcastQueueRef.on('child_added', function(snapshot) {
                const broadcast = snapshot.val();
                const key = snapshot.key;
                
                // If not currently playing, start playing immediately
                if (!isPlaying) {
                    playBroadcast(broadcast.message, key);
                }
                // Otherwise, it will be picked up by the next check
            });
            
            // Play a broadcast message
            function playBroadcast(message, key) {
                isPlaying = true;
                currentBroadcastKey = key;
                
                ticker.innerHTML = '<span class="broadcast-ticker-text">' + escapeHtml(message).toUpperCase() + '</span>';
                ticker.classList.add('active');
                
                // Calculate animation duration based on message length
                const textElement = ticker.querySelector('.broadcast-ticker-text');
                const textWidth = textElement.offsetWidth;
                const containerWidth = ticker.offsetWidth;
                const duration = (textWidth + containerWidth) / 50; // pixels per second
                
                textElement.style.animationDuration = duration + 's';
                
                // Clear the message after animation completes
                setTimeout(function() {
                    // Remove this broadcast from queue
                    broadcastQueueRef.child(key).remove();
                    
                    ticker.innerHTML = '';
                    ticker.classList.remove('active');
                    isPlaying = false;
                    currentBroadcastKey = null;
                    
                    // Check if there's another broadcast waiting
                    checkNextBroadcast();
                }, duration * 1000);
            }
            
            // Check for next broadcast in queue
            function checkNextBroadcast() {
                broadcastQueueRef.orderByChild('timestamp').limitToFirst(1).once('value', function(snapshot) {
                    snapshot.forEach(function(childSnapshot) {
                        const broadcast = childSnapshot.val();
                        const key = childSnapshot.key;
                        
                        if (!isPlaying) {
                            playBroadcast(broadcast.message, key);
                        }
                    });
                });
            }
            
            // Click to send broadcast
            ticker.addEventListener('click', function() {
                const message = prompt('Enter your broadcast message:');
                if (message && message.trim()) {
                    broadcastQueueRef.push({
                        message: message.trim(),
                        timestamp: firebase.database.ServerValue.TIMESTAMP
                    });
                }
            });
            
            // HTML escape function for security
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
    
    <!-- Firepad CSS -->
    <link rel="stylesheet" href="https://cdn.firepad.io/releases/v1.5.9/firepad.css" />
    
    <!-- Firepad JS -->
    <script src="https://cdn.firepad.io/releases/v1.5.9/firepad.min.js"></script>
    
    <!-- Custom JS -->
    <?= $this->fetch('script') ?>
</body>
</html>
