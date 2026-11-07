(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    var list = document.getElementById("cartList");
    var empty = document.getElementById("cartEmpty");
    var actions = document.getElementById("cartActions");
    var sub = document.getElementById("sub");
    var tax = document.getElementById("tax");
    var total = document.getElementById("total");

    /* Phase 3: the cart page is server-rendered from the database on load.
       Quantity +/- / remove / clear now call the real backend (see
       window.cartRequest in main.js) and re-render this page's DOM from
       the server's JSON response — the database is always the source of
       truth, nothing here is computed from localStorage any more. */

    function renderFromData(data) {
      empty.classList.toggle("d-none", data.items.length > 0);
      actions.classList.toggle("d-none", data.items.length === 0);

      list.innerHTML = data.items.map(function (row) {
        return '<div class="cart-row" data-id="' + row.id + '">' +
          '<img src="' + row.image + '" alt="' + row.name + '" loading="lazy">' +
          '<div class="flex-grow-1">' +
            '<span class="card-cat">' + (row.variant_label || '') + '</span>' +
            '<h5 class="mb-1"><a href="' + row.url + '">' + row.name + '</a></h5>' +
            '<span class="price unit-price">' + row.formatted_unit_price + '</span>' +
          '</div>' +
          '<div class="qty-box"><button type="button" data-dec>\u2212</button>' +
            '<input value="' + row.quantity + '" readonly><button type="button" data-inc>+</button></div>' +
          '<div class="text-end" style="min-width:96px">' +
            '<div class="price line-total">' + row.formatted_line_subtotal + '</div>' +
            '<button class="icon-btn mt-2" data-remove><i class="bi bi-trash"></i></button>' +
          '</div></div>';
      }).join("");

      sub.textContent = data.formatted_subtotal;
      tax.textContent = data.formatted_tax;
      total.textContent = data.formatted_total;
      total.classList.remove("pulse");
      void total.offsetWidth;
      total.classList.add("pulse");

      if (window.updateCartCount) window.updateCartCount(data.count);
    }

    function handleResponse(res) {
      if (res.body && res.body.items) renderFromData(res.body);
      if (res.body && res.body.message) window.toast(res.body.message);
    }

    list.addEventListener("click", function (e) {
      var row = e.target.closest(".cart-row");
      if (!row) return;
      var id = row.getAttribute("data-id");

      if (e.target.closest("[data-inc]") || e.target.closest("[data-dec]")) {
        var input = row.querySelector("input");
        var current = parseInt(input.value, 10) || 1;
        var next = e.target.closest("[data-inc]") ? current + 1 : Math.max(1, current - 1);
        window.cartRequest("/cart/" + id, "PATCH", { quantity: next }).then(handleResponse);
        return;
      }

      if (e.target.closest("[data-remove]")) {
        row.classList.add("removing");
        window.cartRequest("/cart/" + id, "DELETE").then(function (res) {
          handleResponse(res);
        });
      }
    });

    document.getElementById("clearBtn").addEventListener("click", function () {
      window.cartRequest("/cart", "DELETE").then(handleResponse);
    });

    document.getElementById("checkout").addEventListener("click", function () {
      var hasItems = list.querySelector(".cart-row") !== null;
      window.toast(hasItems ? "Checkout is a demo in this frontend" : "Your bag is empty");
    });
  });
})();
