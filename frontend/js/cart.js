function getCart() {
  const cart = localStorage.getItem("cart");
  return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartBadge();
}

function addToCart(product, quantity) {
  const cart = getCart();
  const minQty = parseInt(product.minimum_order_quantity, 10) || 1;
  const qtyToAdd = Math.max(quantity, minQty);

  const existingItem = cart.find((item) => item.id === product.id);

  if (existingItem) {
    existingItem.quantity += qtyToAdd;
  } else {
    cart.push({
      id: product.id,
      name: product.name,
      price: product.unit_price,
      quantity: qtyToAdd,
      image: product.image_url,
      minQty: minQty,
    });
  }

  saveCart(cart);
}

function removeFromCart(productId) {
  let cart = getCart();
  cart = cart.filter((item) => item.id !== productId);
  saveCart(cart);
}

function updateQuantity(productId, quantity) {
  let cart = getCart();
  const item = cart.find((item) => item.id === productId);
  const minQty = item ? item.minQty : 1;

  if (item) {
    if (quantity >= minQty) {
      item.quantity = quantity;
    } else {
      alert(`The minimum order quantity for this product is ${minQty}.`);
      item.quantity = minQty;
    }
    saveCart(cart);
  }
}

function clearCart(skipConfirm = false) {
  if (skipConfirm || confirm("Are you sure you want to clear your cart?")) {
    localStorage.removeItem("cart");
    updateCartBadge();
  }
}

function calculateTotal() {
  const cart = getCart();
  return cart.reduce((total, item) => total + item.price * item.quantity, 0);
}

function updateCartBadge() {
  const cart = getCart();
  const cartBadge = document.getElementById("cart-badge");
  if (cartBadge) {
    cartBadge.textContent = cart.length;
  }
}

// Initial badge update on page load
document.addEventListener("DOMContentLoaded", updateCartBadge);
