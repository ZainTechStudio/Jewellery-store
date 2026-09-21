(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    var grid = document.getElementById("grid");
    var empty = document.getElementById("empty");
    var search = document.getElementById("search");
    var cat = document.getElementById("cat");
    var sort = document.getElementById("sort");

    var params = new URLSearchParams(location.search);
    if (params.get("cat")) cat.value = params.get("cat");

    function render() {
      var q = search.value.trim().toLowerCase();
      var list = window.PRODUCTS.filter(function (p) {
        return (!q || p.name.toLowerCase().indexOf(q) > -1) && (!cat.value || p.cat === cat.value);
      });
      if (sort.value === "low") list.sort(function (a, b) { return a.price - b.price; });
      if (sort.value === "high") list.sort(function (a, b) { return b.price - a.price; });
      if (sort.value === "name") list.sort(function (a, b) { return a.name.localeCompare(b.name); });

      grid.innerHTML = list.map(function (p, i) { return window.productCard(p, (i % 4) * 90); }).join("");
      empty.classList.toggle("d-none", list.length > 0);
      if (window.AOS) AOS.refresh();
    }

    search.addEventListener("input", render);
    cat.addEventListener("change", render);
    sort.addEventListener("change", render);
    render();
  });
})();
