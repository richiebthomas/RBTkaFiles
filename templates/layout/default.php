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

    /* User Count Indicator */
    .user-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0;
        margin-right: 20px;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
        cursor: default;
    }

    .user-count:hover {
        color: rgba(255, 255, 255, 0.8);
    }

    .user-count-dot {
        width: 7px;
        height: 7px;
        background: #4ade80;
        border-radius: 50%;
        box-shadow: 0 0 6px rgba(74, 222, 128, 0.6);
        animation: pulse-glow 2s ease-in-out infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { 
            opacity: 1;
            transform: scale(1);
        }
        50% { 
            opacity: 0.6;
            transform: scale(0.95);
        }
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

    /* Collaborative cursor styles */
    .collab-cursor {
        position: fixed;
        pointer-events: none;
        z-index: 9999;
        transform: translate(-50%, -50%);
        transition: transform 0.15s linear;
    }

    .collab-cursor-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(0,0,0,0.3);
        border: 2px solid rgba(255,255,255,0.8);
    }

    .collab-cursor-label {
        position: absolute;
        top: 14px;
        left: 12px;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        color: #111827;
        background: rgba(255,255,255,0.9);
        box-shadow: 0 1px 2px rgba(0,0,0,0.25);
        white-space: nowrap;
    }

    .collab-cursor-label::before {
        content: '';
        position: absolute;
        left: -4px;
        top: 3px;
        border-width: 4px;
        border-style: solid;
        border-color: transparent rgba(255,255,255,0.9) transparent transparent;
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
            
            <div class="ms-auto d-flex align-items-center">
                <!-- User Count -->
                <div class="user-count" title="Users online right now">
                    <span class="user-count-dot"></span>
                    <span id="userCount">0</span>
                    <span style="opacity: 0.6; font-size: 11px;">online</span>
                </div>
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
    
    <!-- Firebase SDK v8 for Realtime Database (lazy loaded) -->
    <script defer src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

    <script>
        // Firebase configuration
        <?php 
        $firebaseConfig = $this->get('firebaseConfig');
        if (!$firebaseConfig) {
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
        
        // Lazy load Firebase features after main content is ready
        function initializeFirebaseFeatures() {
            // Check if Firebase is loaded
            if (typeof firebase === 'undefined') {
                // Retry after a short delay
                setTimeout(initializeFirebaseFeatures, 100);
                return;
            }
        
        // Initialize Firebase
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            // Initialize features when browser is idle
            const runWhenIdle = window.requestIdleCallback || function(cb) { setTimeout(cb, 1); };
            
            runWhenIdle(function() {
                initUserPresence();
                initBroadcastTicker();
                initRefreshSignalListener();
                initCollaborativeCursors();
            });
        }

        // User Presence Tracking
        function initUserPresence() {
            const presenceRef = firebase.database().ref('presence');
            const userCountElement = document.getElementById('userCount');
            
            if (!userCountElement) return;
            
            // Reuse a single user ID per browser (so multiple tabs count as one user)
            let userId = null;
            try {
                const storedId = window.localStorage.getItem('rbtka_presence_user_id');
                if (storedId && typeof storedId === 'string') {
                    userId = storedId;
                }
            } catch (e) {
                // localStorage might be unavailable; ignore and fall back to per-session ID
            }

            if (!userId) {
                userId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                try {
                    window.localStorage.setItem('rbtka_presence_user_id', userId);
                } catch (e) {
                    // Ignore storage errors; presence will still work per tab
                }
            }

            const myPresenceRef = presenceRef.child(userId);
            
            // Store userId globally so it can be accessed by file manager
            window.firebaseUserId = userId;
            
            // Function to update presence with current URL
            function updatePresenceWithUrl() {
                const currentUrl = window.location.pathname;
                myPresenceRef.set({
                    timestamp: firebase.database.ServerValue.TIMESTAMP,
                    url: currentUrl
                });
            }
            
            // Monitor connection state
            const connectedRef = firebase.database().ref('.info/connected');
            connectedRef.on('value', function(snapshot) {
                if (snapshot.val() === true) {
                    // When I disconnect, remove this user
                    myPresenceRef.onDisconnect().remove();
                    
                    // Add this user to presence list with current URL
                    updatePresenceWithUrl();
                }
            });
            
            // Update presence when URL changes (for single-page navigation)
            let lastUrl = window.location.pathname;
            setInterval(function() {
                const currentUrl = window.location.pathname;
                if (currentUrl !== lastUrl) {
                    lastUrl = currentUrl;
                    updatePresenceWithUrl();
                }
            }, 1000);
            
            // Listen for changes in user count
            presenceRef.on('value', function(snapshot) {
                const count = snapshot.numChildren();
                userCountElement.textContent = count;
            });
            
            // Store reference globally for refresh signal functionality
            window.firebasePresenceRef = presenceRef;
        }

        // Initialize Refresh Signal Listener
        function initRefreshSignalListener() {
            const refreshSignalsRef = firebase.database().ref('refreshSignals');
            
            // Listen for new refresh signals
            refreshSignalsRef.on('child_added', function(snapshot) {
                const signal = snapshot.val();
                const signalKey = snapshot.key;
                
                // Check if this signal is for the current URL
                const currentUrl = window.location.pathname;
                if (signal && signal.url === currentUrl) {
                    // Check if this signal is not from current user (avoid refreshing our own changes)
                    if (signal.userId !== window.firebaseUserId) {
                        // Auto-refresh after a short delay
                        setTimeout(function() {
                            if (typeof window.fileManager !== 'undefined' && window.fileManager) {
                                // If file manager exists, reload the current directory
                                window.fileManager.loadDirectory(window.fileManager.currentPath);
                            } else {
                                // Otherwise, reload the page
                                window.location.reload();
                            }
                        }, 500);
                    }
                    
                    // Clean up old signal (optional, but prevents database bloat)
                    setTimeout(function() {
                        refreshSignalsRef.child(signalKey).remove();
                    }, 5000);
                }
            });
        }
        
        // Collaborative cursors (Canva-style)
        function initCollaborativeCursors() {
            if (typeof firebase === 'undefined') return;
            const db = firebase.database();
            const cursorsRef = db.ref('cursors');
            const currentUrl = window.location.pathname;
            const myUserId = window.firebaseUserId || ('user_' + Math.random().toString(36).substr(2, 9));

            // Assign a stable color per user (simple hash to HSL)
            function colorForUser(userId) {
                let hash = 0;
                for (let i = 0; i < userId.length; i++) {
                    hash = ((hash << 5) - hash) + userId.charCodeAt(i);
                    hash |= 0;
                }
                const hue = Math.abs(hash) % 360;
                return 'hsl(' + hue + ', 80%, 55%)';
            }

            const myCursorRef = cursorsRef.child(myUserId);
            myCursorRef.onDisconnect().remove();

            // Track and throttle own cursor updates
            let lastSent = 0;
            const SEND_INTERVAL_MS = 120; // ~8 updates/sec

            function handleMouseMove(e) {
                const now = Date.now();
                if (now - lastSent < SEND_INTERVAL_MS) return;
                lastSent = now;
                const payload = {
                    x: e.clientX,
                    y: e.clientY,
                    url: currentUrl,
                    ts: firebase.database.ServerValue.TIMESTAMP,
                };
                myCursorRef.set(payload);
            }

            window.addEventListener('mousemove', handleMouseMove);

            // Remote cursors rendering
            const remoteCursors = {}; // userId -> { el, targetX, targetY }

            function ensureCursorElement(userId) {
                if (remoteCursors[userId]) return remoteCursors[userId];
                const el = document.createElement('div');
                el.className = 'collab-cursor';

                const dot = document.createElement('div');
                dot.className = 'collab-cursor-dot';
                dot.style.backgroundColor = colorForUser(userId);

                const label = document.createElement('div');
                label.className = 'collab-cursor-label';
                label.style.borderColor = colorForUser(userId);
                label.textContent = userId.replace(/^user_/, '');

                el.appendChild(dot);
                el.appendChild(label);

                document.body.appendChild(el);
                remoteCursors[userId] = {
                    el,
                    targetX: window.innerWidth / 2,
                    targetY: window.innerHeight / 2,
                };
                return remoteCursors[userId];
            }

            function removeCursorElement(userId) {
                const entry = remoteCursors[userId];
                if (entry) {
                    if (entry.el && entry.el.parentNode) {
                        entry.el.parentNode.removeChild(entry.el);
                    }
                    delete remoteCursors[userId];
                }
            }

            // Listen for cursor changes
            cursorsRef.on('child_added', function(snapshot) {
                const userId = snapshot.key;
                if (userId === myUserId) return;
                const data = snapshot.val();
                if (!data || data.url !== currentUrl) return;
                const cursor = ensureCursorElement(userId);
                cursor.targetX = data.x;
                cursor.targetY = data.y;
            });

            cursorsRef.on('child_changed', function(snapshot) {
                const userId = snapshot.key;
                if (userId === myUserId) return;
                const data = snapshot.val();
                if (!data || data.url !== currentUrl) {
                    removeCursorElement(userId);
                    return;
                }
                const cursor = ensureCursorElement(userId);
                cursor.targetX = data.x;
                cursor.targetY = data.y;
            });

            cursorsRef.on('child_removed', function(snapshot) {
                const userId = snapshot.key;
                if (userId === myUserId) return;
                removeCursorElement(userId);
            });

            // Smoothly animate cursor movement based on target positions
            function animateCursors() {
                Object.keys(remoteCursors).forEach((userId) => {
                    const cursor = remoteCursors[userId];
                    const el = cursor.el;
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const currentX = rect.left + rect.width / 2;
                    const currentY = rect.top + rect.height / 2;
                    const lerpFactor = 0.2;
                    const nextX = currentX + (cursor.targetX - currentX) * lerpFactor;
                    const nextY = currentY + (cursor.targetY - currentY) * lerpFactor;
                    el.style.left = nextX + 'px';
                    el.style.top = nextY + 'px';
                });
                requestAnimationFrame(animateCursors);
            }

            requestAnimationFrame(animateCursors);
        }

        // Broadcast Ticker Functionality
        function initBroadcastTicker() {
            const ticker = document.getElementById('broadcastTicker');
            if (!ticker) return;
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
        }

        // Start loading Firebase features after DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeFirebaseFeatures);
        } else {
            // DOM already loaded
            initializeFirebaseFeatures();
        }
    </script>
    
    <!-- Firepad CSS -->
    <link rel="stylesheet" href="https://cdn.firepad.io/releases/v1.5.9/firepad.css" />
    
    <!-- Firepad JS -->
    <script src="https://cdn.firepad.io/releases/v1.5.9/firepad.min.js"></script>
    
    <!-- Custom JS -->
    <?= $this->fetch('script') ?>
</body>
</html>
