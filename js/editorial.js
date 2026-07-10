/* ============================================================
   BRENT YOUNG — EDITORIAL / PASTE-UP ENGINE
   Everything renders from data/portfolio.json.
   ============================================================ */
(function () {
  "use strict";

  var CODE = { video: "VID", design: "DES", photography: "PHO", music: "MUS", podcast: "POD", church: "CHU", social: "SOC" };
  var TABS = [
    ["all", "ALL"],
    ["design", "DESIGN"],
    ["video", "VIDEO"],
    ["photography", "PHOTO"],
    ["social", "SOCIAL"],
    ["music", "MUSIC"],
    ["podcast", "PODCAST"],
    ["church", "CHURCH ✱"]
  ];
  var PAGE = 15;

  var DATA = { featured: [], archive: [] };
  var cur = "all";
  var shown = PAGE;
  var jacketList = [];
  var jacketIdx = -1;

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- helpers ---------- */
  function el(tag, cls, html) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (html !== undefined) n.innerHTML = html;
    return n;
  }

  function esc(s) {
    return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  function isChurch(item) {
    return item.church === true || (item.categories || []).indexOf("church") !== -1;
  }

  function disciplines(item) {
    return (item.categories || []).filter(function (c) { return c !== "church"; });
  }

  function codesFor(item) {
    var d = disciplines(item);
    if (!d.length) d = ["church"];
    return d.map(function (c) { return CODE[c] || c.toUpperCase(); }).join(" · ");
  }

  function jobNo(i) { return String(i + 1).padStart(3, "0"); }

  function matches(item, f) {
    if (f === "all") return true;
    if (f === "church") return isChurch(item);
    return disciplines(item).indexOf(f) !== -1;
  }

  function seeded(seed) {
    var h = 2166136261;
    seed = String(seed);
    for (var i = 0; i < seed.length; i++) {
      h ^= seed.charCodeAt(i);
      h = Math.imul(h, 16777619);
    }
    return function () {
      h += h << 13; h ^= h >>> 7;
      h += h << 3; h ^= h >>> 17;
      h += h << 5;
      return ((h >>> 0) % 1000) / 1000;
    };
  }

  function between(rand, min, max) {
    return min + (max - min) * rand();
  }

  function scatterPaste(node, seed, strength) {
    var rand = seeded(seed);
    var s = strength || 1;
    var rot = between(rand, -4.5, 4.5) * s;
    if (Math.abs(rot) < 0.8) rot += rot < 0 ? -1 : 1;
    node.style.setProperty("--paste-x", between(rand, -13, 13) * s + "px");
    node.style.setProperty("--paste-y", between(rand, 18, 34) * s + "px");
    node.style.setProperty("--paste-rot-start", rot + "deg");
    node.style.setProperty("--paste-rot-mid", (rot * 0.62) + "deg");
  }

  function scatterArt(node, seed) {
    var rand = seeded(seed);
    var rot = between(rand, -3.2, 3.2);
    if (Math.abs(rot) < 0.6) rot += rot < 0 ? -0.8 : 0.8;
    node.style.setProperty("--art-x", between(rand, -8, 8) + "px");
    node.style.setProperty("--art-y", between(rand, -22, -10) + "px");
    node.style.setProperty("--art-rot-start", rot + "deg");
    node.style.setProperty("--art-rot-mid", (rot * 0.48) + "deg");
    node.style.setProperty("--art-scale", between(rand, 1.025, 1.06));
  }

  function scatterLift(node, seed) {
    var rand = seeded(seed);
    var rot = between(rand, -5.5, 5.5);
    if (Math.abs(rot) < 1.5) rot += rot < 0 ? -1.5 : 1.5;
    node.style.setProperty("--lift-x", between(rand, -18, 18) + "px");
    node.style.setProperty("--lift-y", between(rand, -34, -18) + "px");
    node.style.setProperty("--lift-rot", rot + "deg");
    node.style.setProperty("--lift-scale", between(rand, 1.025, 1.055));
  }

  function scatterSlide(node, seed) {
    var rand = seeded(seed);
    var rot = between(rand, -2.4, 2.4);
    node.style.setProperty("--slide-x", between(rand, -4, 4) + "px");
    node.style.setProperty("--slide-y", between(rand, -5, 5) + "px");
    node.style.setProperty("--slide-rot", rot + "deg");
  }

  /* ---------- FPO lazy loader ---------- */
  var io = "IntersectionObserver" in window
    ? new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (!en.isIntersecting) return;
          io.unobserve(en.target);
          placeArt(en.target);
        });
      }, { rootMargin: "180px" })
    : null;

  function placeArt(frame) {
    var img = frame.querySelector("img[data-src]");
    if (!img) { frame.classList.add("placed"); return; }
    img.onload = function () { frame.classList.add("placed"); };
    img.onerror = function () { frame.classList.add("placed"); };
    img.src = img.getAttribute("data-src");
    img.removeAttribute("data-src");
  }

  function fpoFrame(posterSrc, alt) {
    var f = el("div", "fpo");
    f.innerHTML = '<span class="fpo-tag">FPO</span><img data-src="' + esc(posterSrc) + '" alt="' + esc(alt) + '">';
    scatterArt(f, posterSrc + "|" + alt);
    if (io && !reduceMotion) io.observe(f);
    else placeArt(f);
    return f;
  }

  /* ---------- case studies ---------- */
  function renderCases() {
    var host = document.getElementById("caseStudies");
    if (!host) return;
    DATA.featured.forEach(function (cs) {
      var sec = el("section", "case wrap");
      sec.id = cs.id;
      sec.setAttribute("data-num", String(cs.num).padStart(2, ""));

      var head = el("div", "", '<span class="kicker">Case study ' + esc(cs.num) + "</span>" +
        '<h2 class="case-title">' + esc(cs.title) + "</h2>" +
        '<div class="case-codes">' + esc(cs.codes.join(" · ")) +
        (cs.client ? " &middot; " + esc(cs.client).toUpperCase() : "") +
        (cs.year ? " &middot; " + esc(cs.year) : "") +
        (cs.church ? ' &nbsp;<span class="stamp-church">CHURCH</span>' : "") + "</div>");
      sec.appendChild(head);

      var row = el("div", "case-media-row");
      cs.media.forEach(function (m, mi) {
        var fig = el("figure", "case-fig");
        fig.style.margin = "0";
        scatterPaste(fig, cs.id + "|figure|" + mi, 0.75);
        var frame = fpoFrame(m.poster, cs.title + " — figure " + (mi + 1));
        fig.appendChild(frame);
        if (m.type === "video" || m.type === "embed") {
          fig.appendChild(el("span", "play-badge", "▶"));
        }
        var cap = el("figcaption", "figcap",
          "<span>" + esc(m.caption || "") + "</span><span>" + esc(cs.id).toUpperCase() + "-" + jobNo(mi) + "</span>");
        fig.appendChild(cap);
        var open = function () { openJacket(caseMediaAsItem(cs, m), null); };
        frame.style.cursor = "pointer";
        frame.addEventListener("click", open);
        fig.setAttribute("tabindex", "0");
        fig.addEventListener("keydown", function (e) { if (e.key === "Enter") open(); });
        row.appendChild(fig);
      });
      sec.appendChild(row);

      var meta = el("dl", "case-meta");
      [["Challenge", cs.challenge], ["My role", cs.role], ["The craft", cs.craft], ["Impact", cs.impact]]
        .forEach(function (pair) {
          if (!pair[1]) return;
          meta.appendChild(el("dt", "", esc(pair[0]).toUpperCase()));
          meta.appendChild(el("dd", "", esc(pair[1])));
        });
      if (cs.principle) {
        meta.appendChild(el("dt", "", "THE PRINCIPLE"));
        meta.appendChild(el("dd", "", esc(cs.principle) +
          (cs.essay ? ' &mdash; <a href="blog.html#' + esc(cs.essay.slug) + '">read the essay: &ldquo;' + esc(cs.essay.title) + '&rdquo;</a>' : "")));
      }
      sec.appendChild(meta);

      sec.appendChild(el("div", "slugline",
        "<span>JOB NO. " + esc(cs.id).toUpperCase() + "-001" +
        (cs.client ? " &middot; CLIENT: " + esc(cs.client).toUpperCase() : "") +
        "</span><span>PASTE-UP: B. YOUNG</span>"));

      host.appendChild(sec);
    });
  }

  function caseMediaAsItem(cs, m) {
    return {
      title: cs.title,
      type: m.type,
      src: m.src,
      poster: m.poster,
      categories: cs.codes.map(function (c) { return c.toLowerCase(); }),
      church: cs.church,
      role: cs.role,
      story: cs.challenge,
      _caption: m.caption
    };
  }

  /* ---------- flat file ---------- */
  function renderTabs() {
    var tabs = document.getElementById("tabs");
    tabs.innerHTML = "";
    TABS.forEach(function (t) {
      var n = DATA.archive.filter(function (i) { return matches(i, t[0]); }).length;
      var b = el("button", t[0] === cur ? "on" : "", esc(t[1]) + " (" + n + ")");
      b.setAttribute("role", "tab");
      b.setAttribute("aria-selected", t[0] === cur ? "true" : "false");
      b.addEventListener("click", function () {
        if (t[0] === cur) return;
        liftThenFilter(t[0]);
      });
      tabs.appendChild(b);
    });
  }

  function fileCard(item, idx, animIdx) {
    var b = el("button", "fcard");
    b.type = "button";
    if (item.title.length <= 20) b.classList.add("fcard-short-title");
    b.style.animationDelay = (animIdx * 40) + "ms";
    scatterPaste(b, item.title + "|" + idx, 0.95);
    scatterLift(b, item.title + "|lift|" + idx);
    scatterSlide(b, item.title + "|slide|" + idx);

    var media = el("div", "fcard-media");
    var frame = fpoFrame(item.poster, item.title);
    frame.style.position = "absolute";
    frame.style.inset = "0";
    media.appendChild(frame);
    if (isChurch(item)) media.appendChild(el("span", "stamp-church", "CHURCH"));
    if (item.gallery && item.gallery.length > 1) {
      media.appendChild(el("span", "mini-gallery",
        '<svg viewBox="0 0 16 16" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">' +
        '<rect x="1" y="4" width="10.5" height="10.5" rx="1"/>' +
        '<path d="M4.5 4V2.5A1.5 1.5 0 0 1 6 1h7.5A1.5 1.5 0 0 1 15 2.5V10a1.5 1.5 0 0 1-1.5 1.5H12"/>' +
        "</svg>" + item.gallery.length));
    } else if (item.type === "video" || item.type === "embed") {
      media.appendChild(el("span", "mini-play", "▶"));
    }
    b.appendChild(media);

    b.appendChild(el("div", "fcard-title", esc(item.title)));
    b.appendChild(el("div", "fcard-slug",
      '<span class="codes">' + codesFor(item) + "</span><span>NO. " + jobNo(idx) + "</span>"));

    b.addEventListener("click", function () {
      var list = DATA.archive.filter(function (i) { return matches(i, cur); });
      openJacket(item, list);
    });
    return b;
  }

  function paintGrid() {
    var grid = document.getElementById("filegrid");
    var count = document.getElementById("fileCount");
    var more = document.getElementById("pullMore");

    var list = DATA.archive.filter(function (i) { return matches(i, cur); });
    var vis = list.slice(0, shown);

    count.textContent = "SHOWING " + vis.length + " OF " + list.length + " JOBS ON FILE";
    grid.innerHTML = "";
    vis.forEach(function (item, k) {
      var globalIdx = DATA.archive.indexOf(item);
      var card = fileCard(item, globalIdx, k % PAGE);
      card.style.animationDelay = ((k % PAGE) * 60) + "ms";
      grid.appendChild(card);
    });

    if (list.length > shown) {
      more.style.display = "block";
      more.textContent = "PULL MORE FROM THE FILE (" + (list.length - vis.length) + " REMAINING)";
    } else {
      more.style.display = "none";
    }
  }

  function liftThenFilter(next) {
    var grid = document.getElementById("filegrid");
    var cards = Array.prototype.slice.call(grid.children);
    if (reduceMotion || !cards.length) {
      cur = next; shown = PAGE; renderTabs(); paintGrid();
      return;
    }
    cards.forEach(function (c, i) {
      c.style.animationDelay = (i * 22) + "ms";
      c.classList.add("lift");
    });
    setTimeout(function () {
      cur = next; shown = PAGE; renderTabs(); paintGrid();
    }, 480 + cards.length * 22);
  }

  /* ---------- job jacket ---------- */
  var galItems = [];
  var galIdx = 0;

  function normalizeGallery(item) {
    if (!item.gallery || !item.gallery.length) return null;
    return item.gallery.map(function (g) {
      return typeof g === "string" ? { src: g, caption: "" } : g;
    });
  }

  function renderGallery(host, item) {
    var g = galItems[galIdx];
    host.innerHTML =
      '<img class="gal-img" src="' + esc(g.src) + '" alt="' + esc(item.title) + " — " + (galIdx + 1) + '">' +
      '<button class="gal-btn gal-prev" type="button" aria-label="Previous image">&larr;</button>' +
      '<button class="gal-btn gal-next" type="button" aria-label="Next image">&rarr;</button>' +
      '<span class="gal-count">' + (galIdx + 1) + " / " + galItems.length +
      (g.caption ? " &middot; " + esc(g.caption).toUpperCase() : "") + "</span>";
    if (isChurch(item)) host.appendChild(el("span", "stamp-church", "CHURCH"));

    host.querySelector(".gal-prev").addEventListener("click", function (e) { e.stopPropagation(); stepGallery(-1, item); });
    host.querySelector(".gal-next").addEventListener("click", function (e) { e.stopPropagation(); stepGallery(1, item); });

    /* preload neighbors */
    [galIdx + 1, galIdx - 1].forEach(function (n) {
      var m = galItems[(n + galItems.length) % galItems.length];
      if (m) { var pre = new Image(); pre.src = m.src; }
    });
  }

  function stepGallery(dir, item) {
    if (!galItems.length) return;
    galIdx = (galIdx + dir + galItems.length) % galItems.length;
    renderGallery(document.getElementById("jkMedia"), item);
  }

  /* ---------- house PDF viewer (no browser toolbar) ---------- */
  var pdfState = null;

  function ensurePdfJs() {
    return new Promise(function (resolve, reject) {
      if (window.pdfjsLib) return resolve(window.pdfjsLib);
      var s = document.createElement("script");
      s.src = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js";
      s.onload = function () {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
          "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
        resolve(window.pdfjsLib);
      };
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  function openPdf(host, item) {
    host.innerHTML = '<div class="pdf-loading">PULLING PAGES FROM THE JACKET&hellip;</div>';
    ensurePdfJs()
      .then(function (lib) { return lib.getDocument(item.src).promise; })
      .then(function (doc) {
        pdfState = { doc: doc, page: 1, total: doc.numPages };
        host.innerHTML =
          '<canvas class="pdf-canvas"></canvas>' +
          '<button class="gal-btn gal-prev" type="button" aria-label="Previous page">&larr;</button>' +
          '<button class="gal-btn gal-next" type="button" aria-label="Next page">&rarr;</button>' +
          '<button class="gal-btn pdf-fs" type="button" aria-label="Full screen" title="Full screen">' +
          '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' +
          '<path d="M5 1H1v4M9 1h4v4M5 13H1V9M9 13h4V9"/></svg></button>' +
          '<span class="gal-count pdf-count"></span>';
        host.querySelector(".gal-prev").addEventListener("click", function (e) { e.stopPropagation(); stepPdf(-1); });
        host.querySelector(".gal-next").addEventListener("click", function (e) { e.stopPropagation(); stepPdf(1); });
        host.querySelector(".pdf-fs").addEventListener("click", function (e) { e.stopPropagation(); togglePdfFullscreen(host); });
        renderPdfPage();
      })
      .catch(function () {
        /* pdf.js unavailable — fall back to browser viewer, toolbar suppressed where honored */
        pdfState = null;
        host.innerHTML = '<iframe src="' + esc(item.src) + '#toolbar=0&navpanes=0" title="' +
          esc(item.title) + '" style="aspect-ratio:3/4;max-height:60vh;"></iframe>';
      });
  }

  function isPdfFullscreen(host) {
    return document.fullscreenElement === host || host.classList.contains("pdf-max");
  }

  function togglePdfFullscreen(host) {
    if (document.fullscreenElement === host) {
      document.exitFullscreen();
    } else if (host.classList.contains("pdf-max")) {
      host.classList.remove("pdf-max");
      renderPdfPage();
    } else if (host.requestFullscreen) {
      host.requestFullscreen().catch(function () {
        host.classList.add("pdf-max");
        renderPdfPage();
      });
    } else {
      /* iOS Safari and friends: CSS takeover instead of the Fullscreen API */
      host.classList.add("pdf-max");
      renderPdfPage();
    }
  }

  function renderPdfPage() {
    if (!pdfState) return;
    var host = document.getElementById("jkMedia");
    var canvas = host.querySelector(".pdf-canvas");
    var count = host.querySelector(".pdf-count");
    if (!canvas) return;
    pdfState.doc.getPage(pdfState.page).then(function (page) {
      var maxW = host.clientWidth || 640;
      var maxH = isPdfFullscreen(host) ? (host.clientHeight || window.innerHeight) : Infinity;
      var base = page.getViewport({ scale: 1 });
      var fit = Math.min(maxW / base.width, maxH / base.height);
      var dpr = Math.min(window.devicePixelRatio || 1, 2);
      var vp = page.getViewport({ scale: fit * dpr });
      canvas.width = vp.width;
      canvas.height = vp.height;
      canvas.style.width = Math.round(vp.width / dpr) + "px";
      canvas.style.height = Math.round(vp.height / dpr) + "px";
      page.render({ canvasContext: canvas.getContext("2d"), viewport: vp });
      if (count) count.textContent = "PAGE " + pdfState.page + " / " + pdfState.total;
    });
  }

  function stepPdf(dir) {
    if (!pdfState) return;
    pdfState.page = ((pdfState.page - 1 + dir + pdfState.total) % pdfState.total) + 1;
    renderPdfPage();
  }

  document.addEventListener("fullscreenchange", function () {
    if (pdfState) renderPdfPage();
  });

  var pdfResizeTimer = null;
  window.addEventListener("resize", function () {
    if (!pdfState) return;
    clearTimeout(pdfResizeTimer);
    pdfResizeTimer = setTimeout(renderPdfPage, 180);
  });

  function mediaHTML(item) {
    var src = item.src || "";
    if (item.type === "embed") {
      return '<iframe src="' + esc(src) + '" allow="autoplay; fullscreen" allowfullscreen title="' + esc(item.title) + '"></iframe>';
    }
    if (item.type === "pdf" || /\.pdf(\?|$)/i.test(src)) {
      return '<iframe src="' + esc(src) + '" title="' + esc(item.title) + '" style="aspect-ratio:3/4;max-height:60vh;"></iframe>';
    }
    if (/\.mp3(\?|$)/i.test(src)) {
      return '<img src="' + esc(item.poster) + '" alt="' + esc(item.title) + '"><audio controls src="' + esc(src) + '"></audio>';
    }
    if (item.type === "video") {
      return '<video controls autoplay playsinline poster="' + esc(item.poster) + '" src="' + esc(src) + '"></video>';
    }
    return '<img src="' + esc(src) + '" alt="' + esc(item.title) + '">';
  }

  function openJacket(item, list) {
    jacketList = list || [];
    jacketIdx = jacketList.indexOf(item);

    var layer = document.getElementById("jacketLayer");
    var globalIdx = DATA.archive.indexOf(item);
    var no = globalIdx !== -1 ? jobNo(globalIdx) : "CS";

    document.getElementById("jkNo").textContent = "JOB JACKET · NO. " + no;
    document.getElementById("jkPos").textContent = jacketIdx !== -1
      ? (jacketIdx + 1) + " / " + jacketList.length + " IN " + cur.toUpperCase()
      : "FROM CASE STUDY";

    var mediaHost = document.getElementById("jkMedia");
    galItems = normalizeGallery(item) || [];
    galIdx = 0;
    if (pdfState && pdfState.doc) { pdfState.doc.destroy(); }
    pdfState = null;
    if (galItems.length) {
      renderGallery(mediaHost, item);
      mediaHost._galItem = item;
    } else if (item.type === "pdf" || /\.pdf(\?|$)/i.test(item.src || "")) {
      mediaHost._galItem = null;
      openPdf(mediaHost, item);
      if (isChurch(item)) mediaHost.appendChild(el("span", "stamp-church", "CHURCH"));
    } else {
      mediaHost._galItem = null;
      mediaHost.innerHTML = mediaHTML(item);
      if (isChurch(item)) mediaHost.appendChild(el("span", "stamp-church", "CHURCH"));
    }

    document.getElementById("jkTitle").textContent = item.title;
    document.getElementById("jkCodes").textContent = codesFor(item) +
      (item.client ? " · " + item.client.toUpperCase() : "") +
      (item.year ? " · " + item.year : "");

    var meta = document.getElementById("jkMeta");
    meta.innerHTML = "";
    [["ROLE", item.role], ["STORY", item.story], ["TOOLS", item.tools]].forEach(function (pair) {
      if (!pair[1]) return;
      meta.appendChild(el("dt", "", pair[0]));
      meta.appendChild(el("dd", "", esc(pair[1])));
    });

    var prev = document.getElementById("jkPrev");
    var next = document.getElementById("jkNext");
    prev.style.visibility = next.style.visibility = jacketIdx !== -1 ? "visible" : "hidden";

    document.getElementById("jkShare").textContent = globalIdx !== -1
      ? "SHARE THIS JOB → #JOB-" + no
      : "";

    if (globalIdx !== -1 && history.replaceState) {
      history.replaceState(null, "", "#job-" + no);
    }

    layer.classList.add("show");
    document.body.style.overflow = "hidden";
    document.getElementById("jkClose").focus();
  }

  function stepJacket(dir) {
    if (jacketIdx === -1 || !jacketList.length) return;
    jacketIdx = (jacketIdx + dir + jacketList.length) % jacketList.length;
    openJacket(jacketList[jacketIdx], jacketList);
  }

  function closeJacket() {
    var layer = document.getElementById("jacketLayer");
    layer.classList.remove("show");
    if (pdfState && pdfState.doc) { pdfState.doc.destroy(); }
    pdfState = null;
    document.getElementById("jkMedia").innerHTML = "";
    document.body.style.overflow = "";
    if (history.replaceState) history.replaceState(null, "", location.pathname + location.search);
  }

  function wireJacket() {
    document.getElementById("jkClose").addEventListener("click", closeJacket);
    document.getElementById("jkRefile").addEventListener("click", closeJacket);
    document.querySelector(".jacket-backdrop").addEventListener("click", closeJacket);
    document.getElementById("jkPrev").addEventListener("click", function () { stepJacket(-1); });
    document.getElementById("jkNext").addEventListener("click", function () { stepJacket(1); });
    document.getElementById("jkShare").addEventListener("click", function () {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(location.href);
        this.textContent = "LINK COPIED ✓";
      }
    });
    document.addEventListener("keydown", function (e) {
      if (!document.getElementById("jacketLayer").classList.contains("show")) return;
      var mediaHost = document.getElementById("jkMedia");
      if (e.key === "Escape") {
        if (mediaHost.classList.contains("pdf-max")) {
          mediaHost.classList.remove("pdf-max");
          renderPdfPage();
          return;
        }
        if (document.fullscreenElement) return; /* browser handles fullscreen exit */
        closeJacket();
        return;
      }
      if (e.key === "ArrowLeft") {
        if (mediaHost._galItem) stepGallery(-1, mediaHost._galItem);
        else if (pdfState) stepPdf(-1);
        else stepJacket(-1);
      }
      if (e.key === "ArrowRight") {
        if (mediaHost._galItem) stepGallery(1, mediaHost._galItem);
        else if (pdfState) stepPdf(1);
        else stepJacket(1);
      }
    });

    /* swipe to thumb through a gallery on touch screens */
    var touchX = null;
    var mediaEl = document.getElementById("jkMedia");
    mediaEl.addEventListener("touchstart", function (e) {
      touchX = e.touches[0].clientX;
    }, { passive: true });
    mediaEl.addEventListener("touchend", function (e) {
      if (touchX === null || (!mediaEl._galItem && !pdfState)) { touchX = null; return; }
      var dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 40) {
        if (mediaEl._galItem) stepGallery(dx < 0 ? 1 : -1, mediaEl._galItem);
        else stepPdf(dx < 0 ? 1 : -1);
      }
      touchX = null;
    }, { passive: true });
  }

  function openFromHash() {
    var m = location.hash.match(/^#job-(\d{3})$/);
    if (!m) return;
    var idx = parseInt(m[1], 10) - 1;
    if (idx >= 0 && idx < DATA.archive.length) {
      var list = DATA.archive.filter(function (i) { return matches(i, cur); });
      openJacket(DATA.archive[idx], list);
    }
  }

  /* ---------- the blog (latest posts on index) ---------- */
  function renderDesk() {
    var sec = document.getElementById("desk");
    var host = document.getElementById("deskPosts");
    if (!sec || !host || !window.ColumnCore) return;
    window.ColumnCore.load()
      .then(function (posts) {
        if (!posts.length) return;
        posts.slice(0, 3).forEach(function (p) {
          var d = new Date(p.date + "T12:00:00")
            .toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })
            .toUpperCase();
          var entry = el("article", "entry");
          entry.innerHTML =
            '<p class="entry-meta"><span class="kicker">' + esc(p.topic || p.kicker || "Field Notes") + "</span>" +
            '<span class="mono">' + d + "</span></p>" +
            '<h3 class="entry-title"><a href="blog.html#' + esc(p.slug) + '">' + esc(p.title) + "</a></h3>" +
            '<p class="entry-deck">' + esc(p.deck || p.excerpt || "") + "</p>";
          host.appendChild(entry);
        });
        sec.hidden = false;
      })
      .catch(function () { /* no blog data — section stays hidden */ });
  }

  /* ---------- boot ---------- */
  function boot() {
    Array.prototype.slice.call(document.querySelectorAll(".p-in")).forEach(function (node, i) {
      scatterPaste(node, "hero|" + i + "|" + node.className, i === 2 ? 0.45 : 0.7);
    });

    /* static FPO frames written in the HTML (e.g. the hero portrait) —
       the observer only knows about JS-created frames, so wire these up too */
    Array.prototype.slice.call(document.querySelectorAll(".fpo")).forEach(function (f) {
      if (f.querySelector("img[data-src]")) {
        if (io && !reduceMotion) io.observe(f);
        else placeArt(f);
      }
    });

    fetch("data/portfolio.json")
      .then(function (r) { return r.json(); })
      .then(function (json) {
        DATA = json;
        renderCases();
        renderTabs();
        paintGrid();
        wireJacket();
        openFromHash();
        renderDesk();

        document.getElementById("pullMore").addEventListener("click", function () {
          shown += PAGE;
          paintGrid();
        });

        document.getElementById("specToggle").addEventListener("click", function () {
          document.body.classList.toggle("specs");
          this.textContent = document.body.classList.contains("specs")
            ? "HIDE THE BOARD" : "SHOW THE BOARD";
        });

        setTimeout(function () {
          document.getElementById("hero").classList.add("play");
        }, reduceMotion ? 0 : 350);
      })
      .catch(function (err) {
        var host = document.getElementById("caseStudies");
        if (host) host.innerHTML = '<p class="wrap" style="padding:40px 0;">Could not load portfolio data (' + esc(err.message) + "). If you opened this file directly, run it from a web server — fetch() needs http://.</p>";
      });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
