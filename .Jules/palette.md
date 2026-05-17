## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-05-17 - Standardize Empty States
**Learning:** Pages missing standard components like `x-ui.empty-state` make the app feel disjointed and unpolished when users encounter zero-data states (e.g. 0 purchases).
**Action:** Always check views using `@empty` in Blade templates to ensure they use the `x-ui.empty-state` component rather than raw `<p>` tags for consistent visual communication.
