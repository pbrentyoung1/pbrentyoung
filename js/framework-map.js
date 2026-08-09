(function () {
  var mount = document.querySelector('[data-framework-map-mount]');
  if (!mount) return;

  var I = {
    found: '<path class="emap-icon-part emap-icon-part--ground" d="M3 20 H21"/><rect class="emap-icon-part emap-icon-part--block emap-icon-part--block-one" x="6" y="13" width="12" height="6"/><rect class="emap-icon-part emap-icon-part--block emap-icon-part--block-two" x="8" y="8" width="8" height="5"/>',
    strat: '<circle class="emap-icon-part emap-icon-part--compass-ring" cx="12" cy="12" r="8"/><path class="emap-icon-part emap-icon-part--needle" d="M12 6 L14.5 13 L12 11.5 L9.5 13 Z"/>',
    invite: '<path class="emap-icon-part emap-icon-part--stem" d="M12 20 V10"/><path class="emap-icon-part emap-icon-part--leaf emap-icon-part--leaf-left" d="M12 11 C8 10 6 7 6 5 C10 5 12 8 12 11"/><path class="emap-icon-part emap-icon-part--leaf emap-icon-part--leaf-right" d="M12 11 C16 10 18 7 18 5 C14 5 12 8 12 11"/>',
    story: '<path class="emap-icon-part emap-icon-part--page emap-icon-part--page-left" d="M12 7 C10 5 6 5 4 6 V18 C6 17 10 17 12 18"/><path class="emap-icon-part emap-icon-part--page emap-icon-part--page-right" d="M12 7 C14 5 18 5 20 6 V18 C18 17 14 17 12 18"/>',
    eye: '<path class="emap-icon-part emap-icon-part--eye" d="M3 12 C7 6 17 6 21 12 C17 18 7 18 3 12 Z"/><circle class="emap-icon-part emap-icon-part--pupil" cx="12" cy="12" r="2.4"/>'
  };
  function icon(k) {
    return '<svg class="emap-symbol emap-symbol--' + k + '" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' + I[k] + '</svg>';
  }

  var cfg = [
    { id: 'foundations', title: 'Foundations', q: 'What has God entrusted to us?', icon: 'found', face: '#e7d6ad', edge: '#cdb884', ink: '#9a7b2e', tag: 'what holds it up', order: 0 },
    { id: 'ministry-strategy', title: 'Ministry & Strategy', q: 'How does calling take practical form?', icon: 'strat', face: '#d3ddca', edge: '#b4c2a6', ink: '#5f6e4b', order: 1 },
    { id: 'invitation-transformation', title: 'Invitation & Transformation', q: 'How do we help people take a faithful next step?', icon: 'invite', face: '#ecccbb', edge: '#d3a892', ink: '#a9552f', order: 2 },
    { id: 'story-expression', title: 'Story & Expression', q: 'How do we carry what is true?', icon: 'story', face: '#cde0e7', edge: '#a3c4d0', ink: '#2f7189', order: 3 },
    { id: 'encounter-perception', title: 'Encounter & Perception', q: 'What do people actually experience?', icon: 'eye', face: '#e6e2d6', edge: '#c9c2ad', ink: '#736c59', tag: 'what people see', order: 4 }
  ];
  var domOrder = cfg.slice().sort(function (a, b) { return b.order - a.order; });

  mount.innerHTML =
    '<div class="emap-wrap">' +
      '<div class="emap-left">' +
        '<div class="emap-cap" style="margin-bottom:14px;">The framework, from the ground up</div>' +
        '<div class="emap-stack" id="emapStack"></div>' +
        '<div style="display:flex;justify-content:center;margin-top:14px;"><button class="emap-replay" id="emapReplay" type="button">Build again</button></div>' +
      '</div>' +
      '<div class="emap-panel" id="emapPanel" role="region" aria-live="polite" aria-label="Selected framework layer"></div>' +
    '</div>';

  var stack = document.getElementById('emapStack');
  var panel = document.getElementById('emapPanel');
  var replay = document.getElementById('emapReplay');
  var bandEls = {};
  var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  domOrder.forEach(function (c) {
    var d = document.createElement('button');
    d.type = 'button';
    d.className = 'emap-band';
    d.disabled = true;
    d.setAttribute('aria-pressed', 'false');
    d.setAttribute('aria-controls', 'emapPanel');
    d.style.background = c.face;
    d.style.borderBottomColor = c.edge;
    d.style.borderBottomStyle = 'solid';
    d.innerHTML =
      '<div class="emap-band__row">' +
        '<div class="emap-ico" style="border-color:' + c.edge + '">' + icon(c.icon) + '</div>' +
        '<div style="flex:1 1 auto;min-width:0;">' +
          '<div style="display:flex;justify-content:space-between;align-items:baseline;gap:8px;">' +
            '<span class="emap-band__title">' + c.title + '</span>' +
            (c.tag ? '<span class="emap-band__tag">' + c.tag + '</span>' : '') +
          '</div>' +
          '<div class="emap-band__q">' + c.q + '</div>' +
        '</div>' +
      '</div>';
    d.addEventListener('click', function () { select(c.id); });
    stack.appendChild(d);
    bandEls[c.id] = d;
  });

  function sectionData(id) {
    var sec = document.getElementById(id);
    if (!sec) return { items: [], conn: '' };
    var items = [];
    var divs = sec.querySelectorAll('.framework-term-guide > div');
    Array.prototype.forEach.call(divs, function (div) {
      var dtEl = div.querySelector('dt');
      var ddEl = div.querySelector('dd');
      if (!dtEl || !ddEl) return;
      var strong = ddEl.querySelector('strong');
      var q = strong ? strong.textContent.trim() : '';
      var full = ddEl.textContent.trim();
      var def = (q && full.indexOf(q) === 0) ? full.slice(q.length).trim() : full;
      items.push({ term: dtEl.textContent.trim(), q: q, def: def });
    });
    var connEl = sec.querySelector('.framework-connection p');
    return { items: items, conn: connEl ? connEl.textContent.trim() : '' };
  }

  function esc(s) { var t = document.createElement('div'); t.textContent = s; return t.innerHTML; }

  var panelTimers = [];
  var iconTimers = [];
  function animateIcon(container) {
    if (!container || reducedMotion) return;
    container.classList.remove('is-icon-animating');
    void container.offsetWidth;
    container.classList.add('is-icon-animating');
    iconTimers.push(setTimeout(function () {
      container.classList.remove('is-icon-animating');
    }, 900));
  }

  function select(id) {
    var c = null;
    cfg.forEach(function (x) { if (x.id === id) c = x; });
    if (!c) return;
    Object.keys(bandEls).forEach(function (k) {
      var active = k === id;
      bandEls[k].classList.toggle('active', active);
      bandEls[k].setAttribute('aria-pressed', active ? 'true' : 'false');
    });
    animateIcon(bandEls[id].querySelector('.emap-ico'));
    panel.style.setProperty('--sec', c.ink);
    panel.style.setProperty('--sec-soft', c.edge);
    var data = sectionData(id);
    var items = data.items.map(function (t) {
      return '<div class="emap-q-item">' +
        '<span class="emap-q-item__name">' + esc(t.term) + '</span>' +
        (t.q ? '<span class="emap-q-item__q">' + esc(t.q) + '</span>' : '') +
      '</div>';
    }).join('');
    panel.innerHTML =
      '<div class="emap-panel__head">' +
        '<span class="emap-panel__ico">' + icon(c.icon) + '</span>' +
        '<span class="emap-panel__label">' + esc(c.title) + '</span>' +
      '</div>' +
      '<div class="emap-qlist">' + items + '</div>' +
      '<a class="emap-panel__link" href="#' + id + '">Read the ' + esc(c.title) + ' section &rarr;</a>';
    animateIcon(panel.querySelector('.emap-panel__ico'));
    panelTimers.forEach(clearTimeout); panelTimers = [];
    var qs = panel.querySelectorAll('.emap-q-item');
    Array.prototype.forEach.call(qs, function (el, i) {
      panelTimers.push(setTimeout(function () { el.classList.add('in'); }, 80 + i * 95));
    });
  }

  function resetPanel() {
    panel.innerHTML =
      '<div class="emap-panel__label">The framework</div>' +
      '<div class="emap-panel__q">Build it from the ground up.</div>' +
      '<div class="emap-panel__conn">Each layer rests on the one beneath it. Select a layer to see how its ideas work together.</div>';
  }
  resetPanel();

  var timers = [], built = false;
  function measure() {
    var prev = stack.style.height;
    stack.style.height = 'auto';
    Object.keys(bandEls).forEach(function (k) {
      bandEls[k].classList.add('in');
      bandEls[k].querySelector('.emap-band__row').classList.add('in');
    });
    var h = stack.offsetHeight;
    stack.style.height = prev;
    return h;
  }
  function build() {
    timers.forEach(clearTimeout); timers = [];
    iconTimers.forEach(clearTimeout); iconTimers = [];
    built = false;
    replay.disabled = true;
    stack.setAttribute('aria-busy', 'true');
    resetPanel();
    Object.keys(bandEls).forEach(function (k) {
      bandEls[k].classList.remove('active');
      bandEls[k].setAttribute('aria-pressed', 'false');
      bandEls[k].disabled = true;
    });
    stack.style.height = measure() + 'px';
    domOrder.forEach(function (c) {
      bandEls[c.id].classList.remove('in');
      bandEls[c.id].querySelector('.emap-band__row').classList.remove('in');
    });
    if (reducedMotion) {
      Object.keys(bandEls).forEach(function (k) {
        bandEls[k].classList.add('in');
        bandEls[k].querySelector('.emap-band__row').classList.add('in');
        bandEls[k].disabled = false;
      });
      stack.style.height = 'auto';
      stack.setAttribute('aria-busy', 'false');
      replay.disabled = false;
      built = true;
      select('foundations');
      return;
    }
    var per = 620;
    cfg.forEach(function (c) {
      var t0 = c.order * per + 150;
      timers.push(setTimeout(function () { bandEls[c.id].classList.add('in'); }, t0));
      timers.push(setTimeout(function () {
        bandEls[c.id].querySelector('.emap-band__row').classList.add('in');
        bandEls[c.id].disabled = false;
        animateIcon(bandEls[c.id].querySelector('.emap-ico'));
      }, t0 + 300));
    });
    timers.push(setTimeout(function () { select('foundations'); }, 4 * per + 620));
    timers.push(setTimeout(function () {
      stack.style.height = 'auto';
      stack.setAttribute('aria-busy', 'false');
      replay.disabled = false;
      built = true;
    }, 4 * per + 850));
  }

  requestAnimationFrame(build);

  var rzTimer;
  window.addEventListener('resize', function () {
    if (!built) return;
    clearTimeout(rzTimer);
    rzTimer = setTimeout(function () { stack.style.height = 'auto'; }, 120);
  });

  replay.addEventListener('click', build);

  cfg.forEach(function (c) {
    var sec = document.getElementById(c.id);
    if (!sec) return;
    var titleEl = sec.querySelector('.framework-neighborhood__title');
    if (!titleEl || titleEl.querySelector('.framework-section-ico')) return;
    var span = document.createElement('span');
    span.className = 'framework-section-ico';
    span.style.color = c.ink;
    span.setAttribute('aria-hidden', 'true');
    span.innerHTML = icon(c.icon);
    titleEl.insertBefore(span, titleEl.firstChild);
  });
})();
