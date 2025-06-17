<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Project - Project Hub</title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Custom scrollbar styles for overflow content */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 4px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background-color: #f1f5f9; /* slate-100 */
        }
        /* Styles for the custom confirmation modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            padding: 2.5rem;
            border-radius: 0.75rem; /* Equivalent to rounded-lg */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Equivalent to shadow-xl */
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
    </style>
</head>
<body class="font-sans bg-gray-50 text-gray-800 leading-relaxed min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-blue-700 text-white p-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex flex-col sm:flex-row justify-between items-center">
            <h1 class="text-3xl font-bold mb-2 sm:mb-0">Project Hub</h1>
            <nav class="space-x-4">
                <a href="/" class="hover:text-blue-200 px-3 py-2 rounded-md transition duration-200">Home (Projects)</a>
                <a href="#api-tester-placeholder" class="hover:text-blue-200 px-3 py-2 rounded-md transition duration-200">API Tester (Link to previous page)</a>
            </nav>
        </div>
    </header>

    <!-- Main Content Area: Project Details -->
    <main class="container mx-auto my-8 p-6 bg-white rounded-lg shadow-lg flex-grow">
        <div id="projectDetailsContainer">
            <!-- Loading state -->
            <div id="loadingState" class="text-center text-gray-500 text-xl py-16">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p>Loading project details...</p>
            </div>

            <!-- Error state -->
            <div id="errorState" class="hidden text-center text-red-600 text-xl py-16">
                <p>Failed to load project details.</p>
                <p id="errorMessage" class="text-base mt-2"></p>
                <button onclick="window.location.reload()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Try Again</button>
            </div>

            <!-- Project details will be rendered here -->
            <div id="projectContent" class="hidden">
                <h2 class="text-4xl font-bold text-blue-700 mb-6 border-b pb-4" id="projectTitle"></h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Project Overview -->
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Overview</h3>
                        <div class="space-y-3 text-lg text-gray-700">
                            <p><strong class="font-medium text-gray-900">Client:</strong> <span id="projectClient"></span></p>
                            <p><strong class="font-medium text-gray-900">Status:</strong> <span id="projectStatus" class="capitalize px-3 py-1 rounded-full text-sm font-semibold"></span></p>
                            <p><strong class="font-medium text-gray-900">Space:</strong> <span id="projectSpace"></span> sqm</p>
                            <p><strong class="font-medium text-gray-900">Deadline:</strong> <span id="projectDeadline"></span></p>
                            <p><strong class="font-medium text-gray-900">Budget:</strong> $<span id="projectMinPrice"></span> - $<span id="projectMaxPrice"></span></p>
                            <p><strong class="font-medium text-gray-900">Location ID:</strong> <span id="projectLocation"></span></p>
                            <p><strong class="font-medium text-gray-900">Skill ID:</strong> <span id="projectSkill"></span></p>
                            <p><strong class="font-medium text-gray-900">Unit Type ID:</strong> <span id="projectUnitType"></span></p>
                        </div>
                    </div>

                    <!-- Project Description -->
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Description</h3>
                        <p id="projectDescription" class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-200"></p>
                    </div>
                </div>

                <!-- Offers Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-3xl font-bold text-blue-700 mb-6">Project Offers (<span id="offersCount">0</span>)</h3>
                    <div id="offersList" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Offers will be dynamically loaded here -->
                        <p id="noOffersMessage" class="col-span-full text-center text-gray-500 text-lg py-8 hidden">No offers for this project yet.</p>
                    </div>
                </div>

                <!-- Send an Offer Section -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h3 class="text-3xl font-bold text-blue-700 mb-6">Send an Offer</h3>
                    <form id="offerForm" class="space-y-4">
                        <div>
                            <label for="offerPrice" class="block text-lg font-semibold text-gray-700 mb-2">Offer Price ($):</label>
                            <input type="number" id="offerPrice" name="price" placeholder="e.g., 15000" min="0" required
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base">
                        </div>
                        <div>
                            <label for="offerDescription" class="block text-lg font-semibold text-gray-700 mb-2">Your Offer Description:</label>
                            <textarea id="offerDescription" name="description" rows="5" placeholder="Describe your offer, approach, and why you're a good fit for this project." required
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div>
                                <label for="offerStartDate" class="block text-lg font-semibold text-gray-700 mb-2">Start Date:</label>
                                <input type="date" id="offerStartDate" name="start_date" required
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base">
                            </div>
                            <div>
                                <label for="offerDeadline" class="block text-lg font-semibold text-gray-700 mb-2">Your Deadline:</label>
                                <input type="date" id="offerDeadline" name="deadline" required
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base">
                            </div>
                        </div>
                        <div>
                            <label for="offerExpireDate" class="block text-lg font-semibold text-gray-700 mb-2">Offer Expiry Date:</label>
                            <input type="date" id="offerExpireDate" name="expire_date" required
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 ease-in-out text-base">
                        </div>
                        
                        <button type="submit" id="submitOfferBtn"
                                class="w-full py-3 px-6 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75 transition duration-250 ease-in-out transform hover:-translate-y-1">
                            Submit Offer
                        </button>
                        <div id="offerMessage" class="mt-4 p-3 rounded-lg text-center hidden"></div>
                    </form>
                </div>

                <div class="mt-10 text-center">
                    <a href="/" class="inline-block py-3 px-8 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700 transition duration-250 ease-in-out text-lg">
                        &larr; Back to All Projects
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Custom Confirmation Modal -->
    <div id="confirmationModal" class="modal-overlay hidden">
        <div class="modal-content">
            <p id="confirmationMessage" class="text-xl font-semibold text-gray-800 mb-6"></p>
            <div class="flex justify-center space-x-4">
                <button id="confirmYesBtn" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">Yes</button>
                <button id="confirmNoBtn" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition duration-200">No</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const loadingState = document.getElementById('loadingState');
            const errorState = document.getElementById('errorState');
            const errorMessage = document.getElementById('errorMessage');
            const projectContent = document.getElementById('projectContent');

            const projectTitle = document.getElementById('projectTitle');
            const projectClient = document.getElementById('projectClient');
            const projectStatus = document.getElementById('projectStatus');
            const projectSpace = document.getElementById('projectSpace');
            const projectDeadline = document.getElementById('projectDeadline');
            const projectMinPrice = document.getElementById('projectMinPrice');
            const projectMaxPrice = document.getElementById('projectMaxPrice');
            const projectLocation = document.getElementById('projectLocation');
            const projectSkill = document.getElementById('projectSkill');
            const projectUnitType = document.getElementById('projectUnitType');
            const projectDescription = document.getElementById('projectDescription');
            const offersCount = document.getElementById('offersCount');
            const offersList = document.getElementById('offersList');
            const noOffersMessage = document.getElementById('noOffersMessage');

            // Offer form elements
            const offerForm = document.getElementById('offerForm');
            const offerPriceInput = document.getElementById('offerPrice');
            const offerDescriptionInput = document.getElementById('offerDescription');
            const offerStartDateInput = document.getElementById('offerStartDate');
            const offerDeadlineInput = document.getElementById('offerDeadline');
            const offerExpireDateInput = document.getElementById('offerExpireDate');
            const submitOfferBtn = document.getElementById('submitOfferBtn');
            const offerMessageDiv = document.getElementById('offerMessage');

            // Confirmation Modal elements
            const confirmationModal = document.getElementById('confirmationModal');
            const confirmationMessage = document.getElementById('confirmationMessage');
            const confirmYesBtn = document.getElementById('confirmYesBtn');
            const confirmNoBtn = document.getElementById('confirmNoBtn');

            // Store current project data
            let currentProjectData = null;

            // Function to extract project ID from URL
            function getProjectIdFromUrl() {
                const pathParts = window.location.pathname.split('/');
                const id = pathParts[pathParts.length - 1];
                return isNaN(parseInt(id)) ? null : parseInt(id);
            }

            const projectId = getProjectIdFromUrl();

            if (!projectId) {
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                errorMessage.textContent = 'Invalid project ID in URL.';
                return;
            }

            // Function to fetch project details (made reusable for refreshing)
            async function fetchProjectDetails(id) {
                const token = localStorage.getItem('auth_token');
                let headers = {};
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }

                try {
                    const res = await fetch(`/api/projects/${id}`, { headers });
                    if (res.status === 404) {
                        throw new Error('Project not found or you do not have permission to view it.');
                    }
                    if (!res.ok) {
                        const errorText = await res.text();
                        throw new Error(`HTTP error! Status: ${res.status} - ${errorText}`);
                    }
                    const data = await res.json();
                    return data.project; // Assuming the response is { "project": {...} }
                } catch (err) {
                    console.error('Error fetching project:', err);
                    throw err; // Re-throw to be caught by the outer try-catch
                }
            }

            // Function to render project details
            function renderProject(project) {
                projectTitle.textContent = project.title || 'Untitled Project';
                projectClient.textContent = project.client?.name || 'N/A';
                
                // Status badge styling
                let statusClass = 'bg-gray-200 text-gray-800'; // Default
                switch (project.status) {
                    case 'published':
                        statusClass = 'bg-green-100 text-green-700';
                        break;
                    case 'pending':
                        statusClass = 'bg-yellow-100 text-yellow-700';
                        break;
                    case 'in_progress':
                        statusClass = 'bg-blue-100 text-blue-700';
                        break;
                    case 'completed':
                        statusClass = 'bg-purple-100 text-purple-700';
                        break;
                    case 'draft':
                        statusClass = 'bg-orange-100 text-orange-700';
                        break;
                    default:
                        statusClass = 'bg-gray-100 text-gray-600';
                }
                projectStatus.textContent = project.status.replace(/_/g, ' ') || 'N/A';
                projectStatus.className = `capitalize px-3 py-1 rounded-full text-sm font-semibold ${statusClass}`;

                projectSpace.textContent = project.space || 'N/A';
                projectDeadline.textContent = project.deadline ? new Date(project.deadline).toLocaleDateString() : 'N/A';
                projectMinPrice.textContent = project.min_price?.toLocaleString() || '0';
                projectMaxPrice.textContent = project.max_price?.toLocaleString() || '0';
                projectLocation.textContent = project.location_id || 'N/A';
                projectSkill.textContent = project.skill_id || 'N/A';
                projectUnitType.textContent = project.unit_type_id || 'N/A';
                projectDescription.textContent = project.description || 'No description provided for this project.';
            }

            // Function to render offers
            function renderOffers(offers) {
                offersList.innerHTML = ''; // Clear existing offers
                offersCount.textContent = offers.length;

                if (offers.length === 0) {
                    noOffersMessage.classList.remove('hidden');
                    return;
                }
                noOffersMessage.classList.add('hidden');

                offers.forEach(offer => {
                    const offerCard = document.createElement('div');
                    offerCard.className = 'bg-gray-50 border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition duration-200 flex flex-col';
                    offerCard.innerHTML = `
                        <a href="/test/offers/${offer.id}"><h4 class="text-xl font-semibold text-gray-800 mb-3">Offer from ${offer.user?.name || 'Unknown Designer'}</h4></a>
                        <p class="text-gray-600 mb-2 flex-grow">${offer.description || 'No description provided.'}</p>
                        <div class="text-sm text-gray-700 space-y-1 mt-auto">
                            <p><strong class="font-medium text-gray-900">Price:</strong> $${offer.price?.toLocaleString() || 'N/A'}</p>
                            <p><strong class="font-medium text-gray-900">Type:</strong> <span class="capitalize">${offer.type || 'N/A'}</span></p>
                            <p><strong class="font-medium text-gray-900">Deadline:</strong> ${offer.deadline ? new Date(offer.deadline).toLocaleDateString() : 'N/A'}</p>
                            <p><strong class="font-medium text-gray-900">Designer Rating:</strong> <span class="font-bold text-yellow-500">${(offer.user?.rate || 0).toFixed(1)} &#9733;</span></p>
                            <p><strong class="font-medium text-gray-900">Offer Status:</strong> <span class="capitalize px-2 py-0.5 rounded-full text-xs font-semibold ${getOfferStatusClass(offer.status)}">${offer.status || 'N/A'}</span></p>
                        </div>
                        <div class="mt-4 space-y-2">
                            <!-- Accept Offer Button - Only if project is published and offer is pending -->
                            ${currentProjectData && currentProjectData.status === 'published' && offer.status === 'pending' ?
                                `<button class="accept-offer-btn w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200" data-offer-id="${offer.id}">Accept Offer</button>`
                                : ''}
                            <!-- View Offer Details Button -->
                            <a href="/test/offers/${offer.id}" class="w-full inline-block text-center py-2 px-4 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-200">View Offer Details</a>
                        </div>
                    `;
                    offersList.appendChild(offerCard);
                });
                
                // Add event listeners to newly rendered accept buttons
                document.querySelectorAll('.accept-offer-btn').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const offerIdToAccept = event.target.dataset.offerId;
                        showConfirmDialog(`Are you sure you want to accept this offer? This will close other offers for this project.`, () => {
                            acceptOffer(offerIdToAccept);
                        });
                    });
                });
            }

            // Helper to get status badge class for offers
            function getOfferStatusClass(status) {
                switch (status) {
                    case 'accepted': return 'bg-green-100 text-green-700';
                    case 'rejected': return 'bg-red-100 text-red-700';
                    case 'pending': return 'bg-yellow-100 text-yellow-700';
                    case 'declined': return 'bg-gray-200 text-gray-800';
                    default: return 'bg-gray-100 text-gray-600';
                }
            }

            // --- Custom Confirmation Dialog Logic ---
            let confirmCallback = null;

            function showConfirmDialog(message, callback) {
                confirmationMessage.textContent = message;
                confirmCallback = callback;
                confirmationModal.classList.remove('hidden');
            }

            function hideConfirmDialog() {
                confirmationModal.classList.add('hidden');
                confirmCallback = null;
            }

            confirmYesBtn.addEventListener('click', () => {
                if (confirmCallback) {
                    confirmCallback();
                }
                hideConfirmDialog();
            });

            confirmNoBtn.addEventListener('click', () => {
                hideConfirmDialog();
            });

            // --- Accept Offer Logic ---
            async function acceptOffer(offerId) {
                // Find the specific button to update its state
                const button = document.querySelector(`.accept-offer-btn[data-offer-id="${offerId}"]`);
                const originalButtonText = button ? button.textContent : 'Accept Offer';
                if (button) {
                    button.disabled = true;
                    button.textContent = 'Accepting...';
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                }
                
                // Use the offer message div for acceptance feedback as well
                offerMessageDiv.textContent = '';
                offerMessageDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');


                const token = localStorage.getItem('auth_token');
                let headers = {}; // No Content-Type for GET
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }

                try {
                    const res = await fetch(`/api/offers/${offerId}/accept`, {
                        method: 'GET', // As per your API definition
                        headers: headers
                    });

                    const responseText = await res.text();
                    let responseBody = {};
                    try {
                        responseBody = JSON.parse(responseText);
                    } catch (e) {
                        responseBody.message = responseText;
                        console.warn("API response for accept offer was not JSON, treating as plain text message:", responseText);
                    }

                    if (res.ok) {
                        offerMessageDiv.textContent = responseBody.message || 'Offer accepted successfully! Project status updated.';
                        offerMessageDiv.classList.add('bg-green-100', 'text-green-700');
                        // Re-fetch project details to update all offers and project status
                        const updatedProject = await fetchProjectDetails(projectId);
                        currentProjectData = updatedProject; // Update stored project data
                        renderProject(updatedProject); // Re-render project status
                        renderOffers(updatedProject.offers || []); // Re-render offers
                    } else {
                        const message = responseBody.message || `Failed to accept offer (HTTP ${res.status}).`;
                        offerMessageDiv.textContent = `Error: ${message}`;
                        offerMessageDiv.classList.add('bg-red-100', 'text-red-700');
                    }
                } catch (err) {
                    offerMessageDiv.textContent = `Network error: ${err.message}. Please try again.`;
                    offerMessageDiv.classList.add('bg-red-100', 'text-red-700');
                    console.error('Error accepting offer:', err);
                } finally {
                    offerMessageDiv.classList.remove('hidden');
                    if (button) {
                        button.disabled = false;
                        button.textContent = originalButtonText;
                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }
            }

            // Function to handle offer submission (existing logic)
            offerForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                offerMessageDiv.textContent = '';
                offerMessageDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');

                submitOfferBtn.disabled = true;
                submitOfferBtn.textContent = 'Sending Offer...';

                const offerData = {
                    project_id: projectId,
                    price: parseFloat(offerPriceInput.value),
                    description: offerDescriptionInput.value,
                    start_date: offerStartDateInput.value,
                    deadline: offerDeadlineInput.value,
                    expire_date: offerExpireDateInput.value,
                };

                const token = localStorage.getItem('auth_token');
                let headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }

                try {
                    const res = await fetch('/api/offers', {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify(offerData)
                    });

                    const responseText = await res.text();
                    let responseBody = {};
                    try {
                        responseBody = JSON.parse(responseText);
                    } catch (e) {
                        responseBody.message = responseText;
                    }

                    if (res.ok) {
                        offerMessageDiv.textContent = responseBody.message || 'Offer sent successfully! The project client will review it.';
                        offerMessageDiv.classList.add('bg-green-100', 'text-green-700');
                        offerForm.reset();
                        const updatedProject = await fetchProjectDetails(projectId);
                        currentProjectData = updatedProject; // Update stored project data
                        renderOffers(updatedProject.offers || []);
                    } else {
                        const message = responseBody.message || 'Failed to send offer. Please check your input.';
                        offerMessageDiv.textContent = `Error: ${message}`;
                        offerMessageDiv.classList.add('bg-red-100', 'text-red-700');
                    }
                } catch (err) {
                    offerMessageDiv.textContent = `Network error: ${err.message}. Please try again.`;
                    offerMessageDiv.classList.add('bg-red-100', 'text-red-700');
                    console.error('Error sending offer:', err);
                } finally {
                    offerMessageDiv.classList.remove('hidden');
                    submitOfferBtn.disabled = false;
                    submitOfferBtn.textContent = 'Submit Offer';
                }
            });

            // Initial data load
            try {
                const project = await fetchProjectDetails(projectId);
                currentProjectData = project; // Store initial project data
                loadingState.classList.add('hidden');
                projectContent.classList.remove('hidden');
                renderProject(project);
                renderOffers(project.offers || []);
            } catch (err) {
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                errorMessage.textContent = err.message || 'An unknown error occurred.';
            }
        });
    </script>
</body>
</html>