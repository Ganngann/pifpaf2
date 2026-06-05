## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2025-06-05 - Profile Addresses Empty State UX
**Learning:** Replaced an unstyled plain text empty state ("Vous n'avez pas encore d'adresse enregistrée.") with the existing `x-ui.empty-state` component and a clear Call-To-Action. Found that if we add a CTA to the empty state, it creates confusing duplicate actions if the top-level "Add" button isn't conditionally hidden.
**Action:** When creating empty states using the dedicated Blade component and adding CTAs inside them, always ensure top-level buttons serving the same action are hidden when the list is empty to prevent UI clutter and confusion.
