## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-05-16 - Add Empty States with Helpful CTAs
**Learning:** When displaying an empty list (like addresses, purchases, or sales), replacing unstyled `<p>` tags with a consistent `<x-ui.empty-state>` component improves visual consistency. Furthermore, it is a crucial UX pattern to *always* provide a relevant Call-to-Action (CTA) inside the empty state so users know immediately how to resolve the empty state.
**Action:** Always include a CTA link (e.g., using primary button styling) inside the `<x-slot name="actions">` of the `<x-ui.empty-state>` component. Conditionally hide redundant top-level action buttons when the list is empty to avoid duplicating the CTA.
