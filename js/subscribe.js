(function () {
  "use strict";

  var host = document.getElementById("subscribeDialog");
  if (!host) return;

  var panel = host.querySelector(".subscribe-dialog__panel");
  var email = host.querySelector("#EMAIL");
  var opener = null;

  function openDialog(link) {
    opener = link;
    host.hidden = false;
    document.body.classList.add("subscribe-open");
    window.requestAnimationFrame(function () {
      host.classList.add("is-open");
      if (email) email.focus();
      else panel.focus();
    });
  }

  function closeDialog() {
    host.classList.remove("is-open");
    document.body.classList.remove("subscribe-open");
    host.hidden = true;
    if (opener) {
      opener.focus();
      opener = null;
    }
  }

  document.querySelectorAll("[data-subscribe-open]").forEach(function (link) {
    link.addEventListener("click", function (event) {
      if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) return;
      event.preventDefault();
      openDialog(link);
    });
  });

  host.querySelectorAll("[data-subscribe-close]").forEach(function (control) {
    control.addEventListener("click", closeDialog);
  });

  document.addEventListener("keydown", function (event) {
    if (host.hidden) return;
    if (event.key === "Escape") {
      event.preventDefault();
      closeDialog();
      return;
    }
    if (event.key !== "Tab") return;
    var focusable = panel.querySelectorAll('a[href], button:not([disabled]), input:not([type="hidden"]):not([disabled])');
    if (!focusable.length) return;
    var first = focusable[0];
    var last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });
})();
