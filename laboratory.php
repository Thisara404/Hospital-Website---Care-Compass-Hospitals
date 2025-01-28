<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Services - Care Compass Hospitals</title>
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

    <section class="service-details laboratory">
        <h1>Comprehensive Laboratory Services</h1>

        <div class="lab-services">
            <div class="test-categories">
                <div class="test-card">
                    <h3>Blood Tests</h3>
                    <ul>
                        <li>Complete Blood Count (CBC)</li>
                        <li>Lipid Profile</li>
                        <li>Diabetes Screening</li>
                        <li>Thyroid Function Tests</li>
                    </ul>
                </div>
                <div class="test-card">
                    <h3>Imaging Services</h3>
                    <ul>
                        <li>X-Ray</li>
                        <li>MRI Scans</li>
                        <li>CT Scans</li>
                        <li>Ultrasound</li>
                    </ul>
                </div>
                <div class="test-card">
                    <h3>Specialized Tests</h3>
                    <ul>
                        <li>Urine Analysis</li>
                        <li>Hormone Panels</li>
                        <li>Genetic Testing</li>
                        <li>Cancer Screening</li>
                    </ul>
                </div>
            </div>

            <div class="lab-features">
                <h3>Key Laboratory Features</h3>
                <ul>
                    <li>Quick and Accurate Test Results</li>
                    <li>Secure Online Access to Reports</li>
                    <li>Advanced Diagnostic Technologies</li>
                    <li>Confidential Medical Records</li>
                </ul>
                <a href="LaboratoryForm.php" class="btn-primary">Schedule a Test</a>
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
                <form id="contact-form" action="process_contact.php" method="POST">
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
    <script>
        document.getElementById('contact-form').addEventListener('submit', function (e) {
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