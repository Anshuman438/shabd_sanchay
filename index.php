<?php 
require_once 'config.php';
require_once 'includes/helpers.php';
$page_title = "शब्द संचय - शब्दों में एक बेहतर दुनिया";

// Fetch dynamic real-time counts from database
$total_poems = 0;
$total_articles = 0;
$total_stories = 0;
$total_views = 0;

try {
    $res_p = $conn->query("SELECT COUNT(*) as cnt FROM poems");
    if ($res_p) $total_poems = (int)$res_p->fetch_assoc()['cnt'];

    $res_a = $conn->query("SELECT COUNT(*) as cnt FROM articles");
    if ($res_a) $total_articles = (int)$res_a->fetch_assoc()['cnt'];

    $res_s = $conn->query("SELECT COUNT(*) as cnt FROM stories");
    if ($res_s) $total_stories = (int)$res_s->fetch_assoc()['cnt'];

    $res_v = $conn->query("SELECT (SELECT COALESCE(SUM(views),0) FROM poems) + (SELECT COALESCE(SUM(views),0) FROM articles) + (SELECT COALESCE(SUM(views),0) FROM stories) + (SELECT COALESCE(SUM(views),0) FROM plays) as total_views");
    if ($res_v) $total_views = (int)$res_v->fetch_assoc()['total_views'];
} catch (Exception $e) {
    // Fallbacks
    $total_poems = 50;
    $total_articles = 30;
    $total_stories = 15;
    $total_views = 915;
}
$total_works = $total_poems + $total_articles + $total_stories;
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
    <script src="js/matra_engine.js"></script>
