/**
 * PT Berkah Jaya Awing — Admin POS Cart
 */

let cart = [];

function fmtRp(n){
    return 'Rp ' + (parseInt(n) || 0).toLocaleString('id-ID');
}

function getProductById(id){
    return PRODUCTS.find(p => parseInt(p.id) === parseInt(id));
}

function pilihBarang(id){
    const produk = getProductById(id);

    if(!produk){
        alert('Produk tidak ditemukan.');
        return;
    }

    const existing = cart.find(item => parseInt(item.id) === parseInt(id));

    if(existing){
        if(existing.qty >= produk.stok){
            alert('Jumlah sudah mencapai batas stok.');
            return;
        }

        existing.qty += 1;
    }else{
        cart.push({
            id: produk.id,
            nama: produk.nama,
            harga: parseInt(produk.harga) || 0,
            stok: parseInt(produk.stok) || 0,
            qty: 1
        });
    }

    renderCart();
}

function tambahQty(id){
    const item = cart.find(i => parseInt(i.id) === parseInt(id));

    if(!item) return;

    if(item.qty >= item.stok){
        alert('Jumlah melebihi stok.');
        return;
    }

    item.qty += 1;
    renderCart();
}

function kurangQty(id){
    const item = cart.find(i => parseInt(i.id) === parseInt(id));

    if(!item) return;

    item.qty -= 1;

    if(item.qty <= 0){
        cart = cart.filter(i => parseInt(i.id) !== parseInt(id));
    }

    renderCart();
}

function hapusItem(id){
    cart = cart.filter(i => parseInt(i.id) !== parseInt(id));
    renderCart();
}

function getSubtotal(){
    return cart.reduce((sum, item) => {
        return sum + (parseInt(item.harga) * parseInt(item.qty));
    }, 0);
}

function renderCart(){
    const emptyCart = document.getElementById('emptyCart');
    const cartItems = document.getElementById('cartItems');
    const cartData = document.getElementById('cart_data');

    if(!cartItems || !cartData) return;

    if(cart.length === 0){
        emptyCart.style.display = 'block';
        cartItems.innerHTML = '';
    }else{
        emptyCart.style.display = 'none';

        cartItems.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-info">
                    <div class="cart-name">${item.nama}</div>
                    <div class="cart-sub">Rp ${item.harga.toLocaleString('id-ID')} / unit</div>
                </div>

                <div class="cart-actions">
                    <button type="button" onclick="kurangQty(${item.id})">−</button>
                    <span>${item.qty}</span>
                    <button type="button" onclick="tambahQty(${item.id})">+</button>
                </div>

                <div class="cart-total">
                    ${fmtRp(item.harga * item.qty)}
                </div>

                <button type="button" class="cart-remove" onclick="hapusItem(${item.id})">×</button>
            </div>
        `).join('');
    }

    cartData.value = JSON.stringify(cart.map(item => ({
        id: item.id,
        qty: item.qty
    })));

    document.getElementById('subtotal').textContent = fmtRp(getSubtotal());

    updateActiveCards();
    hitungKembalianPOS();
}

function updateActiveCards(){
    document.querySelectorAll('.product-card').forEach(card => {
        const id = parseInt(card.dataset.id);
        const exists = cart.some(item => parseInt(item.id) === id);
        card.classList.toggle('active', exists);
    });
}

function hitungKembalianPOS(){
    const bayar = parseInt(document.getElementById('uang_bayar').value) || 0;
    const total = getSubtotal();
    const kembali = bayar - total;

    const el = document.getElementById('kembalian');

    el.textContent = fmtRp(kembali > 0 ? kembali : 0);

    if(total > 0 && bayar < total){
        el.style.color = 'var(--red)';
    }else{
        el.style.color = 'var(--green)';
    }
}

function resetFormPOS(){
    cart = [];

    document.getElementById('uang_bayar').value = 0;
    document.getElementById('subtotal').textContent = 'Rp 0';
    document.getElementById('kembalian').textContent = 'Rp 0';
    document.getElementById('kembalian').style.color = 'var(--text-main)';
    document.getElementById('cart_data').value = '';

    renderCart();
}

document.addEventListener('DOMContentLoaded', function(){

    const form = document.getElementById('posForm');
    const searchInput = document.getElementById('searchBarang');

    if(searchInput){
        searchInput.addEventListener('input', function(){
            const keyword = this.value.toLowerCase();

            document.querySelectorAll('.product-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }

    if(!form) return;

    form.addEventListener('submit', function(e){
        const bayar = parseInt(document.getElementById('uang_bayar').value) || 0;
        const total = getSubtotal();

        if(cart.length === 0){
            e.preventDefault();
            alert('Keranjang masih kosong.');
            return;
        }

        if(bayar < total){
            e.preventDefault();
            alert('Uang bayar kurang.');
            return;
        }

        document.getElementById('cart_data').value = JSON.stringify(cart.map(item => ({
            id: item.id,
            qty: item.qty
        })));
    });

    renderCart();
});