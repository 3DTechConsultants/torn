async function toggleStar(id) {
    try {
        const queryString = new URLSearchParams({ id: id }).toString();
        const response = await fetch('/star/toggle?' + queryString, {
            method: 'GET',
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.statusText}`);
        }

        const result = await response.json();
        console.log('Toggle successful:', result);
        return result;
    } catch (error) {
        console.error('Error toggling star:', error);
    }
}

function initializeStarToggles() {
    // Select all elements with IDs starting with "userStar"
    const starElements = document.querySelectorAll('[id^="userStars"]');

    starElements.forEach((element) => {
        element.onclick = async () => {
            const userId = element.getAttribute('data-user-id');
            if (userId) {
                const result = await toggleStar(userId);
                if (result && result.success) {
                    // Update the UI based on the new isStarred status
                    element.classList.add('hidden'); // Add hidden class to trigger fade-out

                    setTimeout(() => {
                        element.classList.toggle('bi-star-fill', result.isStarred);
                        element.classList.toggle('bi-star', !result.isStarred);
                        element.classList.remove('hidden'); // Remove hidden class to fade back in
                    }, 100); // Match the duration of the CSS transition
                }
            } else {
                console.error('User ID not found on element:', element);
            }
        };
    });
}

// Call the function to initialize the event listeners
document.addEventListener('DOMContentLoaded', initializeStarToggles);