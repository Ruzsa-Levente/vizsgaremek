// Globális kosár tömb
let cart = [];
let currentIndex = 0;

// Termék kosárba helyezése (méret- és mennyiségkezeléssel)
function addToCart(productName, price, imageUrl, size) {
    if (!size || size === '') {
        alert('Kérlek, válassz méretet!');
        return;
    }

    let existingProduct = cart.find(item => item.name === productName && item.size === size);

    if (existingProduct) {
        existingProduct.quantity += 1;
    } else {
        cart.push({ name: productName, price: price, imageUrl: imageUrl, size: size, quantity: 1 });
    }

    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Termék mennyiségének növelése
function increaseQuantity(productName, size) {
    let product = cart.find(item => item.name === productName && item.size === size);

    if (product) {
        product.quantity += 1;
    }

    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Termék eltávolítása a kosárból (mennyiség csökkentése vagy törlés, ha 1)
function removeFromCart(productName, size) {
    let productIndex = cart.findIndex(item => item.name === productName && item.size === size);

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

// Termék azonnali törlése a kosárból
function deleteFromCart(productName, size) {
    cart = cart.filter(item => item.name !== productName || item.size !== size);
    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}

// Kosár tartalmának frissítése a képernyőn
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
                <span class="cart-item-name">${item.name} (${item.size})</span>
                <span class="cart-item-price">${(item.price * item.quantity).toFixed(2)} Ft</span>
            </div>
            <div class="quantity-controls">
                <button class="quantity-btn" onclick="removeFromCart('${item.name}', '${item.size}')">−</button>
                <span class="quantity-display">${item.quantity}</span>
                <button class="quantity-btn" onclick="increaseQuantity('${item.name}', '${item.size}')">+</button>
            </div>
            <button class="remove-btn" onclick="deleteFromCart('${item.name}', '${item.size}')"><i class="fas fa-times"></i></button>
        `;

        cartItems.appendChild(li);
        total += item.price * item.quantity;
    });

    totalDisplay.textContent = `Összesen: ${total.toFixed(2)} Ft`;
}

// Kosár darabszám frissítése az index oldalon
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (!cartCount) return;

    let itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = itemCount;
}

// Kosár mentése a localStorage-ba
function saveCartToLocalStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Kosár betöltése a localStorage-ból
function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateReceipt();
        updateCartCount();
    }
}

// Átirányítás a termékoldalra
function redirectToProduct(productId) {
    window.location.href = 'product.php?id=' + productId;
}

// Kosár oldalsáv váltása
function toggleCart() {
    document.getElementById('cart-sidebar').classList.toggle('active');
}

// Keresősáv váltása és működése
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

// Termékoldal slider
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

// Görgetési animációk a szekciókhoz
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

// Javasolt termékek görgetése
function scrollSuggestedLeft() {
    document.querySelector(".suggested-slider").scrollBy({ left: -220, behavior: "smooth" });
}

function scrollSuggestedRight() {
    document.querySelector(".suggested-slider").scrollBy({ left: 220, behavior: "smooth" });
}

// DOM betöltési eseménykezelő
document.addEventListener('DOMContentLoaded', function () {
    // Kosár betöltése
    loadCartFromLocalStorage();

    // Swiper inicializálása a főoldalon
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

// Rendelés összefoglalása a billing.php oldalon
const placeOrderBtn = document.getElementById("place-order-btn");
if (placeOrderBtn) {
    placeOrderBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();

        if (!name || !email || !phone) {
            alert("Töltsd ki az összes mezőt!");
            return;
        }

        const cartItems = cart.map(item => ({
            name: item.name,
            price: item.price * item.quantity,
            size: item.size,
            quantity: item.quantity
        }));

        if (cartItems.length === 0) {
            alert("A kosarad üres!");
            return;
        }

        const total = cartItems.reduce((sum, item) => sum + item.price, 0).toFixed(2);
        const deliveryMethod = document.getElementById("delivery").value;

        const orderDetails = {
            cartItems: cartItems,
            total: total,
            delivery: deliveryMethod,
            customer: {
                name: name,
                email: email,
                phone: phone
            }
        };
        
        localStorage.setItem("orderDetails", JSON.stringify(orderDetails));
        window.location.href = "pay.php";
    });
}

    // Rendelés összefoglaló a pay.php oldalon
    const orderSummary = document.getElementById("order-summary");
    if (orderSummary) {
        const orderDetails = JSON.parse(localStorage.getItem("orderDetails"));

        if (!orderDetails || orderDetails.cartItems.length === 0) {
            orderSummary.innerHTML = "<p>Nincs rendelés.</p>";
            return;
        }

        const orderItems = document.getElementById("order-items");
        orderDetails.cartItems.forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.name} (${item.size}) db: ${item.quantity} - Ár: ${item.price.toFixed(2)} Ft`;
            orderItems.appendChild(li);
        });

        document.getElementById("order-total").textContent = `Összesen: ${orderDetails.total} Ft`;
        const deliveryMethod = orderDetails.delivery === "home" ? "Házhozszállítás" : "Áruházi átvétel";
        document.getElementById("delivery-method").textContent = `Szállítási mód: ${deliveryMethod}`;

        if (orderDetails.delivery === "home") {
            document.getElementById("delivery-form").style.display = "block";
        }

        if (orderDetails.delivery === "pickup") {
            document.getElementById("delivery-form2").style.display = "block";
        }
    }

