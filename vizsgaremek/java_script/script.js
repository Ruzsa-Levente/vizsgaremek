// Global cart array
let cart = [];
let currentIndex = 0;

// Add product to cart (with quantity handling)
function addToCart(productName, price, imageUrl) {
    let existingProduct = cart.find(item => item.name === productName);

    if (existingProduct) {
        existingProduct.quantity += 1;
    } else {
        cart.push({ name: productName, price: price, imageUrl: imageUrl, quantity: 1 });
    }

    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Increase quantity of a product
function increaseQuantity(productName) {
    let product = cart.find(item => item.name === productName);

    if (product) {
        product.quantity += 1;
    }

    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Remove product from cart (reduce quantity or remove if 1)
function removeFromCart(productName) {
    let productIndex = cart.findIndex(item => item.name === productName);

    if (productIndex !== -1) {
        if (cart[productIndex].quantity > 1) {
            cart[productIndex].quantity -= 1;
        } else {
            cart.splice(productIndex, 1);
        }
    }

    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Delete product from cart instantly
function deleteFromCart(productName) {
    cart = cart.filter(item => item.name !== productName);
    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Update receipt display
function updateReceipt() {
    const cartItems = document.getElementById('cart-items');
    const totalDisplay = document.getElementById('total');

    if (!cartItems || !totalDisplay) return;

    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        const li = document.createElement('li');
        li.classList.add("cart-item");

        li.innerHTML = `
            <div class="cart-item-content">
                <img src="${item.imageUrl}" alt="${item.name}" class="cart-item-image">
                <span class="cart-item-name">${item.name}</span>
                <span class="cart-item-price">${(item.price * item.quantity).toFixed(2)} Ft</span>
                <button class="quantity-btn" onclick="removeFromCart('${item.name}')">−</button>
                <span class="quantity-display">${item.quantity}</span>
                <button class="quantity-btn" onclick="increaseQuantity('${item.name}')">+</button>
                <button class="remove-btn" onclick="deleteFromCart('${item.name}')">Remove</button>
            </div>
        `;

        cartItems.appendChild(li);
        total += item.price * item.quantity;
    });

    totalDisplay.textContent = `Total: ${total.toFixed(2)} Ft`;
}

// Update cart count on the index page
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (!cartCount) return;

    let itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = itemCount;
}

// Save cart to localStorage
function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Load cart from localStorage
function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateReceipt();
        updateCartCount();
    }
}

// Redirect to product page on click
function redirectToProduct(productId) {
    window.location.href = 'product.php?id=' + productId;
}

// Toggle cart sidebar
function toggleCart() {
    document.getElementById('cart-sidebar').classList.toggle('active');
}

// Search bar toggle and functionality
function toggleSearch() {
    const searchContainer = document.querySelector('.search-container');
    const searchInput = document.getElementById('search-input');
    searchContainer.classList.toggle('active');
    if (searchContainer.classList.contains('active')) {
        searchInput.focus();
    }
}

function hideSearch() {
    const searchInput = document.getElementById('search-input');
    const searchContainer = document.querySelector('.search-container');
    if (!searchInput.value) {
        searchContainer.classList.remove('active');
    }
}

// Product page slider
function showImage(index) {
    const slides = document.querySelectorAll(".slide");
    slides.forEach((slide, i) => {
        slide.classList.remove("active");
        if (i === index) {
            slide.classList.add("active");
        }
    });
}

function prevImage() {
    const slides = document.querySelectorAll(".slide");
    currentIndex = (currentIndex === 0) ? slides.length - 1 : currentIndex - 1;
    showImage(currentIndex);
}

function nextImage() {
    const slides = document.querySelectorAll(".slide");
    currentIndex = (currentIndex === slides.length - 1) ? 0 : currentIndex + 1;
    showImage(currentIndex);
}

