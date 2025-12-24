<?php 
require_once 'config.php';
$page_title = "शब्द संचय - विचारों के नए प्रतिमान";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
    
</head>
<body>
    <!-- Page Loader -->
    <div id="page-loader" aria-label="Loading" role="status" aria-live="polite">
        <div class="loader-spinner"></div>
    </div>
  
    <?php include 'header.php'; ?>

   
    <main class="container">
        <section class="hero">
            <div class="hero-content">
                <h2>शब्द संचय के मंच पे आपका स्वागत है</h2>
                <p class="subheading">देश, समाज और काल को एक सूत्र मे बांधते हुए</p>
                <div class="hero-buttons">
                    <a href="poetry.php" class="btn btn-primary">कविताएँ</a>
                    <a href="articles.php" class="btn btn-secondary">लेख</a>
                </div>
            </div>
            <div class="intro fullscreen" id="about">
                <div class="intro-container">
                    <div class="intro-text">
                        <h\2>हमारे मंच के बारे में</h2>
                        <p>कविताओं, ब्लॉग्स और दैनिक शब्द भंडार के हमारे चयनित संग्रह के माध्यम से साहित्य की सुंदरता को खोजें। हमारा मिशन पाठकों और लेखकों का एक जीवंत समुदाय बनाना है जो शब्दों की शक्ति की सराहना करते हैं।</p>
                        <p>हमारा प्लेटफ़ॉर्म साहित्य प्रेमियों के लिए एक सुरक्षित आश्रय स्थल है, जहाँ शब्दों की मधुरता और विचारों की गहराई का सम्मान किया जाता है। प्रतिदिन नई रचनाएँ, साहित्यिक चर्चाएँ और रचनात्मक लेखन कार्यशालाएँ हमारे समुदाय को समृद्ध करती हैं।</p>                    </div>
                    <div class="intro-image">
                        <img src="https://picsum.photos/600/400" alt="साहित्यिक अवधारणा">
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-posts">
            <h2>प्रमुख रचनाएँ</h2>
            <div class="posts-grid" id="featured-posts-container">
                <!-- Content loaded via JavaScript -->
            </div>
        </section>

        <section class="categories">
            <h2>श्रेणियाँ</h2>
            <div class="categories-grid">
                <a href="poetry.php" class="category-card">
                    <div class="category-icon">✍️</div>
                    <h3>कविताएँ</h3>
                    <p>विभिन्न विषयों पर सुंदर कविताओं का संग्रह</p>
                </a>
                <a href="articles.php" class="category-card">
                    <div class="category-icon">📝</div>
                    <h3>लेख</h3>
                    <p>विचारोत्तेजक और ज्ञानवर्धक लेख</p>
                </a>
                <a href="stories.php" class="category-card">
                    <div class="category-icon">📚</div>
                    <h3>कहानियाँ</h3>
                    <p>मनोरंजक और शिक्षाप्रद कहानियाँ</p>
                </a>
                <a href="play.php" class="category-card">
                    <div class="category-icon">🎭</div>
                    <h3>नाटक</h3>
                    <p>हिंदी नाटक और एकांकी</p>
                </a>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>

    <script>
    // Add this script to handle page loading
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loader when everything is loaded
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
            setTimeout(function() {
                document.getElementById('page-loader').style.display = 'none';
            }, 500); // Match this with the CSS transition time
        });
        
        // In case load event doesn't fire, add a fallback
        setTimeout(function() {
            document.body.classList.add('loaded');
            document.getElementById('page-loader').style.display = 'none';
        }, 3000); // 3 seconds maximum wait time
        
        // Your existing featured content fetch
        async function fetchFeaturedContent() {
            try {
                const [poemsRes, articlesRes] = await Promise.all([
                    fetch('api/get_featured_poems.php'),
                    fetch('api/get_featured_articles.php')
                ]);

                const poems = await poemsRes.json();
                const articles = await articlesRes.json();

                renderFeaturedContent(poems, articles);
            } catch (error) {
                console.error('Error fetching featured content:', error);
                document.getElementById('featured-posts-container').innerHTML = 
                    '<p>प्रमुख रचनाएँ लोड करने में समस्या आई। कृपया बाद में पुनः प्रयास करें।</p>';
            }
        }

        function renderFeaturedContent(poems, articles) {
            const container = document.getElementById('featured-posts-container');
            let html = '';

            const allContent = [...poems, ...articles]
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 3);

            if (allContent.length === 0) {
                html = '<p>कोई प्रमुख रचनाएँ उपलब्ध नहीं हैं।</p>';
            } else {
                html = allContent.map(item => {
                    const isPoem = item.hasOwnProperty('poem_content');
                    return `
                    <article class="post-card">
                        <div class="post-image">
                            <img src="${item.image_url || 'images/default-post.jpg'}" alt="${item.title}" loading="lazy">
                        </div>
                        <div class="post-content">
                            <h3><a href="${isPoem ? 'poem' : 'article'}.php?id=${item.id}">${item.title}</a></h3>
                            <p class="post-meta">${isPoem ? 'कविता' : 'लेख'} • ${new Date(item.created_at).toLocaleDateString('hi-IN')}</p>
                            <p>${isPoem ? item.poem_content.split('\n')[0] : item.excerpt}</p>
                            <a href="${isPoem ? 'poem' : 'article'}.php?id=${item.id}" class="read-more">पूरा पढ़ें</a>
                        </div>
                    </article>
                    `;
                }).join('');
            }

            container.innerHTML = html;
        }

        fetchFeaturedContent();
    });
    </script>

    <script src="js/theme.js"></script>
</body>
</html>