// Fizetés feldolgozása a pay.php oldalon
const payNowBtn = document.getElementById("pay-now-btn");
if (payNowBtn) {
    payNowBtn.addEventListener("click", function (event) {
        const orderDetails = JSON.parse(localStorage.getItem("orderDetails"));
        if (!orderDetails || orderDetails.cartItems.length === 0) {
            alert("A kosarad üres!");
            return;
        }

        let shippingData = {};
        if (orderDetails.delivery === "home") {
            shippingData = {
                name: document.getElementById("name").value.trim(),
                address: document.getElementById("address").value.trim(),
                city: document.getElementById("city").value.trim(),
                zip: document.getElementById("zip").value.trim(),
                phone: document.getElementById("phone").value.trim(),
                email: document.getElementById("email").value.trim()
            };

            if (!shippingData.name || !shippingData.address || !shippingData.city || !shippingData.zip || !shippingData.phone || !shippingData.email) {
                alert("Kérlek, töltsd ki az összes szállítási adatot!");
                event.preventDefault();
                return;
            }
        } else if (orderDetails.delivery === "pickup") {
            shippingData = {
                name: document.getElementById("name2").value.trim(),
                phone: document.getElementById("phone2").value.trim(),
                email: document.getElementById("email2").value.trim()
            };

            if (!shippingData.name || !shippingData.phone || !shippingData.email) {
                alert("Kérlek, töltsd ki az összes kontakt adatot!");
                event.preventDefault();
                return;
            }
        }

        orderDetails.shipping = shippingData;

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
                alert("Sikeres fizetés! Átirányítás...");
                localStorage.removeItem("cart");
                localStorage.removeItem("orderDetails");
                window.location.href = `succes.php?rendeles_id=${data.rendeles_id}`; // Rendelés ID átadása
            } else {
                alert("Hiba: " + data.message);
            }
        })
        .catch(error => console.error("Hiba:", error));
    });
}
    // Vissza a tetejére gomb
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

// Görgetési eseménykezelő az animációkhoz
window.addEventListener('scroll', handleScrollAnimations);

// Elemet lekérdezése
const searchToggle = document.getElementById('search-toggle');
const searchContainer = document.querySelector('.search-container');
const searchInput = document.getElementById('search-input');

// Keresősáv láthatóságának váltása
searchToggle.addEventListener('click', function(event) {
    event.stopPropagation();
    searchContainer.classList.toggle('active');
    if (searchContainer.classList.contains('active')) {
        searchInput.focus();
    }
});

// Handle search form submission (Enter key press)
searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault(); // Prevent form submission in some cases
        const searchTerm = this.value.trim();
        
        if (searchTerm) {
            // Ha van keresési kifejezés
            window.location.href = `clothes.php?search=${encodeURIComponent(searchTerm)}`;
        } else {
            // Ha üres a keresőmező, akkor visszairányít minden termékre
            window.location.href = 'clothes.php';
        }
    }
});


