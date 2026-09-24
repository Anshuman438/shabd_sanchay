<?php
require_once 'config.php';
require_once 'includes/helpers.php';
$page_title = "गोपनीयता नीति (Privacy Policy) - शब्द संचय";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="legal-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="legal-page-wrapper" style="padding: 3.5rem 0 5rem;">
            <div class="container" style="max-width: 880px;">
                
                <nav class="poem-breadcrumb" aria-label="ब्रेडक्रम्ब">
                    <a href="index.php">मुख्य पृष्ठ</a>
                    <span class="bc-sep">›</span>
                    <span class="bc-current">गोपनीयता नीति</span>
                </nav>

                <article class="poem-reading-card" style="padding: 3rem 2.8rem; margin-top: 1.5rem;">
                    <header class="poem-reading-header" style="text-align: left; margin-bottom: 2rem;">
                        <span class="page-eyebrow" style="text-align: left;">नीति एवं नियम • PRIVACY POLICY</span>
                        <h1 class="poem-reading-title" style="font-size: 2.2rem; margin: 0.4rem 0;">गोपनीयता नीति (Privacy Policy)</h1>
                        <div class="heading-artistic-underline" style="margin: 0.6rem 0 1.2rem;"></div>
                        <p style="color: var(--color-ink-light); font-size: 0.95rem;">अंतिम संशोधन तिथि: <?= date('d M Y') ?></p>
                    </header>

                    <div class="legal-body-prose" style="font-family: var(--font-body); font-size: 1.05rem; line-height: 1.85; color: var(--color-ink);">
                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">1. प्रस्तावना एवं हमारा दृष्टिकोण</h2>
                            <p><strong>शब्द संचय</strong> पर हम अपने पाठकों, कवियों, लेखकों और साहित्य प्रेमियों की निजता का सर्वोच्च सम्मान करते हैं। यह नीति स्पष्ट करती है कि जब आप हमारी वेबसाइट पर आते हैं, तो हम आपकी जानकारी को किस प्रकार सुरक्षित और मर्यादित रखते हैं।</p>
                        </section>

                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">2. एकत्रित की जाने वाली जानकारी</h2>
                            <p>हम केवल वही जानकारी एकत्र करते हैं जो आपके साहित्यिक अनुभव को उत्कृष्ट बनाने के लिए आवश्यक है:</p>
                            <ul style="margin-left: 1.5rem; margin-top: 0.5rem; list-style-type: square;">
                                <li><strong>प्रतिक्रिया व संपर्क जानकारी:</strong> जब आप किसी कविता या लेख पर टिप्पणी करते हैं अथवा संपर्क फॉर्म भरते हैं, तो आपका नाम और ईमेल पता सुरक्षित रूप से संग्रहीत किया जाता है।</li>
                                <li><strong>पत्रिका (Newsletter) सदस्यता:</strong> जब आप हमारी साहित्यिक पत्रिका के लिए सदस्यता लेते हैं, तो आपका ईमेल पता केवल नए अंक और साहित्यिक अपडेट भेजने हेतु उपयोग किया जाता है।</li>
                                <li><strong>स्थानीय प्राथमिकताएं (Local Storage):</strong> डार्क/लाइट थीम चयन, बुकमार्क की गई रचनाएँ और पंक्तियाँ आपके अपने ब्राउज़र के लोकल स्टोरेज में ही सुरक्षित रहती हैं।</li>
                            </ul>
                        </section>

                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">3. सूचना की सुरक्षा व गोपनीयता</h2>
                            <p>हम किसी भी परिस्थिति में आपकी व्यक्तिगत जानकारी (जैसे ईमेल या नाम) किसी तीसरे पक्ष (Third Party) को न तो बेचते हैं और न ही विपणन (Commercial Marketing) के उद्देश्य से साझा करते हैं।</p>
                        </section>

                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">4. कुकीज़ एवं डेटा नियंत्रण</h2>
                            <p>हमारी साइट न्यूनतम और आवश्यक कुकीज़ का उपयोग करती है ताकि सत्र सुरक्षित रहे और उपयोगकर्ता अनुभव सहज बना रहे। आप कभी भी अपने ब्राउज़र की सेटिंग से कुकीज़ और लोकल स्टोरेज डेटा साफ़ कर सकते हैं।</p>
                        </section>

                        <section style="margin-bottom: 1.5rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">5. संपर्क एवं समाधान</h2>
                            <p>यदि इस गोपनीयता नीति के संबंध में आपका कोई प्रश्न, सुझाव या अनुरोध है, तो आप बेझिझक हमारे <a href="contact.php" style="color: var(--color-primary); font-weight: 600; text-decoration: underline;">संपर्क पृष्ठ</a> के माध्यम से हमसे संपर्क कर सकते हैं।</p>
                        </section>
                    </div>
                </article>

            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
