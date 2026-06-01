## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-05-17 - Empty State Actions Pattern
**Learning:** The `<x-primary-button>` Blade component only renders a `<button>` element and does not support an `href` attribute, requiring custom styling for links that act as Call-to-Actions in empty states.
**Action:** When adding CTAs to empty states (like the `x-ui.empty-state` component), use standard Tailwind primary button classes on an `<a>` tag to maintain visual consistency without breaking semantic HTML.
