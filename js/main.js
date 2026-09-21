/* ==========================================================
   AURELIA — shared JS (vanilla)
   ========================================================== */
(function () {
  "use strict";

  /* ---------- Product catalogue ---------- */
  const PRODUCTS = [
    { id: "p1", name: "Solitaire Éclat Ring", cat: "Rings",     price: 4850, old: 5400, img: "img/p1.jpg", tag: "Bestseller", rating: 5 },
    { id: "p2", name: "Champagne Halo Pendant", cat: "Necklaces", price: 3290, old: null, img: "img/p2.jpg", tag: "New", rating: 5 },
    { id: "p3", name: "Lumière Drop Earrings", cat: "Earrings",  price: 2740, old: 3100, img: "img/p3.jpg", tag: null, rating: 4 },
    { id: "p4", name: "Aurelia Tennis Bracelet", cat: "Bracelets", price: 6980, old: null, img: "img/p4.jpg", tag: "Signature", rating: 5 },
    { id: "p5", name: "Emerald Royale Ring", cat: "Rings",       price: 8450, old: 9200, img: "img/p5.jpg", tag: "Limited", rating: 5 },
    { id: "p6", name: "Pearl & Gold Heirloom Set", cat: "Sets",  price: 5620, old: null, img: "img/p6.jpg", tag: null, rating: 4 },
    { id: "p7", name: "Ivory Crescent Necklace", cat: "Necklaces", price: 3980, old: null, img: "img/p2.jpg", tag: null, rating: 4 },
    { id: "p8", name: "Golden Whisper Studs", cat: "Earrings",   price: 1890, old: 2200, img: "img/p3.jpg", tag: "Sale", rating: 5 }
  ];
  window.PRODUCTS = PRODUCTS;

  const money = (n) => "$" + n.toLocaleString("en-US");
  window.money = money;
  const byId = (id) => PRODUCTS.find((p) => p.id === id);
  window.findProduct = byId;

  /* ---------- Cart (localStorage) ---------- */
  const KEY = "aurelia_cart";
  const getCart = () => { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; } };
  const saveCart = (c) => { localStorage.setItem(KEY, JSON.stringify(c)); paintCount(); };
  window.getCart = getCart;
  window.saveCart = saveCart;

  function paintCount() {
    const n = getCart().reduce((s, i) => s + i.qty, 0);
    document.querySelectorAll(".cart-count").forEach((el) => {
      el.textContent = n;
      el.style.transform = "scale(1.4)";
      setTimeout(() => (el.style.transform = "scale(1)"), 220);
    });
  }
  window.paintCount = paintCount;

  window.addToCart = function (id, qty) {
    const cart = getCart();
    const found = cart.find((i) => i.id === id);
    if (found) found.qty += qty || 1; else cart.push({ id: id, qty: qty || 1 });
    saveCart(cart);
    toast((byId(id) ? byId(id).name : "Item") + " added to bag");
  };

  /* ---------- Toast ---------- */
  let toastEl;
  function toast(msg) {
    if (!toastEl) {
      toastEl = document.createElement("div");
      toastEl.className = "toast-lux";
      document.body.appendChild(toastEl);
    }
    toastEl.innerHTML = '<i class="bi bi-gem text-gold me-2"></i>' + msg;
    requestAnimationFrame(() => toastEl.classList.add("show"));
    clearTimeout(toastEl._t);
    toastEl._t = setTimeout(() => toastEl.classList.remove("show"), 2600);
  }
  window.toast = toast;

  /* ---------- Card markup ---------- */
  window.productCard = function (p, delay) {
    const stars = '★'.repeat(p.rating) + '☆'.repeat(5 - p.rating);
    return (
      '<div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="' + (delay || 0) + '">' +
        '<article class="card-lux">' +
          '<a href="product.html?id=' + p.id + '" class="card-media d-block">' +
            (p.tag ? '<span class="badge-lux">' + p.tag + '</span>' : '') +
            '<img src="' + p.img + '" alt="' + p.name + '" loading="lazy" width="900" height="900">' +
            '<span class="quick-add" data-add="' + p.id + '">Add to bag</span>' +
          '</a>' +
          '<div class="card-body-lux">' +
            '<span class="card-cat">' + p.cat + '</span>' +
            '<h5 class="mt-1"><a href="product.html?id=' + p.id + '">' + p.name + '</a></h5>' +
            '<div class="stars">' + stars + '</div>' +
            '<div class="price">' + money(p.price) + (p.old ? '<del>' + money(p.old) + '</del>' : '') + '</div>' +
          '</div>' +
        '</article>' +
      '</div>'
    );
  };

  /* ---------- Global delegated events ---------- */
  document.addEventListener("click", function (e) {
    const add = e.target.closest("[data-add]");
    if (add) { e.preventDefault(); window.addToCart(add.getAttribute("data-add"), 1); }
  });

  /* ---------- Navbar scroll state ---------- */
  function initNav() {
    const nav = document.querySelector(".navbar-lux");
    if (!nav) return;
    const onScroll = () => nav.classList.toggle("scrolled", window.scrollY > 40);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ---------- Hero: lightweight background video ---------- */
  function initHeroVideo() {
    const v = document.querySelector(".hero-video");
    if (!v) return;
    const src = v.getAttribute("data-src");
    if (!src) return;

    const load = function () {
      v.setAttribute("src", src);
      v.load();
      const play = v.play();
      if (play && play.catch) play.catch(function () {});
    };

    v.addEventListener("canplay", function () { v.classList.add("is-ready"); }, { once: true });
    v.addEventListener("error", function () { v.remove(); });

    if ("IntersectionObserver" in window) {
      const io = new IntersectionObserver(function (entries) {
        if (entries.some(function (e) { return e.isIntersecting; })) { io.disconnect(); load(); }
      }, { rootMargin: "200px" });
      io.observe(v);
    } else { load(); }
  }

  /* ---------- Hero: interactive 3D ring (inertia + parallax) ---------- */
  function initHero() {
    const ring = document.getElementById("ring3d");
    if (ring) {

      const segs = 44, radius = 118;
      let html = "";
      for (let i = 0; i < segs; i++) {
        html += '<span class="ring-seg" style="transform: rotateY(' + (i * (360 / segs)) + 'deg) translateZ(' + radius + 'px)"></span>';
      }
      ring.innerHTML = html;
    }
    const scene = document.querySelector(".scene");
    const hero = document.querySelector(".hero");
    if (!scene || !hero) return;

    const jewel = scene.querySelector(".jewel");
    const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const fine = window.matchMedia("(pointer:fine)").matches;

    // targets / current values (eased with inertia)
    let tRotY = 0, tRotX = 0, tPanX = 0, tPanY = 0, tDrift = 0;
    let rotY = 0, rotX = 0, panX = 0, panY = 0, drift = 0;
    let hovering = false, running = false, idle = 0;

    function loop() {
      // slow luxury drift while hovering
      if (hovering && !reduce) { idle += 0.0035; tDrift = Math.sin(idle) * 7; }
      else { tDrift += (0 - tDrift) * 0.02; }

      const e = 0.055; // ease-out inertia
      rotY += (tRotY + tDrift - rotY) * e;
      rotX += (tRotX - rotX) * e;
      panX += (tPanX - panX) * e;
      panY += (tPanY - panY) * e;

      scene.style.transform = "rotateY(" + rotY.toFixed(3) + "deg) rotateX(" + rotX.toFixed(3) + "deg)";
      if (jewel) jewel.style.marginLeft = panX.toFixed(2) + "px";
      if (jewel) jewel.style.top = panY.toFixed(2) + "px";

      const settled =
        Math.abs(tRotY + tDrift - rotY) < 0.02 && Math.abs(tRotX - rotX) < 0.02 &&
        Math.abs(tPanX - panX) < 0.05 && Math.abs(tPanY - panY) < 0.05 && !hovering;

      if (settled) { running = false; return; }
      requestAnimationFrame(loop);
    }
    function kick() { if (!running) { running = true; requestAnimationFrame(loop); } }

    if (fine && !reduce) {
      hero.addEventListener("mousemove", function (ev) {
        const r = hero.getBoundingClientRect();
        const nx = (ev.clientX - r.left) / r.width - 0.5;
        const ny = (ev.clientY - r.top) / r.height - 0.5;
        tRotY = nx * 26;
        tRotX = -ny * 18;
        tPanX = nx * 22;   // subtle cursor parallax
        tPanY = ny * 16;
        kick();
      }, { passive: true });

      scene.addEventListener("mouseenter", function () { hovering = true; scene.classList.add("is-active"); kick(); });
      scene.addEventListener("mouseleave", function () { hovering = false; scene.classList.remove("is-active"); kick(); });
      hero.addEventListener("mouseleave", function () {
        hovering = false; scene.classList.remove("is-active");
        tRotY = 0; tRotX = 0; tPanX = 0; tPanY = 0; kick();
      });
    }

    // subtle scroll parallax
    let ticking = false;
    window.addEventListener("scroll", function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        const y = Math.min(window.scrollY, 700);
        const copy = document.querySelector(".hero-copy");
        if (jewel) jewel.style.marginTop = (y * 0.12) + "px";
        if (copy) { copy.style.transform = "translateY(" + (y * 0.18) + "px)"; copy.style.opacity = String(Math.max(0, 1 - y / 620)); }
        ticking = false;
      });
    }, { passive: true });
  }

  /* ---------- Product card tilt + glow ---------- */
  function initTilt() {
    if (!window.matchMedia("(pointer:fine)").matches) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    document.addEventListener("mousemove", function (ev) {
      const card = ev.target.closest(".card-lux");
      if (!card) return;
      if (card._raf) return;
      card._raf = requestAnimationFrame(function () {
        card._raf = null;
        const r = card.getBoundingClientRect();
        const nx = (ev.clientX - r.left) / r.width - 0.5;
        const ny = (ev.clientY - r.top) / r.height - 0.5;
        card.style.transform =
          "perspective(900px) rotateY(" + (nx * 7).toFixed(2) + "deg) rotateX(" + (-ny * 7).toFixed(2) + "deg) translateY(-6px)";
        card.style.boxShadow = "0 26px 60px -26px rgba(212,175,55,.45)";
        card.style.transition = "transform .18s ease-out, box-shadow .4s ease";
      });
    }, { passive: true });

    document.addEventListener("mouseout", function (ev) {
      const card = ev.target.closest(".card-lux");
      if (!card || card.contains(ev.relatedTarget)) return;
      card.style.transition = "transform .8s cubic-bezier(.22,.61,.36,1), box-shadow .6s ease";
      card.style.transform = "";
      card.style.boxShadow = "";
    }, { passive: true });
  }


  /* ---------- Newsletter / contact forms ---------- */
  function initForms() {
    document.querySelectorAll("form[data-fake]").forEach(function (f) {
      f.addEventListener("submit", function (e) {
        e.preventDefault();
        toast(f.getAttribute("data-fake"));
        f.reset();
      });
    });
  }

  /* ---------- Boot ---------- */
  document.addEventListener("DOMContentLoaded", function () {
    if (window.AOS) AOS.init({ duration: 800, easing: "ease-out-cubic", once: true, offset: 70 });
    initNav();
    initHeroVideo();
    initHero();
    initForms();
    initTilt();

    paintCount();
  });
})();
