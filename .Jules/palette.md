## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.
## 2026-05-16 - Replace unstyled empty states with components
**Learning:** Using unstyled paragraphs (`<p>`) for empty states in lists (like addresses or bank accounts) creates a poor, inconsistent user experience without clear next steps.
**Action:** Always replace unstyled empty states with the `x-ui.empty-state` component and include a primary call-to-action (CTA) inside its `<x-slot name="actions">`. When doing so, ensure top-level CTAs are conditionally hidden to prevent duplicate buttons.