// Keresősáv bezárása kattintás kívülre
document.addEventListener('click', function(event) {
    if (!searchContainer.contains(event.target) && searchContainer.classList.contains('active')) {
        searchContainer.classList.remove('active');
    }
});

// Kattintások megakadályozása a keresősávon belül
searchContainer.addEventListener('click', function(event) {
    event.stopPropagation();
});

// Javasolt termékek görgetése
function scrollLeft() {
    document.querySelector(".recommended-container").scrollBy({ left: -200, behavior: "smooth" });
}

function scrollRight() {
    document.querySelector(".recommended-container").scrollBy({ left: 200, behavior: "smooth" });
}


document.addEventListener("DOMContentLoaded", function () {
    // Order details kiolvasása a localStorage-ból
    const orderDetails = JSON.parse(localStorage.getItem("orderDetails")) || {};
    const deliveryForm = document.getElementById("delivery-form");
    const deliveryForm2 = document.getElementById("delivery-form2");
    const orderItemsList = document.getElementById("order-items");
    const orderTotal = document.getElementById("order-total");
    const deliveryMethod = document.getElementById("delivery-method");

    // Ellenőrizzük, hogy vannak-e cartItems, és csak egyszer adjuk hozzá őket
    if (orderDetails.cartItems && orderItemsList.children.length === 0) {
        orderDetails.cartItems.forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.name} (${item.size}) - ${item.quantity} x ${item.price} Ft`;
            orderItemsList.appendChild(li);
        });
        orderTotal.textContent = `Total: ${orderDetails.total} Ft`;
        deliveryMethod.textContent = `Delivery: ${orderDetails.delivery === "home" ? "Házhozszállítás" : "Áruházi átvétel"}`;
    }

    // Ha házhozszállítás van kiválasztva, mutassuk a szállítási űrlapot és töltsük ki az adatokat
    if (orderDetails.delivery === "home" && orderDetails.customer) {
        deliveryForm.style.display = "block";

        // Mezők kitöltése a billing adataival
        document.getElementById("name").value = orderDetails.customer.name || "";
        document.getElementById("phone").value = orderDetails.customer.phone || "";
        document.getElementById("email").value = orderDetails.customer.email || "";
    }

    if (orderDetails.delivery === "pickup" && orderDetails.customer) {
        deliveryForm2.style.display = "block";

        // Mezők kitöltése a billing adataival
        document.getElementById("name2").value = orderDetails.customer.name || "";
        document.getElementById("phone2").value = orderDetails.customer.phone || "";
        document.getElementById("email2").value = orderDetails.customer.email || "";
    }

    
});

    // Intersection Observer beállítása
    const observerOptions = {
        root: null, // Nézetablak az alapértelmezett root
        threshold: 0.1 // 10%-os láthatóság esetén aktiválódik
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Ha a .products-row látható, az összes .trending-product-card animálódik
                if (entry.target.classList.contains('products-row')) {
                    const cards = entry.target.querySelectorAll('.trending-product-card');
                    cards.forEach((card, index) => {
                        // Késleltetés hozzáadása az egyes kártyákhoz
                        setTimeout(() => {
                            card.classList.add('visible');
                        }, index * 100); // 100ms késleltetés kártyánként
                    });
                } else {
                    // Egyéb elemek (pl. .sale-section, .trending-image-container)
                    entry.target.classList.add('visible');
                }
                observer.unobserve(entry.target); // Egyszeri animáció
            }
        });
    }, observerOptions);

    // Szekciók megfigyelése
    const trendingSection = document.querySelector('.products-row'); // A Trending Now termékek konténere
    const saleSection = document.querySelector('.sale-section');
    const trendingImage = document.querySelector('.trending-image-container');

    if (trendingSection) observer.observe(trendingSection);
    if (saleSection) observer.observe(saleSection);
    if (trendingImage) observer.observe(trendingImage);

    
