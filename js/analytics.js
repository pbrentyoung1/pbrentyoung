(function () {
  "use strict";

  function pagePath() {
    return window.location.pathname;
  }

  function cleanPath(href) {
    try {
      return new URL(href, window.location.href).pathname;
    } catch (error) {
      return "";
    }
  }

  function linkLocation(link) {
    var named = link.closest("[data-analytics-location]");
    if (named) return named.getAttribute("data-analytics-location") || "site";
    if (link.closest("header")) return "header";
    if (link.closest("footer, .site-foot, .contact")) return "footer";
    return "content";
  }

  function track(eventName, parameters) {
    if (!eventName || typeof window.gtag !== "function") return;
    var payload = parameters || {};
    if (!payload.page_path) payload.page_path = pagePath();
    window.gtag("event", eventName, payload);
  }

  window.brentTrackEvent = track;

  document.addEventListener("click", function (event) {
    var link = event.target.closest("a[href]");
    if (!link) return;

    var href = link.getAttribute("href") || "";
    var destinationPath = cleanPath(href);
    var locationName = linkLocation(link);
    var related = link.closest(".related-content, .framework-related");

    if (related && destinationPath.indexOf("/blog/") === 0) {
      track("related_content_click", {
        content_source: related.getAttribute("data-related-source") || pagePath(),
        destination_path: destinationPath,
        link_location: locationName
      });
      return;
    }

    if (/\.(?:pdf|csv|docx?|xlsx?|pptx?|zip)$/i.test(destinationPath)) {
      track("resource_download", {
        resource_name: destinationPath.split("/").pop() || "download",
        resource_path: destinationPath,
        link_location: locationName
      });
      return;
    }

    if (/^mailto:/i.test(href)) {
      track("contact_click", { link_location: locationName });
    }
  });

  var subscribeLinks = document.querySelectorAll("[data-subscribe-open]");
  if ("IntersectionObserver" in window && subscribeLinks.length) {
    var seen = [];
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting || seen.indexOf(entry.target) !== -1) return;
        seen.push(entry.target);
        track("subscribe_cta_view", {
          link_location: entry.target.getAttribute("data-analytics-location") || "site"
        });
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    subscribeLinks.forEach(function (link) { observer.observe(link); });
  }
})();
