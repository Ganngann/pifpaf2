## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-07-07 - Add ARIA Labels to User Menu Toggles
**Learning:** Even when a toggle button contains text (like a user's name), if it functions as a dropdown menu toggle, it may benefit from an explicit `aria-label` (e.g., "Menu utilisateur") to clearly describe its primary action to screen reader users, beyond just the visible text.
**Action:** When reviewing navigation dropdowns or setting menus, ensure the toggle element explicitly communicates its menu function via `aria-label` or `aria-haspopup`.
