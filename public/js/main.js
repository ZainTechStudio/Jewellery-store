/* ==========================================================
   AURELIA — shared JS (vanilla)
   ========================================================== */
(function () {
  "use strict";

  /* ---------- Money formatting ---------- */
  const money = (n) => "$" + Number(n).toLocaleString("en-US");
  window.money = money;

  /* ---------- Cart (Phase 3: database-backed) ----------
     The localStorage cart is gone — the database is now the only source
     of truth. This is a small fetch() helper (CSRF token read from the
     <meta name="csrf-token"> tag in layouts/app.blade.php) that every
     cart action (add/update/remove/clear) uses; see cart.js and
     product.js for where it's called. Nothing here decides price,
     stock or product data — the server does, and returns the fresh
     cart state in the response. */
  function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
  }

  window.cartRequest = function (url, method, data) {
    return fetch(url, {
      method: method,
      headers: {
        "X-CSRF-TOKEN": csrfToken(),
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: data ? JSON.stringify(data) : undefined,
    }).then(function (res) {
      return res.json().then(function (body) {
        return { ok: res.ok, status: res.status, body: body };
      });
    });
  };

  function updateCartCount(n) {
    document.querySelectorAll(".cart-count").forEach((el) => {
      el.textContent = n;
      el.style.transform = "scale(1.4)";
      setTimeout(() => (el.style.transform = "scale(1)"), 220);
    });
  }
  window.updateCartCount = updateCartCount;

  // One-time cleanup: earlier phases kept the cart in localStorage under
  // this key. It's no longer read anywhere, but clear it so it can't be
  // mistaken for live data (e.g. in devtools) now that the database cart
  // is authoritative.
  try { localStorage.removeItem("aurelia_cart"); } catch (e) {}

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

  /* ---------- Global delegated events ----------
     Phase 3: the quick-add overlay on each product card (server-rendered
     by partials/product-card.blade.php) now posts to the real cart
     backend instead of writing to localStorage — only a product id is
     sent; the server decides price/availability. */
  document.addEventListener("click", function (e) {
    const add = e.target.closest("[data-add]");
    if (!add) return;
    e.preventDefault();

    window.cartRequest("/cart/add", "POST", {
      product_id: add.getAttribute("data-product-id"),
      quantity: 1,
    }).then(function (res) {
      if (res.body && res.body.message) toast(res.body.message);
      if (res.ok && res.body) updateCartCount(res.body.count);
    }).catch(function () {
      toast("Could not add to bag");
    });
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
  });
})();
