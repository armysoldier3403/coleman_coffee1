// script.js

function validateContactForm() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();

    if (firstName === "") {
        alert("First Name is required.");
        return false;
    }

    if (lastName === "") {
        alert("Last Name is required.");
        return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "" || !emailRegex.test(email)) {
        alert("A valid E-mail Address is required.");
        return false;
    }

    return true;
}

function clearContactForm() {
    document.getElementById('salutation').value = "";
    document.getElementById('firstName').value = "";
    document.getElementById('lastName').value = "";
    document.getElementById('email').value = "";
    document.getElementById('phone').value = "";
    document.getElementById('subject').value = "";
    document.getElementById('comments').value = "";
    document.getElementById('receiveReply').checked = false;
}

// ... (existing validateRegistrationForm, clearRegistrationForm functions) ...

// Image slideshow for Site Map
let currentImageIndex = 0;
const images = ['coffee1.jpg', 'coffee2.jpg'];

function changeImage() {
    const slideshowImage = document.getElementById('slideshowImage');
    if (slideshowImage) {
        slideshowImage.src = images[currentImageIndex];
        currentImageIndex = (currentImageIndex + 1) % images.length;
    }
}

// Function to display current date
function displayCurrentDate() {
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date();
        dateElement.textContent = today.toLocaleDateString('en-US', options);
    }
}

// Function to get and display local date and time using AJAX
function updateLocalDateTime() {
    const localDateTimeElement = document.getElementById('localDateTime');
    if (localDateTimeElement) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_datetime.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                localDateTimeElement.textContent = xhr.responseText;
            } else {
                localDateTimeElement.textContent = "Error loading time.";
            }
        };
        xhr.send();
    }
}

// Poll functions
function getPollResults() {
    const pollResultsDiv = document.getElementById('pollResults');
    if (!pollResultsDiv) return;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_poll_results.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const results = JSON.parse(xhr.responseText);
            let html = '<h3>Poll Results:</h3><ul>';
            let totalVotes = 0;
            for (const item in results) {
                totalVotes += results[item];
            }

            for (const item in results) {
                const percentage = totalVotes > 0 ? ((results[item] / totalVotes) * 100).toFixed(1) : 0;
                html += `<li>${item}: ${results[item]} votes (${percentage}%)</li>`;
            }
            html += '</ul>';
            pollResultsDiv.innerHTML = html;
        } else if (xhr.readyState === 4) {
            pollResultsDiv.innerHTML = '<p>Error loading poll results.</p>';
        }
    };
    xhr.send();
}


document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (validateContactForm()) {
                const formData = new FormData(contactForm); // Get all form data

                fetch('process_contact.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const formMessageDiv = document.getElementById('formMessage');
                    if (data.success) {
                        formMessageDiv.style.color = 'green';
                        formMessageDiv.textContent = data.message;
                        clearContactForm(); // Clear the form on successful submission
                    } else {
                        formMessageDiv.style.color = 'red';
                        formMessageDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const formMessageDiv = document.getElementById('formMessage');
                    formMessageDiv.style.color = 'red';
                    formMessageDiv.textContent = 'An error occurred during submission. Please try again.';
                });
            }
        });

        const resetButton = contactForm.querySelector('input[type="reset"]');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                clearContactForm();
                document.getElementById('formMessage').textContent = ''; // Clear message on reset
            });
        }
    }

    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (validateRegistrationForm()) {
                alert("Registration successful! (Validation only - data not yet saved to DB)");
                // In a real application, you would send this data to a server
            }
        });
        const clearRegFormButton = registrationForm.querySelector('input[type="reset"]');
        if (clearRegFormButton) {
            clearRegFormButton.addEventListener('click', function() {
                clearRegistrationForm();
            });
        }
    }

    const slideshowImage = document.getElementById('slideshowImage');
    if (slideshowImage) {
        setInterval(changeImage, 2000);
    }

    displayCurrentDate();

    if (document.getElementById('localDateTime')) {
        updateLocalDateTime();
        setInterval(updateLocalDateTime, 1000);
    }

    const pollForm = document.getElementById('pollForm');
    if (pollForm) {
        getPollResults();
        pollForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const selectedOption = document.querySelector('input[name="coffee"]:checked');
            if (selectedOption) {
                const formData = new FormData();
                formData.append('coffee', selectedOption.value);

                fetch('process_vote.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Your vote has been recorded!");
                        getPollResults();
                    } else {
                        alert("Failed to record vote: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while voting.");
                });
            } else {
                alert("Please select an option to vote.");
            }
        });
    }
});
