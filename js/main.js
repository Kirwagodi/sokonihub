document.addEventListener('DOMContentLoaded', function() {


    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.4s';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });

    
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const originalText = this.textContent;

            this.textContent = 'Adding...';
            this.disabled = true;

            fetch('cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&product_id=${productId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.textContent = '✓ Added!';
                    this.style.background = '#18a558';
                    // Update cart badge
                    const badge = document.querySelector('.cart-badge');
                    if (badge) badge.textContent = data.cart_count;
                    else {
                        const cartBtn = document.querySelector('.cart-btn');
                        if (cartBtn) {
                            const span = document.createElement('span');
                            span.className = 'cart-badge';
                            span.textContent = data.cart_count;
                            cartBtn.appendChild(span);
                        }
                    }
                } else {
                    this.textContent = data.message || 'Login first';
                    this.style.background = '#e8391a';
                }
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.disabled = false;
                }, 2000);
            })
            .catch(() => {
                this.textContent = 'Error';
                this.disabled = false;
            });
        });
    });

    
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const cartId = this.dataset.cartId;

            fetch('cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=${action}&cart_id=${cartId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    });

   
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Remove this item from cart?')) return;
            const cartId = this.dataset.cartId;

            fetch('cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&cart_id=${cartId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    });

   
    const categoryEmojis = {
        'Electronics': '📱',
        'Fashion': '👗',
        'Home & Kitchen': '🏠',
        'Sports & Fitness': '🏋️',
        'Books & Education': '📚',
        'default': '🛍️'
    };

    
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                // Auto-submit after 1s
            }, 1000);
        });
    }

    
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

   
    const urlParams = new URLSearchParams(window.location.search);
    const activeCat = urlParams.get('cat');
    if (activeCat) {
        document.querySelectorAll('.cat-nav a').forEach(link => {
            if (link.href.includes('cat=' + activeCat)) link.classList.add('active');
        });
    } else if (window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/')) {
        const firstCatLink = document.querySelector('.cat-nav a:first-child');
        if (firstCatLink) firstCatLink.classList.add('active');
    }

});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 24px; right: 24px; z-index: 9999;
        background: ${type === 'success' ? '#18a558' : '#e8391a'};
        color: white; padding: 14px 22px; border-radius: 12px;
        font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 0.9rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        transform: translateY(20px); opacity: 0;
        transition: all 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
