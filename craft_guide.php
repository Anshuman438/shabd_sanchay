<?php
/**
 * Shabd Sanchay - Comprehensive Literary Craft & Metrics Guide (काव्य-शिल्प एवं छंद शास्त्र)
 * Full-fledged educational chapters on Doha, Ghazal, Chaupai, and Matra Science.
 */
$page_title = "काव्य-शिल्प एवं छंद शास्त्र | शब्द संचय";
require_once 'config.php';
$topic = isset($_GET['topic']) ? strtolower(trim($_GET['topic'])) : 'doha';
$valid_topics = ['doha', 'ghazal', 'chaupai', 'matra'];
if (!in_array($topic, $valid_topics)) {
    $topic = 'doha';
}
?>
<!DOCTYPE html>
<html lang="hi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Cinzel:wght@500;700;900&family=Noto+Sans+Devanagari:wght@300;400;500;600;700;800&family=Noto+Serif+Devanagari:wght@400;500;600;700;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Minimalist Editorial Chapter Styling */
        .craft-guide-wrapper {
            background-color: var(--bg);
            min-height: 100vh;
            padding: 3rem 0 5rem;
        }

        .guide-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Top Breadcrumb & Chapter Switcher */
        .guide-nav-header {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .guide-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.88rem;
            color: #7a6654;
            margin-bottom: 1.25rem;
        }

        .guide-breadcrumb a {
            color: #c85a17;
            text-decoration: none;
            font-weight: 600;
        }

        .guide-breadcrumb a:hover {
            text-decoration: underline;
        }

        .guide-tabs-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .guide-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.65rem 1.25rem;
            border-radius: 30px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #3b2c21;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        [data-theme="dark"] .guide-tab-btn {
            background: #1e1814;
            border-color: rgba(255, 255, 255, 0.1);
            color: #f3ece7;
        }

        .guide-tab-btn:hover {
            border-color: #c85a17;
            color: #c85a17;
            transform: translateY(-1px);
        }

        .guide-tab-btn.active {
            background: #c85a17;
            color: #ffffff;
            border-color: #c85a17;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* Chapter Article Card */
        .chapter-article {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            padding: 3rem 3.5rem;
            line-height: 1.9;
            color: #24170e;
        }

        [data-theme="dark"] .chapter-article {
            background: #1a1410;
            border-color: rgba(255, 255, 255, 0.08);
            color: #e5ded8;
        }

        .chapter-meta-tag {
            display: inline-block;
            background: rgba(0, 0, 0, 0.04);
            color: #c85a17;
            padding: 0.3rem 0.85rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .chapter-title {
            font-family: 'Rozha One', 'Noto Serif Devanagari', serif;
            font-size: 2.3rem;
            line-height: 1.35;
            color: #1a1008;
            margin: 0 0 1rem;
        }

        [data-theme="dark"] .chapter-title {
            color: #faefe6;
        }

        .chapter-lead-quote {
            font-family: 'Noto Serif Devanagari', serif;
            font-size: 1.18rem;
            color: #614a38;
            border-left: 3.5px solid #c85a17;
            padding-left: 1.25rem;
            margin: 1.5rem 0 2.5rem;
            font-style: italic;
        }

        [data-theme="dark"] .chapter-lead-quote {
            color: #d1bfb0;
        }

        /* Formula Box */
        .chapter-formula-card {
            background: linear-gradient(135deg, #fdf8f3 0%, #faefe2 100%);
            border: 1.5px solid rgba(200, 90, 23, 0.3);
            border-radius: 14px;
            padding: 1.5rem 1.8rem;
            margin: 2rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        [data-theme="dark"] .chapter-formula-card {
            background: #251b14;
            border-color: rgba(200, 90, 23, 0.4);
        }

        .formula-card-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #c85a17;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }

        .formula-card-equation {
            font-family: 'Rozha One', 'Noto Serif Devanagari', serif;
            font-size: 1.6rem;
            color: #2c1e14;
            margin: 0;
        }

        [data-theme="dark"] .formula-card-equation {
            color: #fcebd9;
        }

        /* Typography & Subheadings */
        .chapter-article h2 {
            font-family: 'Rozha One', 'Noto Serif Devanagari', serif;
            font-size: 1.55rem;
            color: #2c1e14;
            margin: 2.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        [data-theme="dark"] .chapter-article h2 {
            color: #faefe6;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .chapter-article h3 {
            font-size: 1.22rem;
            color: #933804;
            margin: 1.8rem 0 0.75rem;
            font-weight: 700;
        }

        [data-theme="dark"] .chapter-article h3 {
            color: #f6ad55;
        }

        .chapter-article p {
            font-size: 1.05rem;
            margin: 0 0 1.3rem;
            color: #3b2c21;
        }

        [data-theme="dark"] .chapter-article p {
            color: #d8cec5;
        }

        .rules-numbered-list {
            padding-left: 1.4rem;
            margin: 1rem 0 1.8rem;
        }

        .rules-numbered-list li {
            font-size: 1.05rem;
            margin-bottom: 0.85rem;
            color: #3b2c21;
        }

        [data-theme="dark"] .rules-numbered-list li {
            color: #d8cec5;
        }

        /* Verse Annotation Card */
        .verse-annotation-box {
            background: #faf7f2;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            padding: 1.6rem 1.8rem;
            margin: 1.8rem 0 2.2rem;
        }

        [data-theme="dark"] .verse-annotation-box {
            background: #221812;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .verse-annotation-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #c85a17;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .verse-text-highlight {
            font-family: 'Rozha One', 'Noto Serif Devanagari', serif;
            font-size: 1.3rem;
            line-height: 1.8;
            color: #1a1008;
            margin-bottom: 1rem;
            text-align: center;
        }

        [data-theme="dark"] .verse-text-highlight {
            color: #faefe6;
        }

        /* Metric Table */
        .metric-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.95rem;
        }

        .metric-breakdown-table th,
        .metric-breakdown-table td {
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            text-align: left;
        }

        [data-theme="dark"] .metric-breakdown-table th,
        [data-theme="dark"] .metric-breakdown-table td {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .metric-breakdown-table th {
            background: #f5ede4;
            color: #2c1e14;
            font-weight: 700;
        }

        [data-theme="dark"] .metric-breakdown-table th {
            background: #2b1f18;
            color: #faefe6;
        }

        /* Practice CTA Box */
        .chapter-cta-box {
            background: linear-gradient(135deg, #2c1e14 0%, #17100b 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 2rem 2.2rem;
            margin-top: 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .chapter-cta-box h4 {
            margin: 0 0 0.3rem;
            font-size: 1.25rem;
            font-family: 'Rozha One', serif;
            color: #f7d0a4;
        }

        .chapter-cta-box p {
            margin: 0;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .btn-cta-tester {
            background: #c85a17;
            color: #ffffff;
            padding: 0.75rem 1.6rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .btn-cta-tester:hover {
            background: #b34a0f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .chapter-article {
                padding: 1.8rem 1.25rem;
            }
            .chapter-title {
                font-size: 1.8rem;
            }
            .chapter-cta-box {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="craft-guide-wrapper">
        <div class="guide-container">
            
            <!-- Top Navigation & Switcher -->
            <div class="guide-nav-header">
                <div class="guide-breadcrumb">
                    <a href="index.php">← मुख्य पृष्ठ</a>
                    <span>/</span>
                    <a href="index.php#literary-craft">काव्य-शिल्प</a>
                    <span>/</span>
                    <span>शास्त्रीय छंद विधान</span>
                </div>

                <div class="guide-tabs-row">
                    <a href="craft_guide.php?topic=doha" class="guide-tab-btn <?= $topic === 'doha' ? 'active' : '' ?>">
                        दोहा छंद सूत्र
                    </a>
                    <a href="craft_guide.php?topic=ghazal" class="guide-tab-btn <?= $topic === 'ghazal' ? 'active' : '' ?>">
                        ग़ज़ल का व्याकरण
                    </a>
                    <a href="craft_guide.php?topic=chaupai" class="guide-tab-btn <?= $topic === 'chaupai' ? 'active' : '' ?>">
                        चौपाई छंद विधान
                    </a>
                    <a href="craft_guide.php?topic=matra" class="guide-tab-btn <?= $topic === 'matra' ? 'active' : '' ?>">
                        मात्रा गणना विज्ञान
                    </a>
                </div>
            </div>

            <!-- CHAPTER 1: DOHA METRE -->
            <?php if ($topic === 'doha'): ?>
            <article class="chapter-article">
                <span class="chapter-meta-tag">अर्ध-सम मात्रिक छंद विधान</span>
                <h1 class="chapter-title">दोहा छंद सूत्र, विधान एवं प्रामाणिक नियम</h1>
                
                <div class="chapter-lead-quote">
                    "दोहा हिंदी कविता का सबसे समृद्ध और लोकप्रिय मात्रिक छंद है, जिसमें गागर में सागर भरने की असीम सामर्थ्य होती है।"
                </div>

                <div class="chapter-formula-card">
                    <div>
                        <div class="formula-card-title">मात्रा सूत्र (Exact Formula)</div>
                        <div class="formula-card-equation">13 + 11 = 24 मात्राएँ</div>
                    </div>
                    <span style="font-size: 0.9rem; font-weight: 700; color: #7a6654;">कुल 4 चरण (2 दल) • विषम 13 • सम 11</span>
                </div>

                <h2>1. दोहा छंद की मूल संरचना</h2>
                <p>दोहा एक <strong>अर्ध-सम मात्रिक छंद</strong> है। इसके एक पूर्ण दोहे में दो पंक्तियाँ (दो दल) तथा कुल चार चरण होते हैं:</p>
                <ul class="rules-numbered-list">
                    <li><strong>विषम चरण (प्रथम एवं तृतीय चरण):</strong> प्रत्येक में ठीक <strong>13-13 मात्राएँ</strong> होती हैं।</li>
                    <li><strong>सम चरण (द्वितीय एवं चतुर्थ चरण):</strong> प्रत्येक में ठीक <strong>11-11 मात्राएँ</strong> होती हैं।</li>
                    <li><strong>यति (विराम):</strong> प्रत्येक पंक्ति में 13 मात्राओं के बाद (अल्पविराम पर) यति होती है।</li>
                    <li><strong>तुक विधान:</strong> तुक हमेशा सम चरणों के अंत में (चरण 2 और चरण 4) अनिवार्य रूप से मिलती है।</li>
                </ul>

                <h2>2. दोहे का सबसे अनिवार्य नियम (सम चरणान्त विधान)</h2>
                <p>छंद-शास्त्र में दोहे की गेयता और लय को सुरक्षित रखने के लिए सम चरण (11 मात्रा) के अंत का नियम सबसे कठोर माना गया है:</p>
                <ul class="rules-numbered-list">
                    <li><strong>गुरु-लघु (ऽ । = 2-1) अंत:</strong> सम चरण के अंत में अनिवार्य रूप से एक गुरु (2) और उसके बाद एक लघु (1) आना चाहिए (अर्थात 3 मात्रा भार)।</li>
                    <li><strong>कड़ा निषेध:</strong> सम चरण का अंत कभी भी दो गुरु (ऽऽ) अथवा दो लघु (।।) से नहीं होना चाहिए। ऐसा होने पर दोहा छंद दोषयुक्त माना जाता है।</li>
                    <li><strong>विषम चरण का आदि-नियम:</strong> विषम चरण (13 मात्रा) के आरंभ में जगण (।ऽ। = 1-2-1) का आना वर्जित माना जाता है ताकि कविता के प्रवाह में रुकावट न आए।</li>
                </ul>

                <h2>3. प्रामाणिक उदाहरण एवं मात्रा-भार विश्लेषण</h2>
                
                <div class="verse-annotation-box">
                    <div class="verse-annotation-title">संत कबीर दास का कालजयी दोहा:</div>
                    <div class="verse-text-highlight">
                        "बड़ा हुआ तो क्या हुआ, जैसे पेड़ खजूर।<br>
                        पंथी को छाया नहीं, फल लागैं अति दूर॥"
                    </div>

                    <table class="metric-breakdown-table">
                        <thead>
                            <tr>
                                <th>चरण</th>
                                <th>वर्ण एवं मात्रा-विच्छेद</th>
                                <th>मात्रा भार</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>चरण 1 (विषम)</strong></td>
                                <td>ब-ड़ा (3) + हु-आ (3) + तो (2) + क्या (2) + हु-आ (3)</td>
                                <td><strong>13 मात्राएँ</strong></td>
                            </tr>
                            <tr>
                                <td><strong>चरण 2 (सम)</strong></td>
                                <td>जै-से (4) + पेड़ (3) + ख-जू-र (4 [ख=1, जू=2, र=1 -> ऽ। अंत])</td>
                                <td><strong>11 मात्राएँ</strong></td>
                            </tr>
                            <tr>
                                <td><strong>चरण 3 (विषम)</strong></td>
                                <td>पं-थी (4) + को (2) + छा-या (4) + न-हीं (3)</td>
                                <td><strong>13 मात्राएँ</strong></td>
                            </tr>
                            <tr>
                                <td><strong>चरण 4 (सम)</strong></td>
                                <td>फ-ल (2) + ला-गैं (4) + अ-ति (2) + दू-र (3 [दू=2, र=1 -> ऽ। अंत])</td>
                                <td><strong>11 मात्राएँ</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>4. नए कवियों के लिए महत्वपूर्ण सुझाव</h2>
                <p>दोहा लिखते समय सदैव ध्यान रखें कि 13 मात्राओं की यति पर अर्थ का स्वाभाविक ठहराव होना चाहिए। यति पर शब्द को काटना (यति-भंग) काव्य-दोष माना जाता है।</p>

                <div class="chapter-cta-box">
                    <div>
                        <h4>क्या आप अपना दोहा परखना चाहते हैं?</h4>
                        <p>हमारे लाइव प्रो मात्रा गणक पर अपनी पंक्तियाँ लिखें और तत्काल स्वचालित विश्लेषण पाएँ।</p>
                    </div>
                    <a href="index.php#matra-calculator" class="btn-cta-tester">लाइव गणक खोलें →</a>
                </div>
            </article>

            <!-- CHAPTER 2: GHAZAL GRAMMAR -->
            <?php elseif ($topic === 'ghazal'): ?>
            <article class="chapter-article">
                <span class="chapter-meta-tag">उर्दू-हिंदी काव्य शिल्प</span>
                <h1 class="chapter-title">ग़ज़ल का व्याकरण, बहर एवं अरूज़ विधान</h1>
                
                <div class="chapter-lead-quote">
                    "ग़ज़ल का हर शेर अपने आप में एक मुकम्मल और स्वतंत्र कायनात होता है, जो वज़न (बहर), रदीफ़ और क़ाफ़िए के अटूट अनुशासन में बँधा होता है।"
                </div>

                <div class="chapter-formula-card">
                    <div>
                        <div class="formula-card-title">ग़ज़ल के 5 आधार स्तंभ</div>
                        <div class="formula-card-equation">मतला • रदीफ़ • क़ाफ़िया • बहर • मक़्ता</div>
                    </div>
                    <span style="font-size: 0.9rem; font-weight: 700; color: #7a6654;">शेर = मिसरा-ए-ऊला + मिसरा-ए-सानी</span>
                </div>

                <h2>1. ग़ज़ल के पारिभाषिक अंग</h2>
                <ul class="rules-numbered-list">
                    <li><strong>मतला (पहला शेर):</strong> ग़ज़ल का सबसे पहला शेर मतला कहलाता है। इसके दोनों मिसरे (पंक्तियाँ) अनिवार्य रूप से हम-क़ाफ़िया और हम-रदीफ़ होते हैं। यदि दूसरा शेर भी हम-क़ाफ़िया हो तो उसे 'हुस्न-ए-मतला' कहा जाता है।</li>
                    <li><strong>रदीफ़ (स्थिर ध्वनि पद):</strong> शेर के अंत में दोहराया जाने वाला हू-ब-हू शब्द या शब्द-समूह। पूरी ग़ज़ल में रदीफ़ कभी नहीं बदलती (उदा. "क्या है", "होता है", "नहीं आता")।</li>
                    <li><strong>क़ाफ़िया (हम-आवाज़ तुक):</strong> रदीफ़ से ठीक पहले आने वाले हम-वज़न और हम-आवाज़ तुकान्त शब्द (उदा. दवा, हवा, दुआ, सज़ा, ख़ुदा)।</li>
                    <li><strong>बहर (वज़न/मीटर):</strong> ग़ज़ल का संगीत और छंद-ढाँचा। ग़ज़ल के सभी शेरों का दोनों मिसरों में एक ही निश्चित बहर में होना अनिवार्य है।</li>
                    <li><strong>मक़्ता:</strong> ग़ज़ल का अंतिम शेर जिसमें शायर अपना तख़ल्लुस (उपनाम) प्रयोग करता है।</li>
                </ul>

                <h2>2. बहर एवं वज़न का विज्ञान</h2>
                <p>ग़ज़ल में मात्रा भार को 'वज़न' कहा जाता है। प्रत्येक शब्द का लघु (1) और गुरु (2) भार निर्धारित बहर के खांचे में ठीक बैठना चाहिए।</p>
                <p><strong>एज़ाफ़त का लचीलापन:</strong> उर्दू-फ़ारसी के यौगिक पदों (जैसे <em>"दिल-ए-नादाँ"</em> या <em>"शाम-ए-ग़म"</em>) में 'एज़ाफ़त' (ए) को बहर की आवश्यकतानुसार 1 मात्रा (लघु) अथवा 2 मात्रा (दीर्घ) गिना जा सकता है।</p>

                <h2>3. मिर्ज़ा ग़ालिब का प्रामाणिक मतला विश्लेषण</h2>
                
                <div class="verse-annotation-box">
                    <div class="verse-annotation-title">मिर्ज़ा असदुल्लाह ख़ाँ ग़ालिब:</div>
                    <div class="verse-text-highlight">
                        "दिल-ए-नादाँ तुझे हुआ <strong>क्या है</strong>?<br>
                        आख़िर इस दर्द की दवा <strong>क्या है</strong>?"
                    </div>

                    <table class="metric-breakdown-table">
                        <thead>
                            <tr>
                                <th>घटक</th>
                                <th>शब्द</th>
                                <th>भूमिका व नियम</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>रदीफ़ (स्थिर पद)</strong></td>
                                <td>"क्या है"</td>
                                <td>दोनों मिसरों के अंत में समान पद</td>
                            </tr>
                            <tr>
                                <td><strong>क़ाफ़िया (समान तुक)</strong></td>
                                <td>हुआ / दवा</td>
                                <td>रदीफ़ से पूर्व हम-आवाज़ शब्द (मात्रा भार 1+2 = 3)</td>
                            </tr>
                            <tr>
                                <td><strong>बहर वज़न</strong></td>
                                <td>फाएलातुन मफ़ाईलुन फेलुन</td>
                                <td>शास्त्रीय बहर-ए-हज़ज</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="chapter-cta-box">
                    <div>
                        <h4>क्या आप अपनी ग़ज़ल का वज़न जाँचना चाहते हैं?</h4>
                        <p>हमारे प्रो कैलकुलेटर पर अपने मिसरे लिखें और दोनों का मात्रा भार सामंजस्य देखें।</p>
                    </div>
                    <a href="index.php#matra-calculator" class="btn-cta-tester">लाइव बहर विश्लेषक खोलें →</a>
                </div>
            </article>

            <!-- CHAPTER 3: CHAUPAI METRE -->
            <?php elseif ($topic === 'chaupai'): ?>
            <article class="chapter-article">
                <span class="chapter-meta-tag">सम मात्रिक छंद विधान</span>
                <h1 class="chapter-title">चौपाई छंद विधान एवं गति-यति सूत्र</h1>
                
                <div class="chapter-lead-quote">
                    "चौपाई अवधी और हिंदी काव्य का सबसे संगीतमय सम-मात्रिक छंद है, जो रामचरितमानस और लोक-गाथाओं का मेरुदंड है।"
                </div>

                <div class="chapter-formula-card">
                    <div>
                        <div class="formula-card-title">मात्रा सूत्र (Chaupai Metre)</div>
                        <div class="formula-card-equation">16 + 16 मात्राएँ</div>
                    </div>
                    <span style="font-size: 0.9rem; font-weight: 700; color: #7a6654;">प्रत्येक चरण 16 मात्रा • 4 चरण • चरणान्त ऽऽ श्रेष्ठ</span>
                </div>

                <h2>1. चौपाई छंद के मुख्य लक्षण</h2>
                <ul class="rules-numbered-list">
                    <li><strong>सम-मात्रिक छंद:</strong> इसके चारों चरणों में समान रूप से <strong>16-16 मात्राएँ</strong> होती हैं।</li>
                    <li><strong>यति (विराम):</strong> प्रत्येक 16 मात्रा के अंत में अथवा 8-8 मात्रा पर यति होती है।</li>
                    <li><strong>तुक विधान:</strong> पहले चरण की तुक दूसरे से (1-2) तथा तीसरे चरण की तुक चौथे चरण (3-4) से मिलती है।</li>
                </ul>

                <h2>2. चौपाई के अंत के कड़े निषेध एवं श्रेष्ठता</h2>
                <p>चौपाई छंद के चरणान्त में लय का संतुलन बनाए रखने के लिए विशेष नियम निर्धारित हैं:</p>
                <ul class="rules-numbered-list">
                    <li><strong>जगण व तगण का सर्वथा निषेध:</strong> चौपाई के किसी भी चरण के अंत में "जगण" (।ऽ। = 1-2-1) अथवा "तगण" (ऽऽ। = 2-2-1) का आना वर्जित है। ऐसा होने पर पाठ में भारी रुकावट आती है।</li>
                    <li><strong>श्रेष्ठ अंत (दीर्घान्त):</strong> चरण के अंत में "दो गुरु" (ऽऽ = 2-2) आना सबसे शुभ, कर्णप्रिय और संगीतमय माना जाता है (जैसे: सा-ग-र = ऽऽ, उ-जा-ग-र = ऽऽ)। इसके अतिरिक्त "दो लघु" (।।) भी मान्य हैं।</li>
                </ul>

                <h2>3. गोस्वामी तुलसीदास की चौपाई का विश्लेषण</h2>
                
                <div class="verse-annotation-box">
                    <div class="verse-annotation-title">श्री हनुमान चालीसा:</div>
                    <div class="verse-text-highlight">
                        "जय हनुमान ज्ञान गुन सागर।<br>
                        जय कपीस तिहुँ लोक उजागर॥"
                    </div>

                    <table class="metric-breakdown-table">
                        <thead>
                            <tr>
                                <th>चरण</th>
                                <th>मात्रा विच्छेद</th>
                                <th>कुल मात्रा</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>चरण 1</strong></td>
                                <td>ज-य (2) + ह-नु-मा-न (5) + ज्ञा-न (3) + गु-न (2) + सा-ग-र (4)</td>
                                <td><strong>16 मात्राएँ (अंत ऽऽ)</strong></td>
                            </tr>
                            <tr>
                                <td><strong>चरण 2</strong></td>
                                <td>ज-य (2) + क-पी-स (4) + ति-हुँ (2) + लो-क (3) + उ-जा-ग-र (5)</td>
                                <td><strong>16 मात्राएँ (अंत ऽऽ)</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="chapter-cta-box">
                    <div>
                        <h4>अपनी चौपाई का छंदानुशासन परखें</h4>
                        <p>16-16 मात्राओं का शुद्ध संतुलन जाँचने के लिए प्रो मात्रा गणक का उपयोग करें।</p>
                    </div>
                    <a href="index.php#matra-calculator" class="btn-cta-tester">लाइव गणक खोलें →</a>
                </div>
            </article>

            <!-- CHAPTER 4: MATRA SCIENCE -->
            <?php else: ?>
            <article class="chapter-article">
                <span class="chapter-meta-tag">मात्रा शास्त्र (काव्यालय शोध)</span>
                <h1 class="chapter-title">मात्रा गणना एवं संयुक्ताक्षर का संपूर्ण विज्ञान</h1>
                
                <div class="chapter-lead-quote">
                    "काव्य में वर्णों के उच्चारण में लगने वाले समय (काल) को मात्रा कहते हैं। एक निमेष (पलक झपकने के समय) को एक मात्रा माना जाता है।"
                </div>

                <div class="chapter-formula-card">
                    <div>
                        <div class="formula-card-title">मात्रा विभाजन (The Dual Scale)</div>
                        <div class="formula-card-equation">लघु (। = 1 मात्रा) | गुरु (ऽ = 2 मात्राएँ)</div>
                    </div>
                    <span style="font-size: 0.9rem; font-weight: 700; color: #7a6654;">काव्यालय शोध (विनोद तिवारी व वाणी मुरारका) आधारित</span>
                </div>

                <h2>1. ह्रस्व (लघु) एवं दीर्घ (गुरु) वर्णों की पहचान</h2>
                <ul class="rules-numbered-list">
                    <li><strong>लघु वर्ण (। = 1 मात्रा):</strong> अ, इ, उ, ऋ स्वर तथा इनसे युक्त व्यंजन (क, कि, कु, कृ)।</li>
                    <li><strong>गुरु वर्ण (ऽ = 2 मात्राएँ):</strong> आ, ई, ऊ, ए, ऐ, ओ, औ स्वर तथा इनसे युक्त व्यंजन (का, की, कू, के, कै, को, कौ)।</li>
                    <li><strong>अनुस्वार (ं) व विसर्ग (ः):</strong> अनुस्वार युक्त वर्ण (हंस, कंत, संत) और विसर्ग युक्त वर्ण (दुःख, प्रातः) हमेशा <strong>गुरु (2 मात्रा)</strong> होते हैं।</li>
                    <li><strong>चन्द्रबिन्दु (ँ):</strong> चन्द्रबिन्दु केवल नासिक्य उच्चारण है, यह ह्रस्व को दीर्घ नहीं बनाता। अतः <em>"हँस"</em> और <em>"मुँह"</em> में हँ और मुँ की <strong>1 मात्रा (लघु)</strong> ही रहती है।</li>
                </ul>

                <h2>2. संयुक्ताक्षर का संपूर्ण त्रिसूत्र (The Tri-Sutra of Conjuncts)</h2>
                <p>आधे अक्षर (हलन्त) की अपनी कोई स्वतंत्र मात्रा नहीं होती, किंतु वह अपने आसपास के वर्णों पर उच्चारण-दबाव (Stress) डालकर उनका भार बदल देता है:</p>

                <ul class="rules-numbered-list">
                    <li><strong>नियम 1: आरंभिक आधा वर्ण (भार = 0):</strong><br>
                    शब्द के शुरू में आने वाला आधा वर्ण कोई भार नहीं लेता क्योंकि उसका उच्चारण सीधे अगले स्वर के साथ मिलकर होता है।<br>
                    <em>उदाहरण:</em> <code>क्लेश</code> (क्ले[2] + श[1] = 3), <code>प्यार</code> (प्या[2] + र[1] = 3), <code>स्थान</code> (स्था[2] + न[1] = 3)।</li>

                    <li><strong>नियम 2: लघु के बाद आधा वर्ण (लघु बन जाता है गुरु = 2):</strong><br>
                    जब किसी ह्रस्व (लघु) वर्ण के बाद आधा वर्ण आता है, तो उच्चारण का धक्का पहले वाले लघु पर पड़ता है और वह गुरु (2) बन जाता है।<br>
                    <em>उदाहरण:</em> <code>कष्ट</code> (क[2] + ष्ट[1] = 3), <code>कल्प</code> (क[2] + ल्प[1] = 3), <code>सुर्ख़</code> (सु[2] + र्ख़[1] = 3), <code>सख्त</code> (स[2] + ख़्त[1] = 3), <code>सत्य</code> (स[2] + त्य[1] = 3)।</li>

                    <li><strong>नियम 3: दीर्घ के बाद आधा वर्ण + लघु वर्ण (शून्य अतिरिक्त भार = 0):</strong><br>
                    यदि आधा वर्ण किसी दीर्घ वर्ण के बाद आए और उसके बाद लघु वर्ण हो, तो दीर्घ वर्ण पहले से ही 2 मात्रा का है, अतः आधा वर्ण कोई नया भार नहीं जोड़ता।<br>
                    <em>उदाहरण:</em> <code>आत्म</code> (आ[2] + त्म[1] = 3), <code>दीर्घ</code> (दी[2] + र्घ[1] = 3), <code>मूर्ख</code> (मूर्[2] + ख[1] = 3)।</li>

                    <li><strong>विशेष नियम 4: दो दीर्घ वर्णों के बीच फंसा आधा वर्ण (+1 भार):</strong><br>
                    यदि आधा अक्षर दो दीर्घ वर्णों के बीच आ जाए, तो वह अलग से 1 मात्रा का समय लेता है।<br>
                    <em>उदाहरण:</em> <code>आत्मा</code> = आ (2) + त् (1) + मा (2) = <strong>5 मात्राएँ</strong>।</li>
                </ul>

                <h2>3. त्वरित तुलनात्मक अभ्यास तालिका</h2>
                <table class="metric-breakdown-table">
                    <thead>
                        <tr>
                            <th>शब्द</th>
                            <th>मात्रा विच्छेद</th>
                            <th>कुल मात्रा</th>
                            <th>लागू वैज्ञानिक नियम</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>हँस</strong></td>
                            <td>हँ (1) + स (1)</td>
                            <td>2</td>
                            <td>चन्द्रबिन्दु = लघु (1)</td>
                        </tr>
                        <tr>
                            <td><strong>हंस</strong></td>
                            <td>हं (2) + स (1)</td>
                            <td>3</td>
                            <td>अनुस्वार = गुरु (2)</td>
                        </tr>
                        <tr>
                            <td><strong>क्लेश</strong></td>
                            <td>क्ले (2) + श (1)</td>
                            <td>3</td>
                            <td>आरंभिक क् = 0 भार</td>
                        </tr>
                        <tr>
                            <td><strong>कष्ट</strong></td>
                            <td>क (2) + ष्ट (1)</td>
                            <td>3</td>
                            <td>ष् के दबाव से क = गुरु (2)</td>
                        </tr>
                        <tr>
                            <td><strong>आत्म</strong></td>
                            <td>आ (2) + त्म (1)</td>
                            <td>3</td>
                            <td>दीर्घ के बाद आधा त् = 0 अतिरिक्त</td>
                        </tr>
                        <tr>
                            <td><strong>आत्मा</strong></td>
                            <td>आ (2) + त् (1) + मा (2)</td>
                            <td>5</td>
                            <td>दो दीर्घ के बीच आधा त् = +1 भार</td>
                        </tr>
                    </tbody>
                </table>

                <div class="chapter-cta-box">
                    <div>
                        <h4>संयुक्ताक्षरों का स्वयं लाइव परीक्षण करें</h4>
                        <p>हमारे प्रो मात्रा गणक पर क्लेश, प्यार, कष्ट, आत्मा आदि शब्द लिखकर लाइव चिप्स देखें।</p>
                    </div>
                    <a href="index.php#matra-calculator" class="btn-cta-tester">लाइव मात्रा गणक खोलें →</a>
                </div>
            </article>
            <?php endif; ?>

        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
