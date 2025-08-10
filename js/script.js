// js/scripts.js
// This file will contain shared JavaScript functions
// like the date display and AJAX calls to PHP.

function displayCurrentDate() {
    const date = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateElement = document.getElementById('current-date');
    if (dateElement) {
        dateElement.textContent = date.toLocaleDateString('en-US', options);
    }
}
window.onload = displayCurrentDate;

// AJAX call to get date and time from the server
function getMyServerDateTime() {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            const dateElement = document.getElementById('server-date-time');
            if (dateElement) {
                dateElement.textContent = this.responseText;
            }
        }
    };
    xhr.open('GET', 'date.php', true);
    xhr.send();
}
getMyServerDateTime();
setInterval(getMyServerDateTime, 60000); // Refresh every 60 seconds

// Other shared functions can go here...
