// Toast notifications
function showToast(msg, type='success') {
    const icons = { success:'fas fa-check-circle', error:'fas fa-times-circle', info:'fas fa-info-circle' };
    const container = document.getElementById('toastContainer');
    if(!container) return;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
    toast.innerHTML = `<i class="${icons[type]||icons.info}"></i><span>${msg}</span>`;
    container.appendChild(toast);
    
    // Trigger animation
    requestAnimationFrame(() => {
        toast.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => { 
        toast.style.opacity='0'; 
        toast.style.transform='translateX(50px)'; 
        setTimeout(() => toast.remove(), 400); 
    }, 4000);
}

// Mobile Search Toggle
const mobileSearchToggle = document.getElementById('mobileSearchToggle');
if(mobileSearchToggle) {
    mobileSearchToggle.addEventListener('click', () => {
        const searchBar = document.getElementById('searchBar');
        searchBar?.classList.toggle('open');
        if(searchBar?.classList.contains('open')) {
            document.getElementById('searchInput')?.focus();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
}

// Scroll Reveal Animations
function initScrollReveal() {
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card, .category-card, .section-header, .promo-content, .auth-box').forEach(el => {
        el.classList.add('reveal-hidden');
        observer.observe(el);
    });
}

// Add CSS for reveal animations dynamically
const style = document.createElement('style');
style.textContent = `
    .reveal-hidden { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1); }
    .reveal-visible { opacity: 1; transform: translateY(0); }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
});

// Sticky header
const header = document.getElementById('siteHeader');
window.addEventListener('scroll', () => {
    if(header) header.classList.toggle('scrolled', window.scrollY > 60);
});

// Mobile menu
const mobileBtn = document.getElementById('mobileMenuBtn');
const mainNav = document.getElementById('mainNav');
const navOverlay = document.getElementById('navOverlay');
if(mobileBtn) {
    mobileBtn.addEventListener('click', () => {
        mainNav?.classList.toggle('open');
        navOverlay?.classList.toggle('open');
    });
    navOverlay?.addEventListener('click', () => {
        mainNav?.classList.remove('open');
        navOverlay?.classList.remove('open');
    });
}

// Search toggle (Desktop)
const searchToggle = document.getElementById('searchToggle');
const searchBar = document.getElementById('searchBar');
if(searchToggle) {
    searchToggle.addEventListener('click', () => {
        searchBar?.classList.toggle('open');
        if(searchBar?.classList.contains('open')) {
            document.getElementById('searchInput')?.focus();
        }
    });
}

// Live search
const searchInput = document.getElementById('searchInput');
const suggestions = document.getElementById('searchSuggestions');
let searchTimer;
if(searchInput && suggestions) {
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        const q = searchInput.value.trim();
        if(q.length < 2) { suggestions.classList.remove('open'); suggestions.innerHTML=''; return; }
        searchTimer = setTimeout(async () => {
            const res = await fetch(`ajax/search.php?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            if(data.length) {
                suggestions.innerHTML = data.map(p => `
                    <a href="product.php?slug=${p.slug}" class="suggestion-item">
                        <img src="assets/uploads/products/${p.image||''}" onerror="this.src='assets/images/no-product.svg'" alt="${p.name}">
                        <div>
                            <div style="font-weight:600;font-size:0.88rem">${p.name}</div>
                            <div style="color:var(--pink);font-size:0.82rem;font-weight:700">${p.price_fmt}</div>
                        </div>
                    </a>`).join('');
                suggestions.classList.add('open');
            } else {
                suggestions.innerHTML = '<div class="suggestion-item"><span>No products found</span></div>';
                suggestions.classList.add('open');
            }
        }, 300);
    });
    document.addEventListener('click', e => { if(!e.target.closest('#searchBar')) suggestions.classList.remove('open'); });
}

// Add to cart
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.btn-cart');
    if(!btn) return;
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    try {
        const res = await fetch('ajax/cart.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `action=add&product_id=${btn.dataset.id}&qty=${document.getElementById('productQty')?.value||1}`
        });
        const data = await res.json();
        if(data.success) {
            showToast(data.message,'success');
            document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.count > 0 ? data.count : '');
        } else { showToast(data.message,'error'); }
    } catch(err) { showToast('Failed to add to cart','error'); }
    btn.disabled = false;
    btn.innerHTML = originalContent;
});

