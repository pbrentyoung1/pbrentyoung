const gallery = document.getElementById("gallery");
const modal = document.getElementById("modal");
const modalBody = document.getElementById("modal-body");
const closeBtn = document.getElementById("close");
const loadMoreBtn = document.querySelector(".load-more");
const filterButtons = document.querySelectorAll(".filters button");

const state = {
  projects: [],
  filter: "all",
  step: 12,
  visible: 12
};

fetch("data/projects.json")
  .then(r => r.json())
  .then(projects => {
    state.projects = projects;
    render();
  })
  .catch(() => {
    if (gallery) gallery.innerHTML = "<p>Unable to load projects.</p>";
  });

function getFilteredProjects() {
  if (state.filter === "all") return state.projects;
  return state.projects.filter(p => (p.categories || []).includes(state.filter));
}

function render() {
  if (!gallery) return;
  gallery.classList.add("is-sorting");
  gallery.innerHTML = "";

  const filtered = getFilteredProjects();
  const visibleItems = filtered.slice(0, state.visible);

  visibleItems.forEach((p, index) => {
    const card = document.createElement("article");
    card.className = "card";
    card.style.transitionDelay = `${Math.min(index, 12) * 40}ms`;

    const thumb = p.poster || p.src;
    const tags = (p.categories || []).join(" ");
    const isVideo = p.type === "video";

    card.innerHTML = `
      <button class="card__btn" type="button" data-type="${p.type}" data-src="${p.src}" data-title="${p.title}">
        <div class="card__media">
          ${thumb ? `<img src="${thumb}" alt="${p.title}" loading="lazy">` : `<div class="card__placeholder">${p.title}</div>`}
          ${isVideo ? `<span class="play">&#9654;</span>` : ""}
        </div>
        <div class="card__meta">
          <h3>${p.title}</h3>
          ${tags ? `<div class="tags">${tags}</div>` : ""}
        </div>
      </button>
    `;

    gallery.appendChild(card);
  });

  requestAnimationFrame(() => {
    gallery.querySelectorAll(".card").forEach(card => {
      card.classList.add("is-visible");
    });
    gallery.classList.remove("is-sorting");
  });

  if (loadMoreBtn) {
    loadMoreBtn.style.display = filtered.length > state.visible ? "inline-block" : "none";
  }
}

filterButtons.forEach(btn => {
  btn.onclick = () => {
    filterButtons.forEach(b => {
      b.classList.remove("active");
      b.setAttribute("aria-selected", "false");
    });
    btn.classList.add("active");
    btn.setAttribute("aria-selected", "true");

    state.filter = btn.dataset.filter || "all";
    state.visible = state.step;
    render();
  };
});

if (loadMoreBtn) {
  loadMoreBtn.onclick = () => {
    state.visible += state.step;
    render();
  };
}

function openModal(p) {
  if (!modal || !modalBody) return;
  modal.classList.add("show");
  modal.setAttribute("aria-hidden", "false");

  const src = (p.src || "").trim();
  const lowerSrc = src.toLowerCase();
  const isEmbed =
    lowerSrc.includes("youtube.com/embed") ||
    lowerSrc.includes("player.vimeo.com/video");
  const isAudio = lowerSrc.endsWith(".mp3") || lowerSrc.includes(".mp3?");
  const isPdf = lowerSrc.endsWith(".pdf") || lowerSrc.includes(".pdf?");

  if (isPdf) {
    modalBody.innerHTML = `
      <iframe src="${src}" title="${p.title || "Document"}" style="width:100%;height:75vh;border:0;"></iframe>
      <p style="margin:8px 0 0;text-align:center;">
        <a href="${src}" target="_blank" rel="noopener" style="color:#e57c28;text-decoration:underline;">Open PDF in new tab</a>
      </p>
    `;
  } else if (p.type === "video" && isEmbed) {
    modalBody.innerHTML = `
      <div style="position:relative;padding-top:56.25%;">
        <iframe
          src="${src}"
          title="${p.title || "Video"}"
          allow="autoplay; fullscreen; picture-in-picture"
          allowfullscreen
          style="position:absolute;inset:0;width:100%;height:100%;border:0;"
        ></iframe>
      </div>
    `;
  } else if (p.type === "video" && isAudio) {
    modalBody.innerHTML = `
      <audio controls autoplay style="width:100%;">
        <source src="${src}">
      </audio>
    `;
  } else if (p.type === "video") {
    modalBody.innerHTML = `
      <video controls autoplay>
        <source src="${src}">
      </video>
    `;
  } else {
    modalBody.innerHTML = `<img src="${src}" alt="${p.title}">`;
  }
}

function closeModal() {
  if (!modal || !modalBody) return;
  modal.classList.remove("show");
  modal.setAttribute("aria-hidden", "true");
  modalBody.innerHTML = "";
}

if (gallery) {
  gallery.addEventListener("click", e => {
    const btn = e.target.closest(".card__btn");
    if (!btn) return;
    openModal({
      type: btn.getAttribute("data-type"),
      src: btn.getAttribute("data-src"),
      title: btn.getAttribute("data-title")
    });
  });
}

if (closeBtn) {
  closeBtn.onclick = closeModal;
}

if (modal) {
  const backdrop = modal.querySelector(".backdrop");
  if (backdrop) backdrop.onclick = closeModal;
}
