document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    contactForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        let isValid = true;

        // Get form elements
        const firstName = document.getElementById('firstName');
        const lastName = document.getElementById('lastName');
        const email = document.getElementById('email');

        // Get error display elements
        const firstNameError = document.getElementById('firstNameError');
        const lastNameError = document.getElementById('lastNameError');
        const emailError = document.getElementById('emailError');

        // Clear previous errors
        firstNameError.textContent = '';
        lastNameError.textContent = '';
        emailError.textContent = '';

        // Validate First Name
        if (firstName.value.trim() === '') {
            firstNameError.textContent = 'First Name is required.';
            isValid = false;
        }

        // Validate Last Name
        if (lastName.value.trim() === '') {
            lastNameError.textContent = 'Last Name is required.';
            isValid = false;
        }

        // Validate Email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email.value.trim() === '') {
            emailError.textContent = 'Email is required.';
            isValid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            emailError.textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        if (isValid) {
            alert('Form submitted successfully!');
            // Here you would typically send the form data to a server
            // For now, we'll just log it
            console.log('Form Data:', {
                firstName: firstName.value.trim(),
                lastName: lastName.value.trim(),
                email: email.value.trim()
            });
            contactForm.reset(); // Optionally reset after successful submission
        }
    });

    // Reset Form functionality (using the browser's native reset button)
    // The "Reset Form" button with type="reset" automatically clears the form fields.
    // If you want to add custom logic for reset, you'd add an event listener here.
});
