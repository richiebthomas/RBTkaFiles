<!DOCTYPE html>
<html>
<head>
    <title>jQuery Test</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <h1>jQuery Test Page</h1>
    <div id="test">Test div</div>
    
    <script>
        console.log('Page loaded');
        console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'Not loaded');
        
        $(document).ready(function() {
            console.log('jQuery ready');
            $('#test').text('jQuery is working!').css('color', 'green');
        });
    </script>
</body>
</html>
