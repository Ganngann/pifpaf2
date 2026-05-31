## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.
## 2026-05-31 - Prevent Duplicate CTAs with Empty States
**Learning:** When replacing an unstyled empty state with a component (like `x-ui.empty-state`) that includes a Call-to-Action (CTA), it's crucial to conditionally hide any existing top-level page CTAs (e.g., 'Create' or 'Add' buttons) when the list is empty. Otherwise, the user is presented with two identical primary actions on the same screen, creating confusion and poor UX.
**Action:** Always check for and conditionally hide page-level actions when the list is empty and an actionable empty state is displayed.
