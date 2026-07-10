# Session Handoff

Last updated: 2026-07-10 (server-rendered blog rebuild)

## Blog Architecture (NEW — 2026-07-10)

The blog is now server rendered from the Markdown files in `posts/`:

- **Canonical URLs:** `/blog` is the only index URL. `/blog/` and
  `/blog.html` permanently redirect to it. Posts live at `/blog/<slug>`.
- **Shared source of truth:** `inc/blog.php` parses frontmatter, loads and
  sorts posts, renders Markdown, builds heading IDs, scores related posts,
  filters taxonomy/search results, and creates canonical asset URLs.
- **Markdown:** articles remain `.md` files. `league/commonmark` renders the
  body on the server; its locked dependencies are committed so deployment
  has no install step. The server must run PHP 8.2 or newer.
- **Blog index:** `blog.php` renders the featured story, RSS subscription,
  The Short List, topic navigation, full-text search, tag filters, a
  responsive article grid, and 24-post pagination. Topic, tag, search, and
  page state are shareable query-string URLs.
- **Article page:** `blog-post.php` renders a centered wide title/banner,
  centered share controls, an 800px reading column, sticky desktop contents
  for posts with 3+ H2s, a native mobile contents disclosure, linked tags
  and first principle, RSS subscription, and three automatically related
  banner cards. `js/article.js` only enhances copy-link behavior.
- **Selection frontmatter:** `featured: true` selects the lead story.
  `shortlist: 1` (or another positive number) selects and orders The Short
  List. `draft: true` hides a post. See `docs/ARTICLE_TEMPLATE.md`.
- **Banners:** all 10 development posts have simple working-art JPGs at
  `assets/img/blog/<slug>.jpg`, each exactly 1200×630. The same file appears
  in index cards, article heroes, related cards, Open Graph, Twitter, and
  JSON-LD. Replace these when final article art is ready.
- **Generated discovery routes:** `/feed.xml`, `/sitemap.xml`, and
  `/robots.txt` are rendered from the same post data. They never require
  manual post lists.
- **Development indexing:** `blog_public` in `inc/blog-config.php` is the
  single launch flag. While false, blog pages emit `noindex, nofollow`,
  robots disallows `/blog`, and blog URLs are omitted from the sitemap.
- **Local server:** start with `php -S 127.0.0.1:8000 router.php`. PHP's
  built-in server does not read `.htaccess`; `router.php` mirrors production.
- Blog navigation intentionally returns to `index-new.html` until the rebuilt
  homepage is approved for the root URL.
- Analytics is intentionally deferred until launch.

## Homepage Preview and Navigation

- `index-new.html` now includes a server-data-compatible **Field Notes**
  section between The File and Contact. It renders the featured post plus the
  three numbered `shortlist:` posts from `posts/`, with the same topic links,
  dates, read times, banners, and thumbnails used by the blog index.
- `js/editorial.js` loads the shared post index for the homepage preview. If no
  post is marked `featured: true`, the newest visible post is used as the
  fallback feature.
- Blog cards use 600 × 315 derivatives in `assets/img/blog/thumbs/`. Rebuild
  them after replacing banner art with:
  `scripts/generate-blog-thumbnails.sh`.
- The BLOG item in the shared navigation is a taxonomy dropdown. It includes
  All Articles, the five main topics, and The Short List. Desktop opens it on
  hover/focus; mobile opens it as a nested menu from the main navigation.
  The desktop trigger and dropdown include a hover bridge so the menu remains
  open while the pointer moves into it.

## Current Repository State

- `main` is synced with `origin/main` at `27c22f4 Move video hosting to standalone video.pbrentyoung.com site`.
- The complete blog rebuild, homepage Field Notes preview, thumbnail workflow,
  and shared BLOG dropdown are committed in the current changeset and ready
  for server testing.
- `index.html` remains the classic live homepage. `index-new.html` remains
  the editorial rebuild and is the intentional return destination from blog
  pages during development.

## Important Recent Commits

- `ebbfbdf Make flat file a slide table`
  - Adds the slide-table flat-file treatment.
  - Adds generated thumbnail assets under `assets/img/thumbs/`.
  - Adds local impact-label fonts and examples under `assets/fonts/impact_label/`.
  - Adds loupe and non-repro blue pencil graphics.
  - Repoints portfolio poster fields to smaller thumbnail assets.
- `51be6a9 Promote editorial page to homepage`
  - Promoted `index-new.html` into `index.html`.
  - This was later identified as premature; `index.html` has now been restored locally but that restore has not been committed yet.
