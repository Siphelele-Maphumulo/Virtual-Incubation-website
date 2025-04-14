
    // Form validation script
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');
        const submitButton = document.getElementById('submitButton');
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        // Validate email format
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
        
        // Validate a single field
        function validateField(input) {
            const errorDiv = input.nextElementSibling;
            
            if (input.id === 'email' && input.value) {
                if (!isValidEmail(input.value)) {
                    input.classList.add('is-invalid');
                    // Show the email format error message
                    document.querySelector('.invalid-feedback[style*="display: none"]').style.display = 'block';
                    return false;
                } else {
                    document.querySelector('.invalid-feedback[style*="display: none"]').style.display = 'none';
                }
            }
            
            if (input.required && !input.value.trim()) {
                input.classList.add('is-invalid');
                errorDiv.style.display = 'block';
                return false;
            }
            
            input.classList.remove('is-invalid');
            errorDiv.style.display = 'none';
            return true;
        }
        
        // Check if all fields are valid
        function checkFormValidity() {
            let allValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    allValid = false;
                }
            });
            
            submitButton.disabled = !allValid;
            submitButton.classList.toggle('disabled', !allValid);
        }
        
        // Add event listeners
        inputs.forEach(input => {
            // Validate on blur (when user leaves the field)
            input.addEventListener('blur', function() {
                validateField(input);
                checkFormValidity();
            });
            
            // Also validate on input to clear errors as user types
            input.addEventListener('input', function() {
                if (input.classList.contains('is-invalid')) {
                    validateField(input);
                }
                checkFormValidity();
            });
        });
        
        // Form submission handler
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate all fields again on submit
            checkFormValidity();
            
            if (submitButton.disabled) return;
            
            // Simulate form submission
            submitButton.disabled = true;
            submitButton.classList.add('disabled');
            submitButton.textContent = 'Sending...';
            
            // Here you would normally send the form data to a server
            // For demo purposes, we'll just show success message
            setTimeout(() => {
                document.getElementById('submitSuccessMessage').classList.remove('d-none');
                form.reset();
                submitButton.textContent = 'Send Message';
                submitButton.disabled = true;
                submitButton.classList.add('disabled');
                
                // Reset validation states
                inputs.forEach(input => {
                    input.classList.remove('is-invalid', 'is-valid');
                });
            }, 1500);
        });
    });
