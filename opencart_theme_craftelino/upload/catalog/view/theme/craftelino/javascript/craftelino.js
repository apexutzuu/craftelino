(function () {
  "use strict";

  function pad(n) {
    return n < 10 ? "0" + n : String(n);
  }

  function initCountdown() {
    var d = document.getElementById("craft-cd-d");
    var h = document.getElementById("craft-cd-h");
    var m = document.getElementById("craft-cd-m");
    var s = document.getElementById("craft-cd-s");
    var wrap = document.querySelector(".craft-countdown");
    if (!d || !h || !m || !s || !wrap) {
      return;
    }
    var endAttr = wrap.getAttribute("data-campaign-end");
    var endMs = endAttr ? Date.parse(endAttr) : NaN;
    if (Number.isNaN(endMs)) {
      return;
    }
    function remainingSecs() {
      return Math.max(0, Math.floor((endMs - Date.now()) / 1000));
    }
    function tick() {
      var secs = remainingSecs();
      d.textContent = pad(Math.floor(secs / 86400));
      h.textContent = pad(Math.floor((secs % 86400) / 3600));
      m.textContent = pad(Math.floor((secs % 3600) / 60));
      s.textContent = pad(secs % 60);
    }
    tick();
    setInterval(tick, 1000);
  }

  /** Link “Bestsellers” / hero CTA to catalog specials (language prefix from <html lang>). */
  function setSpecialLinks() {
    var lang = document.documentElement.getAttribute("lang") || "en-gb";
    var base = document.querySelector("base");
    var root = base && base.href ? base.href : "/";
    var path = "index.php?route=product/special&language=" + encodeURIComponent(lang);
    try {
      var u = new URL(path, root);
      path = u.pathname + u.search;
    } catch (e) {
      path = root.replace(/\/?$/, "/") + path;
    }
    document.querySelectorAll("[data-craft-special]").forEach(function (el) {
      if (el.tagName === "A" && (!el.getAttribute("href") || el.getAttribute("href") === "#")) {
        el.setAttribute("href", path);
      }
    });
  }

  function initNavDropdown() {
    var btn = document.getElementById("craft-nav-all-btn");
    if (!btn) {
      return;
    }
    if (typeof bootstrap !== "undefined" && bootstrap.Dropdown) {
      bootstrap.Dropdown.getOrCreateInstance(btn, {
        popperConfig: { strategy: "fixed" }
      });
    }
  }

  function initCookieBar() {
    var bar = document.getElementById("craft-cookie-bar");
    if (!bar) {
      return;
    }
    bar.querySelectorAll("[data-craft-cookie-url]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var url = btn.getAttribute("data-craft-cookie-url");
        if (!url) {
          return;
        }
        btn.disabled = true;
        fetch(url, { credentials: "same-origin", headers: { "X-Requested-With": "XMLHttpRequest" } })
          .then(function (r) {
            return r.json().catch(function () {
              return {};
            });
          })
          .then(function () {
            bar.classList.add("craft-cookie-bar--hidden");
            setTimeout(function () {
              if (bar.parentNode) {
                bar.parentNode.removeChild(bar);
              }
            }, 320);
          })
          .catch(function () {
            bar.classList.add("craft-cookie-bar--hidden");
            btn.disabled = false;
          });
      });
    });
  }

  function boot() {
    initCountdown();
    setSpecialLinks();
    initNavDropdown();
    initCookieBar();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }

  window.addEventListener("load", initNavDropdown);
})();
