// Show registration form on clicking "Register Student"
const registerLink = document.getElementById('registerLink');
const registrationSection = document.getElementById('registrationSection');
const welcomeMessage = document.getElementById('welcomeMessage');
const welcomeDescription = document.getElementById('welcomeDescription');

registerLink.addEventListener('click', function (event) {
    event.preventDefault(); // Prevent default anchor behavior
    welcomeMessage.style.display = 'none';
    welcomeDescription.style.display = 'none';
    registrationSection.style.display = 'block'; // Show the form
});

// Form submission handler
const registrationForm = document.getElementById('registrationForm');
registrationForm.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent actual form submission
    
    // Gather form data
    const formData = new FormData(registrationForm);

    // Send the form data to the PHP server via Fetch API
    fetch('registration.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Assuming the PHP response is a string (like success message)
    .then(data => {
        if (data.includes('success')) {
            // Show popup notification for successful registration
            const notificationPopup = document.getElementById('notificationPopup');
            notificationPopup.style.display = 'block';
            notificationPopup.innerText = 'Registration successful!'; // Display success message

            // Hide the registration form
            registrationSection.style.display = 'none';

            // Redirect or reset form after a short delay
            setTimeout(() => {
                notificationPopup.style.display = 'none'; // Hide the notification
                welcomeMessage.style.display = 'block'; // Show the welcome message again
                welcomeDescription.style.display = 'block'; // Show the description
                window.location.href = 'registration.html'; // Redirect back to registration page
            }, 3000); // 3-second delay
        } else {
            alert('Registration failed. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error during registration:', error);
        alert('An error occurred. Please try again later.');
    });
});

// Close popup when the close button is clicked
const closePopupButton = document.querySelector('.close-popup');
closePopupButton.addEventListener('click', function () {
    const notificationPopup = document.getElementById('notificationPopup');
    notificationPopup.style.display = 'none';
});
