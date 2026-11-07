(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    var list = document.getElementById("cartList");
    var empty = document.getElementById("cartEmpty");
    var actions = document.getElementById("cartActions");

    /* Phase 2: cart items now carry their own snapshot (name/price/img/cat/url)
       captured at add-to-cart time (see main.js addToCart) instead of being
       re-looked-up against a hardcoded catalogue via window.findProduct, which
       no longer exists. Malformed/legacy entries (from before this change,
       which only had {id, qty}) are filtered out rather than crashing the page. */
    function rows() {
      return window.getCart()
        .filter(function (i) { return i && i.id && i.name && typeof i.price === "number"; })
        .map(function (i) { return { item: i, qty: i.qty }; });
    }

    function totals(anim) {
      var sub = rows().reduce(function (s, r) { return s + r.item.price * r.qty; }, 0);
      var tax = Math.round(sub * 0.08);
      document.getElementById("sub").textContent = window.money(sub);
      document.getElementById("tax").textContent = window.money(tax);
      var t = document.getElementById("total");
      t.textContent = window.money(sub + tax);
      if (anim) { t.classList.remove("pulse"); void t.offsetWidth; t.classList.add("pulse"); }
    }

    function render() {
      var data = rows();
      empty.classList.toggle("d-none", data.length > 0);
      actions.classList.toggle("d-none", data.length === 0);
      list.innerHTML = data.map(function (r, idx) {
        var it = r.item;
        return '<div class="cart-row" data-id="' + it.id + '" style="animation-delay:' + (idx * 60) + 'ms">' +
          '<img src="' + it.img + '" alt="' + it.name + '" loading="lazy">' +
          '<div class="flex-grow-1">' +
            '<span class="card-cat">' + (it.cat || '') + '</span>' +
            '<h5 class="mb-1"><a href="' + (it.url || '#') + '">' + it.name + '</a></h5>' +
            '<span class="price">' + window.money(it.price) + '</span>' +
          '</div>' +
          '<div class="qty-box"><button type="button" data-dec>−</button>' +
            '<input value="' + r.qty + '" readonly><button type="button" data-inc>+</button></div>' +
          '<div class="text-end" style="min-width:96px">' +
            '<div class="price line-total">' + window.money(it.price * r.qty) + '</div>' +
            '<button class="icon-btn mt-2" data-remove><i class="bi bi-trash"></i></button>' +
          '</div></div>';
      }).join("");
      totals(false);
    }

    list.addEventListener("click", function (e) {
      var row = e.target.closest(".cart-row");
      if (!row) return;
      var id = row.getAttribute("data-id");
      var cart = window.getCart();
      var item = cart.find(function (c) { return String(c.id) === String(id); });
      if (!item) return;

      if (e.target.closest("[data-inc]")) item.qty++;
      else if (e.target.closest("[data-dec]")) item.qty = Math.max(1, item.qty - 1);
      else if (e.target.closest("[data-remove]")) {
        row.classList.add("removing");
        setTimeout(function () {
          window.saveCart(cart.filter(function (c) { return String(c.id) !== String(id); }));
          render();
        }, 380);
        return;
      } else return;

      window.saveCart(cart);
      row.querySelector("input").value = item.qty;
      row.querySelector(".line-total").textContent = window.money(item.price * item.qty);
      totals(true);
    });

    document.getElementById("clearBtn").addEventListener("click", function () {
      window.saveCart([]);
      render();
      window.toast("Bag emptied");
    });

    document.getElementById("checkout").addEventListener("click", function () {
      window.toast(rows().length ? "Checkout is a demo in this frontend" : "Your bag is empty");
    });

    render();
  });
})();
