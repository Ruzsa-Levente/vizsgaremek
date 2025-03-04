let cart = [];

// Add product to cart (with quantity handling)
function addToCart(productName, price, imageUrl) {
    let existingProduct = cart.find(item => item.name === productName);

    if (existingProduct) {
        existingProduct.quantity += 1; // Ha már létezik, növeli a mennyiséget
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
            cart.splice(productIndex, 1); // Ha már csak 1 db van, akkor teljesen eltávolítjuk
        }
    }

    saveCartToLocalStorage();
    updateReceipt();
    updateCartCount();
}
function updateReceipt() {
    const cartItems = document.getElementById('cart-items');
    const totalDisplay = document.getElementById('total');

    if (!cartItems || !totalDisplay) return;

    cartItems.innerHTML = ''; // Clear current items
    let total = 0;

    cart.forEach(item => {
        const li = document.createElement('li');
        li.classList.add("cart-item"); // Stílushoz class

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



// Delete product from cart instantly
function deleteFromCart(productName) {
    cart = cart.filter(item => item.name !== productName); // Törli az adott terméket a kosárból
    saveCartToLocalStorage(); // Frissíti a tárolt kosarat
    updateReceipt(); // Frissíti a megjelenítést
    updateCartCount(); // Kosár ikon frissítése
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

// Load the cart when the page loads
document.addEventListener('DOMContentLoaded', function () {
    loadCartFromLocalStorage();
    updateCartCount();
    updateReceipt();
});


// Redirect to product page on click
function redirectToProduct(productId) {
    window.location.href = 'product.php?id=' + productId;
}


document.addEventListener("DOMContentLoaded", function () {
    new Swiper(".swiper", {
        loop: true,
        autoplay: {
            delay: 3000, // 3 másodpercenként vált
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
});

//Product page swiper
let currentIndex = 0;
const slides = document.querySelectorAll(".slide");

function showImage(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove("active");
        if (i === index) {
            slide.classList.add("active");
        }
    });
}

function prevImage() {
    currentIndex = (currentIndex === 0) ? slides.length - 1 : currentIndex - 1;
    showImage(currentIndex);
}

function nextImage() {
    currentIndex = (currentIndex === slides.length - 1) ? 0 : currentIndex + 1;
    showImage(currentIndex);
}






//Order Summarizing Process
document.addEventListener("DOMContentLoaded", function () {
    const placeOrderBtn = document.getElementById("place-order-btn");
    const orderSummary = document.getElementById("order-summary");
    const payNowBtn = document.getElementById("pay-now-btn");

    // Ha a place order gomb létezik, akkor billing.php oldalon vagyunk
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener("click", function () {
            // Kosár elemek összegyűjtése
            const cartItems = cart.map(item => ({
                name: item.name,
                price: item.price * item.quantity, // Mennyiséggel megszorozva
                quantity: item.quantity
            }));

            if (cartItems.length === 0) {
                alert("Your cart is empty!");
                return;
            }

            // Számoljuk az összesített árat
            const total = cartItems.reduce((sum, item) => sum + item.price, 0).toFixed(2);

            // Szállítási mód mentése
            const deliveryMethod = document.getElementById("delivery").value;

            // Adatok mentése a localStorage-be
            const orderDetails = {
                cartItems: cartItems,
                total: total,
                delivery: deliveryMethod
            };
            localStorage.setItem("orderDetails", JSON.stringify(orderDetails));

            // Átirányítás a fizetési oldalra
            window.location.href = "pay.php";
        });
    }


    // Ha az orderSummary létezik, akkor a pay.php oldalon vagyunk
    if (orderSummary) {
        const orderDetails = JSON.parse(localStorage.getItem("orderDetails"));

        if (!orderDetails || orderDetails.cartItems.length === 0) {
            orderSummary.innerHTML = "<p>No order found.</p>";
            return;
        }

        // Megjeleníti a rendelési tételeket
        // Megjeleníti a rendelési tételeket a fizetési oldalon mennyiséggel együtt
        const orderItems = document.getElementById("order-items");
        orderDetails.cartItems.forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.name} (x${item.quantity}) - Total: Ft${item.price.toFixed(2)}`;
            orderItems.appendChild(li);
        });

        document.getElementById("order-total").textContent = `Total: Ft${orderDetails.total}`;
        const deliveryMethod = orderDetails.delivery === "home" ? "Home Delivery" : "In-Store Pickup";
        document.getElementById("delivery-method").textContent = `Delivery Method: ${deliveryMethod}`;

        // Ha Home Delivery-t választott, mutassa meg a szállítási mezőket
        if (orderDetails.delivery === "home") {
            document.getElementById("delivery-form").style.display = "block";
        }
    }
});

// Az alábbi kódot a fizetési folyamat végén kell használni

document.getElementById("pay-now-btn").addEventListener("click", function (event) {
    const orderDetails = JSON.parse(localStorage.getItem("orderDetails"));
    if (!orderDetails || orderDetails.cartItems.length === 0) {
        alert("Your cart is empty!");
        return;
    }

    // Ellenőrizzük, hogy a szállítási mezők ki vannak-e töltve
    if (orderDetails.delivery === "home") {
        const name = document.getElementById("name").value.trim();
        const address = document.getElementById("address").value.trim();
        const city = document.getElementById("city").value.trim();
        const zip = document.getElementById("zip").value.trim();
        const phone = document.getElementById("phone").value.trim();
    
        if (!name || !address || !city || !zip || !phone) {
            alert("Please fill in all shipping details before proceeding!");
            event.preventDefault();  // Megakadályozza az űrlap elküldését
            return;  // Ha bármelyik mező üres, leállítjuk a folyamatot
        }
    }

    // Ha minden rendben, kérjük el az adatokat
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
            localStorage.removeItem("cart"); // Kosár törlése
            localStorage.removeItem("orderDetails");
            window.location.href = "succes.php"; // Átirányítás megerősítő oldalra
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});
