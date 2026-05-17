<?php
// customer_about.php
$activePage = 'about';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About & Technology - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_about.css">
    <!-- EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
</head>

<body>

    <!-- Shared navbar -->
    <?php require_once 'includes/navbar.php'; ?>

    <div class="layout">
        <main class="main-content">
            <div class="about-container">

                <!-- ── SPLIT LAYOUT ── -->
                <div class="about-split-layout">
                    <!-- Left Column: Info -->
                    <div class="about-info-col">
                        <section class="about-hero">
                            <h1 class="about-title">
                                PharmaSync <span>Basud</span>
                            </h1>
                            <p class="about-subtitle">
                                PharmaSync Basud is a real-time decentralized health-system middleware designed to integrate and unify the inventories of independent local pharmacies across Poblacion, Basud, Camarines Norte.
                            </p>
                            <p class="about-subtitle" style="margin-top: 1rem;">
                                By leveraging standard relational database normalization, direct Google Maps search embedding, and parallel openFDA integrations, our platform provides guest residents and local healthcare providers with seamless, instant medication searches, pharmacy directions, and critical clinical drug guidelines without requiring any registration or accounts.
                            </p>
                            <p class="about-subtitle" style="margin-top: 1rem;">
                                This digital portal acts as a vital municipal utility, bridging the gap between local stock and the community during everyday needs and emergencies alike.
                            </p>
                        </section>
                    </div>

                    <!-- Right Column: Email Form -->
                    <div class="about-form-col">
                        <section class="contact-section">
                            <div class="glass-card contact-card animate-fade">
                                <div class="contact-header">

                                    <h2 class="section-title" style="margin-bottom: 0.5rem; justify-content: center;">Send us a Message</h2>
                                    <p class="contact-desc">
                                        Have questions, suggestions, or need technical assistance? Fill out the form below to reach the support team directly.
                                    </p>
                                </div>

                                <form id="contactForm" onsubmit="submitContactForm(event)">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="contactName">YOUR NAME</label>
                                            <input type="text" id="contactName" name="name" class="form-input"
                                                placeholder="Enter your full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="contactEmail">EMAIL ADDRESS</label>
                                            <input type="email" id="contactEmail" name="email" class="form-input"
                                                placeholder="your.email@example.com" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="contactSubject">SUBJECT</label>
                                        <input type="text" id="contactSubject" name="subject" class="form-input"
                                            placeholder="Enter the subject of your message" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="contactMessage">YOUR MESSAGE</label>
                                        <textarea id="contactMessage" name="message" class="form-input" rows="5"
                                            placeholder="Write your message here..." required style="resize: none;"></textarea>
                                    </div>

                                    <button type="submit" class="form-submit-btn">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                            style="margin-right: 0.4rem;">
                                            <line x1="22" y1="2" x2="11" y2="13"></line>
                                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                        </svg>
                                        Send Message
                                    </button>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>

            </div>
        </main>
    </div>


    <script>
        // ── EMAILJS CONFIGURATION ──
        // Replace these empty strings with your actual EmailJS credentials to receive real emails in your inbox!
        const EMAILJS_PUBLIC_KEY = "oyWQlB5lUg39oUzU4";
        const EMAILJS_SERVICE_ID = "service_ii2z8cp";
        const EMAILJS_TEMPLATE_ID = "template_yy49d2c";

        if (EMAILJS_PUBLIC_KEY) {
            emailjs.init({
                publicKey: EMAILJS_PUBLIC_KEY,
            });
        }

        // Handle AJAX Contact Form Submission
        function submitContactForm(event) {
            event.preventDefault();

            // Anti-Spam Rate Limiter Check (30 seconds)
            const lastSubmit = sessionStorage.getItem('last_contact_timestamp');
            const now = Date.now();
            if (lastSubmit && (now - lastSubmit < 30000)) {
                alert('Please wait 30 seconds before sending another message.');
                return;
            }

            const nameInput = document.getElementById('contactName').value.trim();
            const emailInput = document.getElementById('contactEmail').value.trim();
            const subjectInput = document.getElementById('contactSubject').value;
            const messageInput = document.getElementById('contactMessage').value.trim();

            const submitBtn = event.target.querySelector('.form-submit-btn');
            const originalBtnHTML = submitBtn.innerHTML;

            // Set loading state on button
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 0.4rem; animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor"></path>
                </svg>
                Sending...
            `;

            // Helper function to transition UI to premium success state
            function transitionToSuccess() {
                sessionStorage.setItem('last_contact_timestamp', Date.now());
                const card = document.querySelector('.contact-card');
                card.innerHTML = `
                    <div style="text-align: center; padding: 2rem 0; animation: fadeIn 0.4s ease-out;">
                        <div style="width: 64px; height: 64px; background: #D1FAE5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <h2 style="font-weight: 800; font-size: 1.75rem; color: var(--text-main); margin-bottom: 0.75rem;">Message Sent!</h2>
                        <p style="color: var(--text-muted); line-height: 1.6; max-width: 440px; margin: 0 auto 2rem; font-size: 0.95rem;">
                            Thank you, <strong style="color:var(--text-main);">${escapeHTML(nameInput)}</strong>. Your inquiry has been sent to our healthcare administration team. We will review your message and reply via email at <strong style="color:var(--primary);">${escapeHTML(emailInput)}</strong> shortly.
                        </p>
                        <button onclick="window.location.reload()" class="form-submit-btn" style="max-width: 220px; margin: 0 auto;">
                            Send Another Message
                        </button>
                    </div>
                `;
            }

            // 🌟 ELEGANT DUAL INTEGRATION FLOW:
            // Check if EmailJS key configurations are entered
            if (EMAILJS_PUBLIC_KEY && EMAILJS_SERVICE_ID && EMAILJS_TEMPLATE_ID) {
                // Use EmailJS to dispatch a real live email!
                emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, {
                    from_name: nameInput,
                    from_email: emailInput,
                    subject: subjectInput,
                    message: messageInput
                })
                    .then(() => {
                        transitionToSuccess();
                    })
                    .catch((err) => {
                        alert('EmailJS failed to deliver message: ' + (err.text || JSON.stringify(err)));
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHTML;
                    });
            } else {
                // Fallback seamless transition to database logs
                const payload = {
                    name: nameInput,
                    email: emailInput,
                    category: subjectInput,
                    suggestion: messageInput
                };

                fetch('api/submit_suggestion.php?action=submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            transitionToSuccess();
                        } else {
                            alert('Submission error: ' + res.message);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHTML;
                        }
                    })
                    .catch(err => {
                        alert('Connection error occurred while sending message.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHTML;
                    });
            }
        }

        // Escape HTML helper
        function escapeHTML(str) {
            return str.replace(/[&<>'"]/g,
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                }[tag] || tag)
            );
        }
    </script>

</body>

</html>