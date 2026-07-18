## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-07-18 - Tailwind CSS v4 Focus States
**Learning:** Tailwind CSS v4 removes the `focus:shadow-outline` class. To maintain keyboard accessibility on interactive form elements like inputs and selects, you must explicitly define focus states.
**Action:** Use `focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none` (or similar explicit ring/border classes) instead of the deprecated `focus:shadow-outline` class.
