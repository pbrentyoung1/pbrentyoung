# brentyoung.org Agent Instructions

Before changing this site, read:

1. `docs/PROJECT_KNOWLEDGE.md`
2. `docs/PROJECT_AREAS.md`
3. `docs/FIRST_PRINCIPLES.md`
4. `docs/EDITORIAL_GUIDE.md`
5. `docs/VOICE_NOTES.md`
6. `docs/ARTICLE_TEMPLATE.md` when working on an article

`docs/PROJECT_KNOWLEDGE.md` is the operational source of truth for domains,
branches, deployment, architecture, integrations, media, and open work.
`docs/PROJECT_AREAS.md` defines task ownership, work routing, and the rule that
material decisions must be preserved in the repository.
`docs/SESSION_HANDOFF.md` is a detailed chronological archive and may describe
an older production state.

Preserve these invariants:

- The canonical public site is `https://brentyoung.org`.
- Old `pbrentyoung.com` URLs redirect path-for-path to the canonical site.
- `video.pbrentyoung.com` is the active media host and must not be captured by
  the old-domain redirect.
- `inc/secrets.local.php` is private and must never be read into a response,
  committed, or copied into documentation.
- `/Users/pbrentyoung/Documents/websites/brentyoung-migration` on
  `migration/brentyoung-org` is the authoritative working copy and branch.
- Do new work here. Treat `/Users/pbrentyoung/Documents/websites/pbrentyoung`
  on `main` as legacy migration history until the migration is complete.
- Intended end state: reconcile the migration branch into `main`, then connect
  the `brentyoung.org` production deployment to `main`.
- The two local folders are worktrees of one repository. Inspect both working
  trees before merging branches or moving files between them.
- Existing uncommitted files belong to the user. Commit only files intentionally
  in scope.
- Do not leave a material project decision only in a task. Update the owning
  repository document and report cross-area consequences to the Project Hub.

Follow the voice rules in `docs/VOICE_NOTES.md`. In particular: no em dashes,
no automatic numbered sections, no corporate or AI-cliché language, and do not
write a complete article in Brent's place unless he explicitly asks.
