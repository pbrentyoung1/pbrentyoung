(function () {
  "use strict";

  var host = document.getElementById("subscribeDialog");
  if (!host) return;

  var panel = host.querySelector(".subscribe-dialog__panel");
  var email = host.querySelector("#EMAIL");
  var form = host.querySelector("#sib-form");
  var messageHost = host.querySelector("#sib-form-container");
  var successMessage = host.querySelector("#success-message");
  var errorMessage = host.querySelector("#error-message");
  var opener = null;
  var successTracked = false;
  var errorTracked = false;

  function track(eventName, parameters) {
    if (window.brentTrackEvent) window.brentTrackEvent(eventName, parameters || {});
  }

  function messageIsVisible(message) {
    if (!message || message.hidden || message.getAttribute("aria-hidden") === "true") return false;
    return window.getComputedStyle(message).display !== "none"
      && window.getComputedStyle(message).visibility !== "hidden";
  }

  function openDialog(link) {
    opener = link;
    track("subscribe_open", {
      link_location: link.getAttribute("data-analytics-location") || "site"
    });
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

  if (form) {
    form.addEventListener("submit", function () {
      track("subscribe_submit", {
        link_location: opener ? opener.getAttribute("data-analytics-location") || "site" : "site"
      });
    });
  }

  if (messageHost && "MutationObserver" in window) {
    new MutationObserver(function () {
      if (!successTracked && messageIsVisible(successMessage)) {
        successTracked = true;
        track("subscribe_success", {
          link_location: opener ? opener.getAttribute("data-analytics-location") || "site" : "site"
        });
      }
      if (!errorTracked && messageIsVisible(errorMessage)) {
        errorTracked = true;
        track("subscribe_error", {
          link_location: opener ? opener.getAttribute("data-analytics-location") || "site" : "site"
        });
      }
    }).observe(messageHost, { attributes: true, childList: true, subtree: true });
  }

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
