import PlainWelcome from './components/PlainWelcome.js';
import '../css/app.css';

// Mount the PlainWelcome component
const rootElement = document.getElementById('welcome-root');
if (rootElement) {
    const welcomeComponent = PlainWelcome();
    rootElement.appendChild(welcomeComponent);
    
    // Add interactive effects after mounting
    setTimeout(() => {
        addInteractiveEffects();
    }, 100);
}

function addInteractiveEffects() {
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add floating animation to background elements
    const floatingElements = document.querySelectorAll('.absolute.bg-yellow-400, .absolute.bg-blue-300, .absolute.bg-yellow-300, .absolute.bg-blue-400');
    floatingElements.forEach((element, index) => {
        element.style.animation = `float ${3 + index * 0.5}s ease-in-out infinite`;
    });
    
    // Add parallax effect on scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.absolute.bg-yellow-400, .absolute.bg-blue-300');
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + index * 0.1;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
    
    // Add typing effect to main title
    const titleElement = document.querySelector('h1 span:first-child');
    if (titleElement) {
        const text = titleElement.textContent;
        titleElement.textContent = '';
        titleElement.style.borderRight = '2px solid #FCD34D';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                titleElement.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            } else {
                setTimeout(() => {
                    titleElement.style.borderRight = 'none';
                }, 500);
            }
        };
        
        setTimeout(typeWriter, 1000);
    }
    
    // Add counter animation for stats
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = target.textContent;
                const isPercentage = finalValue.includes('%');
                const numericValue = parseInt(finalValue.replace(/[^0-9]/g, ''));
                
                let currentValue = 0;
                const increment = numericValue / 50;
                
                const counter = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= numericValue) {
                        currentValue = numericValue;
                        clearInterval(counter);
                    }
                    target.textContent = Math.floor(currentValue) + (isPercentage ? '%' : '+');
                }, 30);
                
                observer.unobserve(target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.text-4xl.font-bold.text-yellow-400').forEach(stat => {
        observer.observe(stat);
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-10px) rotate(1deg); }
        66% { transform: translateY(5px) rotate(-1deg); }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .feature-card {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .feature-card:nth-child(1) { animation-delay: 0.1s; }
    .feature-card:nth-child(2) { animation-delay: 0.2s; }
    .feature-card:nth-child(3) { animation-delay: 0.3s; }
    .feature-card:nth-child(4) { animation-delay: 0.4s; }
    .feature-card:nth-child(5) { animation-delay: 0.5s; }
    .feature-card:nth-child(6) { animation-delay: 0.6s; }
    
    .tech-badge {
        transition: all 0.3s ease;
    }
    
    .tech-badge:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
`;
document.head.appendChild(style);

export default PlainWelcome;