(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    /* Phase 2: filtering/search/sort now happen server-side (ShopController)
       against the products table — the URL's query string (?q=&category=&sort=)
       is the source of truth. This form used to filter a hardcoded client-side
       array on every keystroke/change; now it just submits a normal GET request
       so Blade can re-render the grid from the database. */
    var form = document.getElementById("shopFilters");
    if (!form) return;

    var search = document.getElementById("search");
    var cat = document.getElementById("cat");
    var sort = document.getElementById("sort");

    if (cat) cat.addEventListener("change", function () { form.submit(); });
    if (sort) sort.addEventListener("change", function () { form.submit(); });

    if (search) {
      var t;
      search.addEventListener("input", function () {
        clearTimeout(t);
        t = setTimeout(function () { form.submit(); }, 500);
      });
    }
  });
})();
