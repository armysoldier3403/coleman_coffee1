document.addEventListener('DOMContentLoaded', function() {
    const dateElement = document.getElementById('currentDate');

    if (dateElement) { // Check if the element exists on the page
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        // Format: Monday, March 1, 2099
        dateElement.textContent = today.toLocaleDateString('en-US', options);
    }
});
