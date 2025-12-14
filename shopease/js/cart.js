/**
 * ShopEase - Cart JavaScript
 * Handles cart-specific functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Real-time cart total calculation
    function updateCartTotal() {
        const cartItems = document.querySelectorAll('.cart-item');
        let total = 0;

        cartItems.forEach(item => {
            const priceText = item.querySelector('.item-price').textContent;
            const price = parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
            const quantity = parseInt(item.querySelector('input[name="quantity"]').value);
            
            const subtotal = price * quantity;
            total += subtotal;

            // Update item subtotal display
            const subtotalElement = item.querySelector('.item-subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = '₹' + subtotal.toFixed(2);
            }
        });

        // Update summary total
        const totalElements = document.querySelectorAll('.summary-total span:last-child');
        totalElements.forEach(el => {
            el.textContent = '₹' + total.toFixed(2);
        });

        // Update subtotal
        const subtotalElements = document.querySelectorAll('.summary-row:first-child span:last-child');
        subtotalElements.forEach(el => {
            el.textContent = '₹' + total.toFixed(2);
        });
    }

    // Quantity change listeners
    const quantityInputs = document.querySelectorAll('.cart-item input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateCartTotal();
        });
    });

    // Initial calculation
    updateCartTotal();

    // Quantity increment/decrement buttons (if added in future)
    function createQuantityControls() {
        quantityInputs.forEach(input => {
            const container = input.parentElement;
            const currentValue = parseInt(input.value);
            const max = parseInt(input.getAttribute('max')) || 999;

            // Create minus button
            const minusBtn = document.createElement('button');
            minusBtn.textContent = '-';
            minusBtn.type = 'button';
            minusBtn.className = 'qty-btn qty-minus';
            minusBtn.onclick = function() {
                const val = parseInt(input.value);
                if (val > 1) {
                    input.value = val - 1;
                    updateCartTotal();
                }
            };

            // Create plus button
            const plusBtn = document.createElement('button');
            plusBtn.textContent = '+';
            plusBtn.type = 'button';
            plusBtn.className = 'qty-btn qty-plus';
            plusBtn.onclick = function() {
                const val = parseInt(input.value);
                if (val < max) {
                    input.value = val + 1;
                    updateCartTotal();
                }
            };

            // Insert buttons (optional enhancement)
            // container.insertBefore(minusBtn, input);
            // container.appendChild(plusBtn);
        });
    }

    // Remove item animation
    const removeButtons = document.querySelectorAll('.remove-btn');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const cartItem = this.closest('.cart-item');
            if (cartItem && confirm('Remove this item from cart?')) {
                cartItem.style.transition = 'opacity 0.3s, transform 0.3s';
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    // The page will reload after PHP processing
                }, 300);
            } else if (cartItem) {
                e.preventDefault();
            }
        });
    });

    // Cart empty state
    const cartItems = document.querySelectorAll('.cart-item');
    if (cartItems.length === 0) {
        const emptyMessage = document.createElement('div');
        emptyMessage.className = 'empty-cart';
        emptyMessage.innerHTML = `
            <p>Your cart is empty</p>
            <a href="products.php" class="btn">Continue Shopping</a>
        `;
        
        const cartSection = document.querySelector('.cart-items');
        if (cartSection) {
            cartSection.innerHTML = '';
            cartSection.appendChild(emptyMessage);
        }
    }

    // Save cart updates
    const updateForms = document.querySelectorAll('.quantity-form');
    updateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.textContent = 'Updating...';
                submitBtn.disabled = true;
            }
        });
    });
});