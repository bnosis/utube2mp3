
//get nav
document.addEventListener('DOMContentLoaded', function() {
    fetch('nav.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('nav_placeholder').innerHTML = data;

            const menuToggle = document.getElementById('menu-toggle');
            const menuItems = document.getElementById('menu-items');

            if (menuToggle && menuItems) {
                menuToggle.addEventListener('click', () => {
                    menuItems.classList.toggle('menu-active');
                });
            } else {
                console.error('Menu toggle or menu items element not found.');
            }
        })
        .catch(error => console.error('Error loading nav element:', error));
});

//get footer
document.addEventListener('DOMContentLoaded', function() {
    fetch('footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer_placeholder').innerHTML = data;
        })
        .catch(error => console.error('Error loading footer element:', error));
});

//faq dropdown logic
document.addEventListener("DOMContentLoaded", () => {
    const headers = document.querySelectorAll(".dropdown-header");

    headers.forEach(header => {
        header.addEventListener("click", () => {
            const content = header.nextElementSibling;

            if (content) {
                content.classList.toggle("open");
            }
        });
    });
});

//validation for contact form
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.querySelector('#contactForm'); 

    if (contactForm) {
        contactForm.addEventListener('submit', function (event) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!/^[A-Za-z\s]+$/.test(name)) {
                alert('Name must only contain letters and spaces.');
                event.preventDefault(); 
                return;
            }
            

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address.');
                event.preventDefault(); 
                return;
            }

            if (message.length < 10 || message.length > 500) {
                alert('Message must be between 10 and 500 characters.');
                event.preventDefault(); 
                return;
            }
        });
    }
});

//capitilize first letter for name on contact form
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            const value = nameInput.value;
            const capitalized = value
                .split(' ') 
                .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()) 
                .join(' '); 
            nameInput.value = capitalized;
        });
    }
});


// contact us logic
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.contact');
    const submitButton = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loading-spinner');

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            document.getElementById('message-confirmation').innerHTML = '';

            // loading animation
            submitButton.style.display = 'none'; 
            loadingSpinner.style.display = 'block'; 

            const formData = new FormData(contactForm);

            const response = await fetch(contactForm.action, {
                method: contactForm.method,
                body: formData,
            });

            const result = await response.text();

            document.getElementById('message-confirmation').innerHTML = result;

            if (result.includes("Message has been sent")) {
                contactForm.reset();
            }

            loadingSpinner.style.display = 'none';
            submitButton.style.display = 'block';
        });
    }
});


//url download logic
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.utube_link_form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const urlInput = document.querySelector('.url');
        const loadingSpinner = document.getElementById('loading-spinner');
        const loadingAnimation = document.getElementById('loading-animation');
        const downloadFile = document.getElementById('downloadfile');

        const url = urlInput.value.trim();
        if (!url) {
            alert('Please enter a valid YouTube URL.');
            return;
        }

        // Show the spinner
        loadingSpinner.style.display = 'block';
        loadingAnimation.innerHTML = 'Converting... Please wait.';
        downloadFile.innerHTML = '';

        try {
            const formData = new FormData();
            formData.append('url', url);

            const response = await fetch('php/process_url.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();
            loadingAnimation.innerHTML = '';
            loadingSpinner.style.display = 'none';

            if (result.success) {
                // Show success message and download link
                loadingAnimation.innerHTML = `<p>Conversion Successful!</p>`;
                downloadFile.innerHTML = `
                    <p>
                        <a href="${result.file}" download id="downloadLink">
                            <strong>${result.fileName}</strong>
                        </a>
                        <br>
                        <span id="timer" style="display: block; margin-top: 18px;"></span>
                    </p>
                `;

                // Start the 15-minute countdown timer
                startCountdown(5 * 60, document.getElementById('timer'), downloadFile, loadingAnimation);
            } else {
                downloadFile.innerHTML = `<p>Error: ${result.message}</p>`;
            }

            urlInput.value = '';

        } catch (error) {
            loadingSpinner.style.display = 'none';
            loadingAnimation.innerHTML = '';
            downloadFile.innerHTML = `<p>An unexpected error occurred. Please try again later.</p>`;
        }
    });

    // Countdown timer function
    function startCountdown(duration, timerElement, downloadContainer, loadingContainer) {
        let timeRemaining = duration;

        const interval = setInterval(() => {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;

            timerElement.textContent = `Link expires in: ${minutes}:${seconds.toString().padStart(2, '0')} minutes`;

            timeRemaining--;

            if (timeRemaining < 0) {
                clearInterval(interval);
                timerElement.textContent = 'Download link expired.';
                downloadContainer.innerHTML = '';
                loadingContainer.innerHTML = ''; 
            }
        }, 1000);
    }
});

