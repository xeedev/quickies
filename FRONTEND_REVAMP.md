# Quickies — Frontend Revamp Plan

Goal: turn a visually **busy** UI into a **calm, clear, award‑winning** interface
(the kind you'd see featured on awwwards.com) — without changing any backend
behaviour, routes, or tool logic.

---

## Why the current UI feels busy

1. **~50 unique per‑tool gradients.** Every tool card / icon uses its own
   `from-* to-*` gradient, so the eye has nowhere to rest — it reads as noise.
2. **Competing background motion.** Three animated aurora blobs + radial vignette
   sit behind every page and fight with the content.
3. **Per‑card glow blobs.** Each card renders an extra blurred gradient orb on
   hover, multiplying the color chaos.
4. **Heavy glassmorphism everywhere.** `bg-white/5 + backdrop-blur-xl` on every
   surface lowers contrast and legibility.
5. **Gradient text used for many headings**, so nothing stands out as _the_
   heading — hierarchy is flat.
6. **Rounded-3xl + shadows + hover scale on everything**, so no element feels
   more important than another.

## Design principles for the revamp

- **One accent, used sparingly.** Keep the fuchsia→indigo signature gradient ONLY
  for the logo mark and the single primary CTA. Everything else is neutral.
- **Calm by default, color on interaction.** Tool icons render in a neutral
  monochrome tile; the tool's own accent only appears on hover/focus.
- **Real hierarchy.** One clear H1 per page (solid white, tight tracking, large),
  restrained supporting copy, generous whitespace.
- **Quiet canvas.** Replace animated auroras with a single, static, very subtle
  radial wash + fine grid/noise texture. No moving blobs behind content.
- **Higher contrast surfaces.** Slightly more opaque, less-blurred cards with a
  crisp 1px hairline border.
- **Consistent rhythm.** Shared radius, spacing, border, and shadow tokens so
  every surface feels part of one system.
- **Subtle, purposeful motion.** Micro-interactions (fade/rise on load, gentle
  hover lift) — nothing that competes for attention.

---

## Design tokens (defined once in `resources/css/app.css`)

| Token            | Value                                  | Use                       |
| ---------------- | -------------------------------------- | ------------------------- |
| `--bg`           | `#070708`                              | page canvas               |
| `--surface`      | `rgba(255,255,255,0.03)`               | cards / panels            |
| `--surface-2`    | `rgba(255,255,255,0.05)`               | inputs / raised           |
| `--hairline`     | `rgba(255,255,255,0.08)`               | borders                   |
| `--hairline-str` | `rgba(255,255,255,0.14)`               | hover borders             |
| `--text`         | `#f4f5f7`                              | primary text              |
| `--text-muted`   | `#8b8f9a`                              | secondary text            |
| accent           | `indigo-500 → fuchsia-500`             | logo + primary CTA only   |
| radius           | `--r-card: 1rem`, `--r-lg: 1.5rem`     | consistent corners        |

Reusable helper classes: `.surface`, `.surface-hover`, `.hairline`,
`.text-gradient` (only where intentional), `.grid-texture`.

---

## Execution phases (step by step)

- [x] **Phase 1 — Foundation (CSS).** Add design tokens + helper classes, replace
      aurora animation with a quiet static texture, tone down toast/scrollbar.
- [x] **Phase 2 — Layouts.** `layouts/app.blade.php` + `layouts/marketing.blade.php`:
      calmer nav (single pill), quiet background, refined footer, keep all JS
      behaviour (dropdowns, mobile drawer, toasts) intact.
- [x] **Phase 3 — Dashboard.** Calm, uniform tool grid (neutral icons, accent on
      hover), cleaner Smart Toolbox, clearer search + section headers. Keep
      drag‑drop + search + smart-detect JS working.
- [x] **Phase 4 — Prelander (home).** Tighten the hero, cut redundant decorative
      glows, unify section styling, stronger type hierarchy, calmer bento.
- [x] **Phase 5 — Pricing + Upgrade.** Cleaner comparison, one highlighted plan,
      remove gradient overload.
- [x] **Phase 6 — Tool header + tool pages.** Refine the shared `x-tool-header`
      (solid title, neutral icon) so all 60+ tool pages inherit a cleaner look;
      refine the common panel styling.
- [x] **Phase 7 — Auth pages.** Match the new system.
- [x] **Phase 8 — QA.** Build assets, check for regressions, verify JS hooks,
      responsive check.

### Non‑goals / constraints

- No backend, route, controller, or tool-logic changes.
- Keep every existing JS hook (`data-*` attributes, `analyzeSmart`, drag/drop,
  `showNotification`, `copyToClipboard`) working.
- Keep the tool registry (`AppServiceProvider`) untouched — the per-tool
  `from/to` values stay available and are used only as the hover accent.
