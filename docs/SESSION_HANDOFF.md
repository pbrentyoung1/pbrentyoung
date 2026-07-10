# Session Handoff

Last updated: 2026-07-10

## Current Repository State

- `main` is synced with `origin/main` at `1115d29 Restore classic homepage until cutover is approved`.
- Working tree is clean. The index.html restoration and the mobile slide-table fix are now committed.
- `index-new.html` remains the newer editorial/paste-up page and contains the slide-table flat-file treatment.

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

- Video re-upload to video.pbrentyoung.com in progress (2026-07-10). When done, verify all references resolve (script check) and that `pbrentyoung.com/media/<file>` no longer matters — the old `public_html/media` copy is expected to be deleted by the next deploy, which is now harmless.
- Run a redeploy after uploads finish: it delivers the updated MEDIA_HOST in both JS files and clears the stale `public_html/media` copy.
- The old `media.pbrentyoung.com` subdomain can be removed in hPanel once video.pbrentyoung.com is confirmed working.
- Decide whether to remove `.DS_Store` files from tracking in a future cleanup. They were intentionally included in a prior "commit everything" request.
