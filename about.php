<?php
require_once 'config.php';
$page_title = "हमारे बारे में - शब्द संचय";
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
        <section class="intro fullscreen" id="about">
            <div class="intro-container">
                <div class="intro-text">
                    <h2>हमारे मंच के बारे में</h2>
                    <p>कविताओं, ब्लॉग्स और दैनिक शब्द भंडार के हमारे चयनित संग्रह के माध्यम से साहित्य की सुंदरता को खोजें। हमारा मिशन पाठकों और लेखकों का एक जीवंत समुदाय बनाना है जो शब्दों की शक्ति की सराहना करते हैं।</p>
                    <p>हमारा प्लेटफ़ॉर्म साहित्य प्रेमियों के लिए एक सुरक्षित आश्रय स्थल है, जहाँ शब्दों की मधुरता और विचारों की गहराई का सम्मान किया जाता है। प्रतिदिन नई रचनाएँ, साहित्यिक चर्चाएँ और रचनात्मक लेखन कार्यशालाएँ हमारे समुदाय को समृद्ध करती हैं।</p>
                    <p>हमारी टीम में अनुभवी साहित्यकार, युवा रचनाकार और भाषा विशेषज्ञ शामिल हैं जो निरंतर गुणवत्तापूर्ण सामग्री प्रदान करने के लिए समर्पित हैं। साथ ही, हम नवोदित लेखकों को मंच प्रदान करके हिन्दी साहित्य के विकास में योगदान देते हैं।</p>
                </div>
                <div class="intro-image">
                    <img src="https://picsum.photos/600/400" alt="साहित्यिक अवधारणा">
                </div>
            </div>
        </section>

      <!-- Replace the team-members section with this enhanced version -->
<section class="team-section">
    <div class="container">
        <h2>हमारी टीम</h2>
        <div class="team-members">
            <?php
            $team_query = "SELECT * FROM team_members ORDER BY display_order";
            $team_result = $conn->query($team_query);
            
            if ($team_result && $team_result->num_rows > 0) {
                while ($member = $team_result->fetch_assoc()) {
                    $social_links = json_decode($member['social_links'], true);
                    echo '<div class="team-card">';
                    echo '<div class="team-image">';
                    echo '<img src="'.($member['image_url'] ?: 'images/team/default.jpg').'" alt="'.htmlspecialchars($member['name']).'">';
                    echo '</div>';
                    echo '<div class="team-info">';
                    echo '<h3>'.htmlspecialchars($member['name']).'</h3>';
                    echo '<p class="role">'.htmlspecialchars($member['role']).'</p>';
                    if ($member['bio']) {
                        echo '<p class="bio">'.htmlspecialchars($member['bio']).'</p>';
                    }
                    echo '<div class="team-social">';
                    if (!empty($social_links['twitter'])) {
                        echo '<a href="'.htmlspecialchars($social_links['twitter']).'" target="_blank"><i class="fab fa-twitter"></i></a>';
                    }
                    if (!empty($social_links['facebook'])) {
                        echo '<a href="'.htmlspecialchars($social_links['facebook']).'" target="_blank"><i class="fab fa-facebook"></i></a>';
                    }
                    if (!empty($social_links['instagram'])) {
                        echo '<a href="'.htmlspecialchars($social_links['instagram']).'" target="_blank"><i class="fab fa-instagram"></i></a>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="no-team">टीम की जानकारी उपलब्ध नहीं है।</p>';
            }
            ?>
        </div>
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
    });
    </script>
    <script src="js/theme.js"></script>
</body>
</html>
