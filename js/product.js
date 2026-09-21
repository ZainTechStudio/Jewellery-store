(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    var id = new URLSearchParams(location.search).get("id") || "p1";
    var p = window.findProduct(id) || window.PRODUCTS[0];

    document.title = p.name + " — AURELIA Fine Jewellery";
    document.getElementById("crumb").textContent = p.name;
    document.getElementById("pCat").textContent = p.cat;
    document.getElementById("pName").textContent = p.name;
    document.getElementById("pPrice").innerHTML = window.money(p.price) + (p.old ? "<del>" + window.money(p.old) + "</del>" : "");
    document.getElementById("pStars").innerHTML =
      "★".repeat(p.rating) + "☆".repeat(5 - p.rating) +
      '<span class="text-muted-lux ms-2" style="letter-spacing:.14em">128 reviews</span>';

    /* gallery */
    var gallery = [p.img].concat(
      window.PRODUCTS.filter(function (x) { return x.id !== p.id; }).slice(0, 3).map(function (x) { return x.img; })
    );
    var main = document.getElementById("mainImg");
    main.src = gallery[0];
    main.alt = p.name;
    document.getElementById("thumbs").innerHTML = gallery.map(function (src, i) {
      return '<div class="col-3"><div class="thumb' + (i === 0 ? " active" : "") + '" data-src="' + src + '">' +
        '<img src="' + src + '" alt="' + p.name + ' view ' + (i + 1) + '" loading="lazy"></div></div>';
    }).join("");

    document.getElementById("thumbs").addEventListener("click", function (e) {
      var t = e.target.closest(".thumb");
      if (!t) return;
      document.querySelectorAll(".thumb").forEach(function (el) { el.classList.remove("active"); });
      t.classList.add("active");
      main.style.opacity = 0;
      setTimeout(function () { main.src = t.getAttribute("data-src"); main.style.opacity = 1; }, 160);
    });
    main.style.transition = "opacity .25s ease, transform .35s ease";

    /* zoom */
    var box = document.getElementById("galleryMain");
    box.addEventListener("click", function () { box.classList.toggle("zoomed"); });
    box.addEventListener("mousemove", function (e) {
      if (!box.classList.contains("zoomed")) return;
      var r = box.getBoundingClientRect();
      main.style.transformOrigin = ((e.clientX - r.left) / r.width * 100) + "% " + ((e.clientY - r.top) / r.height * 100) + "%";
    });
    box.addEventListener("mouseleave", function () { box.classList.remove("zoomed"); });

    /* swatches */
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
    qty.style.transition = "transform .18s ease";
    document.getElementById("addBtn").addEventListener("click", function () {
      window.addToCart(p.id, parseInt(qty.value, 10) || 1);
    });

    /* related slider — 4 per slide desktop */
    var rel = window.PRODUCTS.filter(function (x) { return x.id !== p.id; });
    var slides = [];
    for (var i = 0; i < rel.length; i += 4) slides.push(rel.slice(i, i + 4));
    document.getElementById("relatedInner").innerHTML = slides.map(function (group, i) {
      return '<div class="carousel-item' + (i === 0 ? " active" : "") + '"><div class="row g-4 px-lg-5">' +
        group.map(function (x) { return window.productCard(x, 0); }).join("") + "</div></div>";
    }).join("");
    if (window.AOS) AOS.refresh();
  });
})();
