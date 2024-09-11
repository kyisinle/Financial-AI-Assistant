<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Financial AI Chatbot</title>
</head>
<body>
    <div id="chatbox">
        <div id="messages"></div>
        <input type="text" id="input" placeholder="Enter your income and expenses...">
    </div>

    <script>
        document.getElementById('input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                var input = e.target.value;
                if (input.trim()) {
                    addMessage(input, 'user');
                    console.log("Sending input to Prolog:", input); // Debugging
                    sendToProlog(input);
                    e.target.value = '';
                }
            }
        });

        function addMessage(message, type) {
            var messages = document.getElementById('messages');
            var div = document.createElement('div');
            div.className = 'message ' + type + '-message';
            div.textContent = message;
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        }

        function sendToProlog(input) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log("Received response from Prolog:", xhr.responseText); // Debugging
                    addMessage(xhr.responseText, 'bot');
                } else {
                    console.error("Error response:", xhr.statusText); // Debugging
                }
            };
            xhr.send('input=' + encodeURIComponent(input));
        }

    </script>
</body>
</html>
