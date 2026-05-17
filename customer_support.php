<?php
// ── 1. Set active nav tab for this page ──
$activePage = 'support';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/customer_support.css">
</head>
<body>

<!-- Shared navbar -->
<?php require_once 'includes/navbar.php'; ?>

<div class="layout">
    <main class="main-content">

        <!-- ── HEADER SECTION ── -->
        <section class="support-header">
            <div class="support-header-content">
                <p class="support-tagline">HOW CAN WE HELP?</p>
                <h1 class="support-title">
                    Your Digital<br>
                    <span class="support-title-blue">Health Sanctuary</span>
                </h1>
                <p class="support-subtitle">
                    Experience a higher standard of care with PharmaSync Basud.
                    Our pharmacists are online and ready to assist you with
                    prescriptions, clinical advice, and local pharmacy coordination.
                </p>
            </div>
        </section>

        <!-- ── MAIN CONTENT GRID ── -->
        <section class="support-main">
            <div class="support-grid">

                <!-- ── LEFT: SERVICE CARDS + FAQ ── -->
                <div class="support-left">

                    <!-- Service Cards Row -->
                    <div class="support-services">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                                    <path d="M21 19a2 2 0 0 1-2 2h-1v-6h3zM3 19a2 2 0 0 0 2 2h1v-6H3z"></path>
                                </svg>
                            </div>
                            <h3 class="service-title">Live Pharmacist Chat</h3>
                            <p class="service-desc">
                                Real-time clinical consultation for medications
                                and symptoms.
                            </p>
                        </div>

                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1"></path>
                                </svg>
                            </div>
                            <h3 class="service-title">Refill Status</h3>
                            <p class="service-desc">
                                Track your current prescriptions and delivery
                                schedules.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="support-faq">
                        <h2 class="faq-title">Frequently Asked Questions</h2>

                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>How do I request a prescription refill?</span>
                                <svg class="faq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="faq-answer">
                                <p>You can request a refill directly through the "Medications" tab in your portal.
                                    Simply select the active prescription and click "Request Refill." Our team in
                                    Basud will process it within 2 hours during business hours.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>Can I speak with a pharmacist about drug interactions?</span>
                                <svg class="faq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="faq-answer">
                                <p>Yes! Our licensed pharmacists are available for live consultation through the
                                    chat feature. Click "Live Pharmacist Chat" above or use the support form to
                                    schedule a consultation about drug interactions, side effects, or dosage questions.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <button class="faq-question" onclick="toggleFAQ(this)">
                                <span>What are your delivery areas in Camarines Norte?</span>
                                <svg class="faq-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div class="faq-answer">
                                <p>We currently deliver throughout Basud, Capalonga, Mercedes, and surrounding
                                    barangays in Camarines Norte. Same-day delivery is available for orders placed
                                    before 2 PM. Emergency medications can be arranged for after-hours delivery.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ── RIGHT: SUPPORT FORM + CONTACT ── -->
                <div class="support-right">

                    <!-- Support Ticket Form -->
                    <div class="support-form-card">
                        <h2 class="form-title">Submit a Support Ticket</h2>

                        <form class="support-form" onsubmit="submitSupportTicket(event)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="yourName">YOUR NAME</label>
                                    <input type="text" id="yourName" name="yourName"
                                           placeholder="John Doe" required>
                                </div>
                                <div class="form-group">
                                    <label for="orderId">ORDER ID</label>
                                    <input type="text" id="orderId" name="orderId"
                                           placeholder="#PH-4921">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inquiryType">INQUIRY TYPE</label>
                                <select id="inquiryType" name="inquiryType" required>
                                    <option value="">Select a category</option>
                                    <option value="prescription">Prescription Refill</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message">MESSAGE</label>
                                <textarea id="message" name="message" rows="4"
                                          placeholder="Describe your concern in detail..." required></textarea>
                            </div>

                            <button type="submit" class="form-submit-btn">Send Inquiry</button>
                        </form>
                    </div>

                    <!-- Local Contact Card -->
                    <div class="support-contact-card">
                        <h3 class="contact-title">Local Basud Pharmacy</h3>

                        <div class="contact-item">
                            <svg class="contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <div>
                                <strong>Main Branch</strong><br>
                                <span>Maharlika Highway St., Basud, Camarines Norte</span>
                            </div>
                        </div>

                        <div class="contact-item">
                            <svg class="contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <div>
                                <strong>+63 (054) 881-2049</strong>
                            </div>
                        </div>

                        <div class="contact-item">
                            <svg class="contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="m2 7 10 5 10-5"></path>
                            </svg>
                            <div>
                                <strong>support@pharmasync-basud.ph</strong>
                            </div>
                        </div>

                        <!-- Map placeholder -->
                        <div class="contact-map">
                            <svg class="map-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="supportGrid" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(37,99,235,0.1)" stroke-width="1"/>
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#supportGrid)" />
                                <path d="M-5 60 L120 20 M20 100 L100 30" stroke="rgba(37,99,235,0.15)" stroke-width="2" fill="none"/>
                            </svg>

                            <!-- Map pins -->
                            <div class="map-pin" style="top: 40%; left: 60%;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" fill="#2563EB" stroke="white" stroke-width="2"/>
                                    <circle cx="12" cy="10" r="3" fill="white"/>
                                </svg>
                            </div>
                            <div class="map-pin" style="top: 65%; left: 35%;">
                                <div class="map-pin-dot" style="background: #F59E0B;"></div>
                            </div>
                            <div class="map-pin" style="top: 25%; left: 45%;">
                                <div class="map-pin-dot" style="background: #10B981;"></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

    </main>
</div>

<!-- Shared footer -->
<?php require_once 'includes/footer.php'; ?>

<script>
    function toggleFAQ(button) {
        const faqItem = button.closest('.faq-item');
        const answer = faqItem.querySelector('.faq-answer');
        const arrow = button.querySelector('.faq-arrow');

        const isOpen = faqItem.classList.contains('open');

        // Close all other FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('open');
        });

        // Toggle current item
        if (!isOpen) {
            faqItem.classList.add('open');
        }
    }

    function submitSupportTicket(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData);

        // Simulate form submission
        console.log('Support ticket submitted:', data);

        // Show success message
        alert('Your support ticket has been submitted! We\'ll respond within 24 hours.');

        // Reset form
        event.target.reset();
    }
</script>

</body>
</html>