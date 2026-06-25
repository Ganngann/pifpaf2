## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2025-06-25 - Improve Empty States with Actionable CTAs
**Learning:** Empty states without actionable buttons can lead to dead ends in the user flow. Wrapping the CTA directly inside the empty state component (`x-ui.empty-state`) significantly improves usability. However, keeping the main page-level CTA (e.g. "Add Address") visible when the empty state CTA is also present creates redundant and potentially confusing UI paths.
**Action:** When replacing unstyled empty states with the `x-ui.empty-state` component and a CTA, conditionally hide any top-level 'Add' or 'Create' buttons to prevent confusing duplicate actions in the UI.
