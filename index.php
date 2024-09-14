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
        <div id="dots" class="dot-container">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        <input type="text" id="input" placeholder="Enter your financial question...">
    </div>

    <script>
        var dotsInterval;

        // Event listener for user input
        document.getElementById('input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                var input = e.target.value;
                if (input.trim()) {
                    addMessage(input, 'user');
                    disableInput(); // Disable input and start dots animation
                    showDotsAnimation(); // Show bouncing dots
                    sendToProlog(input);
                    e.target.value = ''; // Clear the input
                }
            }
        });

        // Initial greeting message
        window.onload = function() {
            var greetingMessage = "Hello! Welcome to your Financial AI Chatbot. How can I assist you today?";
            addMessage(greetingMessage, 'bot');
        };

        // Function to add messages to the chat
        function addMessage(message, type) {
            var messages = document.getElementById('messages');
            var div = document.createElement('div');
            div.className = 'message ' + type + '-message';
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;

            if (type === 'bot') {
                typeMessage(div, message); // Apply typing effect only to bot replies
            } else {
                div.textContent = message; // Directly set the text for user messages
            }
        }

        // Typing effect for bot responses
        function typeMessage(element, message, index = 0, speed = 25) {
            if (index < message.length) {
                element.textContent += message.charAt(index);
                index++;
                setTimeout(() => typeMessage(element, message, index, speed), speed);
            } else {
                enableInput(); // Re-enable input once bot has finished typing
                stopDotsAnimation(); // Stop running dots after bot response
            }
        }

        // Function to send user input to Prolog
        function sendToProlog(input) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    addMessage(xhr.responseText, 'bot');
                } else {
                    console.error("Error response:", xhr.statusText);
                }
            };
            xhr.send('input=' + encodeURIComponent(input));
        }

        // Disable user input and start dots animation
        function disableInput() {
            var input = document.getElementById('input');
            input.disabled = true;
            document.getElementById('dots').style.visibility = 'visible'; // Show bouncing dots
        }

        // Re-enable user input
        function enableInput() {
            var input = document.getElementById('input');
            input.disabled = false;
            input.placeholder = "Enter your financial question...";
            input.focus(); // Focus on the input field
        }

        // Show bouncing dots in the input field area
        function showDotsAnimation() {
            var dots = document.getElementById('dots');
            dots.style.visibility = 'visible';
        }

        // Stop the dots animation
        function stopDotsAnimation() {
            var dots = document.getElementById('dots');
            dots.style.visibility = 'hidden'; // Hide the dots after bot response
        }
    </script>
</body>
</html>
