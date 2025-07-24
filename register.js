document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registrationForm');

    registrationForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        let isValid = true;

        // Get form elements
        const firstNameInput = document.getElementById('regFirstName');
        const lastNameInput = document.getElementById('regLastName');
        const emailInput = document.getElementById('regEmail');
        const passwordInput = document.getElementById('regPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const dobInput = document.getElementById('dob');
        const countrySelect = document.getElementById('country');
        const genderRadios = document.getElementsByName('gender');

        // Get error display elements
        const firstNameError = document.getElementById('regFirstNameError');
        const lastNameError = document.getElementById('regLastNameError');
        const emailError = document.getElementById('regEmailError');
        const passwordError = document.getElementById('regPasswordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');
        const dobError = document.getElementById('dobError');
        const countryError = document.getElementById('countryError');
        const genderError = document.getElementById('genderError');

        // Clear previous error messages
        firstNameError.textContent = '';
        lastNameError.textContent = '';
        emailError.textContent = '';
        passwordError.textContent = '';
        confirmPasswordError.textContent = '';
        dobError.textContent = '';
        countryError.textContent = '';
        genderError.textContent = '';

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
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput.value.trim() === '') {
            emailError.textContent = 'Email address is required.';
            isValid = false;
        } else if (!emailRegex.test(emailInput.value.trim())) {
            emailError.textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        // Validate Password
        const password = passwordInput.value;
        if (password.length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters long.';
            isValid = false;
        }
        // Example: Password must contain at least one uppercase letter, one lowercase letter, and one number
        else if (!/[A-Z]/.test(password)) {
            passwordError.textContent = 'Password needs at least one uppercase letter.';
            isValid = false;
        } else if (!/[a-z]/.test(password)) {
            passwordError.textContent = 'Password needs at least one lowercase letter.';
            isValid = false;
        } else if (!/[0-9]/.test(password)) {
            passwordError.textContent = 'Password needs at least one number.';
            isValid = false;
        }


        // Validate Confirm Password
        if (confirmPasswordInput.value === '') {
            confirmPasswordError.textContent = 'Confirm Password is required.';
            isValid = false;
        } else if (confirmPasswordInput.value !== password) {
            confirmPasswordError.textContent = 'Passwords do not match.';
            isValid = false;
        }

        // Validate Date of Birth (optional, check for empty and minimum age)
        if (dobInput.value === '') {
            dobError.textContent = 'Date of Birth is required.';
            isValid = false;
        } else {
            const birthDate = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            if (age < 13) { // Example: User must be at least 13 years old
                dobError.textContent = 'You must be at least 13 years old to register.';
                isValid = false;
            } else if (birthDate > today) {
                dobError.textContent = 'Date of Birth cannot be in the future.';
                isValid = false;
            }
        }

        // Validate Country
        if (countrySelect.value === '') {
            countryError.textContent = 'Please select your country.';
            isValid = false;
        }

        // Validate Gender (at least one radio button checked)
        let genderSelected = false;
        for (const radio of genderRadios) {
            if (radio.checked) {
                genderSelected = true;
                break;
            }
        }
        if (!genderSelected) {
            genderError.textContent = 'Please select your gender.';
            isValid = false;
        }

        if (isValid) {
            alert('Registration successful!');
            // Log form data to console for demonstration
            console.log('Registration Data:', {
                firstName: firstNameInput.value.trim(),
                lastName: lastNameInput.value.trim(),
                email: emailInput.value.trim(),
                password: password, // For demonstration, in real app, hash this!
                dob: dobInput.value,
                country: countrySelect.value,
                gender: document.querySelector('input[name="gender"]:checked').value,
                newsletter: document.getElementById('newsletter').checked
            });
            registrationForm.reset(); // Clear the form after successful "submission"
        }
    });

    // The 'Clear Form' button with type="reset" natively clears the form fields.
    // If you needed custom JavaScript logic for reset (e.g., hiding error messages),
    // you would add an event listener to it here.
});
