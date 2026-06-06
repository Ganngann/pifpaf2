## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-06-06 - Empty State CTAs and Top-Level Buttons
**Learning:** When replacing an unstyled empty state with a polished component like `x-ui.empty-state` that includes its own prominent Call-to-Action (CTA), leaving the original top-level "Add" or "Create" button visible creates confusing duplicate actions in the UI.
**Action:** Always conditionally hide the top-level creation button (e.g., using `@if($items->isNotEmpty())`) when an empty state component with its own CTA is being displayed.
