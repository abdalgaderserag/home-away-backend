<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Hub - Your Projects</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Custom scrollbar styles for pre and project list overflow */
        pre::-webkit-scrollbar,
        #projectList::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        pre::-webkit-scrollbar-thumb,
        #projectList::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 4px;
        }
        pre::-webkit-scrollbar-track,
        #projectList::-webkit-scrollbar-track {
            background-color: #f1f5f9; /* slate-100 */
        }
    </style>
</head>
<body class="font-sans bg-gray-50 text-gray-800 leading-relaxed min-h-screen flex flex-col">

    <header class="bg-blue-700 text-white p-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex flex-col sm:flex-row justify-between items-center">
            <h1 class="text-3xl font-bold mb-2 sm:mb-0">Project Hub</h1>
            <nav class="space-x-4">
                <a href="#projects" class="hover:text-blue-200 px-3 py-2 rounded-md transition duration-200">Projects</a>
                <a href="#api-tester" class="hover:text-blue-200 px-3 py-2 rounded-md transition duration-200">API Tester</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto my-8 p-6 bg-white rounded-lg shadow-lg flex-grow" id="projects">
        <h2 class="text-3xl font-bold text-blue-700 mb-6 border-b pb-3">Our Projects</h2>

        <div class="mb-6">
            <input type="text" id="projectSearch" placeholder="Search projects by title, description, or client name..."
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg transition duration-200">
        </div>

        <div id="projectList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[200px]">
            <p id="loadingProjects" class="col-span-full text-center text-gray-500 text-xl py-8">Loading projects...</p>
        </div>

        <div id="paginationControls" class="flex justify-center items-center mt-8 space-x-2">
            </div>
    </main>

    <section class="container mx-auto my-8 p-6 bg-white rounded-lg shadow-lg" id="api-tester">
        <h2 class="text-3xl font-bold text-blue-700 mb-6 border-b pb-3">API Tester Sandbox</h2>

        <div class="mb-5">
            <label for="apiUrl" class="block text-lg font-semibold text-gray-700 mb-2">API Endpoint:</label>
            <input type="text" id="apiUrl" value="/api/projects"
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base">
        </div>

        <div class="mb-5">
            <label for="requestMethod" class="block text-lg font-semibold text-gray-700 mb-2">Method:</label>
            <select id="requestMethod"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="requestBody" class="block text-lg font-semibold text-gray-700 mb-2">Request Body (JSON):</label>
            <textarea id="requestBody" rows="8" placeholder='Enter JSON body for POST/PUT requests (e.g., {"title": "New Project", "description": "Details"})'
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base font-mono"></textarea>
        </div>

        <button id="sendRequest"
                class="w-full py-3 px-6 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-250 ease-in-out transform hover:-translate-y-1">
            Send Request
        </button>

        <div class="mt-6 text-center">
            <a href="/test/projects/create"
               class="inline-block py-2 px-5 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700 transition duration-250 ease-in-out">
                Go to Create Project Page
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-700 mb-4">API Response Output</h2>
            <pre id="responseOutput"
                 class="bg-gray-50 p-6 rounded-lg border border-gray-200 text-gray-900 whitespace-pre-wrap break-words max-h-96 overflow-y-auto font-mono text-sm shadow-inner">—</pre>
        </div>
    </section>

    <footer class="bg-gray-800 text-white p-6 mt-10 text-center">
        <p>&copy; 2025 Project Hub. All rights reserved.</p>
    </footer>

    <script>
        const projectList = document.getElementById('projectList');
        const loadingProjects = document.getElementById('loadingProjects');
        const paginationControls = document.getElementById('paginationControls');
        const projectSearch = document.getElementById('projectSearch');

        let currentPage = 1;
        let currentSearchQuery = '';
        let debounceTimer;

        // Utility for debouncing search input
        const debounce = (func, delay) => {
            return function(...args) {
                const context = this;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(context, args), delay);
            };
        };

        // --- Project Listing & Search Logic ---
        async function fetchProjects(page = 1, query = '') {
            loadingProjects.classList.remove('hidden');
            projectList.innerHTML = ''; // Clear previous projects
            paginationControls.innerHTML = ''; // Clear pagination controls

            const token = localStorage.getItem('auth_token'); // Assuming auth_token is needed for projects API
            let headers = {};
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            const url = new URL('/api/projects', window.location.origin);
            url.searchParams.append('page', page);
            if (query) {
                url.searchParams.append('search', query); // Assuming your API supports 'q' for search
            }

            try {
                const res = await fetch(url.toString(), { headers });
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                const data = await res.json();
                loadingProjects.classList.add('hidden'); // Hide loading message
                renderProjects(data.data);
                renderPagination(data);
            } catch (err) {
                loadingProjects.textContent = `Failed to load projects: ${err.message}. Please try again later.`;
                loadingProjects.classList.add('text-red-600');
                console.error('Error fetching projects:', err);
            }
        }

        function renderProjects(projects) {
            if (projects.length === 0) {
                projectList.innerHTML = '<p class="col-span-full text-center text-gray-500 text-xl py-8">No projects found.</p>';
                return;
            }

            projects.forEach(project => {
                const projectCard = document.createElement('div');
                projectCard.className = 'bg-white border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition duration-200';
                projectCard.innerHTML = `
                    <div>
                        <h3 class="text-xl font-semibold text-blue-800 mb-2">${project.title}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">${project.description || 'No description provided.'}</p>
                        <div class="text-sm text-gray-700 space-y-1">
                            <p><strong class="font-medium">Client:</strong> ${project.client?.name || 'N/A'}</p>
                            <p><strong class="font-medium">Location:</strong> ${project.location_id ? `Location ID ${project.location_id}` : 'N/A'}</p>
                            <p><strong class="font-medium">Status:</strong> <span class="capitalize px-2 py-0.5 rounded-full text-xs ${project.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">${project.status}</span></p>
                            <p><strong class="font-medium">Space:</strong> ${project.space || 'N/A'} sqm</p>
                            <p><strong class="font-medium">Budget:</strong> $${project.min_price?.toLocaleString() || '0'} - $${project.max_price?.toLocaleString() || '0'}</p>
                            <p><strong class="font-medium">Deadline:</strong> ${project.deadline ? new Date(project.deadline).toLocaleDateString() : 'N/A'}</p>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="/test/projects/${project.id}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">View Details &rarr;</a>
                    </div>
                `;
                projectList.appendChild(projectCard);
            });
        }

        function renderPagination(meta) {
            paginationControls.innerHTML = ''; // Clear previous pagination
            if (meta.last_page <= 1) return; // No pagination needed for 1 or less pages

            const createButton = (label, page, active, disabled) => {
                const button = document.createElement('button');
                button.textContent = label.replace(/&laquo; Previous/, 'Previous').replace(/Next &raquo;/, 'Next');
                button.className = `px-4 py-2 rounded-lg text-sm font-medium transition duration-200 ${
                    active ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                } ${disabled ? 'opacity-50 cursor-not-allowed' : ''}`;
                button.disabled = disabled;
                if (!disabled) {
                    button.addEventListener('click', () => {
                        currentPage = page;
                        fetchProjects(currentPage, currentSearchQuery);
                        window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll to top on page change
                    });
                }
                return button;
            };

            // Previous Button
            const prevButton = createButton('Previous', meta.current_page - 1, false, !meta.prev_page_url);
            paginationControls.appendChild(prevButton);

            // Page numbers (simple approach: show all if few, or a range)
            meta.links.forEach(link => {
                if (link.url && link.label.match(/^\d+$/)) { // Only render numeric page links
                    const pageNum = parseInt(link.label);
                    const pageButton = createButton(link.label, pageNum, link.active, false);
                    paginationControls.appendChild(pageButton);
                }
            });

            // Next Button
            const nextButton = createButton('Next', meta.current_page + 1, false, !meta.next_page_url);
            paginationControls.appendChild(nextButton);
        }

        // Search event listener (debounced)
        projectSearch.addEventListener('keyup', debounce(() => {
            currentSearchQuery = projectSearch.value;
            currentPage = 1; // Reset to first page on new search
            fetchProjects(currentPage, currentSearchQuery);
        }, 300)); // 300ms debounce

        // --- API Tester Sandbox Logic (from previous version, slightly adapted) ---
        document.getElementById('sendRequest').addEventListener('click', async () => {
            const apiUrl = document.getElementById('apiUrl').value;
            const requestMethod = document.getElementById('requestMethod').value;
            const requestBody = document.getElementById('requestBody').value;
            const responseOutput = document.getElementById('responseOutput');

            responseOutput.textContent = 'Loading...';
            responseOutput.classList.remove('text-green-700', 'text-red-700', 'text-gray-900'); // Clear previous status colors

            const token = localStorage.getItem('auth_token');
            let options = {
                method: requestMethod,
                headers: {
                    'Content-Type': 'application/json'
                }
            };

            if (token) {
                options.headers['Authorization'] = 'Bearer ' + token;
            }

            if (requestMethod === 'POST' || requestMethod === 'PUT') {
                try {
                    options.body = JSON.stringify(JSON.parse(requestBody));
                } catch (e) {
                    responseOutput.textContent = 'Error: Invalid JSON body. Please check your syntax.';
                    responseOutput.classList.add('text-red-700', 'font-bold');
                    return;
                }
            } else {
                delete options.body;
                if (requestMethod === 'GET' || requestMethod === 'DELETE') {
                    delete options.headers['Content-Type'];
                }
            }

            try {
                const res = await fetch(apiUrl, options);
                const text = await res.text();
                let formattedJson;

                try {
                    formattedJson = JSON.stringify(JSON.parse(text), null, 2);
                    responseOutput.classList.add(res.ok ? 'text-green-700' : 'text-red-700');
                } catch {
                    formattedJson = text;
                    responseOutput.classList.add(res.ok ? 'text-gray-900' : 'text-red-700', !res.ok && 'font-bold');
                }

                responseOutput.textContent = `HTTP ${res.status} ${res.statusText}\n\n` + formattedJson;

            } catch (err) {
                responseOutput.textContent = 'Error: ' + err.message;
                responseOutput.classList.add('text-red-700', 'font-bold');
            }
        });

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            // Load projects when the page loads
            fetchProjects(currentPage, currentSearchQuery);

            // Pre-fill the API URL for the sandbox
            document.getElementById('apiUrl').value = '/api/projects';
        });
    </script>
</body>
</html>