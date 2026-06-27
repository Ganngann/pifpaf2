## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.

## 2026-05-17 - Add CTAs to Empty States
**Learning:** Empty states without actionable next steps lead to dead ends and reduce user engagement, especially on primary transaction pages like "Mes Achats" or "Mes Ventes".
**Action:** When implementing or updating empty states (e.g., using `x-ui.empty-state`), always include a relevant Call-To-Action (CTA) inside the `<x-slot name="actions">` (like "Browse items" or "Create listing") to guide the user to the next logical step. Use anchor tags styled as buttons for navigation.
