document.addEventListener('DOMContentLoaded', function() {
    // Get the toggle for advanced settings and the advanced settings section
    var toggleAdvancedSettings = document.getElementById('toggle-advanced-settings');
    var advancedSettings = document.getElementById('advanced-settings-section');

    // Check if the toggle exists
    if (toggleAdvancedSettings) {
        // Add an event listener to toggle visibility of advanced settings
        toggleAdvancedSettings.addEventListener('change', function() {
            if (this.checked) {
                advancedSettings.style.display = 'block'; // Show advanced settings
            } else {
                advancedSettings.style.display = 'none'; // Hide advanced settings
            }
        });
    }

    // Get the settings form
    var settingsForm = document.getElementById('coding-bunny-settings-form');

    // Check if the form exists
    if (settingsForm) {
        // Add an event listener for form submission
        settingsForm.addEventListener('submit', function(event) {
            var phoneNumberInput = document.getElementById('whatsapp-number');
            var phoneNumber = phoneNumberInput.value;

            // Validate the phone number
            if (!validatePhoneNumber(phoneNumber)) {
                alert('Please enter a valid phone number.'); // Alert if invalid
                event.preventDefault(); // Prevent form submission
            }
        });
    }

    // Function to validate phone numbers
    function validatePhoneNumber(phoneNumber) {
        var phoneRegex = /^[0-9]+$/; // Regular expression for numbers
        return phoneRegex.test(phoneNumber); // Test if phone number is valid
    }

});