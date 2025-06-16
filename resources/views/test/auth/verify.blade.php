<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Custom font for better aesthetics */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Verify Email</h2>
        <div id="message-box" class="mb-4 hidden p-3 rounded-md text-sm" role="alert"></div>

        <form id="verify-form">
            <div class="mb-4">
                <label for="code" class="block text-gray-700 text-sm font-medium mb-2">Verification Code</label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="Enter your verification code"
                    required
                >
            </div>
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg"
            >
                Verify
            </button>
        </form>
        <button
            id="resend-code-button"
            class="w-full mt-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg"
        >
            Resend Code
        </button>
    </div>

    <script>
        document.getElementById('verify-form').addEventListener('submit', async (e) => {
            e.preventDefault(); // Prevent default form submission

            const codeInput = document.getElementById('code');
            const code = codeInput.value.trim();
            const messageBox = document.getElementById('message-box');

            // Clear previous messages
            messageBox.innerHTML = '';
            messageBox.classList.add('hidden');
            messageBox.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');

            if (!code) {
                showMessage('Please enter a verification code.', 'error');
                return;
            }

            try {
                const response = await fetch('/api/email/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                        // If authentication token is needed, add it here:
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                    },
                    body: JSON.stringify({ code: code })
                });

                const data = await response.json();

                if (response.ok) {
                    showMessage(data.message || 'Email verified successfully!', 'success');
                    // Optionally clear the input field
                    codeInput.value = '';
                } else {
                    // Handle API errors (e.g., validation errors, invalid code)
                    const errorMessage = data.message || (data.errors ? Object.values(data.errors).flat().join('; ') : 'Verification failed.');
                    showMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error during verification:', error);
                showMessage('An unexpected error occurred. Please try again.', 'error');
            }
        });

        // Event listener for the resend button
        document.getElementById('resend-code-button').addEventListener('click', async () => {
            const messageBox = document.getElementById('message-box');

            // Clear previous messages
            messageBox.innerHTML = '';
            messageBox.classList.add('hidden');
            messageBox.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');

            showMessage('Sending new verification code...', 'info'); // Provide immediate feedback

            try {
                const response = await fetch('/api/email/verify/resend', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showMessage(data.message || 'Verification code resent successfully!', 'success');
                } else {
                    const errorMessage = data.message || (data.errors ? Object.values(data.errors).flat().join('; ') : 'Failed to resend code.');
                    showMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error during resend:', error);
                showMessage('An unexpected error occurred while trying to resend the code. Please try again.', 'error');
            }
        });


        /**
         * Displays a message in the message box.
         * @param {string} message - The message to display.
         * @param {'success' | 'error' | 'info'} type - The type of message ('success', 'error', or 'info').
         */
        function showMessage(message, type) {
            const messageBox = document.getElementById('message-box');
            messageBox.textContent = message;
            messageBox.classList.remove('hidden');
            messageBox.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700', 'bg-blue-100', 'text-blue-700'); // Remove all previous types

            if (type === 'success') {
                messageBox.classList.add('bg-green-100', 'text-green-700');
            } else if (type === 'error') {
                messageBox.classList.add('bg-red-100', 'text-red-700');
            } else if (type === 'info') { // Added for informational messages
                messageBox.classList.add('bg-blue-100', 'text-blue-700');
            }
        }
    </script>
</body>
</html>
