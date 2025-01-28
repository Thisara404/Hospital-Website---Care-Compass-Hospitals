<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Services - Care Compass Hospitals</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Include the same header as the main page -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="assets/logo.png" alt="Care Compass Logo">
            </div>
            <ul class="nav-links">
                <li><a href="index.html#services">Services</a></li>
                <li><a href="index.html#about">About Us</a></li>
                <li><a href="index.html#contact">Contact</a></li>
                <li><a href="login.php" id="Login-btn" class="btn-primary">Login</a></li>
            </ul>
        </nav>
    </header>

    <section class="service-details emergency">
        <h1>Emergency Care Services</h1>

        <div class="emergency-info">
            <div class="emergency-contact">
                <h2>24/7 Emergency Helpline</h2>
                <p class="emergency-number">(555) EMERGENCY</p>
                <p class="emergency-number">(555) 123-4567</p>
            </div>

            <div class="emergency-services">
                <h3>Our Emergency Services Include:</h3>
                <ul>
                    <li>Trauma Services</li>
                    <li>Critical Care</li>
                    <li>Ambulance Services</li>
                    <li>Immediate Medical Intervention</li>
                    <li>Rapid Diagnostic Support</li>
                </ul>
            </div>

            <div class="quick-action">
                <h3>Quick Emergency Actions</h3>
                <div class="action-buttons">
                    <a href="tel:555-123-4567" class="btn-primary">Call Ambulance</a>
                    <a href="#" class="btn-secondary">Locate Nearest Branch</a>
                </div>
            </div>
        </div>
    </section>
    <!-- About Us Section -->
    <section id="about" class="about-us">
        <h2>About Care Compass Hospitals</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Care Compass Hospitals was founded in 2010 with a mission to provide compassionate, high-quality
                    healthcare to our community. Our dedicated team of medical professionals is committed to delivering
                    exceptional patient care with empathy, expertise, and innovation.</p>
                <p>We believe in a patient-centered approach, focusing on personalized treatment plans that address both
                    physical and emotional well-being. Our state-of-the-art facilities are equipped with the latest
                    medical technologies, ensuring accurate diagnostics and effective treatments.</p>
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
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
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
                <form>
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="tel" name="phone" placeholder="Your Phone Number">
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>
    <!-- Include the same footer as the main page -->
    <footer>
        <p>&copy; 2025 Care Compass Hospitals. All rights reserved.</p>
    </footer>
</body>

</html>