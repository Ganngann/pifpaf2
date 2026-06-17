## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2025-06-17 - Replace unstyled empty states with x-ui.empty-state and CTA
**Learning:** Unstyled `<p>` tags for empty states (like on the purchases page) provide poor UX. The project has a dedicated `x-ui.empty-state` component that should be used for visual consistency. Furthermore, empty states should always include a helpful Call-To-Action (CTA) to guide the user.
**Action:** Always use `<x-ui.empty-state>` for empty lists, and include a CTA button (using the `<x-slot name="actions">` slot and applying primary button classes to an `<a>` tag) to help users take the next logical step.
