<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
</head>

<body>
    <!-- Navigation -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="assets/logo.png" alt="Care Compass Logo">
            </div>
            <ul class="nav-links">
                <li><a href="#services">Services</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="SelectUser.php" id="Login-btn" class="btn-primary">Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome to Care Compass Hospitals</h1>
        <p>Providing expert care, every step of the way.</p>
        <a href="#services" class="btn-secondary">Explore Services</a>
    </div>

    <!-- Modify the existing service cards to include links -->
    <div id="services" class="services">
        <h2>Our Services</h2>
        <div class="service-gallery">
            <div class="card">
                <a href="channeling.php">
                    <img src="assets/channeling.jpg" alt="Channeling Service">
                    <div class="card-content">
                        <h3>Channeling Services</h3>
                        <p>Reserve your appointment with our expert doctors.</p>
                    </div>
                </a>
            </div>
            <div class="card">
                <a href="emergency.php">
                    <img src="assets/emergency.jpg" alt="Emergency Service">
                    <div class="card-content">
                        <h3>Emergency Care</h3>
                        <p>24/7 emergency services for immediate assistance.</p>
                    </div>
                </a>
            </div>
            <div class="card">
                <a href="laboratory.php">
                    <img src="assets/labarotary.jpg" alt="Laboratory Service">
                    <div class="card-content">
                        <h3>Laboratory Services</h3>
                        <p>Accurate and timely medical test results.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- About Us Section -->
    <div id="about" class="about-us">
        <h2>About Care Compass Hospitals</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Care Compass Hospitals was founded in 2010 with a mission to provide compassionate, high-quality healthcare to our community. Our dedicated team of medical professionals is committed to delivering exceptional patient care with empathy, expertise, and innovation.</p>
                <p>We believe in a patient-centered approach, focusing on personalized treatment plans that address both physical and emotional well-being. Our state-of-the-art facilities are equipped with the latest medical technologies, ensuring accurate diagnostics and effective treatments.</p>
            </div>
            <div class="about-stats">
                <div class="stat-card">
                    <h3>15+</h3>
                    <p>Years of Healthcare Excellence</p>
                </div>
                <div class="stat-card">
                    <h3>100+</h3>
                    <p>Dedicated Healthcare Professionals</p>
                </div>
                <div class="stat-card">
                    <h3>50,000+</h3>
                    <p>Patients Served Annually</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div id="contact" class="contact">
        <h2>Contact Us</h2>
        <div class="contact-container">
            <div class="contact-info">
                <div class="contact-detail">
                    <h3>Hospital Address</h3>
                    <p>123 Healthcare Avenue, Wellness City, HC 54321</p>
                </div>
                <div class="contact-detail">
                    <h3>Phone Numbers</h3>
                    <p>Emergency: (555) 123-4567</p>
                    <p>General Inquiries: (555) 987-6543</p>
                </div>
                <div class="contact-detail">
                    <h3>Email</h3>
                    <p>info@carecompass.com</p>
                    <p>support@carecompass.com</p>
                </div>
            </div>
            <div class="contact-form">
                <form id="contact-form" action="process_contact.php" method="POST">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="tel" name="phone" placeholder="Your Phone Number">
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Care Helloooooo  Compass Hospitals. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();

            fetch('process_contact.php', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        this.reset();
                    } else {
                        alert(data.message);
                    }
                });
        });
    </script>
</body>

</html>