// Scroll animations for sections
function handleScrollAnimations() {
    const sections = document.querySelectorAll('.marketing-section, .extended-marketing-section');
    sections.forEach(section => {
        const position = section.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        if (position < windowHeight) {
            section.classList.add('visible');
        }
    });
}

// Suggested products scrolling
function scrollSuggestedLeft() {
    document.querySelector(".suggested-slider").scrollBy({ left: -220, behavior: "smooth" });
}

function scrollSuggestedRight() {
    document.querySelector(".suggested-slider").scrollBy({ left: 220, behavior: "smooth" });
}

// DOM Content Loaded Event Listener
document.addEventListener('DOMContentLoaded', function () {
    // Load cart
    loadCartFromLocalStorage();

    // Initialize Swiper for homepage
    if (document.querySelector(".swiper")) {
        new Swiper(".swiper", {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            }
        });
    }

    // Search bar enter key functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    window.location.href = `clothes.php?search=${encodeURIComponent(searchTerm)}`;
                }
            }
        });
    }

    // Order summarizing process for billing.php
    const placeOrderBtn = document.getElementById("place-order-btn");
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener("click", function () {
            const cartItems = cart.map(item => ({
                name: item.name,
                price: item.price * item.quantity,
                quantity: item.quantity
            }));

            if (cartItems.length === 0) {
                alert("Your cart is empty!");
                return;
            }

            const total = cartItems.reduce((sum, item) => sum + item.price, 0).toFixed(2);
            const deliveryMethod = document.getElementById("delivery").value;

            const orderDetails = {
                cartItems: cartItems,
                total: total,
                delivery: deliveryMethod
            };
            localStorage.setItem("orderDetails", JSON.stringify(orderDetails));
            window.location.href = "pay.php";
        });
    }

    // Order summary for pay.php
    const orderSummary = document.getElementById("order-summary");
    if (orderSummary) {
        const orderDetails = JSON.parse(localStorage.getItem("orderDetails"));

        if (!orderDetails || orderDetails.cartItems.length === 0) {
            orderSummary.innerHTML = "<p>No order found.</p>";
            return;
        }

        const orderItems = document.getElementById("order-items");
        orderDetails.cartItems.forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.name} (x${item.quantity}) - Total: Ft${item.price.toFixed(2)}`;
            orderItems.appendChild(li);
        });

        document.getElementById("order-total").textContent = `Total: Ft${orderDetails.total}`;
        const deliveryMethod = orderDetails.delivery === "home" ? "Home Delivery" : "In-Store Pickup";
        document.getElementById("delivery-method").textContent = `Delivery Method: ${deliveryMethod}`;

        if (orderDetails.delivery === "home") {
            document.getElementById("delivery-form").style.display = "block";
        }
    }

    // Payment processing for pay.php
    const payNowBtn = document.getElementById("pay-now-btn");
    if (payNowBtn) {
        payNowBtn.addEventListener("click", function (event) {
            const orderDetails = JSON.parse(localStorage.getItem("orderDetails"));
            if (!orderDetails || orderDetails.cartItems.length === 0) {
                alert("Your cart is empty!");
                return;
            }

            if (orderDetails.delivery === "home") {
                const name = document.getElementById("name").value.trim();
                const address = document.getElementById("address").value.trim();
                const city = document.getElementById("city").value.trim();
                const zip = document.getElementById("zip").value.trim();
                const phone = document.getElementById("phone").value.trim();

                if (!name || !address || !city || !zip || !phone) {
                    alert("Please fill in all shipping details before proceeding!");
                    event.preventDefault();
                    return;
                }
            }

            fetch("php/process_payment.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(orderDetails)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Payment successful! Redirecting...");
                    localStorage.removeItem("cart");
                    localStorage.removeItem("orderDetails");
                    window.location.href = "succes.php";
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    }

    // Back to top button
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});

// Scroll event listener for animations
window.addEventListener('scroll', handleScrollAnimations);
