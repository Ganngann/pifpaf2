## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-06-10 - Better empty states UX
**Learning:** When using empty states with call-to-actions, keeping a top-level 'Add' button alongside the empty state CTA creates confusing duplicate actions in the UI.
**Action:** Always conditionally hide top-level 'Add' or 'Create' buttons when displaying an empty state that already contains a primary CTA for that action.
