document.addEventListener('DOMContentLoaded', function() {
    const images = [
        'coffee1.jpg',
        'coffee2.jpg'
        // Add more image paths here if you have them, e.g., 'images/coffee3.jpg'
    ];
    let currentImageIndex = 0;
    const slideshowImage = document.getElementById('slideshow-image');

    function startSlideshow() {
        setInterval(function() {
            currentImageIndex = (currentImageIndex + 1) % images.length; // Cycle through images
            slideshowImage.src = images[currentImageIndex];
            slideshowImage.alt = "Our Coffee Selection " + (currentImageIndex + 1); // Update alt text
        }, 2000); // Change image every 2 seconds (2000 milliseconds)
    }

    // Start the slideshow when the page loads
    startSlideshow();
});

Global.js
document.addEventListener('DOMContentLoaded', function() {
    const dateElement = document.getElementById('currentDate');

    if (dateElement) { // Check if the element exists on the page
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        // Format: Monday, March 1, 2099
        dateElement.textContent = today.toLocaleDateString('en-US', options);
    }
});
