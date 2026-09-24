<?php
require_once 'config.php';
require_once 'includes/helpers.php';

$page_title = "मात्रा गणना एवं काव्य-शिल्प - शब्द संचय";
$active_tab = isset($_GET['tab']) ? htmlspecialchars(trim($_GET['tab']), ENT_QUOTES, 'UTF-8') : 'calculator';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="हिंदी छंद शास्त्र, लाइव मात्रा गणक टूल, दोहा, चौपाई, ग़ज़ल बहर और काव्य-शिल्प के प्रामाणिक नियम।">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="learn-craft-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="learn-page-wrapper">
            <!-- Hero Banner -->
            <section class="poetry-page-hero">
                <div class="container">
                    <div class="poetry-hero-content">
                        <span class="page-eyebrow">काव्य-शिल्प, छंद शास्त्र व मात्रा विज्ञान • LITERARY METRICS & CRAFT</span>
                        <h1 class="page-main-title">मात्रा गणना एवं काव्य-शिल्प</h1>
                        <div class="heading-artistic-underline"></div>
                        <p class="page-lead-subtitle">हिंदी छंद शास्त्र के प्रामाणिक नियम, लाइव मात्रा गणक यंत्र, दोहा-चौपाई शिल्प और ग़ज़ल की बहर का संपूर्ण मार्गदर्शक।</p>

                        <!-- Interactive Learn Navigation Tabs -->
                        <div class="category-pills-bar learn-tabs-bar" id="learn-tabs-bar">
                            <button type="button" class="category-pill <?= $active_tab === 'calculator' ? 'active' : '' ?>" data-tab="calculator">
                                <span>🧮 लाइव मात्रा गणक (Tool)</span>
                            </button>
                            <button type="button" class="category-pill <?= $active_tab === 'matra_rules' ? 'active' : '' ?>" data-tab="matra_rules">
                                <span>📜 मात्रा गणना नियम</span>
                            </button>
                            <button type="button" class="category-pill <?= $active_tab === 'doha' ? 'active' : '' ?>" data-tab="doha">
                                <span>🖋️ दोहा शिल्प</span>
                            </button>
                            <button type="button" class="category-pill <?= $active_tab === 'ghazal' ? 'active' : '' ?>" data-tab="ghazal">
                                <span>🎼 ग़ज़ल व अरूज़</span>
                            </button>
                            <button type="button" class="category-pill <?= $active_tab === 'chaupai' ? 'active' : '' ?>" data-tab="chaupai">
                                <span>📖 चौपाई व अन्य छंद</span>
                            </button>
                            <button type="button" class="category-pill <?= $active_tab === 'alankar' ? 'active' : '' ?>" data-tab="alankar">
                                <span>🎭 रस व अलंकार</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Content Area -->
            <section class="poetry-content-section learn-content-section">
                <div class="container">

                    <!-- =========================================================================
                         TAB 1: LIVE MATRA CALCULATOR & METRIC ANALYZER
                         ========================================================================= -->
                    <div class="learn-tab-pane <?= $active_tab === 'calculator' ? 'active' : '' ?>" id="pane-calculator">
                        <div class="matra-calc-container">
                            <div class="calc-card-header">
                                <div class="calc-title-group">
                                    <h2 class="calc-main-title">लाइव हिंदी मात्रा गणक (Interactive Meter Calculator)</h2>
                                    <p class="calc-subtitle">अपनी काव्य पंक्ति नीचे लिखें या पेस्ट करें। हमारा सिस्टम तुरंत प्रत्येक वर्ण का लघु (।) / गुरु (ऽ) मान, कुल मात्राएँ और संभावित छंद का विश्लेषण करेगा।</p>
                                </div>
                                <div class="calc-preset-group">
                                    <span class="preset-label">त्वरित उदाहरण:</span>
                                    <button type="button" class="calc-preset-btn" onclick="applyPreset('रहिमन पानी राखिये, बिन पानी सब सून।\nपानी गये न ऊबरे, मोती मानुष चून॥')">रहिमन पानी (दोहा)</button>
                                    <button type="button" class="calc-preset-btn" onclick="applyPreset('जय हनुमान ज्ञान गुन सागर। जय कपीस तिहुँ लोक उजागर॥')">हनुमान चालीसा (चौपाई)</button>
                                    <button type="button" class="calc-preset-btn" onclick="applyPreset('लहरों से डर कर नौका पार नहीं होती,\nकोशिश करने वालों की कभी हार नहीं होती।')">कोशिश करने वालों (कविता)</button>
                                    <button type="button" class="calc-preset-btn" onclick="applyPreset('हो गई है पीर पर्वत सी पिघलनी चाहिए,\nइस हिमालय से कोई गंगा निकलनी चाहिए।')">पीर पर्वत सी (ग़ज़ल)</button>
                                </div>
                            </div>

                            <!-- Input Area -->
                            <div class="calc-input-wrap">
                                <textarea id="matra-input-text" class="calc-textarea" rows="4" placeholder="यहाँ अपनी कविता, दोहा, चौपाई या ग़ज़ल की पंक्तियाँ टाइप करें... (उदा. रहिमन देखि बड़ेन को...)"></textarea>
                                <div class="calc-action-bar">
                                    <button type="button" class="calc-clear-btn" onclick="clearCalculator()">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        <span>साफ़ करें</span>
                                    </button>
                                    <span class="calc-status-hint">⚡ वास्तविक समय में गणना (Real-time Instant Evaluation)</span>
                                </div>
                            </div>

                            <!-- Real-Time Results Display -->
                            <div class="calc-results-wrapper" id="calc-results-output">
                                <!-- Rendered dynamically via JS -->
                            </div>

                            <!-- Quick Reference Key -->
                            <div class="calc-legend-card">
                                <h3 class="legend-title">मात्रा संकेत एवं नियम निर्देशिका</h3>
                                <div class="legend-items-grid">
                                    <div class="legend-item">
                                        <span class="legend-symbol laghu">।</span>
                                        <div class="legend-text">
                                            <strong>लघु वर्ण (1 मात्रा):</strong> अ, इ, उ, ऋ, चंद्रबिंदु (ँ) तथा बिना मात्रा वाले शुद्ध व्यंजन (क, ख, ग...)।
                                        </div>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-symbol guru">ऽ</span>
                                        <div class="legend-text">
                                            <strong>गुरु वर्ण (2 मात्रा):</strong> आ, ई, ऊ, ए, ऐ, ओ, औ, अनुस्वार (ं), विसर्ग (ः) एवं संयुक्ताक्षर से पूर्व का वर्ण।
                                        </div>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-symbol rule">⚡</span>
                                        <div class="legend-text">
                                            <strong>संयुक्ताक्षर का प्रभाव:</strong> यदि किसी लघु वर्ण के ठीक बाद आधा अक्षर (जैसे 'क्त', 'प्र', 'त्य') आए, तो पूर्व लघु वर्ण <strong>गुरु (2)</strong> हो जाता है।
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =========================================================================
                         TAB 2: MATRA SCIENCE RULES
                         ========================================================================= -->
                    <div class="learn-tab-pane <?= $active_tab === 'matra_rules' ? 'active' : '' ?>" id="pane-matra_rules">
                        <article class="learn-article-parchment">
                            <span class="article-section-badge">अध्याय 1 • छंद विज्ञान</span>
                            <h2 class="learn-chapter-title">मात्रा गणना के अचूक एवं प्रामाणिक नियम</h2>
                            <div class="heading-artistic-underline" style="margin-left:0;"></div>

                            <p class="chapter-intro-lead">
                                छंद शास्त्र में किसी वर्ण (अक्षर) के उच्चारण में लगने वाले समय को <strong>'मात्रा'</strong> कहते हैं। पलक झपकने के समय को एक मात्रा (लघु) माना जाता है, तथा उसके दुगने समय को दो मात्राएँ (गुरु) कहा जाता है।
                            </p>

                            <div class="rules-cards-stack">
                                <div class="rule-card">
                                    <div class="rule-badge">नियम 1</div>
                                    <h3 class="rule-heading">ह्रस्व (लघु) वर्ण — मान = 1 मात्रा (।)</h3>
                                    <p>निम्नलिखित वर्णों की 1 मात्रा मानी जाती है:</p>
                                    <ul>
                                        <li>मूल स्वर: <strong>अ, इ, उ, ऋ</strong></li>
                                        <li>ह्रस्व स्वर युक्त व्यंजन: <strong>क, कि, कु, कृ</strong> इत्यादि।</li>
                                        <li>अनुनासिक (चंद्रबिंदु) युक्त वर्ण: <strong>हँस, चाँदनी का 'चाँ' (दीर्घ होने पर गुरु, लेकिन 'हँ' लघु)</strong>। चंद्रबिंदु अपने वर्ण का भार नहीं बढ़ाता।</li>
                                    </ul>
                                </div>

                                <div class="rule-card">
                                    <div class="rule-badge">नियम 2</div>
                                    <h3 class="rule-heading">दीर्घ (गुरु) वर्ण — मान = 2 मात्राएँ (ऽ)</h3>
                                    <p>निम्नलिखित वर्णों की 2 मात्राएँ गिनी जाती हैं:</p>
                                    <ul>
                                        <li>दीर्घ स्वर: <strong>आ, ई, ऊ, ए, ऐ, ओ, औ</strong></li>
                                        <li>दीर्घ मात्रा वाले व्यंजन: <strong>का, की, कू, के, कै, को, कौ</strong></li>
                                        <li>अनुस्वार (बिंदी) युक्त वर्ण: <strong>कंग, संत, चंदन</strong> ('सं' = 2, 'चन्' = 2)</li>
                                        <li>विसर्ग युक्त वर्ण: <strong>अतः, दुःख</strong> ('अतः' में 'तः' = 2 मात्राएँ)</li>
                                    </ul>
                                </div>

                                <div class="rule-card highlight-card">
                                    <div class="rule-badge highlight">महत्वपूर्ण नियम 3</div>
                                    <h3 class="rule-heading">संयुक्ताक्षर (आधे अक्षर) से पूर्व का वर्ण</h3>
                                    <p>यदि किसी लघु वर्ण के बाद कोई आधा अक्षर (संयुक्त व्यंजन) आता है, तो उच्चारण का आघात (Stress) पड़ने के कारण वह <strong>लघु वर्ण गुरु (2)</strong> बन जाता है:</p>
                                    <div class="example-box">
                                        <p><strong>सत्य:</strong> 'स' (1) + आधा 'त्' मिलकर 'स' = <strong>2 (ऽ)</strong>, और 'य' = <strong>1 (।)</strong> → कुल = <strong>3 मात्राएँ (ऽ ।)</strong></p>
                                        <p><strong>भक्त:</strong> 'भ' (1) + आधा 'क्' मिलकर 'भ' = <strong>2 (ऽ)</strong>, और 'त' = <strong>1 (।)</strong> → कुल = <strong>3 मात्राएँ (ऽ ।)</strong></p>
                                        <p><strong>धर्म:</strong> 'ध' + 'र्' = <strong>2 (ऽ)</strong>, 'म' = <strong>1 (।)</strong> → कुल = <strong>3 मात्राएँ (ऽ ।)</strong></p>
                                    </div>
                                </div>

                                <div class="rule-card">
                                    <div class="rule-badge">नियम 4</div>
                                    <h3 class="rule-heading">शब्द के आरंभ में आने वाला आधा अक्षर</h3>
                                    <p>यदि आधा अक्षर शब्द के प्रारंभ में हो, तो उसका कोई मात्रा भार नहीं होता:</p>
                                    <div class="example-box">
                                        <p><strong>प्यार:</strong> आधा 'प्' (0) + 'या' (2) + 'र' (1) = <strong>3 मात्राएँ (ऽ ।)</strong></p>
                                        <p><strong>प्रेम:</strong> 'प्रे' (2) + 'म' (1) = <strong>3 मात्राएँ (ऽ ।)</strong></p>
                                        <p><strong>स्थान:</strong> आधा 'स्' (0) + 'था' (2) + 'न' (1) = <strong>3 मात्राएँ (ऽ ।)</strong></p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- =========================================================================
                         TAB 3: DOHA CRAFT & GUIDELINES
                         ========================================================================= -->
                    <div class="learn-tab-pane <?= $active_tab === 'doha' ? 'active' : '' ?>" id="pane-doha">
                        <article class="learn-article-parchment">
                            <span class="article-section-badge">अध्याय 2 • मात्रिक छंद</span>
                            <h2 class="learn-chapter-title">दोहा शिल्प, विधान एवं संरचना</h2>
                            <div class="heading-artistic-underline" style="margin-left:0;"></div>

                            <p class="chapter-intro-lead">
                                दोहा हिंदी साहित्य का सर्वाधिक लोकप्रिय और प्रतिष्ठित अर्धसम मात्रिक छंद है। इसमें दो पंक्तियाँ (चार चरण) होती हैं।
                            </p>

                            <!-- Formula Visual Card -->
                            <div class="learn-formula-visual">
                                <div class="formula-col">
                                    <span class="formula-label">प्रथम चरण (विषम)</span>
                                    <span class="formula-value">13 मात्राएँ</span>
                                </div>
                                <div class="formula-sep">+</div>
                                <div class="formula-col">
                                    <span class="formula-label">द्वितीय चरण (सम)</span>
                                    <span class="formula-value">11 मात्राएँ</span>
                                </div>
                                <div class="formula-sep">=</div>
                                <div class="formula-col total">
                                    <span class="formula-label">एक पंक्ति (दल)</span>
                                    <span class="formula-value">24 मात्राएँ</span>
                                </div>
                            </div>

                            <div class="craft-rules-list">
                                <div class="craft-point">
                                    <h4>1. चरणों का विभाजन:</h4>
                                    <p>पहले और तीसरे चरण (विषम चरण) में <strong>13-13 मात्राएँ</strong> होती हैं। दूसरे और चौथे चरण (सम चरण) में <strong>11-11 मात्राएँ</strong> होती हैं।</p>
                                </div>
                                <div class="craft-point">
                                    <h4>2. पादांत (चरण का अंत) नियम:</h4>
                                    <p>दूसरे और चौथे चरण (सम चरण) के अंत में अनिवार्य रूप से <strong>गुरु-लघु (ऽ ।)</strong> अर्थात् 2-1 मात्रा आनी चाहिए। कभी भी अंत में दो गुरु (ऽ ऽ) या दो लघु (। ।) नहीं होने चाहिए।</p>
                                </div>
                                <div class="craft-point">
                                    <h4>3. विषम चरण का प्रारंभ:</h4>
                                    <p>13 मात्राओं वाले विषम चरण के आरंभ में कभी भी <strong>'जगण' (। ऽ ।)</strong> नहीं आना चाहिए। (उदा. 'महान', 'नदी' जैसे 1-2-1 शब्द से प्रारंभ वर्जित है)।</p>
                                </div>
                            </div>

                            <div class="literary-masterpiece-example">
                                <h3 class="example-title">आदर्श उदाहरण (कबीरदास जी):</h3>
                                <blockquote class="poetic-quote-block">
                                    पोथी पढ़ि पढ़ि जग मुआ, पंडित भया न कोय। (13 + 11 = 24)<br>
                                    ढाई आखर प्रेम का, पढ़े सो पंडित होय॥ (13 + 11 = 24)
                                </blockquote>
                            </div>
                        </article>
                    </div>

                    <!-- =========================================================================
                         TAB 4: GHAZAL AND ARŪZ
                         ========================================================================= -->
                    <div class="learn-tab-pane <?= $active_tab === 'ghazal' ? 'active' : '' ?>" id="pane-ghazal">
                        <article class="learn-article-parchment">
                            <span class="article-section-badge">अध्याय 3 • अरूज़ शास्त्र</span>
                            <h2 class="learn-chapter-title">ग़ज़ल का शिल्प, अरूज़ एवं बहर</h2>
                            <div class="heading-artistic-underline" style="margin-left:0;"></div>

                            <p class="chapter-intro-lead">
                                ग़ज़ल केवल शेरों का संग्रह नहीं, बल्कि एक अनुशासित छंदबद्ध कला है जिसमें प्रत्येक शेर अपने आप में एक संपूर्ण विचार समेटे होता है।
                            </p>

                            <div class="ghazal-elements-grid">
                                <div class="ghazal-element-card">
                                    <span class="element-tag">1. मतला (Matla)</span>
                                    <p>ग़ज़ल का पहला शेर जिसके दोनों मिसरों (पंक्तियों) में <strong>काफ़िया और रदीफ़</strong> का पूर्ण निर्वाह होता है।</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">2. रदीफ़ (Radeef)</span>
                                    <p>शेर के अंत में आने वाले हूबहू दोहराए जाने वाले शब्द (जैसे "चाहिए", "होता है")।</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">3. काफ़िया (Qafia)</span>
                                    <p>रदीफ़ से ठीक पहले आने वाले हम-आवाज़ (Rhyming) शब्द (जैसे "पिघलनी / निकलनी", "पानी / कहानी")।</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">4. मक़्ता (Maqta)</span>
                                    <p>ग़ज़ल का अंतिम शेर, जिसमें शायर अपना उपनाम (तख़ल्लुस) दर्ज करता है।</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">5. बहर व वज़्न (Bahr & Wazan)</span>
                                    <p>ग़ज़ल का वह निश्चित मीटर / लय जिसके आधार पर सभी मिसरे बराबर तौले जाते हैं (लघु-गुरु का निश्चित क्रम)।</p>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- =========================================================================
                         TAB 5: CHAUPAI AND OTHER METERS
                         ========================================================================= -->
                    <div class="learn-tab-pane <?= $active_tab === 'chaupai' ? 'active' : '' ?>" id="pane-chaupai">
                        <article class="learn-article-parchment">
                            <span class="article-section-badge">अध्याय 4 • मात्रिक छंद</span>
                            <h2 class="learn-chapter-title">चौपाई, सोरठा एवं अन्य मात्रिक छंद</h2>
                            <div class="heading-artistic-underline" style="margin-left:0;"></div>

                            <div class="rules-cards-stack">
                                <div class="rule-card">
                                    <div class="rule-badge">चौपाई विधान</div>
                                    <h3 class="rule-heading">चौपाई (16 मात्राएँ प्रति चरण)</h3>
                                    <p>चौपाई एक सम मात्रिक छंद है। इसके प्रत्येक चरण में <strong>16 मात्राएँ</strong> होती हैं। चरण के अंत में जगण (। ऽ ।) अथवा तगण (ऽ ऽ ।) का आना वर्जित है। अंत में सामान्यतः दो गुरु (ऽ ऽ) आते हैं।</p>
                                    <div class="example-box">
                                        <p><strong>उदाहरण (श्रीरामचरितमानस):</strong><br>
                                        जय हनुमान ज्ञान गुन सागर। (16 मात्राएँ)<br>
                                        जय कपीस तिहुँ लोक उजागर॥ (16 मात्राएँ)</p>
                                    </div>
                                </div>

                                <div class="rule-card">
                                    <div class="rule-badge">सोरठा विधान</div>
                                    <h3 class="rule-heading">सोरठा (दोहा का उल्टा छंद)</h3>
                                    <p>सोरठा, दोहे का ठीक उल्टा होता है। इसके पहले और तीसरे (विषम) चरण में <strong>11-11 मात्राएँ</strong> तथा दूसरे और चौथे (सम) चरण में <strong>13-13 मात्राएँ</strong> होती हैं। तुक विषम चरणों में मिलती है।</p>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- =========================================================================
                         TAB 6: RAS & ALANKAR
                         ========================================================================= -->
                    <div class="learn-tab-pane <?= $active_tab === 'alankar' ? 'active' : '' ?>" id="pane-alankar">
                        <article class="learn-article-parchment">
                            <span class="article-section-badge">अध्याय 5 • काव्य सौंदर्य</span>
                            <h2 class="learn-chapter-title">अलंकार एवं नवरस विवेचन</h2>
                            <div class="heading-artistic-underline" style="margin-left:0;"></div>

                            <div class="ghazal-elements-grid">
                                <div class="ghazal-element-card">
                                    <span class="element-tag">अनुप्रास अलंकार</span>
                                    <p>जहाँ वर्णों की आवृत्ति बार-बार हो। (उदा. <em>चारु चंद्र की चंचल किरणें</em>)</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">यमक अलंकार</span>
                                    <p>एक ही शब्द कई बार आए और हर बार अर्थ भिन्न हो। (उदा. <em>कनक कनक ते सौ गुनी</em>)</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">श्लेष अलंकार</span>
                                    <p>एक ही शब्द के प्रसंगवश अनेक अर्थ निकलते हों। (उदा. <em>रहिमन पानी राखिये...</em>)</p>
                                </div>
                                <div class="ghazal-element-card">
                                    <span class="element-tag">उपमा व रूपक</span>
                                    <p>जहाँ किसी वस्तु की तुलना प्रसिद्ध वस्तु से की जाए या अभेद आरोप हो। (उदा. <em>मुख मयंक सम मंजु मनोहर</em>)</p>
                                </div>
                            </div>
                        </article>
                    </div>

                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    // =========================================================================
    // ADVANCED HINDI DEVANAGARI MATRA PARSER & METRIC ANALYZER
    // =========================================================================

    // Devanagari Characters Classifications
    const VOWELS_LAGHU = ['अ', 'इ', 'उ', 'ऋ'];
    const VOWELS_GURU  = ['आ', 'ई', 'ऊ', 'ए', 'ऐ', 'ओ', 'औ', 'अं', 'अः'];
    const MATRAS_LAGHU = ['\u093F', '\u0941', '\u0943']; // िक, ुक, ृक
    const MATRAS_GURU  = ['\u093E', '\u0940', '\u0942', '\u0947', '\u0948', '\u094B', '\u094C', '\u0902', '\u0903']; // ा, ी, ू, े, ै, ो, ौ, ं, ः
    const VIRAMA       = '\u094D'; // हलन्त ्
    const CHANDRABINDU = '\u0901'; // ँ
    const NUKTA        = '\u093C'; // ़

    function analyzeLineMatras(line) {
        if (!line || line.trim().length === 0) return null;

        // Clean punctuation while keeping Devanagari and spaces
        const rawLine = line.trim();
        const words = rawLine.split(/\s+/);
        let lineTotalMatras = 0;
        let lineSymbolString = '';
        let wordDetails = [];

        words.forEach(word => {
            // Clean non-devanagari characters from word for counting
            const cleanWord = word.replace(/[^\u0900-\u097F]/g, '');
            if (!cleanWord) return;

            const syllables = parseWordSyllables(cleanWord);
            let wordMatraCount = 0;
            let wordSymbols = '';

            syllables.forEach(syl => {
                wordMatraCount += syl.weight;
                wordSymbols += syl.weight === 2 ? 'ऽ' : '।';
            });

            lineTotalMatras += wordMatraCount;
            lineSymbolString += (lineSymbolString ? ' ' : '') + wordSymbols;

            wordDetails.push({
                word: cleanWord,
                syllables: syllables,
                total: wordMatraCount,
                symbols: wordSymbols
            });
        });

        // Identify Meter / Chhand Characteristics
        let chhandGuess = identifyMeter(lineTotalMatras, line);

        return {
            originalText: rawLine,
            totalMatras: lineTotalMatras,
            symbolString: lineSymbolString,
            words: wordDetails,
            meterGuess: chhandGuess
        };
    }

    // Break Hindi word into syllables and assign weights
    function parseWordSyllables(word) {
        const chars = Array.from(word);
        let units = [];
        let i = 0;

        while (i < chars.length) {
            let char = chars[i];

            // Independent Vowel
            if (VOWELS_LAGHU.includes(char)) {
                units.push({ text: char, weight: 1 });
                i++;
                continue;
            }
            if (VOWELS_GURU.includes(char)) {
                units.push({ text: char, weight: 2 });
                i++;
                continue;
            }

            // Consonant
            if (char >= '\u0915' && char <= '\u0939' || char >= '\u0958' && char <= '\u095F') {
                let unitText = char;
                let weight = 1; // Default inherent 'a' = 1

                // Check next modifiers
                let nextIdx = i + 1;
                let hasHalant = false;

                while (nextIdx < chars.length) {
                    let nextChar = chars[nextIdx];
                    if (nextChar === NUKTA) {
                        unitText += nextChar;
                        nextIdx++;
                    } else if (nextChar === VIRAMA) {
                        hasHalant = true;
                        unitText += nextChar;
                        nextIdx++;
                        break;
                    } else if (MATRAS_GURU.includes(nextChar)) {
                        unitText += nextChar;
                        weight = 2;
                        nextIdx++;
                    } else if (MATRAS_LAGHU.includes(nextChar)) {
                        unitText += nextChar;
                        weight = 1;
                        nextIdx++;
                    } else if (nextChar === CHANDRABINDU) {
                        unitText += nextChar;
                        nextIdx++;
                    } else {
                        break;
                    }
                }

                units.push({ text: unitText, weight: weight, hasHalant: hasHalant });
                i = nextIdx;
                continue;
            }

            i++;
        }

        // Apply Conjunct / Samyuktakshar stress rule (Halant makes preceding syllable Guru)
        for (let j = 0; j < units.length - 1; j++) {
            if (units[j + 1].hasHalant) {
                // If halant is followed by another consonant, previous syllable becomes Guru
                if (j >= 0 && units[j].weight === 1) {
                    units[j].weight = 2;
                }
            }
        }

        // Remove free halant units from syllable weight count
        return units.filter(u => !u.hasHalant);
    }

    // Chhand / Meter Guesser
    function identifyMeter(total, line) {
        if (total === 13) return 'विषम चरण (दोहा 1st/3rd चरण - 13 मात्राएँ)';
        if (total === 11) return 'सम चरण (दोहा 2nd/4th या सोरठा चरण - 11 मात्राएँ)';
        if (total === 16) return 'चौपाई चरण (16 मात्राएँ)';
        if (total === 24) {
            if (line.includes(',') || line.includes('।') || line.includes('॥')) {
                return 'दोहा दल (13 + 11 = 24 मात्राएँ)';
            }
            return 'रोला / दोहा दल (24 मात्राएँ)';
        }
        if (total === 28) return 'हरिगीतिका छंद (28 मात्राएँ)';
        if (total === 32) return 'घनाक्षरी / सवैया छंद आधार';
        return `${total} मात्रिक छंद विधान`;
    }

    // Perform live calculation from input
    function performLiveCalculation() {
        const input = document.getElementById('matra-input-text');
        const output = document.getElementById('calc-results-output');
        if (!input || !output) return;

        const text = input.value;
        if (!text || text.trim().length === 0) {
            output.innerHTML = `
                <div class="calc-placeholder-state">
                    <span class="placeholder-icon">✍️</span>
                    <p class="placeholder-text">ऊपर बॉक्स में कोई भी काव्य पंक्ति लिखें या उदाहरण बटन पर क्लिक करें।</p>
                </div>
            `;
            return;
        }

        const lines = text.split('\n');
        const results = lines.map(line => analyzeLineMatras(line)).filter(r => r !== null);

        if (results.length === 0) {
            output.innerHTML = '';
            return;
        }

        let html = '<div class="calc-lines-stack">';

        results.forEach((res, index) => {
            html += `
                <div class="calc-line-result-card">
                    <!-- Line Top Summary -->
                    <div class="line-res-header">
                        <span class="line-index-badge">पंक्ति ${index + 1}</span>
                        <div class="line-meter-badge">${escapeHtml(res.meterGuess)}</div>
                        <div class="line-total-pill">
                            <span class="count-num">${res.totalMatras}</span>
                            <span class="count-label">मात्राएँ</span>
                        </div>
                    </div>

                    <!-- Syllables Breakdown Visual -->
                    <div class="line-breakdown-flow">
                        ${res.words.map(w => `
                            <div class="word-metric-block">
                                <div class="word-text-row">${escapeHtml(w.word)}</div>
                                <div class="word-syllables-row">
                                    ${w.syllables.map(s => `
                                        <div class="syl-cell ${s.weight === 2 ? 'is-guru' : 'is-laghu'}" title="${escapeHtml(s.text)} = ${s.weight} मात्रा">
                                            <span class="syl-char">${escapeHtml(s.text)}</span>
                                            <span class="syl-val">${s.weight === 2 ? 'ऽ (2)' : '। (1)'}</span>
                                        </div>
                                    `).join('')}
                                </div>
                                <div class="word-total-tag">${w.total}</div>
                            </div>
                        `).join('')}
                    </div>

                    <!-- Pattern Row -->
                    <div class="line-pattern-strip">
                        <span class="pattern-label">मात्रा विन्यास:</span>
                        <code class="pattern-code">${escapeHtml(res.symbolString)}</code>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        output.innerHTML = html;
    }

    function applyPreset(text) {
        const input = document.getElementById('matra-input-text');
        if (input) {
            input.value = text;
            performLiveCalculation();
            input.focus();
        }
    }

    function clearCalculator() {
        const input = document.getElementById('matra-input-text');
        if (input) {
            input.value = '';
            performLiveCalculation();
            input.focus();
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Initialize Event Handlers
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('matra-input-text');
        if (input) {
            input.addEventListener('input', performLiveCalculation);
            // Run initial demo
            applyPreset('रहिमन पानी राखिये, बिन पानी सब सून।\nपानी गये न ऊबरे, मोती मानुष चून॥');
        }

        // Tab Switching Logic
        const tabsBar = document.getElementById('learn-tabs-bar');
        if (tabsBar) {
            tabsBar.addEventListener('click', (e) => {
                const btn = e.target.closest('.category-pill');
                if (!btn) return;

                const targetTab = btn.getAttribute('data-tab');
                if (!targetTab) return;

                document.querySelectorAll('.learn-tabs-bar .category-pill').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                document.querySelectorAll('.learn-tab-pane').forEach(p => p.classList.remove('active'));
                const activePane = document.getElementById('pane-' + targetTab);
                if (activePane) {
                    activePane.classList.add('active');
                }

                // Update URL parameter without full reload
                const url = new URL(window.location);
                url.searchParams.set('tab', targetTab);
                window.history.replaceState({}, '', url);
            });
        }

        // Auto scroll to content if specific tab is requested in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tab') && urlParams.get('tab') !== 'calculator') {
            const activePane = document.getElementById('pane-' + urlParams.get('tab'));
            if (activePane) {
                setTimeout(() => {
                    activePane.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 150);
            }
        }
    });
    </script>
</body>
</html>
