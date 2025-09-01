<?php
/**
 * CakePHP Error Page (400.php) customized to display as "404 Not Found"
 * Path: /templates/Error/error400.php
 *
 * Even though this is for HTTP 400, the visuals intentionally show 404.
 */
$this->disableAutoLayout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - File Not Found</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P:wght@400&display=swap');
        :root {
            --bg-color: #0a0a0a;
            --text-color: #00ff41;
            --accent-color: #003d1a;
            --poster-color: #f5f5dc;
            --poster-border: #8b4513;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: var(--bg-color);
            color: var(--text-color);
            font-family: 'Press Start 2P', monospace;
            min-height: 100vh;
            padding: 1rem;
            overflow-x: hidden;
        }
        .container { display: flex; align-items: center; justify-content: center; min-height: 100vh; max-width: 1200px; margin: 0 auto; }
        .poster-section { flex: 1; display: flex; align-items: center; justify-content: center; }
        .text-section { flex: 1; padding-left: 2rem; text-align: left; }
        .error-code { font-size: 4rem; margin-bottom: 2rem; text-shadow: 2px 2px 0px var(--accent-color); animation: glitch 5s infinite; }
        .typewriter { overflow: hidden; white-space: nowrap; border-right: 3px solid var(--text-color); animation: typing 2s steps(20, end), blink-caret 0.75s step-end infinite; }
        .typewriter-delay { animation-delay: 2s; animation-fill-mode: both; }
        @keyframes typing { from { width: 0; } to { width: 100%; } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes glitch { 0%, 100% { transform: translate(0); } 5% { transform: translate(-1px, 1px); } 10% { transform: translate(1px, -1px); } 15% { transform: translate(-1px, -1px); } 20% { transform: translate(1px, 1px); } 95% { transform: translate(0); } }
        .missing-poster { width: 320px; height: 400px; background: var(--poster-color); border: 3px solid var(--poster-border); position: relative; box-shadow: 0 8px 20px rgba(0,0,0,0.8); animation: sway 3s ease-in-out infinite; color: #333; font-family: 'Courier New', monospace; padding: 20px; }
        @keyframes sway { 0%, 100% { transform: rotate(-1deg); } 50% { transform: rotate(1deg); } }
        .missing-poster::before { content: ''; position: absolute; top: -10px; left: 50%; transform: translateX(-50%); width: 40px; height: 20px; background: #8b4513; clip-path: polygon(0% 0%, 100% 0%, 90% 100%, 10% 100%); }
        .poster-title { font-size: 1.2rem; font-weight: bold; margin-bottom: 15px; text-align: center; color: #8b0000; border-bottom: 2px solid #8b0000; padding-bottom: 10px; }
        .file-photo { width: 120px; height: 120px; background: #ddd; margin: 15px auto; border: 2px solid #333; position: relative; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #666; }
        .file-photo::before { content: '📄'; font-size: 3rem; opacity: 0.8; position: absolute; filter: hue-rotate(45deg) saturate(2) brightness(1.2); }
        .file-photo::after { content: '404'; position: absolute; bottom: 5px; right: 5px; font-size: 0.6rem; color: #999; background: white; padding: 2px 4px; }
        .poster-details { text-align: left; font-size: 0.5rem; line-height: 1.8; margin-top: 15px; }
        .detail-row { margin-bottom: 8px; }
        .label { font-weight: bold; display: inline-block; width: 60px; }
        .reward { text-align: center; margin-top: 20px; padding: 8px; border: 2px dashed #8b0000; background: #ffe4e1; font-size: 0.6rem; color: #8b0000; font-weight: bold; }
        .thumbtacks { position: absolute; width: 8px; height: 8px; background: #ff6b6b; border-radius: 50%; box-shadow: inset -2px -2px 3px rgba(0,0,0,0.3); }
        .tack-1 { top: 15px; left: 15px; } .tack-2 { top: 15px; right: 15px; } .tack-3 { bottom: 30px; left: 30px; } .tack-4 { bottom: 30px; right: 30px; }
        .message { margin: 2rem 0; line-height: 1.8; }
        .message h1 { font-size: 1.1rem; margin-bottom: 1.5rem; }
        .submessage { font-size: 0.7rem; color: #888; line-height: 2; margin-bottom: 1rem; }
        .submessage-line { display: block; margin-bottom: 0.5rem; }
        .return-btn { background: transparent; color: var(--text-color); border: 2px solid var(--text-color); padding: 12px 24px; font-family: 'Press Start 2P', monospace; font-size: 0.6rem; margin-top: 2rem; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .return-btn:hover { background: var(--text-color); color: var(--bg-color); box-shadow: 0 0 8px var(--text-color); }
        @media (max-width: 768px) {
            .container { flex-direction: column; text-align: center; }
            .text-section { padding-left: 0; padding-top: 2rem; text-align: center; }
            .error-code { font-size: 2.5rem; }
            .missing-poster { width: 240px; height: 300px; padding: 15px; }
            .file-photo { width: 80px; height: 80px; }
            .file-photo::before { font-size: 2rem; }
            .poster-title { font-size: 1rem; }
            .typewriter { white-space: normal; border-right: none; animation: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="poster-section">
            <div class="missing-poster">
                <div class="thumbtacks tack-1"></div>
                <div class="thumbtacks tack-2"></div>
                <div class="thumbtacks tack-3"></div>
                <div class="thumbtacks tack-4"></div>
                <div class="poster-title">MISSING CASE FILE</div>
                <div class="file-photo"></div>
                <div class="poster-details">
                    <div class="detail-row"><span class="label">NAME:</span> unknown_file.html</div>
                    <div class="detail-row"><span class="label">TYPE:</span> Web Resource</div>
                    <div class="detail-row"><span class="label">SIZE:</span> Variable</div>
                    <div class="detail-row"><span class="label">LAST SEEN:</span> Server logs unclear</div>
                    <div class="detail-row"><span class="label">STATUS:</span> Not Found</div>
                </div>
                <div class="reward">REWARD: SUCCESSFUL NAVIGATION IF FOUND, PLEASE RETURN TO HOMEPAGE</div>
            </div>
        </div>
        <div class="text-section">
            <div class="error-code">404</div>
            <div class="message">
                <h1 class="typewriter">FILE NOT FOUND</h1>
                <div class="submessage">
                    <span class="submessage-line typewriter typewriter-delay">The requested resource has gone missing from our servers.</span>
                    <span class="submessage-line typewriter" style="animation-delay: 4s; animation-fill-mode: both;">If you have any information about its whereabouts,</span>
                    <span class="submessage-line typewriter" style="animation-delay: 6s; animation-fill-mode: both;">please contact the webmaster.</span>
                </div>
            </div>
            <a href="/" class="return-btn" style="animation: fadeIn 1s ease-in 8s both;">RETURN HOME</a>
        </div>
    </div>
</body>
</html>
