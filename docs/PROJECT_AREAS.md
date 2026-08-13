# Project Areas and Task Organization

Last updated: 2026-07-30

The brentyoung.org project uses one central Project Hub and five specialized
tasks. The tasks separate different kinds of thinking without creating separate
sources of truth.

Conversations help the work move. The repository preserves what the project
learns.

## Project Hub

The pinned **brentyoung.org Project Hub** is the coordination layer for the
whole site.

Use the Hub for:

- Current priorities and project-wide status
- Work that affects more than one area
- Decisions that require tradeoffs between areas
- Migration milestones and major handoffs
- Resolving uncertainty about where work belongs
- Periodic consolidation of decisions into repository documentation

The Hub does not replace the specialized tasks. It connects them and keeps the
whole project moving in one direction.

## Specialized Areas

| Area | Owns | Does not own | Primary durable references |
| --- | --- | --- | --- |
| Administration & Deployment | Domains, DNS, Hostinger, Git, branches, Search Console, analytics, integrations, backups, security, migration status, and production verification | Visual direction, article drafting, or campaign copy unless required for an operational change | `PROJECT_KNOWLEDGE.md`, deployment configuration, repository history |
| Site Design & Development | Layout, typography, components, responsive behavior, interaction, accessibility, performance, browser testing, and implementation | Brand positioning or editorial direction without Strategy or Content alignment | Application code, `PROJECT_KNOWLEDGE.md`, `infographics/STYLE_GUIDE.md` |
| Brand & Strategy | Positioning, audience, First Principles, brand architecture, content pillars, frameworks, product direction, and long-term priorities | Routine deployment or final article production | `FIRST_PRINCIPLES.md`, `EDITORIAL_GUIDE.md`, `CONTENT_ROADMAP.md`, `CONCEPT_MAP.md`, `IDEA_GLOSSARY.md` |
| Content & Blog | Article development, editing, voice, glossary connections, banners, thumbnails, publishing, internal linking, RSS, and the editorial calendar | Platform operations or independent changes to brand strategy | `VOICE_NOTES.md`, `ARTICLE_TEMPLATE.md`, `EDITORIAL_GUIDE.md`, `CONTENT_ROADMAP.md`, posts and glossary files |
| Social Media & Distribution | Social adaptations, channel plans, newsletters, Substack, launch sequences, repurposing, calls to action, distribution experiments, and performance learning | Changing the source article or brand position without returning to Content or Strategy | Published source content, `DISTRIBUTION_STRATEGY.md`, `PUBLISHING_ACTION_PLAN.md`, `EDITORIAL_GUIDE.md`, `VOICE_NOTES.md` |

## Routing Work

Start work in the area that owns the primary decision.

- A DNS problem belongs to Administration even if it affects the visible site.
- A responsive navigation problem belongs to Design & Development.
- A question about who the site serves belongs to Brand & Strategy.
- A new essay or glossary definition belongs to Content & Blog.
- Turning a published essay into an Instagram sequence belongs to Social Media
  & Distribution.

When a request crosses areas, the owning task should name the dependency and
bring the resulting decision back to the Hub. Do not silently make a
project-wide strategy decision inside an implementation task.

## Durable Knowledge Rule

A chat is never the only record of a material decision.

Material decisions include:

- A change to the canonical domain, branch, deployment, integration, or service
- A new or revised First Principle, definition, voice rule, audience, or
  positioning statement
- A reusable design pattern or component behavior
- A publishing or distribution workflow
- A decision that another task must follow
- A known risk, blocker, or deferred follow-up

When a material decision is made, the task handling it should:

- Update the owning repository document in the same body of work
- Preserve the reason for the decision when the reason will matter later
- Add or update a follow-up when implementation remains incomplete
- Report cross-area consequences to the Project Hub
- Avoid copying full conversations into documentation

Repository truth takes precedence over task memory. When a conversation and the
repository disagree, inspect the current code and production behavior, resolve
the disagreement, and update the documentation.

## Working Rules for Every Area

- Work from `/Users/pbrentyoung/Documents/websites/brentyoung-migration` on
  `migration/brentyoung-org` until the migration is complete.
- Read `AGENTS.md` and `PROJECT_KNOWLEDGE.md` before acting.
- Preserve unrelated and uncommitted user files.
- Commit only intentionally scoped work.
- Never expose or commit `inc/secrets.local.php`.
- Use the constitutional editorial documents instead of reconstructing the
  site's voice from memory.
- Verify work in proportion to its risk before calling it complete.
- Record durable decisions before closing a substantial task.

## Content Flow

The five areas should form one connected system:

```text
Brand & Strategy
      ↓
Content & Blog
      ↓
Social Media & Distribution

Site Design & Development supports the experience.
Administration & Deployment keeps the system reliable.
The Project Hub keeps every area aligned.
```

Social content should normally begin with a durable source idea from the site,
not an isolated demand to keep a feed busy. Distribution can reveal what
resonates, but performance does not rewrite the site's convictions by itself.
Insights that may affect positioning or editorial direction return to Strategy
or Content for deliberate consideration.

## Article Task Pattern

Content & Blog is the long-running editorial room. A substantial article may
use a temporary dedicated task when the draft, research, or visual development
would overwhelm the editorial room.

The temporary article task must still:

- Read the same constitutional documents
- Work section by section with Brent unless he asks for a complete draft
- Return the finished article and any new voice or terminology decisions to the
  authoritative worktree
- Update Content & Blog or the Project Hub with the result

Temporary tasks are work rooms, not new knowledge silos.
