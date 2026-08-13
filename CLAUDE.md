# brentyoung.org — Working Notes

This file is loaded at the start of every session. Read it, then read the
canonical docs below before writing or editing anything on the site.

## Read before drafting

The source of truth for voice and brand lives in `docs/`:

- `docs/VOICE_NOTES.md` — the concrete "how." Hard-won voice rules captured as
  we edit. **Append new rules here** whenever one is agreed.
- `docs/EDITORIAL_GUIDE.md` — the "why." Mission, positioning, tone, what we avoid.
- `docs/FIRST_PRINCIPLES.md` — the convictions every decision must serve.
- `docs/ARTICLE_TEMPLATE.md` — the beats every article follows.
- `docs/IDEA_GLOSSARY.md` — canonical definitions of house language.
- `docs/PROJECT_KNOWLEDGE.md` — current domains, branches, deployment,
  architecture, integrations, and open operational work.
- `docs/PROJECT_AREAS.md` — task ownership, work routing, and durable knowledge
  rules.
- `docs/SESSION_HANDOFF.md` — chronological implementation notes and historical
  follow-ups.

## Voice non-negotiables (the mistakes we keep re-fixing)

- **No em dashes.** Use parentheses or split into two sentences.
- **Don't number** ideas, sections, or lists unless explicitly asked.
- Say **"your brand," not "a brand."** Address the reader directly (you, your
  church). Use we/our/us for the shared calling of the church.
- Tone is direct, pastoral, warm, practical, hopeful. Lead with invitation and
  stewardship — never scolding. Keep the reader's dignity intact.
- Prefer concrete human examples over abstract brand language.
- Avoid: corporate-speak, clichés, clickbait, "Top 10/Hacks/Secrets,"
  thought-leader language, and AI tells (hedging filler, over-symmetry,
  "it's not X, it's Y" pile-ups). Avoid manipulation-implying words.
- Terminology: **Gospel** (the good news) vs **Gospels** (the four accounts);
  **Church** (universal) vs **church** (a local congregation).
- Guardrail: we don't manufacture desire, manipulate decisions, or create
  transformation — that belongs to God. StoryBrand / Evangelistic Marketing
  are messaging frameworks, not theology.
- Every article must connect to a First Principle and leave the reader thinking
  "I've never looked at it that way before." Curiosity first, answers second.

## How we edit together

Don't write the piece for Brent. Go through it section by section — Brent
drives. When he makes a tone/style change, sweep the whole piece for the same
pattern, then record the rule in `docs/VOICE_NOTES.md`.

## Architecture quick reference

- This folder on `migration/brentyoung-org` is the authoritative working copy
  and branch. Start new work here. The sibling `pbrentyoung` checkout on `main`
  is legacy migration history until the migration is complete. The intended
  end state is a reconciled `main` branch deployed to `brentyoung.org`.
- Homepage is `index.php`. Shared header/footer come from `blog_site_header()`
  and `blog_site_footer()` in `inc/blog.php` — never copy that markup into a page.
- Publish a post: Markdown in `posts/`, add the filename to `posts/index.json`,
  banner at `assets/img/blog/<slug>.jpg` (1200×630), commit, deploy. Posts live
  at `/blog/<slug>`. RSS (`/feed.xml`) and the sitemap update automatically.
- Glossary: one Markdown file per term in `glossary/`, filename = slug.
- Brevo runs email subscriptions (`brevo_form_action` in `inc/blog-config.php`;
  free plan caps 300 sends/day). Clarity analytics via `js/nav.js`.

## Deploy gotchas

- GitHub → Hostinger. The deploy **mirrors the repo**: server files not in the
  repo get deleted. `assets/video/` is gitignored and lives on
  video.pbrentyoung.com — never re-upload videos to the deploy path.
- This folder is a git worktree on branch `migration/brentyoung-org`.
