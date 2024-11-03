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

    // Show popup notification
    const notificationPopup = document.getElementById('notificationPopup');
    notificationPopup.style.display = 'block';

    // Hide the registration form
    registrationSection.style.display = 'none';

    // Redirect to the dashboard after a short delay
    setTimeout(() => {
        notificationPopup.style.display = 'none'; // Hide the notification
        welcomeMessage.style.display = 'block'; // Show the welcome message again
        welcomeDescription.style.display = 'block'; // Show the description
    }, 3000); // 3-second delay
});

// Close popup when the close button is clicked
const closePopupButton = document.querySelector('.close-popup');
closePopupButton.addEventListener('click', function () {
    const notificationPopup = document.getElementById('notificationPopup');
    notificationPopup.style.display = 'none';
});
