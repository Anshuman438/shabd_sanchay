<?php
// submit.php - Public User Submission Portal for Poems, Articles, Stories, and Plays
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$logged_user = get_logged_in_user($conn);
$page_title = "अपनी रचना भेजें | शब्द संचय";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
    <style>
        .submit-portal-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }
        .submit-hero-card {
            background: linear-gradient(135deg, rgba(200, 90, 23, 0.08) 0%, rgba(200, 90, 23, 0.02) 100%);
            border: 1px solid rgba(200, 90, 23, 0.2);
            border-radius: 20px;
            padding: 2.2rem;
            text-align: center;
            margin-bottom: 2.2rem;
            position: relative;
        }
        [data-theme="dark"] .submit-hero-card {
            background: linear-gradient(135deg, rgba(246, 173, 85, 0.1) 0%, rgba(246, 173, 85, 0.02) 100%);
            border-color: rgba(246, 173, 85, 0.22);
        }
        .submit-author-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--card-bg, #ffffff);
            border: 1px solid rgba(200, 90, 23, 0.25);
            border-radius: 16px;
            padding: 1rem 1.4rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            flex-wrap: wrap;
            gap: 1rem;
        }
        [data-theme="dark"] .submit-author-banner {
            background: #201a16;
            border-color: rgba(246, 173, 85, 0.25);
        }
        .author-banner-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .author-banner-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #c85a17;
        }
        .type-selector-pills {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.8rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 600px) {
            .type-selector-pills {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .type-pill-btn {
            background: var(--card-bg, #ffffff);
            border: 2px solid rgba(200, 90, 23, 0.2);
            border-radius: 14px;
            padding: 1rem 0.8rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.22s ease;
            color: var(--text);
            font-family: inherit;
        }
        [data-theme="dark"] .type-pill-btn {
            background: #201a16;
            border-color: rgba(246, 173, 85, 0.2);
        }
        .type-pill-btn:hover {
            border-color: #c85a17;
            transform: translateY(-2px);
        }
        .type-pill-btn.active {
            background: #c85a17;
            color: #ffffff;
            border-color: #c85a17;
            box-shadow: 0 6px 20px rgba(200, 90, 23, 0.35);
        }
        [data-theme="dark"] .type-pill-btn.active {
            background: #f6ad55;
            color: #1c1511;
            border-color: #f6ad55;
        }
        .type-pill-icon {
            font-size: 1.6rem;
            display: block;
            margin-bottom: 0.3rem;
        }
        .type-pill-title {
            font-weight: 700;
            font-size: 0.95rem;
        }
        .submit-form-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid rgba(200, 90, 23, 0.18);
            border-radius: 20px;
            padding: 2.2rem 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        [data-theme="dark"] .submit-form-card {
            background: #201a16;
            border-color: rgba(246, 173, 85, 0.22);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .form-section-title {
            font-family: 'Rozha One', 'Biryani', serif;
            font-size: 1.25rem;
            color: #c85a17;
            margin-bottom: 1.2rem;
            border-bottom: 1.5px dashed rgba(200, 90, 23, 0.25);
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        [data-theme="dark"] .form-section-title {
            color: #f6ad55;
            border-bottom-color: rgba(246, 173, 85, 0.25);
        }
        .submit-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
        }
        @media (max-width: 640px) {
            .submit-row-2 {
                grid-template-columns: 1fr;
            }
            .submit-form-card {
                padding: 1.5rem;
            }
        }
        .submit-input-group {
            margin-bottom: 1.35rem;
        }
        .submit-input-group label {
            display: block;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
            color: var(--text);
        }
        .submit-input-group .input-hint {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: normal;
            margin-left: 0.4rem;
        }
        .submit-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid rgba(0, 0, 0, 0.12);
            border-radius: 10px;
            font-family: 'Noto Sans Devanagari', 'Inter', sans-serif;
            font-size: 0.95rem;
            background: var(--bg);
            color: var(--text);
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        [data-theme="dark"] .submit-control {
            border-color: rgba(255, 255, 255, 0.14);
            background: #181310;
        }
        .submit-control:focus {
            outline: none;
            border-color: #c85a17;
            box-shadow: 0 0 0 3px rgba(200, 90, 23, 0.14);
        }
        .mode-switch-tabs {
            display: flex;
            gap: 0.5rem;
            background: rgba(200, 90, 23, 0.08);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 1.5rem;
        }
        [data-theme="dark"] .mode-switch-tabs {
            background: rgba(255, 255, 255, 0.06);
        }
        .mode-tab-btn {
            flex: 1;
            padding: 0.6rem;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.92rem;
            border-radius: 8px;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .mode-tab-btn.active {
            background: #c85a17;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(200, 90, 23, 0.3);
        }
        [data-theme="dark"] .mode-tab-btn.active {
            background: #f6ad55;
            color: #1c1511;
        }
        .live-preview-box {
            display: none;
            background: var(--bg);
            border: 1.5px dashed rgba(200, 90, 23, 0.35);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            font-family: 'Noto Serif Devanagari', serif;
        }
        [data-theme="dark"] .live-preview-box {
            border-color: rgba(246, 173, 85, 0.35);
            background: #181310;
        }
        .btn-submit-creation {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #c85a17 0%, #a04000 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.08rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.22s ease;
            box-shadow: 0 6px 20px rgba(200, 90, 23, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }
        [data-theme="dark"] .btn-submit-creation {
            background: linear-gradient(135deg, #f6ad55 0%, #dd6b20 100%);
            color: #1c1511;
            box-shadow: 0 6px 20px rgba(246, 173, 85, 0.25);
        }
        .btn-submit-creation:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(200, 90, 23, 0.45);
        }
        .agreement-row {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            margin: 1.4rem 0 1.8rem;
            font-size: 0.88rem;
            color: #64748b;
        }
        .agreement-row input {
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="submit-portal-wrapper">
            
            <!-- Hero Card -->
            <div class="submit-hero-card">
                <span class="page-eyebrow" style="color: #c85a17; font-weight: 800; font-size: 0.85rem; letter-spacing: 1.2px;">साहित्यिक सहभागिता • SUBMIT CREATION</span>
                <h1 style="font-family: 'Rozha One', 'Biryani', serif; font-size: 2.2rem; margin: 0.5rem 0 0.6rem;">अपनी मौलिक रचना साझा करें</h1>
                <div class="heading-artistic-underline" style="margin: 0.4rem auto 0.8rem;"></div>
                <p style="color: var(--text); opacity: 0.85; max-width: 620px; margin: 0 auto; font-size: 0.98rem; line-height: 1.6;">
                    कविता, विचारोत्तेजक लेख, मार्मिक कहानी या नाटक — अपनी रचना सीधे 'शब्द संचय' संपादकीय मंडल को भेजें। व्यवस्थापक द्वारा स्वीकृत होते ही यह पोर्टल पर प्रकाशित हो जाएगी।
                </p>
            </div>

            <!-- Author Status Banner -->
            <?php if ($logged_user): ?>
                <div class="submit-author-banner">
                    <div class="author-banner-left">
                        <img src="<?= htmlspecialchars($logged_user['profile_photo'] ?: 'images/authors/author-default.jpg') ?>" alt="<?= htmlspecialchars($logged_user['name']) ?>" class="author-banner-avatar">
                        <div>
                            <div style="font-weight: 800; font-size: 1.05rem;"><?= htmlspecialchars($logged_user['name']) ?></div>
                            <small style="color: #10b981; font-weight: 600;">✓ सत्यापित रचनाकार खाता (<?= htmlspecialchars($logged_user['email']) ?>)</small>
                        </div>
                    </div>
                    <div>
                        <a href="login.php?action=logout&redirect=submit.php" class="btn-action btn-delete" style="text-decoration:none; padding: 6px 14px; border-radius: 8px;">लॉग आउट</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="submit-author-banner" style="background: rgba(200, 90, 23, 0.05); border-style: dashed;">
                    <div>
                        <strong style="color: #c85a17;">💡 क्या आपका रचनाकार खाता है?</strong>
                        <p style="margin: 3px 0 0 0; font-size: 0.88rem; color: #64748b;">लॉग इन करने पर आपका नाम, बायो और प्रोफ़ाइल चित्र स्वचालित रूप से रचना के साथ जुड़ जाएँगे।</p>
                    </div>
                    <div style="display: flex; gap: 0.6rem;">
                        <a href="login.php?redirect=submit.php" class="btn-mode-pill active" style="text-decoration: none; padding: 0.45rem 1rem;">लॉग इन / रजिस्टर करें</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Content Type Selector Pills -->
            <div class="type-selector-pills">
                <button type="button" class="type-pill-btn active" data-type="poem" onclick="selectContentType('poem')">
                    <span class="type-pill-icon">🪶</span>
                    <span class="type-pill-title">कविता (Poem)</span>
                </button>
                <button type="button" class="type-pill-btn" data-type="article" onclick="selectContentType('article')">
                    <span class="type-pill-icon">📰</span>
                    <span class="type-pill-title">लेख (Article)</span>
                </button>
                <button type="button" class="type-pill-btn" data-type="story" onclick="selectContentType('story')">
                    <span class="type-pill-icon">📖</span>
                    <span class="type-pill-title">कहानी (Story)</span>
                </button>
                <button type="button" class="type-pill-btn" data-type="play" onclick="selectContentType('play')">
                    <span class="type-pill-icon">🎭</span>
                    <span class="type-pill-title">नाटक (Play)</span>
                </button>
            </div>

            <!-- Main Form Card -->
            <div class="submit-form-card">
                
                <!-- View Mode Tabs (Edit vs Live Preview) -->
                <div class="mode-switch-tabs">
                    <button type="button" class="mode-tab-btn active" id="tab-edit" onclick="switchMode('edit')">✍️ संपादन मोड (Edit)</button>
                    <button type="button" class="mode-tab-btn" id="tab-preview" onclick="switchMode('preview')">👁️ लाइव प्रिव्यू (Live Preview)</button>
                </div>

                <!-- Live Preview Container -->
                <div class="live-preview-box" id="live-preview-container">
                    <div style="text-align: center; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(200,90,23,0.2); padding-bottom: 1rem;">
                        <span class="category-pill active" id="prev-category-badge" style="font-size: 0.78rem; display: inline-block; margin-bottom: 0.5rem;">विधा</span>
                        <h2 id="prev-title" style="font-family: 'Rozha One', serif; font-size: 1.8rem; margin: 0.3rem 0;">शीर्षक</h2>
                        <div style="font-size: 0.9rem; color: #8c7a6b; margin-top: 0.3rem;">
                            रचनाकार: <strong id="prev-author-name"><?= htmlspecialchars($logged_user['name'] ?? 'रचनाकार का नाम') ?></strong>
                        </div>
                    </div>
                    <div id="prev-excerpt-box" style="font-style: italic; background: rgba(200,90,23,0.06); padding: 0.8rem 1.2rem; border-left: 3px solid #c85a17; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem;">
                        <span id="prev-excerpt">संक्षिप्त भूमिका...</span>
                    </div>
                    <div id="prev-content" style="line-height: 2; font-size: 1.1rem; white-space: pre-wrap; color: var(--text);">
                        यहाँ आपकी रचना का प्रिव्यू प्रदर्शित होगा...
                    </div>
                </div>

                <!-- Submission Form -->
                <form id="creation-submit-form" enctype="multipart/form-data">
                    <input type="hidden" name="content_type" id="input-content-type" value="poem">

                    <div class="form-section-title">
                        <span>1.</span> <span>रचना का विवरण (Content Details)</span>
                    </div>

                    <div class="submit-input-group">
                        <label for="input-title">रचना का शीर्षक (Title) *</label>
                        <input type="text" name="title" id="input-title" class="submit-control" required placeholder="उदा. रश्मिरथी, ईदगाह, साहित्य और समाज..." oninput="updateLivePreview()">
                    </div>

                    <div class="submit-row-2">
                        <div class="submit-input-group">
                            <label for="input-category">विधा / उप-श्रेणी (Category) *</label>
                            <select name="category" id="input-category" class="submit-control" onchange="updateLivePreview()">
                                <option value="सामाजिक">सामाजिक (Social)</option>
                                <option value="शृंगार व प्रेम">शृंगार व प्रेम (Romantic/Love)</option>
                                <option value="राष्ट्रभक्ति">राष्ट्रभक्ति (Patriotic)</option>
                                <option value="प्रकृति व पर्यावरण">प्रकृति व पर्यावरण (Nature)</option>
                                <option value="दर्शन व अध्यात्म">दर्शन व अध्यात्म (Philosophy)</option>
                                <option value="हास्य-व्यंग्य">हास्य-व्यंग्य (Satire/Humor)</option>
                                <option value="ऐतिहासिक">ऐतिहासिक (Historical)</option>
                                <option value="रहस्य व रोमांच">रहस्य व रोमांच (Mystery/Suspense)</option>
                                <option value="सामान्य" selected>सामान्य (General)</option>
                            </select>
                        </div>

                        <div class="submit-input-group">
                            <label for="input-image">कवर चित्र (Cover Image File) <span class="input-hint">वैकल्पिक</span></label>
                            <input type="file" name="image_file" id="input-image" class="submit-control" accept="image/*">
                        </div>
                    </div>

                    <div class="submit-input-group">
                        <label for="input-excerpt">संक्षिप्त परिचय / सारांश (Short Excerpt) <span class="input-hint">1-2 पंक्तियाँ</span></label>
                        <input type="text" name="excerpt" id="input-excerpt" class="submit-control" placeholder="रचना का मुख्य भाव या सारांश लिखें..." oninput="updateLivePreview()">
                    </div>

                    <div class="submit-input-group">
                        <label for="input-content">मुख्य रचना / पाठ (Main Content) * <span class="input-hint">पूरी रचना यहाँ लिखें या पेस्ट करें</span></label>
                        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.4rem;" id="content-hint-text">
                            💡 कविता के लिए प्रत्येक पद के बीच एक खाली पंक्ति रखें। (कहानियों के पृष्ठ विभाजन के लिए <code>---PAGE---</code> लिख सकते हैं)
                        </div>
                        <textarea name="content" id="input-content" class="submit-control" rows="12" required placeholder="यहाँ अपनी रचना लिखें..." oninput="updateLivePreview()"></textarea>
                    </div>

                    <div class="form-section-title" style="margin-top: 2rem;">
                        <span>2.</span> <span>रचनाकार का विवरण (Author Info)</span>
                    </div>

                    <div class="submit-row-2">
                        <div class="submit-input-group">
                            <label for="input-author-name">रचनाकार का नाम (Author Name) *</label>
                            <input type="text" name="author_name" id="input-author-name" class="submit-control" required value="<?= htmlspecialchars($logged_user['name'] ?? '') ?>" placeholder="उदा. महादेवी वर्मा" oninput="updateLivePreview()">
                        </div>

                        <div class="submit-input-group">
                            <label for="input-author-email">ईमेल पता (Email) * <span class="input-hint">स्वीकृति सूचना हेतु</span></label>
                            <input type="email" name="author_email" id="input-author-email" class="submit-control" required value="<?= htmlspecialchars($logged_user['email'] ?? '') ?>" placeholder="your.email@example.com">
                        </div>
                    </div>

                    <div class="submit-row-2">
                        <div class="submit-input-group">
                            <label for="input-author-bio">संक्षिप्त रचनाकार परिचय (Author Bio)</label>
                            <input type="text" name="author_bio" id="input-author-bio" class="submit-control" value="<?= htmlspecialchars($logged_user['bio'] ?? '') ?>" placeholder="साहित्यिक रुचि, निवास स्थान आदि...">
                        </div>

                        <?php if (empty($logged_user)): ?>
                            <div class="submit-input-group">
                                <label for="input-author-photo">रचनाकार चित्र (Author Photo) <span class="input-hint">वैकल्पिक</span></label>
                                <input type="file" name="author_photo_file" id="input-author-photo" class="submit-control" accept="image/*">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="agreement-row">
                        <input type="checkbox" id="check-agreement" required>
                        <label for="check-agreement">
                            मैं प्रमाणित करता/करती हूँ कि यह मेरी मौलिक रचना है एवं इसके प्रकाशन के सर्वाधिकार 'शब्द संचय' को प्रदान करता/करती हूँ।
                        </label>
                    </div>

                    <button type="submit" class="btn-submit-creation" id="btn-submit-form">
                        <span>रचना समीक्षा हेतु सबमिट करें</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    let currentType = 'poem';

    function selectContentType(type) {
        currentType = type;
        document.getElementById('input-content-type').value = type;
        
        document.querySelectorAll('.type-pill-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.type === type);
        });

        const hint = document.getElementById('content-hint-text');
        if (type === 'poem') {
            hint.innerHTML = '💡 कविता के लिए प्रत्येक पद के बीच एक खाली पंक्ति रखें।';
        } else if (type === 'story') {
            hint.innerHTML = '💡 कहानी के विभिन्न पृष्ठ बनाने के लिए पृष्ठों के बीच <code>---PAGE---</code> लिख सकते हैं।';
        } else if (type === 'play') {
            hint.innerHTML = '💡 नाटक के अंकों व पात्र संवादों को स्पष्ट रूप से दर्शाएँ (उदा. <strong>राम:</strong> नमस्कार...)।';
        } else {
            hint.innerHTML = '💡 लेख के अनुच्छेदों (Paragraphs) को स्पष्ट रखें।';
        }

        updateLivePreview();
    }

    function switchMode(mode) {
        const previewBox = document.getElementById('live-preview-container');
        const formEl = document.getElementById('creation-submit-form');
        const editTab = document.getElementById('tab-edit');
        const prevTab = document.getElementById('tab-preview');

        if (mode === 'preview') {
            updateLivePreview();
            previewBox.style.display = 'block';
            editTab.classList.remove('active');
            prevTab.classList.add('active');
            previewBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            previewBox.style.display = 'none';
            prevTab.classList.remove('active');
            editTab.classList.add('active');
        }
    }

    function updateLivePreview() {
        const title = document.getElementById('input-title').value.trim() || 'रचना का शीर्षक';
        const author = document.getElementById('input-author-name').value.trim() || 'रचनाकार का नाम';
        const category = document.getElementById('input-category').value;
        const excerpt = document.getElementById('input-excerpt').value.trim();
        const content = document.getElementById('input-content').value.trim() || 'यहाँ आपकी रचना का पाठ प्रदर्शित होगा...';

        document.getElementById('prev-title').textContent = title;
        document.getElementById('prev-author-name').textContent = author;
        document.getElementById('prev-category-badge').textContent = category;

        const excerptBox = document.getElementById('prev-excerpt-box');
        if (excerpt) {
            excerptBox.style.display = 'block';
            document.getElementById('prev-excerpt').textContent = excerpt;
        } else {
            excerptBox.style.display = 'none';
        }

        document.getElementById('prev-content').textContent = content;
    }

    // Submit handler via AJAX
    document.getElementById('creation-submit-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn-submit-form');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span>रचना भेजी जा रही है...</span>';

        const formData = new FormData(this);

        try {
            const response = await fetch('api/submit_creation.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('🎉 ' + result.message);
                window.location.href = 'index.php';
            } else {
                alert('त्रुटि: ' + (result.message || 'रचना सबमिट नहीं हो सकी।'));
            }
        } catch (err) {
            console.error(err);
            alert('सर्वर से संपर्क करने में समस्या आई। कृपया पुनः प्रयास करें।');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
    </script>
</body>
</html>
