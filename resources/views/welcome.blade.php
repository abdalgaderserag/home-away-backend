<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Links</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Test Navigation Links</h2>

        <nav>
            <ul class="space-y-4">
                <li>
                    <a href="/test/projects" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg">
                        Go to Projects
                    </a>
                </li>
                <li>
                    <a href="/test/projects/create" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg">
                        Create Project
                    </a>
                </li>
                <li>
                    <a href="/test/register" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg">
                        Register
                    </a>
                </li>
                <li>
                    <a href="/test/notifications" class="block w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg">
                        View Notifications
                    </a>
                </li>
                <li>
                    <a href="/test/verify" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75 transition duration-200 shadow-md hover:shadow-lg">
                        Verify Email
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</body>
</html>
