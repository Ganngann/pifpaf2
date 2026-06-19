## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2024-06-19 - Consistent Empty States with CTA
**Learning:** Unstyled empty states leave users without a clear path forward, causing friction. Having multiple identical CTA buttons (one top-level, one in empty state) when the list is empty creates visual noise and confusion.
**Action:** Always replace unstyled empty text with the `x-ui.empty-state` component and include an actionable CTA inside the `<x-slot name="actions">`. Conditionally hide top-level "Add" buttons when the list is empty to prevent redundant actions on the screen.