- `54d6abf Use hand-drawn principle cues`
  - Replaces the First Principles SVG cues with hand-drawn PNGs.

## Flat File Slide Table Decisions

The flat file is now designed as mounted slides on a light table:

- Slide cards use a single warm off-white mount color.
- The film aperture uses a `3 / 2` aspect ratio.
- Card title sits centered in the top label area using Google font `Permanent Marker`.
- Bottom slide area is reserved for mono ephemera/job markings.
- Cards get stable seeded randomness through CSS variables set in `js/editorial.js`.
- Hover behavior:
  - slow transition,
  - slight lift,
  - stronger shadow,
  - subtle zoom,
  - card straightens on hover.
- Impact-label filters:
  - use local `Impact Label Reversed` font,
  - appear as loose black embossed tape strips,
  - are not sticky,
  - do not straighten on hover,
  - hover/active state uses brightness/color treatment only.
- Loupe and pencil:
  - loupe uses transparent `assets/img/loupeExample.png`,
  - pencil uses transparent `assets/img/nonReproBluePencil.png`,
  - both are desktop/tablet margin ephemera and hidden on mobile.

## Thumbnail Performance Work

The active editorial portfolio uses `data/portfolio.json`.

Changes made:

- Generated 66 thumbnail assets in `assets/img/thumbs/`.
- Replaced all active `poster` references in `data/portfolio.json` with thumbnail paths.
- Also updated legacy `data/projects.json` where possible.
- Kept full-size `src` values intact so opened modals/detail views still use original media.
- Original unique active poster payload was about `61.9 MB`.
- New thumbnail payload is about `5.3 MB`.

One legacy image could not be thumbnailed because ImageMagick reported an invalid PNG header:

- `assets/img/vlcsnap-2020-07-20-18h13m25s366.png`

## Video Hosting (RESOLVED 2026-07-10)

Videos are self-hosted on `video.pbrentyoung.com`, set up as a STANDALONE
website in hPanel (not a subdomain of pbrentyoung.com), so its document
root sits outside pbrentyoung.com's deploy-managed tree and mirror
deploys cannot delete it. (An earlier attempt used media.pbrentyoung.com
as a subdomain, but Hostinger forces subdomain roots inside public_html.)

How it works:

- The full contents of local `assets/video/` are uploaded flat to the
  video.pbrentyoung.com site root, keeping original filenames.
- `data/portfolio.json` and `data/projects.json` keep plain local
  `assets/video/...` paths — never edit them for hosting reasons.
- `mediaSrc()` in BOTH `js/editorial.js` and `js/main.js` translates at
  render time: on production hosts, `assets/video/<file>` becomes
  `https://video.pbrentyoung.com/<file>`; on localhost the relative path
  is kept so local dev plays from the local folder.
- Verified 2026-07-10: all 29 unique video references across both JSON
  files return HTTP 200 with video/* content types on the subdomain,
  and the server supports range requests (HTTP 206) for seeking.
- New videos: drop the file in local `assets/video/`, upload the same
  file to the video.pbrentyoung.com site root, reference it in JSON as
  `assets/video/<file>` — no code changes needed.

## Deployment Notes

- GitHub is current through `1115d29`.
- The git deployment MIRRORS the repo: server files not in the repo get deleted on deploy. This is what wiped `assets/video/` from the host (it is gitignored). Do not re-upload videos to the deploy-managed path — they will be deleted again on the next deploy.
- 2026-07-10: Brent manually uploaded `index.html` (classic homepage) and `css/editorial.css` to the live host; verified live — pbrentyoung.com serves the classic homepage and the current editorial.css. The repo now matches this state, so future deploys won't clobber it.
- Hostinger has previously served stale/old files after GitHub pushes.
- If live site does not update, verify whether Hostinger is pulling from GitHub or needs manual upload/deploy.
- `index.html` should remain the older homepage until the user explicitly approves making `index-new.html` the root homepage.

## Outstanding Follow-Ups

- Brent will replace the ten development article drafts with final copy.
- Replace each working-art blog banner with final 1200×630 art as the article
  is finalized.
- Review the provisional `tags:` and `principle:` values during copy editing.
- Revisit the title “The Short List” if a stronger editorial label emerges.
- At launch, set `blog_public` to `true`, choose analytics, switch blog
  navigation from `index-new.html` to `/` when the rebuild becomes the live
  homepage, then verify production rewrites and social cards.
- Decide whether to remove `.DS_Store` files from tracking in a future cleanup.
