
// Contact Form Submission
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Here you would typically send the form data to a server
        // For this example, we'll just show a success message
        
        const formSuccess = document.getElementById('formSuccess');
        contactForm.style.display = 'none';
        formSuccess.style.display = 'block';
        
        // Reset form after 3 seconds
        setTimeout(() => {
            contactForm.reset();
            contactForm.style.display = 'block';
            formSuccess.style.display = 'none';
        }, 3000);
    });
}

// FAQ Accordion Functionality
const faqQuestions = document.querySelectorAll('.faq-question');
if (faqQuestions.length > 0) {
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            faqItem.classList.toggle('active');
            
            // Close other open items
            faqQuestions.forEach(q => {
                if (q !== question && q.parentElement.classList.contains('active')) {
                    q.parentElement.classList.remove('active');
                }
            });
        });
    });
}

// Share Button Functionality
const shareButton = document.querySelector('.share-button');
if (shareButton) {
    shareButton.addEventListener('click', () => {
        const shareOptions = document.querySelector('.share-options');
        shareOptions.style.display = shareOptions.style.display === 'flex' ? 'none' : 'flex';
    });
}

// Like Button Functionality
const likeButton = document.querySelector('.like-button');
if (likeButton) {
    likeButton.addEventListener('click', () => {
        if (!likeButton.classList.contains('liked')) {
            const currentLikes = parseInt(likeButton.textContent.match(/\d+/)[0]);
            likeButton.innerHTML = `❤️ ${currentLikes + 1}`;
            likeButton.classList.add('liked');
        }
    });
}

// Smooth Scrolling for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});


// Lazy Loading Images
if ('IntersectionObserver' in window) {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                img.removeAttribute('loading');
                observer.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
}

// Form Validation
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = 'red';
                isValid = false;
                
                // Remove error style after user starts typing
                input.addEventListener('input', () => {
                    input.style.borderColor = '';
                });
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('कृपया सभी आवश्यक फ़ील्ड भरें');
        }
    });
});

// Poetry Filter Functionality (example)
const categoryFilter = document.getElementById('category-filter');
const sortBy = document.getElementById('sort-by');

if (categoryFilter && sortBy) {
    categoryFilter.addEventListener('change', filterPoems);
    sortBy.addEventListener('change', filterPoems);
}

function filterPoems() {
    // In a real implementation, this would filter and sort the poems
    console.log('Filtering poems by:', categoryFilter.value, 'and sorting by:', sortBy.value);
    // You would typically fetch filtered/sorted data from a server or filter client-side
}

// Initialize animations
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.hero-content, .hero-image, .post-card, .category-card');
    
    animatedElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 100);
    });
});


// Mobile Menu Functionality
function setupMobileMenu() {
    const mobileMenuButton = document.createElement('button');
    mobileMenuButton.className = 'mobile-menu-button';
    mobileMenuButton.innerHTML = '☰';
    mobileMenuButton.setAttribute('aria-label', 'Toggle menu');
    
    const header = document.querySelector('.site-header .container');
    if (header) {
        header.prepend(mobileMenuButton);
        
        mobileMenuButton.addEventListener('click', () => {
            const nav = document.querySelector('.main-nav');
            nav.classList.toggle('active');
            mobileMenuButton.innerHTML = nav.classList.contains('active') ? '✕' : '☰';
        });
    }
}

// Call this function when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    setupMobileMenu();
    // ... rest of your existing DOMContentLoaded code
});