// Wishlist toggle
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.wishlist-btn');
    if(!btn) return;
    try {
        const res = await fetch('ajax/wishlist.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`product_id=${btn.dataset.id}`
        });
        const data = await res.json();
        if(data.login_required) { 
            showToast('Please login to add to wishlist', 'error');
            setTimeout(() => window.location.href = 'login.php', 1500);
            return; 
        }
        if(data.success) {
            btn.classList.toggle('active', data.action === 'added');
            const icon = btn.querySelector('i');
            if(icon) {
                icon.className = data.action === 'added' ? 'fas fa-heart' : 'far fa-heart';
            }
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Error adding to wishlist', 'error');
        }
    } catch(err) { showToast('Error','error'); }
});

// Newsletter
document.querySelectorAll('.newsletter-form, #mainNewsletter').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = form.querySelector('input[name="email"]').value;
        try {
            const res = await fetch('ajax/newsletter.php', {
                method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`email=${encodeURIComponent(email)}`
            });
            const data = await res.json();
            showToast(data.message, data.success ? 'success' : 'error');
            if(data.success) form.reset();
        } catch { showToast('Error','error'); }
    });
});

// Countdown timer
function startCountdown(endTime) {
    const elements = { days: document.getElementById('cd-days'), hours: document.getElementById('cd-hours'), mins: document.getElementById('cd-mins'), secs: document.getElementById('cd-secs') };
    if(!elements.secs) return;
    const update = () => {
        const diff = endTime - Date.now();
        if(diff <= 0) { Object.values(elements).forEach(el => el && (el.textContent = '00')); return; }
        elements.days && (elements.days.textContent = String(Math.floor(diff/86400000)).padStart(2,'0'));
        elements.hours && (elements.hours.textContent = String(Math.floor((diff%86400000)/3600000)).padStart(2,'0'));
        elements.mins && (elements.mins.textContent = String(Math.floor((diff%3600000)/60000)).padStart(2,'0'));
        elements.secs && (elements.secs.textContent = String(Math.floor((diff%60000)/1000)).padStart(2,'0'));
    };
    update(); setInterval(update, 1000);
}
startCountdown(Date.now() + 3 * 24 * 3600000);

// Product gallery
function initGallery() {
    const main = document.getElementById('galleryMain');
    const thumbs = document.querySelectorAll('.gallery-thumb');
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            if(main) main.src = thumb.dataset.img;
        });
    });
}
initGallery();

// Qty controls
function initQtyControls() {
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.qty-value, input[name="qty"]');
            if(!input) return;
            let val = parseInt(input.value) || 1;
            if(btn.dataset.action === 'plus') val++;
            else if(btn.dataset.action === 'minus' && val > 1) val--;
            input.value = val;
        });
    });
}
initQtyControls();

// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const parent = btn.closest('.product-tabs');
        parent?.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        parent?.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        const tabContent = document.getElementById(btn.dataset.tab);
        if(tabContent) tabContent.classList.add('active');
    });
});

// Payment option selector
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');
        option.querySelector('input[type="radio"]').checked = true;
    });
});

// Cart quantity update (cart page)
document.addEventListener('change', async (e) => {
    const input = e.target.closest('.cart-qty-input');
    if(!input) return;
    const cartId = input.dataset.id;
    const qty = parseInt(input.value);
    try {
        const res = await fetch('ajax/cart.php', {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`action=update&cart_id=${cartId}&qty=${qty}`
        });
        const data = await res.json();
        if(data.success) { showToast('Cart updated','success'); setTimeout(()=>location.reload(),600); }
    } catch {}
});

// Smooth scroll for anchors
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
        const target = document.querySelector(a.getAttribute('href'));
        if(target) { e.preventDefault(); target.scrollIntoView({behavior:'smooth'}); }
    });
});

// Filter sidebar price range display
const priceMin = document.getElementById('priceMin');
const priceMax = document.getElementById('priceMax');
const priceDisplay = document.getElementById('priceDisplay');
if(priceMin && priceMax && priceDisplay) {
    const update = () => priceDisplay.textContent = `Rs. ${parseInt(priceMin.value).toLocaleString()} - Rs. ${parseInt(priceMax.value).toLocaleString()}`;
    priceMin.addEventListener('input', update); priceMax.addEventListener('input', update); update();
}
