(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    /* Phase 2: the product name/price/description/gallery/related-products
       are now rendered server-side by Blade from the real database product
       (resources/views/product.blade.php + ProductController). This file
       now only handles client-side-only UI interactions: thumbnail swap,
       image zoom, the (cosmetic) metal swatch toggle, the quantity stepper,
       and reading the add-to-bag button's data attributes into the cart. */

    var main = document.getElementById("mainImg");

    /* thumbnail swap */
    var thumbs = document.getElementById("thumbs");
    if (thumbs && main) {
      thumbs.addEventListener("click", function (e) {
        var t = e.target.closest(".thumb");
        if (!t) return;
        document.querySelectorAll(".thumb").forEach(function (el) { el.classList.remove("active"); });
        t.classList.add("active");
        main.style.opacity = 0;
        setTimeout(function () { main.src = t.getAttribute("data-src"); main.style.opacity = 1; }, 160);
      });
      main.style.transition = "opacity .25s ease, transform .35s ease";
    }

    /* zoom */
    var box = document.getElementById("galleryMain");
    if (box && main) {
      box.addEventListener("click", function () { box.classList.toggle("zoomed"); });
      box.addEventListener("mousemove", function (e) {
        if (!box.classList.contains("zoomed")) return;
        var r = box.getBoundingClientRect();
        main.style.transformOrigin = ((e.clientX - r.left) / r.width * 100) + "% " + ((e.clientY - r.top) / r.height * 100) + "%";
      });
      box.addEventListener("mouseleave", function () { box.classList.remove("zoomed"); });
    }

    /* swatches — cosmetic only, see product.blade.php for why */
    document.querySelectorAll(".swatch").forEach(function (s) {
      s.addEventListener("click", function () {
        document.querySelectorAll(".swatch").forEach(function (x) { x.classList.remove("active"); });
        s.classList.add("active");
      });
    });

    /* qty + add */
    var qty = document.getElementById("qty");
    document.querySelectorAll("[data-step]").forEach(function (b) {
      b.addEventListener("click", function () {
        var v = Math.max(1, (parseInt(qty.value, 10) || 1) + parseInt(b.getAttribute("data-step"), 10));
        qty.value = v;
        qty.style.transform = "scale(1.25)";
        setTimeout(function () { qty.style.transform = "scale(1)"; }, 180);
      });
    });
    if (qty) qty.style.transition = "transform .18s ease";

    var addBtn = document.getElementById("addBtn");
    if (addBtn) {
      addBtn.addEventListener("click", function () {
        window.addToCart({
          id: addBtn.getAttribute("data-product-id"),
          name: addBtn.getAttribute("data-product-name"),
          price: addBtn.getAttribute("data-product-price"),
          img: addBtn.getAttribute("data-product-img"),
          cat: addBtn.getAttribute("data-product-cat"),
          url: addBtn.getAttribute("data-product-url"),
        }, parseInt(qty ? qty.value : 1, 10) || 1);
      });
    }

    if (window.AOS) AOS.refresh();
  });
})();
