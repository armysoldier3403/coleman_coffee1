document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    contactForm.addEventListener('submit', function(event) {
        // Prevent default form submission to handle validation with JavaScript
        event.preventDefault();

        let isValid = true;

        // Get form elements
        const firstNameInput = document.getElementById('firstName');
        const lastNameInput = document.getElementById('lastName');
        const emailInput = document.getElementById('email');

        // Get error display elements
        const firstNameError = document.getElementById('firstNameError');
        const lastNameError = document.getElementById('lastNameError');
        const emailError = document.getElementById('emailError');

        // Clear previous error messages
        firstNameError.textContent = '';
        lastNameError.textContent = '';
        emailError.textContent = '';

        // Validate First Name
        if (firstNameInput.value.trim() === '') {
            firstNameError.textContent = 'First Name is required.';
            isValid = false;
        }

        // Validate Last Name
        if (lastNameInput.value.trim() === '') {
            lastNameError.textContent = 'Last Name is required.';
            isValid = false;
        }

        // Validate Email Address
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex
        if (emailInput.value.trim() === '') {
            emailError.textContent = 'Email address is required.';
            isValid = false;
        } else if (!emailRegex.test(emailInput.value.trim())) {
            emailError.textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        if (isValid) {
            // If all fields are valid, you can proceed with form submission
            // For this example, we'll just show an alert and log the data
            alert('Form submitted successfully!');
            console.log('Contact Form Data:', {
                salutation: document.getElementById('salutation').value,
                firstName: firstNameInput.value.trim(),
                lastName: lastNameInput.value.trim(),
                email: emailInput.value.trim(),
                phone: document.getElementById('phone').value.trim(),
                subject: document.getElementById('subject').value.trim(),
                comments: document.getElementById('comments').value.trim(),
                receiveReply: document.getElementById('reply').checked
            });
            // You might want to submit the form programmatically here, e.g.:
            // contactForm.submit();
            contactForm.reset(); // Clear the form after successful "submission"
        }
    });

    // The 'Reset Form' button with type="reset" natively clears the form fields.
    // If you needed custom JavaScript logic for reset (e.g., hiding error messages),
    // you would add an event listener to it. For now, the browser's default is fine.
});
