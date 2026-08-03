<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Test Alpine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div x-data="{ open: false }">
        <button id="test-button" @click="open = !open">Toggle</button>
        <div id="test-content" x-show="open" style="display: none;">
            Content
        </div>
    </div>
    
    <script>
        setTimeout(() => {
            const btn = document.getElementById('test-button');
            const content = document.getElementById('test-content');
            console.log("Before click, display:", content.style.display);
            btn.click();
            setTimeout(() => {
                console.log("After click, display:", content.style.display);
                
                // Write results to a file we can read
                fetch('/test-alpine-result?result=' + content.style.display)
            }, 100);
        }, 1000);
    </script>
</body>
</html>
