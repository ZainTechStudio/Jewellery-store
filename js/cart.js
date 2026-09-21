(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    var list = document.getElementById("cartList");
    var empty = document.getElementById("cartEmpty");
    var actions = document.getElementById("cartActions");

    function rows() {
      return window.getCart().map(function (i) {
        var p = window.findProduct(i.id);
        return p ? { p: p, qty: i.qty } : null;
      }).filter(Boolean);
    }

    function totals(anim) {
      var sub = rows().reduce(function (s, r) { return s + r.p.price * r.qty; }, 0);
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
        return '<div class="cart-row" data-id="' + r.p.id + '" style="animation-delay:' + (idx * 60) + 'ms">' +
          '<img src="' + r.p.img + '" alt="' + r.p.name + '" loading="lazy">' +
          '<div class="flex-grow-1">' +
            '<span class="card-cat">' + r.p.cat + '</span>' +
            '<h5 class="mb-1"><a href="product.html?id=' + r.p.id + '">' + r.p.name + '</a></h5>' +
            '<span class="price">' + window.money(r.p.price) + '</span>' +
          '</div>' +
          '<div class="qty-box"><button type="button" data-dec>−</button>' +
            '<input value="' + r.qty + '" readonly><button type="button" data-inc>+</button></div>' +
          '<div class="text-end" style="min-width:96px">' +
            '<div class="price line-total">' + window.money(r.p.price * r.qty) + '</div>' +
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
      var item = cart.find(function (c) { return c.id === id; });
      if (!item) return;

      if (e.target.closest("[data-inc]")) item.qty++;
      else if (e.target.closest("[data-dec]")) item.qty = Math.max(1, item.qty - 1);
      else if (e.target.closest("[data-remove]")) {
        row.classList.add("removing");
        setTimeout(function () {
          window.saveCart(cart.filter(function (c) { return c.id !== id; }));
          render();
        }, 380);
        return;
      } else return;

      window.saveCart(cart);
      var p = window.findProduct(id);
      row.querySelector("input").value = item.qty;
      row.querySelector(".line-total").textContent = window.money(p.price * item.qty);
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
