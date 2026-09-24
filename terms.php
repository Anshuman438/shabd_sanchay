<?php
require_once 'config.php';
require_once 'includes/helpers.php';
$page_title = "नियम एवं शर्तें (Terms of Service) - शब्द संचय";
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
                    <span class="bc-current">नियम एवं शर्तें</span>
                </nav>

                <article class="poem-reading-card" style="padding: 3rem 2.8rem; margin-top: 1.5rem;">
                    <header class="poem-reading-header" style="text-align: left; margin-bottom: 2rem;">
                        <span class="page-eyebrow" style="text-align: left;">मंच की नियमावली • TERMS OF SERVICE</span>
                        <h1 class="poem-reading-title" style="font-size: 2.2rem; margin: 0.4rem 0;">नियम एवं शर्तें (Terms & Conditions)</h1>
                        <div class="heading-artistic-underline" style="margin: 0.6rem 0 1.2rem;"></div>
                        <p style="color: var(--color-ink-light); font-size: 0.95rem;">अंतिम संशोधन तिथि: <?= date('d M Y') ?></p>
                    </header>

                    <div class="legal-body-prose" style="font-family: var(--font-body); font-size: 1.05rem; line-height: 1.85; color: var(--color-ink);">
                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">1. नियमों की स्वीकृति</h2>
                            <p><strong>शब्द संचय</strong> पोर्टल का उपयोग करके आप इन नियमों व शर्तों को पूर्णतः स्वीकार करते हैं। यदि आप इन शर्तों से असहमत हैं, तो कृपया मंच की सेवाओं का उपयोग न करें।</p>
                        </section>

                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">2. बौद्धिक संपदा एवं कॉपीराइट (Copyright)</h2>
                            <p>इस मंच पर प्रकाशित कविताएँ, लेख, निबंध और कहानियाँ संबंधित कवियों व लेखकों की अनन्य बौद्धिक संपदा हैं।</p>
                            <ul style="margin-left: 1.5rem; margin-top: 0.5rem; list-style-type: square;">
                                <li>साहित्यिक कृतियों को व्यक्तिगत अध्ययन, साझा करने या पठन हेतु प्रयोग किया जा सकता है, बशर्ते रचनाकार और स्रोत का समुचित उल्लेख किया जाए।</li>
                                <li>बिना पूर्व लिखित अनुमति के किसी भी रचना का व्यावसायिक या अनाधिकृत पुनरुत्पादन निषिद्ध है।</li>
                            </ul>
                        </section>

                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">3. साहित्यिक आचार संहिता एवं टिप्पणियाँ</h2>
                            <p>पाठक और लेखक रचनात्मक संवाद और प्रतिक्रियाएँ देने के लिए स्वतंत्र हैं। हालांकि:</p>
                            <ul style="margin-left: 1.5rem; margin-top: 0.5rem; list-style-type: square;">
                                <li>अमर्यादित, आपत्तिजनक, विद्वेषपूर्ण अथवा स्पैम टिप्पणियों को बिना किसी पूर्व सूचना के हटाया जा सकता है।</li>
                                <li>रचनाकारों की मौलिकता और मानवीय गरिमा का सम्मान करना प्रत्येक सदस्य का दायित्व है।</li>
                            </ul>
                        </section>

                        <section style="margin-bottom: 2rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">4. मात्रा गणना एवं साहित्यिक टूल्स का उपयोग</h2>
                            <p>मंच पर उपलब्ध मात्रा गणना टूल, छंद नियमावली और पोस्टर जनरेटर लेखकों और विद्यार्थियों के साहित्यिक मार्गदर्शन हेतु निःशुल्क प्रदान किए गए हैं। इनका उपयोग रचना अभ्यास के लिए स्वतंत्र रूप से किया जा सकता है।</p>
                        </section>

                        <section style="margin-bottom: 1.5rem;">
                            <h2 style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--color-primary-dark); margin-bottom: 0.6rem;">5. संपर्क एवं सूचना</h2>
                            <p>नियमों व शर्तों अथवा कॉपीराइट संबंधी किसी भी सूचना के लिए आप <a href="contact.php" style="color: var(--color-primary); font-weight: 600; text-decoration: underline;">संपर्क पृष्ठ</a> के माध्यम से संपादकीय मंडल से संवाद कर सकते हैं।</p>
                        </section>
                    </div>
                </article>

            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
