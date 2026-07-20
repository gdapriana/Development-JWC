<!--

RULE.

1. taruh code ini di "public_html/theme/front/partials/components/cart.php".
2. include file ini dibawah footer.
3. Setidaknya ada button dengan id "cartButtonModal_" untuk membuka cart.
4. Untuk setiap card product, setidaknya memiliki button dengan class "addProductToCartButton_"
   dan terdapat atribut tambahan pada button yaitu data-image="", data-title="" dan data-price=""


-->


<style>
    /* PENTING: Tombol pemicu modal harus memiliki position relative agar badge absolute bekerja */
    #cartButtonModal_ {
        position: relative;
    }

    /* Gaya Desain Badge Merah Premium */
    .cartBadge_ {
        position: absolute;
        top: -6px;
        right: -6px;
        background-color: #ff4d4f;
        /* Warna merah notifikasi */
        color: white;
        font-size: 0.7rem;
        font-weight: bold;
        border-radius: 50%;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
        pointer-events: none;
        /* Menghindari gangguan saat tombol diklik */
        transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    /* Efek Pop Animasi saat item bertambah */
    .cartBadge_.pop {
        transform: scale(1.3);
    }

    #cartDetailsContainer_ {
        position: fixed;
        width: 100%;
        height: 100dvh;
        top: 0;
        right: 0;
        z-index: 999999999;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transition: all ease-in-out .5s;

        &.active {
            opacity: 1;
            visibility: visible;

            .cartDetailsContent {
                transform: translateX(0);
            }
        }

        .cartDetailsContent {
            padding: 2rem;
            position: absolute;
            right: 0;
            top: 0;
            width: 80%;
            max-width: 500px;
            height: 100%;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transform: translateX(100%);
            transition: transform ease-in-out .4s;
            box-sizing: border-box;
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);

            .cartDetailsContentHeader {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-bottom: 1rem;
                border-bottom: 1px solid #eee;

                .title {
                    font-family: var(--primtext, sans-serif);
                    color: var(--colors, #333);
                    margin: 0;
                    font-size: 1.6rem;
                    font-weight: bold;
                    text-transform: capitalize;
                }

                #cartButtonCloseModal_ {
                    background-color: var(--colors, #333);
                    color: white;
                    border: none;
                    border-radius: 50%;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: background-color 0.2s ease;

                    &:hover {
                        background-color: #555;
                    }
                }
            }

            .cartDetailsContentBody {
                flex: 1;
                overflow-y: auto;
                margin-top: 1rem;
                margin-bottom: 1rem;
                padding-right: 5px;

                /* Desain Scrollbar */
                &::-webkit-scrollbar {
                    width: 6px;
                }

                &::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }

                &::-webkit-scrollbar-thumb {
                    background: #ccc;
                    border-radius: 10px;
                }

                &::-webkit-scrollbar-thumb:hover {
                    background: #999;
                }

                .cartEmptyState {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 70%;
                    color: #888;
                    text-align: center;

                    i {
                        font-size: 3rem;
                        margin-bottom: 1rem;
                        color: #ddd;
                    }

                    p {
                        margin: 0;
                        font-size: 1rem;
                    }
                }

                .cartItem {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    padding: 1rem 0;
                    border-bottom: 1px solid #eee;
                    animation: fadeIn 0.3s ease;

                    .cartItemImage {
                        width: 70px;
                        height: 70px;
                        object-fit: cover;
                        border-radius: 8px;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                    }

                    .cartItemDetails {
                        flex: 1;

                        .cartItemTitle {
                            font-size: 0.95rem;
                            font-weight: 600;
                            color: #333;
                            margin: 0 0 0.25rem 0;
                            line-height: 1.3;
                        }

                        .cartItemPrice {
                            font-size: 0.9rem;
                            color: var(--colors, #666);
                            font-weight: 700;
                        }

                        .cartItemQty {
                            display: flex;
                            align-items: center;
                            border: 1px solid #e0e0e0;
                            border-radius: 6px;
                            overflow: hidden;
                            width: fit-content;
                            margin-top: 0.5rem;
                            background-color: #fafafa;

                            .qtyBtn {
                                background: none;
                                border: none;
                                width: 28px;
                                height: 28px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                cursor: pointer;
                                font-weight: bold;
                                font-size: 1rem;
                                color: #333;
                                transition: background-color 0.2s ease;

                                &:hover {
                                    background-color: #ededed;
                                }

                                &:active {
                                    background-color: #e0e0e0;
                                }
                            }

                            .qtyValue {
                                padding: 0 8px;
                                font-size: 0.85rem;
                                font-weight: 600;
                                min-width: 24px;
                                text-align: center;
                                color: #333;
                                border-left: 1px solid #e0e0e0;
                                border-right: 1px solid #e0e0e0;
                            }
                        }
                    }

                    .cartItemRemove {
                        background: none;
                        border: none;
                        color: #ff4d4f;
                        cursor: pointer;
                        padding: 0.5rem;
                        font-size: 1rem;
                        transition: transform 0.2s ease;

                        &:hover {
                            transform: scale(1.15);
                        }
                    }
                }
            }

            .cartDetailsContentFooter {
                border-top: 1px solid #eee;
                padding-top: 1rem;

                .cartTotalRow {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 1.2rem;
                    font-size: 1.1rem;
                    font-weight: bold;
                    color: #333;
                }

                .checkoutBtn {
                    width: 100%;
                    padding: 0.9rem;
                    background-color: var(--colors, #333);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: opacity 0.2s ease;

                    &:hover {
                        opacity: 0.9;
                    }
                }
            }
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<!-- ==================== 2. HTML STRUCTURE ==================== -->
<section id="cartDetailsContainer_">
    <div class="cartDetailsContent">
        <header class="cartDetailsContentHeader">
            <h3 class="title">Cart</h3>
            <button id="cartButtonCloseModal_"><i class="fas fa-x"></i></button>
        </header>
        <div class="cartDetailsContentBody" id="cartItemsList_">
            <!-- Render dinamis produk -->
        </div>
        <div class="cartDetailsContentFooter">
            <div class="cartTotalRow">
                <span>Total:</span>
                <span id="cartTotalPrice_">Rp 0</span>
            </div>
            <button class="checkoutBtn" id="checkoutButton_">Checkout</button>
        </div>
    </div>
</section>
<!-- ==================== 3. JAVASCRIPT ==================== -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const cartBtn = document.querySelector('#cartButtonModal_'); // Tombol keranjang belanja Anda
        const cartCloseBtn = document.querySelector('#cartButtonCloseModal_');
        const cartContainer = document.querySelector('#cartDetailsContainer_');
        const cartItemsList = document.querySelector('#cartItemsList_');
        const cartTotalPrice = document.querySelector('#cartTotalPrice_');
        const checkoutBtn = document.querySelector('#checkoutButton_');
        const STORAGE_KEY = 'shopping_cart';
        const WHATSAPP_NUMBER = '6281234567890'; // Sesuaikan dengan nomor Anda
        const formatCurrency = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        };
        const getCart = () => {
            const cartData = localStorage.getItem(STORAGE_KEY);
            return cartData ? JSON.parse(cartData) : [];
        };
        const saveCart = (cart) => {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        };
        // Fungsi memperbarui / me-render badge pada tombol pemicu
        const updateCartBadge = () => {
            if (!cartBtn) return;

            const cart = getCart();
            // Menghitung total jumlah kuantitas dari semua item
            const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);

            let badge = cartBtn.querySelector('.cartBadge_');

            if (totalItems > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'cartBadge_';
                    cartBtn.appendChild(badge);
                }
                badge.textContent = totalItems;
                badge.style.display = 'flex';

                // Efek animasi pop kecil ketika ada perubahan kuantitas
                badge.classList.remove('pop');
                void badge.offsetWidth; // Trigger reflow CSS
                badge.classList.add('pop');
            } else {
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        };
        const renderCart = () => {
            const cart = getCart();

            if (!cartItemsList) return;

            cartItemsList.innerHTML = '';
            if (cart.length === 0) {
                cartItemsList.innerHTML = `
                    <div class="cartEmptyState">
                        <i class="fas fa-shopping-bag"></i>
                        <p>Keranjang belanja Anda kosong</p>
                    </div>
                `;
                if (cartTotalPrice) cartTotalPrice.textContent = formatCurrency(0);

                // Selaraskan update badge
                updateCartBadge();
                return;
            }
            let total = 0;
            cart.forEach((item, index) => {
                const price = Number(item.price) || 0;
                const quantity = Number(item.quantity) || 1;

                const subtotal = price * quantity;
                total += subtotal;
                const itemHTML = `
                    <div class="cartItem">
                        <img class="cartItemImage" src="${item.img}" alt="${item.title}">
                        <div class="cartItemDetails">
                            <h4 class="cartItemTitle">${item.title}</h4>
                            <span class="cartItemPrice">${formatCurrency(price)}</span>
                            
                            <div class="cartItemQty">
                                <button class="qtyBtn qtyMinus" data-index="${index}">-</button>
                                <span class="qtyValue">${quantity}</span>
                                <button class="qtyBtn qtyPlus" data-index="${index}">+</button>
                            </div>
                        </div>
                        <button class="cartItemRemove" data-index="${index}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                cartItemsList.insertAdjacentHTML('beforeend', itemHTML);
            });
            if (cartTotalPrice) {
                cartTotalPrice.textContent = formatCurrency(total);
            }
            // Panggil fungsi update badge setiap render ulang keranjang
            updateCartBadge();
        };
        const addProductToCart = (product) => {
            const cart = getCart();

            const existingProduct = cart.find(item => item.title === product.title);
            if (existingProduct) {
                existingProduct.quantity = (existingProduct.quantity || 1) + 1;
            } else {
                product.quantity = 1;
                cart.push(product);
            }
            saveCart(cart);
            renderCart();
            if (cartContainer) {
                cartContainer.classList.add('active');
            }
        };
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => {
                const cart = getCart();

                if (cart.length === 0) {
                    alert('Keranjang belanja Anda masih kosong!');
                    return;
                }
                let message = `*DETAIL PESANAN BARU*\n`;
                message += `----------------------------------\n\n`;

                let total = 0;
                cart.forEach((item, index) => {
                    const price = Number(item.price) || 0;
                    const quantity = Number(item.quantity) || 1;
                    const subtotal = price * quantity;
                    total += subtotal;
                    message += `${index + 1}. *${item.title}* (x${quantity})\n`;
                    message += `    • Harga: ${formatCurrency(price)}\n`;
                    message += `    • Subtotal: ${formatCurrency(subtotal)}\n\n`;
                });
                message += `----------------------------------\n`;
                message += `*TOTAL HARGA:* ${formatCurrency(total)}\n\n`;
                message += `Halo Admin, saya ingin memesan barang-barang di atas. Mohon segera diproses.`;
                const encodedText = encodeURIComponent(message);
                const whatsappUrl = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodedText}`;

                window.open(whatsappUrl, '_blank');
            });
        }
        document.addEventListener('click', (e) => {
            const addBtn = e.target.closest('.addProductToCartButton_');
            if (addBtn) {
                e.preventDefault();
                const title = addBtn.dataset.title || '';
                const img = addBtn.dataset.image || '';
                const price = parseFloat(addBtn.dataset.price) || 0;
                const product = {
                    title: title,
                    img: img,
                    price: price
                };
                addProductToCart(product);
            }
        });
        if (cartItemsList) {
            cartItemsList.addEventListener('click', (e) => {
                const cart = getCart();
                const plusBtn = e.target.closest('.qtyPlus');
                if (plusBtn) {
                    const index = parseInt(plusBtn.dataset.index);
                    if (cart[index]) {
                        cart[index].quantity = (cart[index].quantity || 1) + 1;
                        saveCart(cart);
                        renderCart();
                    }
                    return;
                }
                const minusBtn = e.target.closest('.qtyMinus');
                if (minusBtn) {
                    const index = parseInt(minusBtn.dataset.index);
                    if (cart[index]) {
                        if (cart[index].quantity > 1) {
                            cart[index].quantity -= 1;
                        } else {
                            cart.splice(index, 1);
                        }
                        saveCart(cart);
                        renderCart();
                    }
                    return;
                }
                const removeBtn = e.target.closest('.cartItemRemove');
                if (removeBtn) {
                    const index = parseInt(removeBtn.dataset.index);
                    if (index > -1) {
                        cart.splice(index, 1);
                    }
                    saveCart(cart);
                    renderCart();
                }
            });
        }
        if (cartBtn) {
            cartBtn.addEventListener('click', () => {
                cartContainer.classList.add('active');
            });
        }
        if (cartCloseBtn) {
            cartCloseBtn.addEventListener('click', () => {
                cartContainer.classList.remove('active');
            });
        }
        if (cartContainer) {
            cartContainer.addEventListener('click', (e) => {
                if (e.target === cartContainer) {
                    cartContainer.classList.remove('active');
                }
            });
        }

        renderCart();
    });
</script>