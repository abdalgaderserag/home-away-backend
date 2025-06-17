<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notifications</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Custom font for better aesthetics */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            /* Light gray background */
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Your Notifications</h2>
        <div id="message-box" class="mb-4 hidden p-3 rounded-md text-sm" role="alert"></div>

        <div class="flex justify-center mb-6">
            <button id="fetch-notifications-button"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg">
                Fetch Notifications
            </button>
        </div>

        <div id="notifications-container" class="space-y-4">
            <!-- Notifications will be loaded here -->
            <p class="text-center text-gray-500" id="no-notifications-message">Click "Fetch Notifications" to see your
                alerts.</p>
        </div>
    </div>

    <script>
        document.getElementById('fetch-notifications-button').addEventListener('click', async () => {
            const notificationsContainer = document.getElementById('notifications-container');
            const messageBox = document.getElementById('message-box');
            const noNotificationsMessage = document.getElementById('no-notifications-message');

            // Clear previous messages and notifications
            messageBox.innerHTML = '';
            messageBox.classList.add('hidden');
            messageBox.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700',
                'bg-blue-100', 'text-blue-700');
            notificationsContainer.innerHTML = ''; // Clear existing notifications
            noNotificationsMessage.classList.add('hidden'); // Hide the initial message

            showMessage('Fetching notifications...', 'info');

            try {
                const response = await fetch('/api/user/notifications', {
                    method: 'POST', // As per your request, using POST
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // If authentication token is needed, add it here:
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                    },
                    // You can send a body if the API expects it, e.g., an empty JSON object
                    body: JSON.stringify({})
                });

                const data = await response.json();

                if (response.ok) {
                    console.log(data.messages);
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(notification => {
                            const notificationElement = document.createElement('div');
                            notificationElement.className =
                                'bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200';
                            notificationElement.innerHTML = `
                                <h3 class="font-semibold text-lg text-gray-800">${notification.data.type || 'Notification'}</h3>
                                <p class="text-gray-600 mt-1">${notification.data.message || 'No message provided.'}</p>
                                ${notification.data.timestamp ? `<p class="text-gray-400 text-sm mt-2">${new Date(notification.data.timestamp).toLocaleString()}</p>` : ''}
                            `;
                            notificationsContainer.appendChild(notificationElement);
                        });
                        showMessage('Notifications loaded successfully!', 'success');
                    } else {
                        console.log(1);
                        showMessage('No new notifications.', 'info');
                        const noNotificationsFound = document.createElement('p');
                        noNotificationsFound.className = 'text-center text-gray-500';
                        noNotificationsFound.textContent = 'No notifications found.';
                        notificationsContainer.appendChild(noNotificationsFound);
                    }
                } else {
                    const errorMessage = data.message || (data.errors ? Object.values(data.errors).flat().join(
                        '; ') : 'Failed to fetch notifications.');
                    showMessage(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                showMessage('An unexpected error occurred while fetching notifications. Please try again.',
                    'error');
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
            messageBox.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700', 'bg-blue-100',
                'text-blue-700'); // Remove all previous types

            if (type === 'success') {
                messageBox.classList.add('bg-green-100', 'text-green-700');
            } else if (type === 'error') {
                messageBox.classList.add('bg-red-100', 'text-red-700');
            } else if (type === 'info') {
                messageBox.classList.add('bg-blue-100', 'text-blue-700');
            }
        }
    </script>
</body>

</html>
