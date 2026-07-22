# Blog Infographic Style

Blog infographics should feel like working diagrams pinned to the site's editorial grid—not detached presentation slides.

## Canonical reference

Use `assets/img/blog/brand-vs-branding.svg` as the primary visual reference.

## Visual grammar

- Transparent canvas around a warm paper plate
- Slight plate rotation and soft lifted shadow
- Two translucent tape strips near the top edge
- Editorial kicker in blue or the diagram's primary accent
- Playfair/Georgia headline, IBM Plex Mono/Consolas labels, Source Serif/Georgia body copy
- Double rule beneath the header
- White content panels with quiet gray rules
- Restrained palette: charcoal, paper, blue, red, gold, and green
- Dark colophon bar containing the diagram's single takeaway
- Decorative effects stay behind the information; clarity wins

## Content rules

- Preserve one argument per diagram.
- Use sentence case for headlines and uppercase mono labels for navigation and structure.
- Capitalize **Brand** when it means perception; use **branding** for expression.
- Keep all meaningful text as live SVG text for accessibility and future editing.
- Include a useful `<title>` and `<desc>` in every SVG.
- Write alt text in the Markdown reference that explains the diagram rather than repeating its headline.

## Production rules

- Prefer SVG for diagrams and infographics.
- Use a `viewBox` so the asset scales responsively.
- Embed font stacks and visual definitions within each SVG; an SVG used through an `<img>` does not inherit page styles.
- Check XML validity and inspect the asset at its actual article width before publishing.
- Keep earlier raster versions unless their removal is handled as a separate cleanup task.
