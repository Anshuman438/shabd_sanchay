<?php
require_once 'config.php';
require_once 'includes/helpers.php';

$page_title = "संपर्क करें एवं रचना भेजें - शब्द संचय";

// Handle form submission
$form_success = false;
$form_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? $conn->real_escape_string(trim($_POST['subject'])) : 'सामान्य प्रश्न';
    $message = isset($_POST['message']) ? $conn->real_escape_string(trim($_POST['message'])) : '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $form_error = "कृपया सभी आवश्यक फ़ील्ड (नाम, ईमेल एवं संदेश) भरें।";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_error = "कृपया एक मान्य ईमेल पता दर्ज करें।";
    } else {
        // Resilient insert into contacts table
        $query = "INSERT INTO contacts (name, email, subject, message, created_at) 
                  VALUES ('$name', '$email', '$subject', '$message', NOW())";
        
        if (@$conn->query($query)) {
            $form_success = true;
        } else {
            // If created_at doesn't exist, try fallback insert
            $fallback_query = "INSERT INTO contacts (name, email, subject, message) 
                               VALUES ('$name', '$email', '$subject', '$message')";
            if (@$conn->query($fallback_query)) {
                $form_success = true;
            } else {
                $form_error = "संदेश प्रेषित करने में कुछ तकनीकी बाधा आई। कृपया थोड़ी देर बाद पुनः प्रयास करें।";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="शब्द संचय संपादकीय मंडल से संपर्क करें। अपनी कविता, कहानी या आलेख प्रकाशन हेतु भेजें।">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="contact-modern-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <!-- Hero Section -->
        <section class="poetry-page-hero contact-hero">
            <div class="container">
                <div class="poetry-hero-content">
                    <span class="page-eyebrow">संवाद एवं संपर्क • GET IN TOUCH & CONTRIBUTE</span>
                    <h1 class="page-main-title">हमसे संवाद करें</h1>
                    <div class="heading-artistic-underline"></div>
                    <p class="page-lead-subtitle">रचना प्रकाशन, संपादकीय सुझाव, साहित्यिक विचार-विमर्श या किसी भी जिज्ञासा के लिए हमारी टीम सदैव आपके साथ है।</p>
                </div>
            </div>
        </section>

        <!-- Main Contact Content Section -->
        <section class="contact-content-section">
            <div class="container">
                <div class="contact-dual-grid">
                    
                    <!-- Left: Interactive Contact & Submission Form -->
                    <div class="contact-form-parchment">
                        <div class="form-header-badge">
                            <span class="form-badge-icon">✍️</span>
                            <span>संपादकीय संवाद पटल</span>
                        </div>
                        <h2 class="contact-form-heading">अपनी बात या रचना हम तक पहुँचाएँ</h2>
                        <p class="contact-form-sub">नीचे दिए गए फ़ॉर्म को भरें। हम आपके संदेश की समीक्षा कर शीघ्रातिशीघ्र उत्तर देंगे।</p>

                        <?php if ($form_success): ?>
                            <div class="contact-alert success-alert">
                                <span class="alert-icon">✨</span>
                                <div class="alert-text">
                                    <strong>हार्दिक धन्यवाद!</strong>
                                    <p>आपका संदेश / रचना हमारे संपादकीय मंडल को सफलतापूर्वक प्राप्त हो गई है। हम शीघ्र ही आपके ईमेल पर संपर्क करेंगे।</p>
                                </div>
                            </div>
                        <?php elseif (!empty($form_error)): ?>
                            <div class="contact-alert error-alert">
                                <span class="alert-icon">⚠️</span>
                                <div class="alert-text">
                                    <strong>त्रुटि:</strong>
                                    <p><?= htmlspecialchars($form_error) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form class="modern-contact-form" method="POST" action="contact.php">
                            <div class="form-row-dual">
                                <div class="form-field-group">
                                    <label for="contact_name" class="field-label">आपका नाम <span class="required-star">*</span></label>
                                    <div class="input-icon-wrap">
                                        <input type="text" id="contact_name" name="name" required placeholder="उदा. रामधारी सिंह" value="<?= isset($_POST['name']) && !$form_success ? htmlspecialchars($_POST['name']) : '' ?>">
                                    </div>
                                </div>

                                <div class="form-field-group">
                                    <label for="contact_email" class="field-label">ईमेल पता <span class="required-star">*</span></label>
                                    <div class="input-icon-wrap">
                                        <input type="email" id="contact_email" name="email" required placeholder="उदा. kavita@example.com" value="<?= isset($_POST['email']) && !$form_success ? htmlspecialchars($_POST['email']) : '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-field-group">
                                <label for="contact_subject" class="field-label">संवाद का उद्देश्य / विषय</label>
                                <div class="select-icon-wrap">
                                    <select id="contact_subject" name="subject">
                                        <option value="रचना प्रकाशन हेतु (कविता / कहानी / आलेख)">🖋️ रचना प्रकाशन हेतु (कविता / कहानी / आलेख)</option>
                                        <option value="संपादकीय सुझाव व प्रतिक्रिया">💬 संपादकीय सुझाव व प्रतिक्रिया</option>
                                        <option value="सामान्य प्रश्न एवं जानकारी">❓ सामान्य प्रश्न एवं जानकारी</option>
                                        <option value="सहयोग एवं साहित्यिक साझेदारी">🤝 सहयोग एवं साहित्यिक साझेदारी</option>
                                        <option value="तकनीकी सहायता">⚡ तकनीकी सहायता</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-field-group">
                                <label for="contact_message" class="field-label">संदेश या आपकी रचना <span class="required-star">*</span></label>
                                <textarea id="contact_message" name="message" rows="6" required placeholder="यहाँ अपना संदेश, सुझाव या रचना का पूरा पाठ (शीर्षक सहित) लिखें..."><?= isset($_POST['message']) && !$form_success ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                            </div>

                            <button type="submit" class="btn-contact-submit">
                                <span>संदेश प्रेषित करें</span>
                                <span class="btn-arrow">➔</span>
                            </button>
                        </form>
                    </div>

                    <!-- Right: Info Cards & Guidelines -->
                    <div class="contact-info-column">
                        
                        <!-- Contact Methods List -->
                        <div class="contact-methods-stack">
                            
                            <!-- Email Card -->
                            <div class="contact-method-card">
                                <div class="method-icon-circle">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>
                                <div class="method-content">
                                    <span class="method-label">ईमेल संपर्क</span>
                                    <h3 class="method-title">संपादकीय एवं रचना विभाग</h3>
                                    <p class="method-desc">
                                        <a href="mailto:editor@shabdsanchay.com" class="method-link">editor@shabdsanchay.com</a><br>
                                        <a href="mailto:info@shabdsanchay.com" class="method-link">info@shabdsanchay.com</a>
                                    </p>
                                    <span class="method-note">⚡ 24 से 48 घंटों में उत्तर</span>
                                </div>
                            </div>

                            <!-- Phone Card -->
                            <div class="contact-method-card">
                                <div class="method-icon-circle">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <div class="method-content">
                                    <span class="method-label">दूरभाष</span>
                                    <h3 class="method-title">हेल्पलाइन व संपादकीय परामर्श</h3>
                                    <p class="method-desc">
                                        <strong>+91 98765 43210</strong> (संपादक मंडल)<br>
                                        <strong>+91 87654 32109</strong> (तकनीकी सहायता)
                                    </p>
                                    <span class="method-note">🕒 सोमवार से शनिवार • प्रात: 10:00 से सायं 6:00 बजे तक</span>
                                </div>
                            </div>

                            <!-- Address Card -->
                            <div class="contact-method-card">
                                <div class="method-icon-circle">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                                <div class="method-content">
                                    <span class="method-label">साहित्यिक केंद्र</span>
                                    <h3 class="method-title">मुख्य कार्यालय</h3>
                                    <p class="method-desc">
                                        शब्द संचय साहित्य संस्थान<br>
                                        123 साहित्य नगर, हिंदी मार्ग, नई दिल्ली - 110001
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submission Guidelines Card -->
                        <div class="submission-rules-card">
                            <div class="rules-card-header">
                                <span class="rules-sparkle">📜</span>
                                <h4>रचना प्रेषण के मुख्य दिशा-निर्देश</h4>
                            </div>
                            <ul class="rules-check-list">
                                <li>
                                    <span class="check-icon">✓</span>
                                    <span>रचना पूर्णतः <strong>मौलिक एवं आपकी अपनी</strong> होनी चाहिए।</span>
                                </li>
                                <li>
                                    <span class="check-icon">✓</span>
                                    <span>कृपया रचना <strong>देवनागरी लिपि (Unicode Hindi)</strong> में ही भेजें।</span>
                                </li>
                                <li>
                                    <span class="check-icon">✓</span>
                                    <span>रचना के साथ अपना <strong>संक्षिप्त परिचय (Bio)</strong> व शहर अवश्य लिखें।</span>
                                </li>
                                <li>
                                    <span class="check-icon">✓</span>
                                    <span>स्वीकृत रचनाएँ <strong>3 से 5 कार्यदिवसों</strong> के भीतर प्रकाशित की जाती हैं।</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Social Media Connectivity -->
                        <div class="contact-social-card">
                            <h4 class="social-card-title">सोशल मीडिया पर जुड़े रहें</h4>
                            <div class="social-pills-row">
                                <a href="#" class="social-pill" aria-label="Instagram">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                    <span>Instagram</span>
                                </a>
                                <a href="#" class="social-pill" aria-label="YouTube">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                                    <span>YouTube</span>
                                </a>
                                <a href="#" class="social-pill" aria-label="X Twitter">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    <span>Twitter</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive FAQ Accordion -->
        <section class="contact-faq-section">
            <div class="container">
                <div class="section-center-heading">
                    <span class="section-eyebrow">जिज्ञासा समाधान • FAQS</span>
                    <h2 class="section-main-heading">अक्सर पूछे जाने वाले प्रश्न</h2>
                    <div class="heading-artistic-underline"></div>
                </div>

                <div class="faq-accordion-container">
                    <?php
                    $faqs = @$conn->query("SELECT * FROM faqs ORDER BY id");
                    
                    if ($faqs && $faqs->num_rows > 0) {
                        while($faq = $faqs->fetch_assoc()) {
                            echo '
                            <div class="modern-faq-card">
                                <button type="button" class="faq-accordion-btn">
                                    <span class="faq-q-text">'.htmlspecialchars($faq['question']).'</span>
                                    <span class="faq-chevron-icon">+</span>
                                </button>
                                <div class="faq-accordion-body">
                                    <p>'.htmlspecialchars($faq['answer']).'</p>
                                </div>
                            </div>';
                        }
                    } else {
                        // High quality default literary FAQs
                        ?>
                        <div class="modern-faq-card active">
                            <button type="button" class="faq-accordion-btn">
                                <span class="faq-q-text">शब्द संचय पर रचना प्रकाशित कराने की प्रक्रिया क्या है?</span>
                                <span class="faq-chevron-icon">−</span>
                            </button>
                            <div class="faq-accordion-body">
                                <p>आप ऊपर दिए गए फ़ॉर्म के माध्यम से अपनी रचना (कविता, कहानी, आलेख) शीर्षक और अपने संक्षिप्त परिचय के साथ भेज सकते हैं। संपादकीय मंडल द्वारा समीक्षा के पश्चात् स्वीकृत रचनाएँ मंच पर प्रकाशित कर आपको ईमेल द्वारा सूचित किया जाता है।</p>
                            </div>
                        </div>

                        <div class="modern-faq-card">
                            <button type="button" class="faq-accordion-btn">
                                <span class="faq-q-text">क्या रचना प्रकाशन के लिए कोई शुल्क देय होता है?</span>
                                <span class="faq-chevron-icon">+</span>
                            </button>
                            <div class="faq-accordion-body">
                                <p>बिल्कुल नहीं। शब्द संचय एक विशुद्ध गैर-व्यावसायिक साहित्यिक मंच है। यहाँ रचना प्रकाशन पूरी तरह से निःशुल्क और केवल साहित्यिक गुणवत्ता व मौलिकता पर आधारित है।</p>
                            </div>
                        </div>

                        <div class="modern-faq-card">
                            <button type="button" class="faq-accordion-btn">
                                <span class="faq-q-text">मात्रा गणक टूल का उपयोग कैसे करें?</span>
                                <span class="faq-chevron-icon">+</span>
                            </button>
                            <div class="faq-accordion-body">
                                <p>आप हमारे नेविगेशन बार में 'मात्रा गणना व सीखें' पेज पर जा सकते हैं। वहाँ अपनी पंक्ति टाइप करते ही सिस्टम वास्तविक समय में लघु (।) और गुरु (ऽ) मात्राओं की स्वचालित गणना एवं छंद की पहचान कर देता है।</p>
                            </div>
                        </div>

                        <div class="modern-faq-card">
                            <button type="button" class="faq-accordion-btn">
                                <span class="faq-q-text">क्या पूर्व-प्रकाशित रचनाएँ भी भेजी जा सकती हैं?</span>
                                <span class="faq-chevron-icon">+</span>
                            </button>
                            <div class="faq-accordion-body">
                                <p>हम प्राथमिकता मौलिक एवं अप्रकाशित रचनाओं को देते हैं। यदि आपकी रचना किसी पुस्तक में पूर्व-प्रकाशित है, तो संदेश में उसका संदर्भ अवश्य उल्लेख करें।</p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    // FAQ Accordion Toggle Interaction
    document.addEventListener('DOMContentLoaded', () => {
        const faqCards = document.querySelectorAll('.modern-faq-card');
        faqCards.forEach(card => {
            const btn = card.querySelector('.faq-accordion-btn');
            if (btn) {
                btn.addEventListener('click', () => {
                    const wasActive = card.classList.contains('active');
                    
                    // Close all
                    faqCards.forEach(c => {
                        c.classList.remove('active');
                        const chevron = c.querySelector('.faq-chevron-icon');
                        if (chevron) chevron.textContent = '+';
                    });
                    
                    // Toggle current
                    if (!wasActive) {
                        card.classList.add('active');
                        const chevron = card.querySelector('.faq-chevron-icon');
                        if (chevron) chevron.textContent = '−';
                    }
                });
            }
        });
    });
    </script>
</body>
</html>