</head>
<body class="home-doodle-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <section class="section-hero panoramic-hero-section">
            <div class="hero-bg-layer" id="hero-bg-layer"></div>
            <div class="container hero-container" id="hero-content-wrap">
                <!-- Hero Left: Typography & Actions -->
                <div class="hero-left">
                    <!-- Grand Headline with Biryani Typing Effect -->
                    <h1 class="hero-title biryani-typed-title" id="heroTypedTitle">
                        <span class="type-line-1" id="typeLine1"></span><br>
                        <span class="hero-title-highlight type-line-2" id="typeLine2"></span><span class="typing-cursor" id="typingCursor">|</span>
                    </h1>

                    <p class="hero-subtitle">
                        कविता, ग़ज़ल, छंद और मानवीय अनुभूतियों का एक समर्पित साहित्यिक संचय - जहाँ हर रचना एक जीवंत संवाद है और हर पंक्ति आत्मा का विस्तार।
                    </p>

                    <!-- CTAs -->
                    <div class="hero-actions">
                        <a href="#what-we-offer" class="btn-doodle-primary">
                            <span>साहित्य-संसार में प्रवेश करें</span>
                            <span class="btn-arrow">➔</span>
                        </a>
                        <a href="learn.php" class="btn-doodle-secondary">
                            <span>मात्रा व छंद प्रयोगशाला</span>
                        </a>
                    </div>

                    <!-- Signature Micro Trust / Highlights Strip -->
                    <div class="hero-trust-strip">
                        <span class="trust-pill">1000+ चयनित रचनाएँ</span>
                        <span class="trust-pill">प्रामाणिक छंद गणना</span>
                        <span class="trust-pill">100% विज्ञापन-मुक्त</span>
                    </div>
                </div>

                <!-- Hero Right: Interactive Poster Creator (Picture Box + Custom Text + Templates + Download) -->
                <div class="hero-right">
                    <div class="poster-studio-card" id="poster-studio-card">
                        <!-- Top Header / Bar -->
                        <div class="poster-studio-header">
                            <div class="studio-title-badge">
                                <span>काव्य-पोस्टर निर्माता</span>
                            </div>
                            <!-- Quick Template Selector Pills -->
                            <div class="template-selector-pills" id="template-selector">
                                <button type="button" class="tmpl-btn" data-tmpl="gold" title="स्वर्णिम प्रभात">स्वर्ण</button>
                                <button type="button" class="tmpl-btn" data-tmpl="parchment" title="प्राचीन पांडुलिपि">पांडुलिपि</button>
                                <button type="button" class="tmpl-btn active" data-tmpl="midnight" title="निशांत एकांत">निशांत</button>
                                <button type="button" class="tmpl-btn" data-tmpl="sunset" title="सांध्य रक्तिम">संध्या</button>
                                <button type="button" class="tmpl-btn" data-tmpl="minimal" title="धवल शांति">धवल</button>
                            </div>
                        </div>

                        <!-- The Visual Picture Box (Where text fits into photo template - 1:1 Square) -->
                        <div class="poster-photo-box tmpl-midnight" id="poster-photo-box">
                            <!-- Background Art / Graphic Layer -->
                            <div class="poster-bg-visual" id="poster-bg-visual"></div>
                            
                            <!-- Official Shabd Sanchay Logo Badge at Top Right Corner (Font: Biryani) -->
                            <div class="poster-brand-logo-badge" id="poster-brand-logo-badge">
                                <div class="brand-logo-title">शब्द संचय</div>
                                <div class="brand-logo-tagline">विचारों के नए प्रतिमान</div>
                            </div>

                            <!-- Main Fitted Poetic Text Overlay inside the Picture -->
                            <div class="poster-text-overlay">
                                <span class="poster-quote-mark">“</span>
                                <p class="poster-rendered-text" id="poster-rendered-text">
                                    लहरों से डर कर नौका पार नहीं होती, कोशिश करने वालों की कभी हार नहीं होती।
                                </p>
                                <div class="poster-rendered-author-row">
                                    <span class="poster-author-dash">—</span>
                                    <span class="poster-rendered-author" id="poster-rendered-author">हरिवंश राय बच्चन</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Interactive Input Area -->
                        <div class="poster-controls-area">
                            <div class="poster-input-group">
                                <div class="poster-input-header">
                                    <label for="poster-input-text" class="poster-input-label">अपनी पंक्तियाँ / शे'र लिखें:</label>
                                    <div class="poster-quick-actions">
                                        <button type="button" class="btn-micro-action" id="btn-shuffle-poster-couplet" title="प्रसिद्ध शे'र भरें">
                                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"/></svg>
                                            <span>शे'र बदलें</span>
                                        </button>
                                        <button type="button" class="btn-micro-action" id="btn-clear-poster-text" title="टेक्स्ट साफ़ करें">
                                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            <span>साफ़</span>
                                        </button>
                                    </div>
                                </div>
                                <textarea id="poster-input-text" class="poster-textarea" rows="2" placeholder="अपनी पंक्तियाँ या शे'र यहाँ लिखें...">लहरों से डर कर नौका पार नहीं होती, कोशिश करने वालों की कभी हार नहीं होती।</textarea>
                            </div>

                            <div class="poster-author-row-input">
                                <input type="text" id="poster-input-author" class="poster-author-input" placeholder="कवि / रचनाकार का नाम" value="हरिवंश राय बच्चन">
                                <button type="button" class="btn-download-poster" id="btn-download-poster" title="शब्द संचय लोगो के साथ HD इमेज डाउनलोड करें">
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>डाउनलोड</span>
                                </button>
                            </div>
                        </div>

                        <!-- Hidden Canvas for 4:4 / 1:1 Square High-Resolution Image Export -->
                        <canvas id="poster-export-canvas" width="1080" height="1080" style="display: none;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Floating High-Visibility Scroll Down Button -->
            <a href="#what-we-offer" class="hero-scroll-cue" aria-label="नीचे देखें">
                <span class="scroll-cue-pill">
                    <span class="scroll-cue-text">रचनाएँ देखें</span>
                    <span class="scroll-cue-icon">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </span>
            </a>
        </section>

        <!-- =========================================================
             SLIDING CARD SHEET (SECTIONS 2-5 SLIDE OVER HERO ON SCROLL)
        ========================================================= -->
        <div class="sliding-card-sheet" id="what-we-offer">
            <div class="card-sheet-pill-handle">
                <span class="pill-handle-bar"></span>
            </div>

            <!-- =========================================================
                 SECTION 2: WHAT WE OFFER (हम क्या साझा करते हैं?)
            ========================================================= -->
            <!-- =========================================================
                 SECTION 2: WHAT WE OFFER (हम क्या साझा करते हैं?) - MAGICAL
            ========================================================= -->
            <section class="section-offerings">
            <div class="container">
                <div class="section-header-row reveal-zoom-fade">
                    <div class="header-title-wrap">
                        <span class="section-eyebrow">साहित्यिक विधाएँ • EXPLORE GENRES</span>
                        <h2 class="section-main-heading">हम क्या साझा करते हैं?</h2>
                        <div class="heading-artistic-underline"></div>
                        <p class="section-sub-heading">कविता, संस्कृति, यात्रा और समाज - हर विधा में संवेदना और विचारों <br>का अनूठा संगम जो हमें आपसे, और आपसे हिंदी साहित्य से जोड़ता है।</p>
                    </div>
                    <a href="poetry.php" class="btn-explore-pill">
                        <span>सभी विधाएँ देखें</span>
                        <span class="link-arrow">→</span>
                    </a>
                </div>

                <!-- 4 Refined Artistic Hand-Drawn Doodle Category Cards -->
                <div class="offerings-grid">
                    <!-- Card 1: कविताएँ -->
                    <a href="poetry.php" class="offering-card magical-card doodle-card reveal-zoom-fade delay-1" data-tilt>
                        <div class="card-art-backdrop"></div>
                        
                        <!-- Hand-drawn Doodle Corner Stars & Accents -->
                        <div class="doodle-corner-accent top-left">✦</div>
                        <div class="doodle-corner-accent top-right">✧</div>

                        <!-- Watermark Background Doodle Motif -->
                        <div class="card-watermark-doodle">
                            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                <path d="M20 70 C 35 60, 48 62, 50 72 C 52 62, 65 60, 80 70 L 80 30 C 65 20, 52 22, 50 32 C 48 22, 35 20, 20 30 Z" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M68 18 C 76 12, 85 15, 80 28 C 74 38, 62 50, 54 58" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M50 32 L 50 72" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="30" cy="40" r="1.5" fill="currentColor"/>
                                <circle cx="70" cy="40" r="1.5" fill="currentColor"/>
                            </svg>
                        </div>

                        <div class="doodle-icon-circle-wrapper">
                            <div class="doodle-icon-circle">
                                <svg viewBox="0 0 100 100" class="doodle-svg" fill="none" stroke="currentColor">
                                    <path d="M20 70 C 35 60, 48 62, 50 72 C 52 62, 65 60, 80 70 L 80 30 C 65 20, 52 22, 50 32 C 48 22, 35 20, 20 30 Z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M50 32 L 50 72" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M28 38 C 36 34, 44 35, 46 40" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M28 48 C 36 44, 44 45, 46 50" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M72 38 C 64 34, 56 35, 54 40" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M68 18 C 76 12, 85 15, 80 28 C 74 38, 62 50, 54 58" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M68 25 L 75 32" stroke-width="1.5" stroke-linecap="round"/>
                                    <circle cx="22" cy="22" r="1.5" fill="currentColor"/>
                                    <circle cx="85" cy="45" r="1.5" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="doodle-sparkle-dot s1">✦</span>
                            <span class="doodle-sparkle-dot s2">●</span>
                        </div>

                        <div class="offering-details">
                            <h3 class="offering-title">कविताएँ</h3>
                            <div class="doodle-title-swash">
                                <svg viewBox="0 0 60 10" width="48" height="8" fill="none" stroke="currentColor">
                                    <path d="M2 5 Q 15 1, 30 5 T 58 5" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <p class="offering-desc">दिल से निकले, सीधे आपके दिल तक</p>
                        </div>

                        <div class="card-action-pill doodle-pill">
                            <span>रचनाएँ पढ़ें</span>
                            <span class="pill-arrow">➔</span>
                        </div>
                    </a>

                    <!-- Card 2: लेख व विचार -->
                    <a href="articles.php" class="offering-card magical-card doodle-card reveal-zoom-fade delay-2" data-tilt>
                        <div class="card-art-backdrop"></div>
                        
                        <!-- Hand-drawn Doodle Corner Stars & Accents -->
                        <div class="doodle-corner-accent top-left">✦</div>
                        <div class="doodle-corner-accent top-right">✧</div>

                        <!-- Watermark Background Doodle Motif -->
                        <div class="card-watermark-doodle">
                            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                <path d="M28 22 L 72 22 L 72 78 L 28 78 Z" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M36 34 L 64 34 M 36 44 L 64 44 M 36 54 L 54 54" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="50" cy="14" r="2.5" fill="currentColor"/>
                            </svg>
                        </div>

                        <div class="doodle-icon-circle-wrapper">
                            <div class="doodle-icon-circle">
                                <svg viewBox="0 0 100 100" class="doodle-svg" fill="none" stroke="currentColor">
                                    <path d="M26 20 L 68 20 C 72 20, 76 24, 76 28 L 76 76 C 76 80, 72 84, 68 84 L 26 84 C 22 84, 18 80, 18 76 L 18 28 C 18 24, 22 20, 26 20 Z" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M28 34 L 66 34" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M28 46 L 66 46" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M28 58 L 52 58" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M62 62 L 78 78 M 78 62 L 62 78" stroke-width="1.8" stroke-linecap="round" opacity="0.4"/>
                                    <circle cx="68" cy="62" r="1.5" fill="currentColor"/>
                                    <circle cx="28" cy="72" r="1.5" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="doodle-sparkle-dot s1">✧</span>
                            <span class="doodle-sparkle-dot s2">✦</span>
                        </div>

                        <div class="offering-details">
                            <h3 class="offering-title">लेख व विचार</h3>
                            <div class="doodle-title-swash">
                                <svg viewBox="0 0 60 10" width="48" height="8" fill="none" stroke="currentColor">
                                    <path d="M2 5 Q 15 9, 30 5 T 58 5" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <p class="offering-desc">संस्कृति, इतिहास, दर्शन और समाज पर गंभीर विमर्श</p>
                        </div>

                        <div class="card-action-pill doodle-pill">
                            <span>आलेख पढ़ें</span>
                            <span class="pill-arrow">➔</span>
                        </div>
                    </a>

                    <!-- Card 3: कहानियाँ -->
                    <a href="stories.php" class="offering-card magical-card doodle-card reveal-zoom-fade delay-3" data-tilt>
                        <div class="card-art-backdrop"></div>
                        
                        <!-- Hand-drawn Doodle Corner Stars & Accents -->
                        <div class="doodle-corner-accent top-left">✦</div>
                        <div class="doodle-corner-accent top-right">✧</div>

                        <!-- Watermark Background Doodle Motif -->
                        <div class="card-watermark-doodle">
                            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                <path d="M20 72 C 35 62, 48 64, 50 74 C 52 64, 65 62, 80 72 L 80 28 C 65 18, 52 20, 50 30 C 48 20, 35 18, 20 28 Z" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M50 30 L 50 74" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <div class="doodle-icon-circle-wrapper">
                            <div class="doodle-icon-circle">
                                <svg viewBox="0 0 100 100" class="doodle-svg" fill="none" stroke="currentColor">
                                    <path d="M20 72 C 35 62, 48 64, 50 74 C 52 64, 65 62, 80 72 L 80 28 C 65 18, 52 20, 50 30 C 48 20, 35 18, 20 28 Z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M50 30 L 50 74" stroke-width="2.2" stroke-linecap="round"/>
                                    <path d="M28 38 C 36 34, 44 35, 46 40" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M28 48 C 36 44, 44 45, 46 50" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M72 38 C 64 34, 56 35, 54 40" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M72 48 C 64 44, 56 45, 54 50" stroke-width="1.8" stroke-linecap="round"/>
                                    <circle cx="50" cy="18" r="4" stroke-width="1.8"/>
                                    <path d="M50 14 L 50 10" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <span class="doodle-sparkle-dot s1">✦</span>
                            <span class="doodle-sparkle-dot s2">●</span>
                        </div>

                        <div class="offering-details">
                            <h3 class="offering-title">कहानियाँ</h3>
                            <div class="doodle-title-swash">
                                <svg viewBox="0 0 60 10" width="48" height="8" fill="none" stroke="currentColor">
                                    <path d="M2 5 Q 15 1, 30 5 T 58 5" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <p class="offering-desc">कालजयी व समकालीन कथाकारों की अमर कहानियाँ</p>
                        </div>

                        <div class="card-action-pill doodle-pill">
                            <span>कहानियाँ पढ़ें</span>
                            <span class="pill-arrow">➔</span>
                        </div>
                    </a>

                    <!-- Card 4: मात्रा गणना व सीखें -->
                    <a href="learn.php" class="offering-card magical-card doodle-card reveal-zoom-fade delay-4" data-tilt>
                        <div class="card-art-backdrop"></div>
                        
                        <!-- Hand-drawn Doodle Corner Stars & Accents -->
                        <div class="doodle-corner-accent top-left">✦</div>
                        <div class="doodle-corner-accent top-right">✧</div>

                        <!-- Watermark Background Doodle Motif -->
                        <div class="card-watermark-doodle">
                            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                <rect x="22" y="20" width="56" height="60" rx="8" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M32 38 L 48 38 M 32 50 L 42 50 M 58 38 L 68 38 M 58 50 L 68 50" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="40" cy="66" r="3" fill="currentColor"/>
                                <circle cx="60" cy="66" r="3" fill="currentColor"/>
                            </svg>
                        </div>

                        <div class="doodle-icon-circle-wrapper">
                            <div class="doodle-icon-circle">
                                <svg viewBox="0 0 100 100" class="doodle-svg" fill="none" stroke="currentColor">
                                    <rect x="24" y="18" width="52" height="64" rx="8" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <rect x="32" y="26" width="36" height="16" rx="4" stroke-width="1.8" fill="rgba(200,90,23,0.1)"/>
                                    <text x="36" y="38" font-family="monospace" font-size="11" font-weight="bold" fill="currentColor">। ऽ । ऽ</text>
                                    <circle cx="36" cy="54" r="3" fill="currentColor"/>
                                    <circle cx="50" cy="54" r="3" fill="currentColor"/>
                                    <circle cx="64" cy="54" r="3" fill="currentColor"/>
                                    <circle cx="36" cy="68" r="3" fill="currentColor"/>
                                    <circle cx="50" cy="68" r="3" fill="currentColor"/>
                                    <circle cx="64" cy="68" r="3" fill="currentColor"/>
                                </svg>
                            </div>
                            <span class="doodle-sparkle-dot s1">✧</span>
                            <span class="doodle-sparkle-dot s2">✦</span>
                        </div>

                        <div class="offering-details">
                            <h3 class="offering-title">मात्रा गणना व सीखें</h3>
                            <div class="doodle-title-swash">
                                <svg viewBox="0 0 60 10" width="48" height="8" fill="none" stroke="currentColor">
                                    <path d="M2 5 Q 15 9, 30 5 T 58 5" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <p class="offering-desc">छंद शास्त्र, लाइव मात्रा गणक एवं काव्य-शिल्प</p>
                        </div>

                        <div class="card-action-pill doodle-pill">
                            <span>सीखना शुरू करें</span>
                            <span class="pill-arrow">➔</span>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- =========================================================
             CONNECTOR STRIP: DAILY LITERARY DUO (आज का शे'र एवं आज का शब्द)
        ========================================================= -->
        <section class="section-daily-duo reveal-zoom-fade" id="daily-duo-section">
            <div class="daily-duo-container">
                <div class="daily-duo-grid">
                    
                    <!-- CARD 1: आज का शे'र (Sher / Thought of the Day) -->
                    <div class="daily-card daily-sher-card">
                        <div class="daily-card-header">
                            <div class="daily-card-badge sher-badge">
                                <span>आज का शे'र</span>
                            </div>
                            <div class="daily-card-actions">
                                <button type="button" class="btn-card-action" id="btn-shuffle-sher" title="दूसरा शे'र देखें" aria-label="Next Sher">
                                    <span>नया शे'र</span>
                                </button>
                                <button type="button" class="btn-card-action" id="btn-copy-sher" title="शे'र कॉपी करें" aria-label="Copy Sher">
                                    <span>कॉपी</span>
                                </button>
                            </div>
                        </div>

                        <div class="daily-card-body sher-card-body">
                            <div class="sher-quote-icon">“</div>
                            <div class="sher-content">
                                <p class="sher-text" id="spotlight-sher-text">
                                    सिर्फ हंगामा खड़ा करना मेरा मकसद नहीं, मेरी कोशिश है कि ये सूरत बदलनी चाहिए। मेरे सीने में नहीं तो तेरे सीने में सही, हो कहीं भी आग, लेकिन आग जलनी चाहिए।
                                </p>
                                <div class="sher-footer">
                                    <span class="sher-dash">—</span>
                                    <span class="sher-author" id="spotlight-sher-author">दुष्यंत कुमार</span>
                                    <span class="sher-tag" id="spotlight-sher-tag">ग़ज़ल • साये में धूप</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: आज का शब्द (Word of the Day) -->
                    <div class="daily-card daily-word-card">
                        <div class="daily-card-header">
                            <div class="daily-card-badge word-badge">
                                <span>आज का शब्द</span>
                            </div>
                            <div class="daily-card-actions">
                                <button type="button" class="btn-card-action btn-speak" id="btn-speak-word" title="उच्चारण सुनें" aria-label="Pronounce word">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                                    </svg>
                                    <span>उच्चारण</span>
                                </button>
                                <button type="button" class="btn-card-action" id="btn-shuffle-word" title="दूसरा शब्द देखें" aria-label="Next Word">
                                    <span>नया शब्द</span>
                                </button>
                                <button type="button" class="btn-card-action" id="btn-copy-word" title="शब्द कॉपी करें" aria-label="Copy Word">
                                    <span>कॉपी</span>
                                </button>
                            </div>
                        </div>

                        <div class="daily-card-body word-card-body">
                            <div class="word-headline">
                                <h3 class="word-main-title" id="spotlight-word-title">जिजीविषा</h3>
                                <span class="word-grammar-badge" id="spotlight-word-grammar">[संज्ञा, स्त्रीलिंग • तत्सम]</span>
                            </div>
                            <div class="word-info-group">
                                <div class="word-info-row">
                                    <span class="info-label">अर्थ:</span>
                                    <p class="info-desc" id="spotlight-word-meaning">जीने की अदम्य इच्छा, जीवन के प्रति अगाध प्रेम व घोर संकटों में भी अडिग रहने का अटूट संकल्प।</p>
                                </div>
                                <div class="word-info-row usage-row">
                                    <span class="info-label">प्रयोग:</span>
                                    <p class="info-desc usage-text" id="spotlight-word-usage">"हर पतझड़ के बाद जो शाखों में नई कोंपलें फूटती हैं, वह प्रकृति की अदम्य जिजीविषा का ही प्रमाण है।"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Toast Notification for Copy -->
                <div class="spotlight-toast" id="spotlight-toast" aria-live="polite">
                    <span>✓ क्लिपबोर्ड में कॉपी हो गया!</span>
                </div>
            </div>
        </section>

        <!-- =========================================================
             SECTION 3: SELECTED POEMS / WORKS (चयनित रचनाएँ)
        ========================================================= -->
        <section class="section-selected-works" id="selected-works">
            <div class="container">
                <div class="section-header-row reveal-zoom-fade">
                    <div class="header-title-wrap">
                        <span class="section-eyebrow">FEATURED SELECTIONS</span>
                        <h2 class="section-main-heading">चयनित रचनाएँ <span class="heading-dash">-</span></h2>
                        <p class="section-sub-heading">कुछ शब्द जो याद रह जाते हैं और दिल को छू लेते हैं...</p>
                    </div>
                    <a href="poetry.php" class="section-link-more">और रचनाएँ देखें <span class="link-arrow">→</span></a>
                </div>

                <!-- Dynamic Grid for Selected Works -->
                <div class="selected-works-grid" id="selected-works-container">
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                    <div class="skeleton-card"></div>
                </div>
            </div>
        </section>

        <!-- =========================================================
             SECTION 4: LITERARY CRAFT & FORMULAS (काव्य-शिल्प एवं छंद सूत्र)
        ========================================================= -->
        <section class="section-loved-topics section-literary-craft" id="literary-craft">
            <div class="container">
                <div class="section-header-row reveal-zoom-fade">
                    <div class="header-title-wrap">
                        <span class="section-eyebrow">LITERARY CRAFT & FORMULAS • साहित्य-शिल्प व व्याकरण</span>
                        <h2 class="section-main-heading">काव्य, छंद एवं ग़ज़ल सूत्र <span class="heading-dash">-</span></h2>
                        <p class="section-sub-heading">मात्रा गणना, बहर, रदीफ़-क़ाफ़िया और छंद-विधान के व्यावहारिक सूत्र सीखें</p>
                    </div>
                    <a href="learn.php" class="btn-explore-pill">
                        <span>सम्पूर्ण काव्य-शिल्प सीखें</span>
                        <span class="link-arrow">→</span>
                    </a>
                </div>

                <!-- 4 Educational Formula Cards -->
                <div class="topics-grid formula-cards-grid">
                    <!-- Formula 1: Doha Metre -->
                    <a href="learn.php?tab=doha" class="topic-card formula-card reveal-zoom-fade delay-1">
                        <div class="topic-image-box formula-image-box">
                            <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?w=600&auto=format&fit=crop&q=80" alt="दोहा छंद सूत्र" loading="lazy">
                            <span class="formula-pill-badge">13 + 11 = 24 मात्राएँ</span>
                        </div>
                        <div class="topic-info formula-info">
                            <div class="topic-text-col">
                                <span class="craft-tag">मात्रिक छंद विधान</span>
                                <h3 class="topic-title">दोहा छंद सूत्र</h3>
                                <p class="topic-subtitle">विषम चरण 13, सम 11 मात्रा | अंत में गुरु-लघु (ऽ ।)</p>
                                <div class="formula-micro-sample">
                                    <em>"बड़ा हुआ तो क्या हुआ, जैसे पेड़ खजूर..."</em>
                                </div>
                            </div>
                            <span class="topic-arrow-btn" title="विस्तार से पढ़ें">→</span>
                        </div>
                    </a>

                    <!-- Formula 2: Ghazal Grammar -->
                    <a href="learn.php?tab=ghazal" class="topic-card formula-card reveal-zoom-fade delay-2">
                        <div class="topic-image-box formula-image-box">
                            <img src="https://images.unsplash.com/photo-1516962215378-7fa2e137ae93?w=600&auto=format&fit=crop&q=80" alt="ग़ज़ल का व्याकरण" loading="lazy">
                            <span class="formula-pill-badge">मतला • रदीफ़ • क़ाफ़िया</span>
                        </div>
                        <div class="topic-info formula-info">
                            <div class="topic-text-col">
                                <span class="craft-tag">ग़ज़ल व्याकरण</span>
                                <h3 class="topic-title">ग़ज़ल का व्याकरण</h3>
                                <p class="topic-subtitle">बहर (वज़न) + हम-क़ाफ़िया व मुस्तकिल रदीफ़</p>
                                <div class="formula-micro-sample">
                                    <em>"दिल-ए-नादाँ तुझे हुआ क्या है / दवा क्या है..."</em>
                                </div>
                            </div>
                            <span class="topic-arrow-btn" title="विस्तार से पढ़ें">→</span>
                        </div>
                    </a>

                    <!-- Formula 3: Chaupai Metre -->
                    <a href="learn.php?tab=chaupai" class="topic-card formula-card reveal-zoom-fade delay-3">
                        <div class="topic-image-box formula-image-box">
                            <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80" alt="चौपाई छंद विधान" loading="lazy">
                            <span class="formula-pill-badge">16 मात्राएँ प्रति चरण</span>
                        </div>
                        <div class="topic-info formula-info">
                            <div class="topic-text-col">
                                <span class="craft-tag">सम मात्रिक छंद</span>
                                <h3 class="topic-title">चौपाई छंद विधान</h3>
                                <p class="topic-subtitle">प्रत्येक चरण 16 मात्रा | अंत में 'दो गुरु' (ऽऽ) श्रेष्ठ</p>
                                <div class="formula-micro-sample">
                                    <em>"जय हनुमान ज्ञान गुन सागर / तिहुँ लोक उजागर..."</em>
                                </div>
                            </div>
                            <span class="topic-arrow-btn" title="विस्तार से पढ़ें">→</span>
                        </div>
                    </a>

                    <!-- Formula 4: Matra Counting -->
                    <a href="learn.php?tab=matra_rules" class="topic-card formula-card reveal-zoom-fade delay-4">
                        <div class="topic-image-box formula-image-box">
                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&auto=format&fit=crop&q=80" alt="मात्रा गणना विज्ञान" loading="lazy">
                            <span class="formula-pill-badge">लघु (। = 1) vs गुरु (ऽ = 2)</span>
                        </div>
                        <div class="topic-info formula-info">
                            <div class="topic-text-col">
                                <span class="craft-tag">मात्रा विज्ञान</span>
                                <h3 class="topic-title">मात्रा गणना विज्ञान</h3>
                                <p class="topic-subtitle">ह्रस्व स्वर (1) | दीर्घ स्वर व संयुक्ताक्षर पूर्व (2)</p>
                                <div class="formula-micro-sample">
                                    <em>"स (1) + म (1) + य (1) = 3 | का (2) + ल (1) = 3"</em>
                                </div>
                            </div>
                            <span class="topic-arrow-btn" title="विस्तार से पढ़ें">→</span>
                        </div>
                    </a>
                </div>

                <!-- =========================================================
                     PRO MATRA & CHHAND CALCULATOR WIDGET (परम मात्रा गणक)
                ========================================================= -->
                <div class="pro-matra-calculator-widget reveal-zoom-fade" id="pro-matra-calculator">
                    <div class="calc-widget-header">
                        <div class="calc-widget-title">
                            <div>
                                <h3>परम मात्रा गणक एवं छंद विश्लेषक</h3>
                                <p style="margin: 0; font-size: 0.8rem; opacity: 0.8;">काव्य-शास्त्र व काव्यालय नियमावली पर आधारित स्वचालित मात्रा व बहर विश्लेषक</p>
                            </div>
                            <span class="calc-badge-pro">LIVE PRO</span>
                        </div>
                        <div class="calc-mode-toggles">
                            <button type="button" class="btn-mode-toggle active" id="modeStandardBtn" data-mode="standard">मानक खड़ी बोली</button>
                            <button type="button" class="btn-mode-toggle" id="modeFlexibleBtn" data-mode="flexible">अवधी / उर्दू बहर</button>
                        </div>
                    </div>

                    <!-- Quick Preset Verses & Clear Action -->
                    <div class="calc-presets-row">
                        <div class="calc-presets-left">
                            <span class="preset-label">प्रामाणिक उदाहरण:</span>
                            <button type="button" class="btn-calc-preset" data-preset="chaupai">तुलसी चौपाई</button>
                            <button type="button" class="btn-calc-preset" data-preset="doha">कबीर दोहा</button>
                            <button type="button" class="btn-calc-preset" data-preset="veer">झाँसी की रानी (30 मात्रा)</button>
                            <button type="button" class="btn-calc-preset" data-preset="ghazal">ग़ालिब मतला</button>
                            <button type="button" class="btn-calc-preset" data-preset="sanyukt">संयुक्ताक्षर परीक्षा</button>
                        </div>
                        <button type="button" class="btn-calc-clear" id="proMatraClearBtn" title="टाइपिंग क्षेत्र साफ़ करें">
                            <span>साफ़ करें</span>
                        </button>
                    </div>

                    <!-- Input Area -->
                    <div class="calc-input-wrapper">
                        <textarea id="proMatraTextarea" class="calc-textarea" rows="2" placeholder="यहाँ अपनी कविता, दोहा, चौपाई, शेर या कोई भी हिंदी पंक्ति लिखें...">क्लेश प्यार कष्ट कल्प आत्मा दीर्घ सुर्ख़ सख्त</textarea>
                        <button type="button" class="btn-input-clear-float" id="proMatraFloatClearBtn" title="साफ़ करें" aria-label="Clear text">&times;</button>
                    </div>

                    <!-- Real-time Analysis Output Container -->
                    <div id="proCalcAnalysisContainer" class="calc-analysis-container">
                        <!-- Detected Meter Certificate Banner -->
                        <div id="proMeterCertCard" class="meter-certificate-card">
                            <div class="meter-cert-left">
                                <div>
                                    <h4 id="proMeterCertName" class="meter-cert-name">चौपाई छंद</h4>
                                    <p id="proMeterCertDesc" class="meter-cert-desc">16 मात्राओं का प्रामाणिक संतुलन</p>
                                </div>
                            </div>
                            <span id="proMeterCertBadge" class="meter-cert-badge">16 मात्राएँ</span>
                        </div>

                        <!-- Lines Breakdown -->
                        <div id="proLinesBreakdownList"></div>
                    </div>

                    <!-- Live Pro Widget Footer Bar linking to Learn Page -->
                    <div class="calc-widget-footer-bar">
                        <div class="calc-footer-info">
                            <span class="calc-footer-sparkle">✨</span>
                            <span>विस्तृत वर्ण-वार विभाजन, यति-गति नियम व संपूर्ण काव्य-शास्त्र के लिए:</span>
                        </div>
                        <a href="learn.php?tab=calculator" class="btn-calc-pro-link">
                            <span>संपूर्ण मात्रा व छंद प्रयोगशाला खोलें</span>
                            <span class="link-arrow">➔</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- =========================================================
             SECTION 5: STAY CONNECTED NEWSLETTER BANNER
        ========================================================= -->
        <section class="section-newsletter">
            <div class="container">
                <div class="newsletter-card reveal-zoom-fade">
                    <!-- Flying Paper Airplane Doodle -->
                    <div class="paper-plane-doodle">
                        <svg viewBox="0 0 160 80" width="140" height="70" fill="none" stroke="currentColor">
                            <path d="M10 65 Q 40 60, 70 35 T 120 20" stroke-width="1.8" stroke-dasharray="4 4" opacity="0.6"/>
                            <polygon points="120,20 155,10 142,32" stroke-width="2" fill="currentColor" fill-opacity="0.2"/>
                            <polygon points="120,20 142,32 135,42" stroke-width="1.8" fill="currentColor" fill-opacity="0.4"/>
                        </svg>
                    </div>

                    <div class="newsletter-inner-content">
                        <span class="newsletter-eyebrow">STAY CONNECTED</span>
                        <h2 class="newsletter-heading">शब्दों की दुनिया से जुड़े रहें</h2>
                        <p class="newsletter-sub">नई रचनाएँ, विशेष लेख और सांस्कृतिक सामग्री सीधे अपने इनबॉक्स में प्राप्त करें।</p>

                        <form id="home-newsletter-form" class="newsletter-form-inline">
                            <div class="input-with-icon">
                                <input type="email" id="home-newsletter-email" required placeholder="अपना ईमेल पता दर्ज करें..." aria-label="Email Address">
                            </div>
                            <button type="submit" class="btn-subscribe">
                                <span>सब्सक्राइब करें</span>
                            </button>
                        </form>
                        <div id="home-newsletter-msg" class="newsletter-feedback-msg"></div>
                    </div>

                    <!-- Side Botanical & Book Art Doodle -->
                    <div class="newsletter-side-art">
                        <p class="side-art-quote">
                            पढ़िए,<br>
                            सोचिए,<br>
                            महसूस कीजिए<br>
                            और जुड़े रहिए...
                    </div>
                </div>
            </div>
        </section>
        </div><!-- End .sliding-card-sheet -->
    </main>



    <?php include 'footer.php'; ?>

    <!-- Client Script for Real-time Selected Works & Bookmarking -->
    <script>
    function extractOneLinePreview(text) {
        if (!text) return '';
        const clean = text.replace(/<br\s*[\/]?>/gi, '\n').replace(/<[^>]*>/g, '');
        const lines = clean.split(/[\r\n]+/).map(l => l.trim()).filter(l => l.length > 0);
        return lines.length > 0 ? lines[0] : '';
    }

    function getBookmarks() {
        try {
            return JSON.parse(localStorage.getItem('shabd_bookmarks') || '[]');
        } catch(e) {
            return [];
        }
    }

    function toggleBookmark(id, type, btnElement) {
        let bookmarks = getBookmarks();
        const key = `${type}_${id}`;
        const index = bookmarks.indexOf(key);
        
        if (index > -1) {
            bookmarks.splice(index, 1);
            btnElement.classList.remove('bookmarked');
            btnElement.setAttribute('title', 'सहेजें');
        } else {
            bookmarks.push(key);
            btnElement.classList.add('bookmarked');
            btnElement.setAttribute('title', 'सहेजा गया');
        }
        localStorage.setItem('shabd_bookmarks', JSON.stringify(bookmarks));
    }

    async function fetchSelectedWorks() {
        const container = document.getElementById('selected-works-container');
        const bookmarks = getBookmarks();

        try {
            const [poemsRes, articlesRes] = await Promise.all([
                fetch('api/get_featured_poems.php'),
                fetch('api/get_featured_articles.php')
            ]);
            
            const poemsData = await poemsRes.json();
            const articlesData = await articlesRes.json();

            const poems = (Array.isArray(poemsData) ? poemsData : []).map(p => ({
                id: p.id,
                title: p.title,
                author: p.author_name || 'अंशुमान सिंह',
                type: 'कविता',
                link: `poem.php?id=${p.id}`,
                image_url: p.image_url && !p.image_url.startsWith('images/') ? p.image_url : `https://picsum.photos/seed/${encodeURIComponent(p.title)}/600/400`,
                created_at: p.created_at,
                content: extractOneLinePreview(p.poem_content)
            }));

            const articles = (Array.isArray(articlesData) ? articlesData : []).map(a => ({
                id: a.id,
                title: a.title,
                author: a.author_name || 'अंशुमान सिंह',
                type: a.category || 'लेख',
                link: `article.php?id=${a.id}`,
                image_url: a.image_url && !a.image_url.startsWith('images/') ? a.image_url : `https://picsum.photos/seed/${encodeURIComponent(a.title)}/600/400`,
                created_at: a.created_at,
                content: extractOneLinePreview(a.excerpt || a.content)
            }));

            let allItems = [...poems, ...articles]
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 4);

            // Fallback content if empty
            if (allItems.length === 0) {
                allItems = [
                    {
                        id: 1,
                        title: 'वो शाम फिर नहीं आई',
                        author: 'अंशुमान सिंह',
                        type: 'कविता',
                        link: 'poetry.php',
                        image_url: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop&q=80',
                        created_at: '2026-03-12',
                        content: 'कुछ यादें हमेशा अधूरी रहती हैं, शायद इसलिए वो याद रहती हैं...'
                    },
                    {
                        id: 2,
                        title: 'वाराणसी : एक अनुभव',
                        author: 'अंशुमान सिंह',
                        type: 'यात्रा',
                        link: 'stories.php',
                        image_url: 'https://images.unsplash.com/photo-1561361513-2d000a50f0dc?w=600&auto=format&fit=crop&q=80',
                        created_at: '2026-03-05',
                        content: 'यह सिर्फ एक शहर नहीं, एक अहसास है जो रूह में उतर जाता है...'
                    },
                    {
                        id: 3,
                        title: 'आज के समय में हिंदी',
                        author: 'अंशुमान सिंह',
                        type: 'लेख',
                        link: 'articles.php',
                        image_url: 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?w=600&auto=format&fit=crop&q=80',
                        created_at: '2026-02-28',
                        content: 'भाषा सिर्फ संवाद नहीं, एक संस्कृति है जो हमें जोड़े रखती है...'
                    },
                    {
                        id: 4,
                        title: 'बिखरे हुए ख्वाब',
                        author: 'अंशुमान सिंह',
                        type: 'कविता',
                        link: 'poetry.php',
                        image_url: 'https://images.unsplash.com/photo-1518495973542-4542c06a5843?w=600&auto=format&fit=crop&q=80',
                        created_at: '2026-02-15',
                        content: 'हर टूटन भी एक कहानी कहती है, बस सुनने वाला चाहिए...'
                    }
                ];
            }

            container.innerHTML = allItems.map((item, index) => {
                const dateObj = new Date(item.created_at);
                const dateStr = !isNaN(dateObj) ? `${dateObj.getDate()} ${['जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितंबर','अक्टूबर','नवंबर','दिसंबर'][dateObj.getMonth()]}` : '23 सितंबर';
                const isBookmarked = bookmarks.includes(`${item.type}_${item.id}`);

                return `
                <article class="selected-work-card reveal-zoom-fade delay-${(index % 4) + 1} is-revealed">
                    <div class="card-cover-wrapper">
                        <img src="${item.image_url}" alt="${item.title}" class="card-cover-img" loading="lazy" onerror="this.onerror=null; this.src='https://picsum.photos/seed/${encodeURIComponent(item.title)}/600/400';">
                        <span class="card-floating-badge">${item.type}</span>
                    </div>
                    <div class="card-body-content">
                        <div class="card-author-row">
                            <div class="author-avatar-small">
                                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=60&auto=format&fit=crop&q=80" alt="${item.author}">
                            </div>
                            <div class="author-info-wrap">
                                <span class="author-name-text">${item.author}</span>
                                <span class="publish-date-text">${dateStr}</span>
                            </div>
                        </div>

                        <h3 class="card-work-title"><a href="${item.link}">${item.title}</a></h3>

                        <p class="card-work-excerpt">“${item.content || 'विचारों और शब्दों की गहराई में उतरती एक सुंदर साहित्यिक रचना...'}”</p>

                        <div class="card-footer-action-row">
                            <button class="btn-bookmark-action ${isBookmarked ? 'bookmarked' : ''}" onclick="toggleBookmark(${item.id}, '${item.type}', this)" title="${isBookmarked ? 'सहेजा गया' : 'सहेजें'}">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="${isBookmarked ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span>${isBookmarked ? 'सहेजा गया' : 'सहेजें'}</span>
                            </button>

                            <a href="${item.link}" class="btn-card-read">
                                <span>पढ़ें</span>
                                <span class="btn-arrow">→</span>
                            </a>
                        </div>
                    </div>
                </article>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching selected works:', error);
            container.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">रचनाएँ लोड करने में समस्या आई।</p>';
        }
    }

    // Scroll Animation, Parallax & Page-2 Stoppage Controller
    function initScrollAnimations() {
        const heroBg = document.getElementById('hero-bg-layer');
        const heroContent = document.getElementById('hero-content-wrap');
        const heroCue = document.querySelector('.hero-scroll-cue');
        const slidingSheet = document.querySelector('.sliding-card-sheet');

        function getHeaderOffset() {
            const header = document.querySelector('.site-header');
            return header ? header.offsetHeight : 68;
        }

        function getDockY() {
            if (!slidingSheet) return window.innerHeight - 68;
            return Math.max(0, slidingSheet.offsetTop - getHeaderOffset());
        }

        // Parallax and zoom/fade visual transforms
        function onScroll() {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const vh = window.innerHeight;
            const heroSection = document.querySelector('.section-hero.panoramic-hero-section');

            if (scrollY <= vh * 1.15) {
                if (heroSection) {
                    heroSection.style.visibility = 'visible';
                    heroSection.style.pointerEvents = scrollY > vh * 0.7 ? 'none' : 'auto';
                }

                const progress = Math.min(1, Math.max(0, scrollY / (vh * 0.85)));

                // Background artwork smooth zoom-in & subtle fade
                if (heroBg) {
                    const scale = 1 + progress * 0.15;
                    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    const baseOpacity = isDark ? 0.12 : 0.22;
                    const opacity = Math.max(0, baseOpacity * (1 - progress * 1.2));
                    heroBg.style.transform = `scale(${scale})`;
                    heroBg.style.opacity = opacity;
                }

                // Hero typography translate upward and fade
                if (heroContent) {
                    const translateY = -progress * 60;
                    const opacity = Math.max(0, 1 - progress * 1.4);
                    heroContent.style.transform = `translateY(${translateY}px)`;
                    heroContent.style.opacity = opacity;
                }

                // Floating scroll cue fade out
                if (heroCue) {
                    const cueOpacity = Math.max(0, 1 - progress * 2.5);
                    heroCue.style.opacity = cueOpacity;
                    heroCue.style.pointerEvents = progress > 0.2 ? 'none' : 'auto';
                }

                // Sliding card sheet bloom/scale docking
                if (slidingSheet) {
                    const sheetScale = 0.98 + progress * 0.02;
                    slidingSheet.style.transform = `scale(${sheetScale})`;
                    slidingSheet.style.transformOrigin = 'center top';
                }
            } else {
                // Completely hide and disable the sticky hero when scrolled past
                // to eliminate background ghosting / glitching on page refresh or deep scrolling
                if (heroSection) {
                    heroSection.style.visibility = 'hidden';
                    heroSection.style.pointerEvents = 'none';
                }
                if (heroBg) {
                    heroBg.style.opacity = '0';
                }
                if (heroContent) {
                    heroContent.style.opacity = '0';
                }
                if (heroCue) {
                    heroCue.style.opacity = '0';
                    heroCue.style.pointerEvents = 'none';
                }
                if (slidingSheet) {
                    slidingSheet.style.transform = 'none';
                }
            }
        }

        window.addEventListener('scroll', () => {
            requestAnimationFrame(onScroll);
        }, { passive: true });
        onScroll(); // initial call on page load/refresh

        // IntersectionObserver for elements reveal on scroll
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        document.querySelectorAll('.reveal-zoom-fade').forEach(el => {
            revealObserver.observe(el);
        });

        // -----------------------------------------------------------------
        // Intelligent Page 1 -> Page 2 Stoppage & Magnetic Snap Controller
        // -----------------------------------------------------------------
        let isSnapping = false;
        let snapTimeout = null;

        function scrollToPosition(targetY, duration = 650) {
            isSnapping = true;
            window.scrollTo({
                top: targetY,
                behavior: 'smooth'
            });

            clearTimeout(snapTimeout);
            snapTimeout = setTimeout(() => {
                isSnapping = false;
            }, duration + 100);
        }

        // Wheel interceptor for distinct Page 1 to Page 2 stop
        window.addEventListener('wheel', (e) => {
            if (isSnapping) {
                // If currently snapping to second page or back, absorb extra wheel momentum to ensure clean stoppage
                e.preventDefault();
                return;
            }

            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const dockY = getDockY();

            // Case 1: On Hero (Page 1) and user scrolls DOWN -> stop cleanly at Page 2
            if (scrollY < 40 && e.deltaY > 15) {
                e.preventDefault();
                scrollToPosition(dockY, 650);
            }
            // Case 2: In transition between Page 1 and Page 2 and user scrolls DOWN -> snap to Page 2
            else if (scrollY > 40 && scrollY < dockY - 30 && e.deltaY > 10) {
                e.preventDefault();
                scrollToPosition(dockY, 500);
            }
            // Case 3: Exactly docked at Page 2 and user scrolls UP -> snap back to Page 1 (Hero)
            else if (scrollY <= dockY + 25 && scrollY >= dockY - 15 && e.deltaY < -15) {
                e.preventDefault();
                scrollToPosition(0, 650);
            }
        }, { passive: false });

        // Touch swipe support for mobile/tablets
        let touchStartY = 0;
        window.addEventListener('touchstart', (e) => {
            if (e.touches && e.touches.length > 0) {
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (isSnapping) {
                e.preventDefault();
                return;
            }
            if (!e.touches || e.touches.length === 0) return;

            const touchCurrentY = e.touches[0].clientY;
            const deltaY = touchStartY - touchCurrentY;
            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const dockY = getDockY();

            // Swipe UP (scroll down) from Hero
            if (scrollY < 30 && deltaY > 35) {
                e.preventDefault();
                scrollToPosition(dockY, 600);
            }
            // Swipe DOWN (scroll up) from docked Page 2
            else if (scrollY <= dockY + 20 && scrollY >= dockY - 10 && deltaY < -35) {
                e.preventDefault();
                scrollToPosition(0, 600);
            }
        }, { passive: false });

        // Smooth handler for CTA and Scroll Cue links
        document.querySelectorAll('a[href="#what-we-offer"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                scrollToPosition(getDockY(), 700);
            });
        });
    }

    // 3D Perspective Tilt & Cursor Glare Controller for Magical Cards
    function initMagicalTiltCards() {
        const cards = document.querySelectorAll('.magical-card');
        
        cards.forEach(card => {
            let bounds = null;

            function updateBounds() {
                bounds = card.getBoundingClientRect();
            }

            card.addEventListener('mouseenter', updateBounds);

            card.addEventListener('mousemove', (e) => {
                if (!bounds) updateBounds();
                const mouseX = e.clientX - bounds.left;
                const mouseY = e.clientY - bounds.top;
                
                const centerX = bounds.width / 2;
                const centerY = bounds.height / 2;

                const rotateX = ((mouseY - centerY) / centerY) * -7;
                const rotateY = ((mouseX - centerX) / centerX) * 7;

                card.style.setProperty('--mouse-x', `${mouseX}px`);
                card.style.setProperty('--mouse-y', `${mouseY}px`);
                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-8px) scale(1.025)`;
            });

            card.addEventListener('mouseleave', () => {
                bounds = null;
                card.style.transform = '';
            });
        });
    }

    // Initialize the Pro Matra & Chhand Calculator Widget
    function initProMatraCalculator() {
        const textarea = document.getElementById('proMatraTextarea');
        if (!textarea) return;

        // Initialize Engine
        const engine = new HindiMatraEngine({ mode: 'standard' });

        const standardBtn = document.getElementById('modeStandardBtn');
        const flexibleBtn = document.getElementById('modeFlexibleBtn');
        const presetBtns = document.querySelectorAll('.btn-calc-preset');
        const certCard = document.getElementById('proMeterCertCard');
        const certName = document.getElementById('proMeterCertName');
        const certDesc = document.getElementById('proMeterCertDesc');
        const certBadge = document.getElementById('proMeterCertBadge');
        const linesList = document.getElementById('proLinesBreakdownList');

        const clearBtn = document.getElementById('proMatraClearBtn');
        const floatClearBtn = document.getElementById('proMatraFloatClearBtn');

        const PRESET_VERSES = {
            chaupai: "जय हनुमान ज्ञान गुन सागर।\nजय कपीस तिहुँ लोक उजागर॥",
            doha: "बड़ा हुआ तो क्या हुआ जैसे पेड़ खजूर।\nपंथी को छाया नहीं फल लागैं अति दूर॥",
            veer: "बुंदेले हरबोलों के मुँह हमने सुनी कहानी थी।\nखूब लड़ी मर्दानी वह तो झाँसी वाली रानी थी॥",
            ghazal: "दिल-ए-नादाँ तुझे हुआ क्या है।\nआख़िर इस दर्द की दवा क्या है॥",
            sanyukt: "क्लेश प्यार कष्ट कल्प आत्मा दीर्घ सुर्ख़ सख्त"
        };

        function renderAnalysis() {
            const text = textarea.value;
            if (floatClearBtn) {
                floatClearBtn.style.display = text.length > 0 ? 'flex' : 'none';
            }

            if (!text.trim()) {
                if (certCard) certCard.style.display = 'none';
                if (linesList) {
                    linesList.innerHTML = '<div style="text-align: center; color: #8c7b6d; padding: 2.2rem 1rem; font-style: italic; background: #faf7f4; border-radius: 12px; border: 1px dashed rgba(0,0,0,0.1);">मात्रा विश्लेषण देखने के लिए यहाँ अपनी पंक्तियाँ लिखें अथवा ऊपर दिए गए प्रामाणिक उदाहरणों में से कोई एक चुनें...</div>';
                }
                return;
            }

            const analysis = engine.analyzeText(text);

            // 1. Meter Certificate Banner
            if (certCard && certName && certDesc && certBadge) {
                certCard.style.display = 'flex';
                if (analysis.meterDetection) {
                    certName.textContent = analysis.meterDetection.name;
                    certDesc.textContent = analysis.meterDetection.desc || analysis.meterDetection.description;
                    certBadge.textContent = analysis.meterDetection.badge;
                } else if (analysis.lines.length > 0) {
                    const lineCounts = analysis.lines.map(l => l.lineTotal).join(' + ');
                    certName.textContent = 'स्वच्छंद मात्रिक पंक्ति (Custom Verse)';
                    certDesc.textContent = `प्रत्येक पंक्ति का मात्रा भार: [ ${lineCounts} ] = कुल ${analysis.grandTotal} मात्राएँ`;
                    certBadge.textContent = `${analysis.grandTotal} कुल मात्राएँ`;
                }
            }

            // 2. Lines & Syllables Breakdown
            if (linesList) {
                if (analysis.lines.length === 0) {
                    linesList.innerHTML = '';
                    return;
                }

                let html = '';
                analysis.lines.forEach((line) => {
                    let wordsHtml = '';
                    line.words.forEach((w) => {
                        let sylChipsHtml = '';
                        w.syllables.forEach((s) => {
                            const chipClass = s.weight === 2 ? 'chip-guru' : 'chip-laghu';
                            const conjunctClass = s.isConjunctAugmented ? ' chip-conjunct-heavy' : '';
                            const explanation = s.ruleExplanation || (s.weight === 2 ? 'गुरु (2 मात्रा)' : 'लघु (1 मात्रा)');
                            
                            const label = s.weight >= 2 ? 'गुरु' : 'लघु';
                            sylChipsHtml += `
                                <div class="akshara-chip ${chipClass}${conjunctClass}" title="${s.text}: ${explanation}">
                                    <span class="chip-char">${s.text}</span>
                                    <span class="chip-symbol">${label} (${s.weight})</span>
                                </div>
                            `;
                        });

                        wordsHtml += `
                            <div class="pro-word-group">
                                <span class="pro-word-text">${w.word}</span>
                                <div class="pro-word-syllables-row">
                                    ${sylChipsHtml}
                                </div>
                            </div>
                        `;
                    });

                    html += `
                        <div class="pro-line-block">
                            <div class="pro-line-header">
                                <div class="pro-line-index-title">
                                    <span class="pro-line-index-badge">पंक्ति ${line.lineIndex}</span>
                                    <span>"${line.originalText}"</span>
                                </div>
                                <span class="pro-line-total-badge">${line.lineTotal} मात्राएँ</span>
                            </div>
                            <div class="pro-line-words-flow">
                                ${wordsHtml}
                            </div>
                            <div class="pro-line-rhythm-strip">
                                <strong>लय ढाँचा (Rhythm):</strong>
                                <span>${line.patternString || '—'}</span>
                                <span style="opacity: 0.5;">|</span>
                                <span style="letter-spacing: 1px;">${line.patternWeights || '—'}</span>
                            </div>
                        </div>
                    `;
                });

                linesList.innerHTML = html;
            }
        }

        // Mode Toggles
        if (standardBtn && flexibleBtn) {
            standardBtn.addEventListener('click', () => {
                standardBtn.classList.add('active');
                flexibleBtn.classList.remove('active');
                engine.setMode('standard');
                renderAnalysis();
            });

            flexibleBtn.addEventListener('click', () => {
                flexibleBtn.classList.add('active');
                standardBtn.classList.remove('active');
                engine.setMode('flexible');
                renderAnalysis();
            });
        }

        // Preset buttons
        presetBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const presetKey = btn.getAttribute('data-preset');
                if (PRESET_VERSES[presetKey]) {
                    textarea.value = PRESET_VERSES[presetKey];
                    renderAnalysis();
                }
            });
        });

        // Clear action handlers
        function clearTypingArea() {
            textarea.value = '';
            textarea.focus();
            renderAnalysis();
        }

        if (clearBtn) clearBtn.addEventListener('click', clearTypingArea);
        if (floatClearBtn) floatClearBtn.addEventListener('click', clearTypingArea);

        // Live input typing
        textarea.addEventListener('input', renderAnalysis);

        // Initial default test verse (संयुक्ताक्षर परीक्षा)
        textarea.value = PRESET_VERSES.sanyukt;
        renderAnalysis();
    }

    // =========================================================
    // DAILY LITERARY DUO: SHER & WORD OF THE DAY CONTROLLER
    // =========================================================
    function initDailySpotlight() {
        const shers = [
            {
                text: "सिर्फ हंगामा खड़ा करना मेरा मकसद नहीं, मेरी कोशिश है कि ये सूरत बदलनी चाहिए।<br>मेरे सीने में नहीं तो तेरे सीने में सही, हो कहीं भी आग, लेकिन आग जलनी चाहिए।",
                plainText: "सिर्फ हंगामा खड़ा करना मेरा मकसद नहीं, मेरी कोशिश है कि ये सूरत बदलनी चाहिए।\nमेरे सीने में नहीं तो तेरे सीने में सही, हो कहीं भी आग, लेकिन आग जलनी चाहिए।",
                author: "दुष्यंत कुमार",
                tag: "ग़ज़ल • साये में धूप"
            },
            {
                text: "तू न थकेगा कभी, तू न थमेगा कभी, तू न मुड़ेगा कभी,<br>कर शपथ, कर शपथ, कर शपथ! अग्निपथ! अग्निपथ! अग्निपथ!",
                plainText: "तू न थकेगा कभी, तू न थमेगा कभी, तू न मुड़ेगा कभी,\nकर शपथ, कर शपथ, कर शपथ! अग्निपथ! अग्निपथ! अग्निपथ!",
                author: "हरिवंश राय बच्चन",
                tag: "कविता • अग्निपथ"
            },
            {
                text: "हज़ारों ख़्वाहिशें ऐसी कि हर ख़्वाहिश पे दम निकले,<br>बहुत निकले मिरे अरमाँ लेकिन फिर भी कम निकले।",
                plainText: "हज़ारों ख़्वाहिशें ऐसी कि हर ख़्वाहिश पे दम निकले,\nबहुत निकले मिरे अरमाँ लेकिन फिर भी कम निकले।",
                author: "मिर्ज़ा ग़ालिब",
                tag: "उर्दू ग़ज़ल"
            },
            {
                text: "सच है, विपत्ति जब आती है, कायर को ही दहलाती है,<br>सूरमा नहीं विचलित होते, क्षण एक नहीं धीरज खोते।",
                plainText: "सच है, विपत्ति जब आती है, कायर को ही दहलाती है,\nसूरमा नहीं विचलित होते, क्षण एक नहीं धीरज खोते।",
                author: "रामधारी सिंह 'दिनकर'",
                tag: "काव्य • रश्मिरथी"
            },
            {
                text: "विस्तृत नभ का कोई कोना, मेरा न कभी अपना होना,<br>परिचय इतना इतिहास यहीं, उमड़ी कल थी मिट आज चली!",
                plainText: "विस्तृत नभ का कोई कोना, मेरा न कभी अपना होना,\nपरिचय इतना इतिहास यहीं, उमड़ी कल थी मिट आज चली!",
                author: "महादेवी वर्मा",
                tag: "काव्य • सांध्यगीत"
            },
            {
                text: "हाथ छूटें भी तो रिश्ते नहीं छोड़ा करते,<br>वक्त की शाख़ से लम्हे नहीं तोड़ा करते।",
                plainText: "हाथ छूटें भी तो रिश्ते नहीं छोड़ा करते,\nवक्त की शाख़ से लम्हे नहीं तोड़ा करते।",
                author: "गुलज़ार",
                tag: "कविता / नज़्म"
            },
            {
                text: "पोथी पढ़ि पढ़ि जग मुआ, पंडित भया न कोय।<br>ढाई आखर प्रेम का, पढ़े सो पंडित होय॥",
                plainText: "पोथी पढ़ि पढ़ि जग मुआ, पंडित भया न कोय।\nढाई आखर प्रेम का, पढ़े सो पंडित होय॥",
                author: "संत कबीर दास",
                tag: "साखी / दोहा"
            },
            {
                text: "ले चल वहाँ भुलावा देकर, मेरे नाविक धीरे-धीरे!<br>जिस निर्जन में सागर लहरी, अंबर के कानों में गहरी कथा कहती हो...",
                plainText: "ले चल वहाँ भुलावा देकर, मेरे नाविक धीरे-धीरे!\nजिस निर्जन में सागर लहरी, अंबर के कानों में गहरी कथा कहती हो...",
                author: "जयशंकर प्रसाद",
                tag: "काव्य • कामायनी / लहर"
            },
            {
                text: "बुंदेले हरबोलों के मुँह हमने सुनी कहानी थी,<br>खूब लड़ी मर्दानी वह तो झाँसी वाली रानी थी।",
                plainText: "बुंदेले हरबोलों के मुँह हमने सुनी कहानी थी,\nखूब लड़ी मर्दानी वह तो झाँसी वाली रानी थी।",
                author: "सुभद्रा कुमारी चौहान",
                tag: "वीर रस कविता"
            },
            {
                text: "लोग टूट जाते हैं एक घर बनाने में,<br>तुम तरस नहीं खाते बस्तियाँ जलाने में।",
                plainText: "लोग टूट जाते हैं एक घर बनाने में,\nतुम तरस नहीं खाते बस्तियाँ जलाने में।",
                author: "बशीर बद्र",
                tag: "शे'र / ग़ज़ल"
            }
        ];

        const words = [
            {
                word: "जिजीविषा",
                grammar: "[संज्ञा, स्त्रीलिंग • तत्सम]",
                meaning: "जीने की अदम्य इच्छा, जीवन के प्रति अगाध प्रेम व घोर संकटों में भी अडिग रहने का अटूट संकल्प।",
                usage: "\"हर पतझड़ के बाद जो शाखों में नई कोंपलें फूटती हैं, वह प्रकृति की अदम्य जिजीविषा का ही प्रमाण है।\""
            },
            {
                word: "तसव्वुर",
                grammar: "[संज्ञा, पुल्लिंग • अरबी / उर्दू]",
                meaning: "कल्पना, मन में किसी व्यक्ति, दृश्य या अनुभूति का चित्र संजोना, चिंतन या ख़याल।",
                usage: "\"तसव्वुर में ही सही उनका दीदार तो हुआ, मुद्दतों बाद दिल को कुछ करार तो हुआ।\""
            },
            {
                word: "उन्मेष",
                grammar: "[संज्ञा, पुल्लिंग • तत्सम]",
                meaning: "चेतना का जाग्रत होना, अंतर्दृष्टि या नई कलात्मक प्रेरणा का प्रस्फुटन, आँखें खुलना।",
                usage: "\"काव्य की इस अमर पंक्ति ने मेरे भीतर एक नए वैचारिक उन्मेष को जन्म दिया।\""
            },
            {
                word: "सजल",
                grammar: "[विशेषण • तत्सम]",
                meaning: "जल से युक्त, आंसुओं या ओस से भीगा हुआ, अत्यंत भावुक, स्निग्ध व कोमल।",
                usage: "\"विदाई की उस मर्मस्पर्शी बेला में उनकी सजल आँखों ने बिना कहे सब कुछ बयाँ कर दिया।\""
            },
            {
                word: "शफ़क़",
                grammar: "[संज्ञा, स्त्रीलिंग • फ़ारसी / उर्दू]",
                meaning: "सूर्यास्त या सूर्योदय के समय क्षितिज पर फैलने वाली सुर्ख़, स्वर्णिम व गुलाबी आभा।",
                usage: "\"शफ़क़ की लाली जब दरिया के पानी पर तैरने लगी, तो साँझ किसी ग़ज़ल सी हसीन हो गई।\""
            },
            {
                word: "अनहद",
                grammar: "[विशेषण / संज्ञा • सूफी-संत परंपरा]",
                meaning: "सीमाहीन, असीम; वह अनाहत व दिव्य आंतरिक नाद जो बिना किसी आघात के निरंतर गूँजता है।",
                usage: "\"कबीर के भजनों में जब अनहद नाद गूँजता है, तब मन सांसारिक कोलाहल से परे शांत हो जाता है।\""
            },
            {
                word: "मुंतज़िर",
                grammar: "[विशेषण • अरबी / उर्दू]",
                meaning: "प्रतीक्षारत, इंतज़ार करने वाला, किसी प्रिय या शुभ घड़ी की आस में पलकें बिछाए हुए।",
                usage: "\"मुद्दतों से ये आँखें किसी मुंतज़िर की तरह उसी वीरान मोड़ पर टिकी रहीं।\""
            },
            {
                word: "अकिंचन",
                grammar: "[विशेषण • तत्सम]",
                meaning: "जिसके पास कुछ भी भौतिक धन न हो, अत्यंत विनम्र, निःस्व, सर्वथा समर्पित।",
                usage: "\"साहित्य के इस असीम सागर के सम्मुख मैं स्वयं को केवल एक अकिंचन साधक मानता हूँ।\""
            },
            {
                word: "नैसर्गिक",
                grammar: "[विशेषण • तत्सम]",
                meaning: "स्वाभाविक, प्राकृतिक, जो कृत्रिम या बनावटी न होकर मूल रूप से सहज व सच्चा हो।",
                usage: "\"उनकी रचनाओं में बनावटीपन नहीं, बल्कि एक नैसर्गिक सौंदर्य और सरलता की खुशबू है।\""
            },
            {
                word: "इंतख़ाब",
                grammar: "[संज्ञा, पुल्लिंग • अरबी / उर्दू]",
                meaning: "चयन, उत्कृष्टतम का चुनाव, सर्वोत्तम संग्रह (Selection / Anthology)।",
                usage: "\"यह पुस्तक हिंदी-उर्दू साहित्य की सर्वश्रेष्ठ रचनाओं का एक अनूठा इंतख़ाब है।\""
            }
        ];

        // Pick daily base index using day of year
        const now = new Date();
        const startOfYear = new Date(now.getFullYear(), 0, 0);
        const dayOfYear = Math.floor((now - startOfYear) / (1000 * 60 * 60 * 24));
        let sherIdx = Math.abs(dayOfYear) % shers.length;
        let wordIdx = Math.abs(dayOfYear) % words.length;

        // Elements
        const sherTextEl = document.getElementById('spotlight-sher-text');
        const sherAuthorEl = document.getElementById('spotlight-sher-author');
        const sherTagEl = document.getElementById('spotlight-sher-tag');
        const btnShuffleSher = document.getElementById('btn-shuffle-sher');
        const btnCopySher = document.getElementById('btn-copy-sher');

        const wordTitleEl = document.getElementById('spotlight-word-title');
        const wordGrammarEl = document.getElementById('spotlight-word-grammar');
        const wordMeaningEl = document.getElementById('spotlight-word-meaning');
        const wordUsageEl = document.getElementById('spotlight-word-usage');
        const btnSpeakWord = document.getElementById('btn-speak-word');
        const btnShuffleWord = document.getElementById('btn-shuffle-word');
        const btnCopyWord = document.getElementById('btn-copy-word');

        const toast = document.getElementById('spotlight-toast');

        // Toast feedback
        function showToast(msg) {
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2400);
        }

        // Render functions
        function renderSher(idx) {
            const item = shers[idx];
            if (!sherTextEl) return;

            sherTextEl.style.opacity = '0';
            setTimeout(() => {
                sherTextEl.innerHTML = item.text;
                if (sherAuthorEl) sherAuthorEl.textContent = item.author;
                if (sherTagEl) sherTagEl.textContent = item.tag;
                sherTextEl.style.opacity = '1';
            }, 120);
        }

        function renderWord(idx) {
            const item = words[idx];
            const body = document.querySelector('.word-card-body');
            if (!wordTitleEl) return;

            if (body) body.style.opacity = '0';
            setTimeout(() => {
                wordTitleEl.textContent = item.word;
                if (wordGrammarEl) wordGrammarEl.textContent = item.grammar;
                if (wordMeaningEl) wordMeaningEl.textContent = item.meaning;
                if (wordUsageEl) wordUsageEl.textContent = item.usage;
                if (body) body.style.opacity = '1';
            }, 120);
        }

        // Sher Actions
        if (btnShuffleSher) {
            btnShuffleSher.addEventListener('click', () => {
                sherIdx = (sherIdx + 1) % shers.length;
                renderSher(sherIdx);
            });
        }

        if (btnCopySher) {
            btnCopySher.addEventListener('click', () => {
                const s = shers[sherIdx];
                const copyText = `“${s.plainText}”\n— ${s.author} (${s.tag})\n\n[शब्द संचय - आज का शे'र]`;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(copyText).then(() => {
                        showToast('✓ शे\'र क्लिपबोर्ड में कॉपी हो गया!');
                    }).catch(() => {
                        showToast('✗ कॉपी करने में असमर्थ');
                    });
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = copyText;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    showToast('✓ शे\'र क्लिपबोर्ड में कॉपी हो गया!');
                }
            });
        }

        // Word Actions
        if (btnShuffleWord) {
            btnShuffleWord.addEventListener('click', () => {
                wordIdx = (wordIdx + 1) % words.length;
                renderWord(wordIdx);
            });
        }

        if (btnCopyWord) {
            btnCopyWord.addEventListener('click', () => {
                const w = words[wordIdx];
                const copyText = `आज का शब्द: ${w.word} ${w.grammar}\nअर्थ: ${w.meaning}\nप्रयोग: ${w.usage}\n\n[शब्द संचय - आज का शब्द]`;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(copyText).then(() => {
                        showToast('✓ शब्द व अर्थ कॉपी हो गया!');
                    }).catch(() => {
                        showToast('✗ कॉपी करने में असमर्थ');
                    });
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = copyText;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    showToast('✓ शब्द व अर्थ कॉपी हो गया!');
                }
            });
        }

        if (btnSpeakWord) {
            btnSpeakWord.addEventListener('click', () => {
                const w = words[wordIdx];
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(w.word);
                    utterance.lang = 'hi-IN';
                    utterance.rate = 0.88;
                    window.speechSynthesis.speak(utterance);
                } else {
                    showToast('वॉइस उच्चारण उपलब्ध नहीं है');
                }
            });
        }

        // Initial render
        renderSher(sherIdx);
        renderWord(wordIdx);
    }

    // Interactive Poetry Poster Creator Controller
    function initPosterStudio() {
        const inputText = document.getElementById('poster-input-text');
        const inputAuthor = document.getElementById('poster-input-author');
        const renderText = document.getElementById('poster-rendered-text');
        const renderAuthor = document.getElementById('poster-rendered-author');
        const photoBox = document.getElementById('poster-photo-box');
        const moodTag = document.getElementById('poster-mood-tag');
        const tmplBtns = document.querySelectorAll('.tmpl-btn');
        const btnShuffle = document.getElementById('btn-shuffle-poster-couplet');
        const btnClear = document.getElementById('btn-clear-poster-text');
        const btnDownload = document.getElementById('btn-download-poster');
        const exportCanvas = document.getElementById('poster-export-canvas');

        if (!inputText || !renderText || !photoBox) return;

        const couplets = [
            { text: "लहरों से डर कर नौका पार नहीं होती, कोशिश करने वालों की कभी हार नहीं होती।", author: "हरिवंश राय बच्चन" },
            { text: "सिर्फ हंगामा खड़ा करना मेरा मकसद नहीं, मेरी कोशिश है कि ये सूरत बदलनी चाहिए।", author: "दुष्यंत कुमार" },
            { text: "हज़ारों ख़्वाहिशें ऐसी कि हर ख़्वाहिश पे दम निकले, बहुत निकले मिरे अरमाँ लेकिन फिर भी कम निकले।", author: "मिर्ज़ा ग़ालिब" },
            { text: "सच है, विपत्ति जब आती है, कायर को ही दहलाती है, सूरमा नहीं विचलित होते, क्षण एक नहीं धीरज खोते।", author: "रामधारी सिंह 'दिनकर'" },
            { text: "हो गई है पीर पर्वत-सी पिघलनी चाहिए, इस हिमालय से कोई गंगा निकलनी चाहिए।", author: "दुष्यंत कुमार" },
            { text: "तू ज़िंदा है तो ज़िंदगी की जीत में यकीन कर, अगर कहीं है स्वर्ग तो उतार ला ज़मीन पर।", author: "शंकर शैलेंद्र" }
        ];

        const templateMoods = {
            'gold': 'स्वर्णिम प्रभात',
            'parchment': 'प्राचीन पांडुलिपि',
            'midnight': 'निशांत एकांत',
            'sunset': 'सांध्य रक्तिम',
            'minimal': 'धवल शांति'
        };

        let currentTmpl = 'midnight';
        let coupletIdx = 0;

        // Real-time text sync
        inputText.addEventListener('input', () => {
            const val = inputText.value.trim();
            renderText.textContent = val || "अपनी पंक्तियाँ यहाँ लिखें...";
            adjustTextSize();
        });

        // Real-time author sync
        if (inputAuthor && renderAuthor) {
            inputAuthor.addEventListener('input', () => {
                const val = inputAuthor.value.trim();
                renderAuthor.textContent = val || "रचनाकार";
            });
        }

        // Auto-scale font size if text is long
        function adjustTextSize() {
            const len = (inputText.value || '').length;
            if (len > 120) {
                renderText.style.fontSize = '0.95rem';
                renderText.style.lineHeight = '1.45';
            } else if (len > 70) {
                renderText.style.fontSize = '1.1rem';
                renderText.style.lineHeight = '1.55';
            } else {
                renderText.style.fontSize = '1.25rem';
                renderText.style.lineHeight = '1.65';
            }
        }

        // Template Switcher
        tmplBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tmplBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const tmpl = btn.dataset.tmpl;
                currentTmpl = tmpl;

                // Remove previous tmpl classes
                photoBox.className = 'poster-photo-box tmpl-' + tmpl;
                if (moodTag && templateMoods[tmpl]) {
                    moodTag.textContent = templateMoods[tmpl];
                }
            });
        });

        // Shuffle Couplet
        if (btnShuffle) {
            btnShuffle.addEventListener('click', () => {
                coupletIdx = (coupletIdx + 1) % couplets.length;
                const c = couplets[coupletIdx];
                inputText.value = c.text;
                renderText.textContent = c.text;
                if (inputAuthor && renderAuthor) {
                    inputAuthor.value = c.author;
                    renderAuthor.textContent = c.author;
                }
                adjustTextSize();
            });
        }

        // Clear
        if (btnClear) {
            btnClear.addEventListener('click', () => {
                inputText.value = '';
                renderText.textContent = 'अपनी पंक्तियाँ यहाँ लिखें...';
                inputText.focus();
            });
        }

        // HD Poster Download with Shabd Sanchay Logo (Exact 1:1 / 4:4 Square Match)
        if (btnDownload && exportCanvas) {
            btnDownload.addEventListener('click', async () => {
                const ctx = exportCanvas.getContext('2d');
                const width = exportCanvas.width; // 1080
                const height = exportCanvas.height; // 1080

                // Ensure Google fonts are fully loaded into the browser canvas engine before rendering
                try {
                    await document.fonts.ready;
                    await Promise.all([
                        document.fonts.load('900 52px "Biryani"'),
                        document.fonts.load('700 17px "Biryani"'),
                        document.fonts.load('bold 46px "Rozha One"'),
                        document.fonts.load('italic bold 32px "Noto Serif Devanagari"')
                    ]);
                } catch (e) {
                    console.log('Font load verification:', e);
                }

                function renderCanvasPoster(bgImage) {
                    ctx.clearRect(0, 0, width, height);

                    // 1. Draw Background according to Template
                    if (currentTmpl === 'gold') {
                        // Soft warm parchment base
                        const grad = ctx.createLinearGradient(0, 0, width, height);
                        grad.addColorStop(0, '#ffffff');
                        grad.addColorStop(0.5, '#fff7ee');
                        grad.addColorStop(1, '#fee4cc');
                        ctx.fillStyle = grad;
                        ctx.fillRect(0, 0, width, height);

                        // Draw background art if available
                        if (bgImage) {
                            ctx.globalAlpha = 0.40;
                            ctx.drawImage(bgImage, 0, 0, width, height);
                            ctx.globalAlpha = 1.0;
                        }

                        // Subtle golden border
                        ctx.strokeStyle = 'rgba(200, 90, 23, 0.4)';
                        ctx.lineWidth = 14;
                        ctx.strokeRect(28, 28, width - 56, height - 56);

                    } else if (currentTmpl === 'parchment') {
                        // Antique parchment
                        const grad = ctx.createRadialGradient(width / 2, height / 2, 100, width / 2, height / 2, 700);
                        grad.addColorStop(0, '#fdf8eb');
                        grad.addColorStop(0.65, '#f7e8cb');
                        grad.addColorStop(1, '#edd3a8');
                        ctx.fillStyle = grad;
                        ctx.fillRect(0, 0, width, height);

                        // Antique ornamental borders
                        ctx.strokeStyle = 'rgba(140, 88, 53, 0.5)';
                        ctx.lineWidth = 14;
                        ctx.strokeRect(28, 28, width - 56, height - 56);
                        ctx.strokeStyle = 'rgba(200, 90, 23, 0.35)';
                        ctx.lineWidth = 3;
                        ctx.strokeRect(48, 48, width - 96, height - 96);

                    } else if (currentTmpl === 'midnight') {
                        // Deep Royal Starry Midnight
                        const grad = ctx.createRadialGradient(width * 0.65, height * 0.35, 80, width / 2, height / 2, 750);
                        grad.addColorStop(0, '#1a2640');
                        grad.addColorStop(0.5, '#0c1322');
                        grad.addColorStop(1, '#05080e');
                        ctx.fillStyle = grad;
                        ctx.fillRect(0, 0, width, height);

                        // Golden hairline frame
                        ctx.strokeStyle = 'rgba(246, 173, 85, 0.45)';
                        ctx.lineWidth = 8;
                        ctx.strokeRect(28, 28, width - 56, height - 56);

                    } else if (currentTmpl === 'sunset') {
                        // Terracotta sunset gradient
                        const grad = ctx.createLinearGradient(0, 0, width, height);
                        grad.addColorStop(0, '#fff3ea');
                        grad.addColorStop(0.5, '#fedbc9');
                        grad.addColorStop(1, '#f8b492');
                        ctx.fillStyle = grad;
                        ctx.fillRect(0, 0, width, height);

                        ctx.strokeStyle = 'rgba(184, 58, 27, 0.45)';
                        ctx.lineWidth = 14;
                        ctx.strokeRect(28, 28, width - 56, height - 56);

                    } else {
                        // Minimal Pure White Zen
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, width, height);

                        ctx.strokeStyle = '#2b211a';
                        ctx.lineWidth = 14;
                        ctx.strokeRect(28, 28, width - 56, height - 56);
                        ctx.strokeStyle = 'rgba(200, 90, 23, 0.35)';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(46, 46, width - 92, height - 92);
                    }

                    // 2. Color Scheme for Text based on template
                    let textColor = '#1f1712';
                    let authorColor = '#c85a17';
                    let logoTitleColor = '#1f1712';
                    let logoTaglineColor = '#634f42';
                    let quoteMarkColor = 'rgba(200, 90, 23, 0.35)';

                    if (currentTmpl === 'midnight') {
                        textColor = '#ffffff';
                        authorColor = '#fedbb8';
                        logoTitleColor = '#fedbb8';
                        logoTaglineColor = '#f7d0a4';
                        quoteMarkColor = 'rgba(246, 173, 85, 0.35)';
                    } else if (currentTmpl === 'gold') {
                        authorColor = '#c85a17';
                        logoTitleColor = '#8c3b0c';
                        logoTaglineColor = '#5a3c2c';
                    } else if (currentTmpl === 'parchment') {
                        textColor = '#352012';
                        authorColor = '#8c3b0c';
                        logoTitleColor = '#5c2c0e';
                        logoTaglineColor = '#704225';
                    } else if (currentTmpl === 'sunset') {
                        textColor = '#40120a';
                        authorColor = '#a32b0d';
                        logoTitleColor = '#8c250c';
                        logoTaglineColor = '#634f42';
                    }

                    // 3. Draw Official Shabd Sanchay Logo Badge at Top-Right (Font: Biryani, Subtext strictly bounded underneath)
                    ctx.textBaseline = 'alphabetic';
                    ctx.textAlign = 'center';

                    // Measure main title 'शब्द संचय'
                    ctx.font = '900 52px "Biryani", "Noto Sans Devanagari", sans-serif';
                    const mainTitle = 'शब्द संचय';
                    const tagline = 'विचारों के नए प्रतिमान';
                    const mainTitleWidth = ctx.measureText(mainTitle).width;
                    const rightMargin = 70;
                    const logoCenterX = width - rightMargin - (mainTitleWidth / 2);

                    // Draw Title
                    ctx.fillStyle = logoTitleColor;
                    ctx.fillText(mainTitle, logoCenterX, 86);

                    // Calculate strictly bounded tagline font size so width matches title length
                    let tagFontSize = 16.5;
                    ctx.font = `700 ${tagFontSize}px "Biryani", "Noto Sans Devanagari", sans-serif`;
                    let tagWidth = ctx.measureText(tagline).width;
                    if (tagWidth > mainTitleWidth) {
                        tagFontSize = (tagFontSize * (mainTitleWidth / tagWidth));
                        ctx.font = `700 ${tagFontSize.toFixed(1)}px "Biryani", "Noto Sans Devanagari", sans-serif`;
                    }

                    // Draw Tagline centered directly under title
                    ctx.fillStyle = logoTaglineColor;
                    ctx.fillText(tagline, logoCenterX, 118);

                    // 4. Quotation Mark Glyph
                    ctx.fillStyle = quoteMarkColor;
                    ctx.font = '110px Georgia, serif';
                    ctx.textAlign = 'center';
                    ctx.fillText('“', width / 2, 300);

                    // 5. Draw Word-Wrapped Poem Text
                    const poemText = (inputText.value || '').trim() || 'अपनी पंक्तियाँ यहाँ लिखें...';
                    ctx.fillStyle = textColor;
                    ctx.font = 'bold 46px "Rozha One", "Noto Serif Devanagari", serif';
                    ctx.textAlign = 'center';

                    const maxWidth = width - 240;
                    const lineHeight = 68;
                    const wordsArr = poemText.split(' ');
                    let line = '';
                    let lines = [];

                    for (let i = 0; i < wordsArr.length; i++) {
                        const testLine = line + wordsArr[i] + ' ';
                        const metrics = ctx.measureText(testLine);
                        if (metrics.width > maxWidth && i > 0) {
                            lines.push(line);
                            line = wordsArr[i] + ' ';
                        } else {
                            line = testLine;
                        }
                    }
                    lines.push(line);

                    // Calculate vertical starting point to center within square
                    const totalTextHeight = lines.length * lineHeight;
                    let startY = (height / 2) - (totalTextHeight / 2) + 40;

                    for (let j = 0; j < lines.length; j++) {
                        ctx.fillText(lines[j].trim(), width / 2, startY + (j * lineHeight));
                    }

                    // 6. Draw Author Attribution
                    const authorText = (inputAuthor.value || '').trim() || 'रचनाकार';
                    ctx.fillStyle = authorColor;
                    ctx.font = 'italic bold 32px "Noto Serif Devanagari", Georgia, serif';
                    ctx.fillText('— ' + authorText, width / 2, startY + (lines.length * lineHeight) + 65);

                    // 7. Trigger Direct Download
                    const link = document.createElement('a');
                    link.download = 'shabd-sanchay-kavya-poster.png';
                    link.href = exportCanvas.toDataURL('image/png', 1.0);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Feedback
                    if (typeof showToast === 'function') {
                        showToast('✓ 4:4 काव्य-पोस्टर सफलतापूर्वक डाउनलोड हो गया!');
                    }
                }

                // If gold template, load artwork image before rendering
                if (currentTmpl === 'gold') {
                    const img = new Image();
                    img.crossOrigin = 'anonymous';
                    img.onload = () => renderCanvasPoster(img);
                    img.onerror = () => renderCanvasPoster(null);
                    img.src = 'images/hero_panoramic_art.png';
                } else {
                    renderCanvasPoster(null);
                }
            });
        }
    }

    // Biryani Font Hero Headline Typing Effect
    function initHeroTypingEffect() {
        const titleEl = document.getElementById('heroTypedTitle');
        if (!titleEl) return;

        const line1El = document.getElementById('typeLine1');
        const line2El = document.getElementById('typeLine2');
        const cursorEl = document.getElementById('typingCursor');

        const line1Text = "जहाँ शब्द केवल पढ़े नहीं,";
        const line2Text = "महसूस किए जाते हैं ॥";

        // Devanagari Grapheme segmentation to ensure pristine Hindi matras & conjuncts during typing
        const getGraphemes = (text) => {
            if (typeof Intl !== 'undefined' && Intl.Segmenter) {
                const segmenter = new Intl.Segmenter('hi', { granularity: 'grapheme' });
                return Array.from(segmenter.segment(text), s => s.segment);
            }
            return Array.from(text);
        };

        const line1Graphemes = getGraphemes(line1Text);
        const line2Graphemes = getGraphemes(line2Text);

        let idx1 = 0;
        let idx2 = 0;

        function typeLine1() {
            if (idx1 < line1Graphemes.length) {
                line1El.textContent += line1Graphemes[idx1];
                idx1++;
                setTimeout(typeLine1, 55 + Math.random() * 20);
            } else {
                setTimeout(typeLine2, 220);
            }
        }

        function typeLine2() {
            if (idx2 < line2Graphemes.length) {
                line2El.textContent += line2Graphemes[idx2];
                idx2++;
                setTimeout(typeLine2, 65 + Math.random() * 25);
            } else {
                if (cursorEl) {
                    cursorEl.classList.add('finished');
                }
            }
        }

        setTimeout(typeLine1, 200);
    }

    // Home Newsletter Handler & Initialization
    document.addEventListener('DOMContentLoaded', () => {
        initHeroTypingEffect();
        initScrollAnimations();
        initMagicalTiltCards();
        fetchSelectedWorks();
        initProMatraCalculator();
        initDailySpotlight();
        initPosterStudio();

        const form = document.getElementById('home-newsletter-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const emailInput = document.getElementById('home-newsletter-email');
                const msgBox = document.getElementById('home-newsletter-msg');
                const email = emailInput.value.trim();

                if (!email) return;

                try {
                    const res = await fetch('api/subscribe_newsletter.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: email })
                    });
                    const data = await res.json();
                    msgBox.style.display = 'block';
                    if (data.success) {
                        msgBox.className = 'newsletter-feedback-msg success';
                        msgBox.textContent = '✓ ' + data.message;
                        emailInput.value = '';
                    } else {
                        msgBox.className = 'newsletter-feedback-msg error';
                        msgBox.textContent = '✗ ' + data.message;
                    }
                } catch (err) {
                    msgBox.style.display = 'block';
                    msgBox.className = 'newsletter-feedback-msg error';
                    msgBox.textContent = '✗ नेटवर्क त्रुटि, कृपया पुनः प्रयास करें।';
                }
            });
        }
    });
    </script>
</body>
</html>
