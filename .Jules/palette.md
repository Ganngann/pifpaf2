## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.
## 2026-07-21 - Missing ARIA Labels on Grouped Form Inputs
**Learning:** In visually grouped form fields (like min/max price or location/distance), a single visible `<label>` is often used for the group, leaving the individual `<input>` or `<select>` elements without an accessible name.
**Action:** Always add explicit `aria-label` attributes to individual inputs within visual groupings to ensure screen readers can identify each specific field correctly.
