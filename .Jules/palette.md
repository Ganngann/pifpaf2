## 2026-05-16 - Add ARIA Labels to Navigation Elements
**Learning:** Icon-only links and toggle buttons (like the notifications bell and hamburger menu) are common in Laravel Breeze/Alpine navigation components but often lack accessible labels by default.
**Action:** When adding or reviewing icon-only interactive elements, ensure `aria-label` is present. For elements that toggle state (like dropdowns or mobile menus), bind `aria-expanded` to the state variable (e.g., `:aria-expanded="open.toString()"` in Alpine.js) to communicate state to screen readers.
## 2026-05-18 - Use consistent empty states with CTAs
**Learning:** Generic text indicating an empty state (like "Aucun article trouvé.") leaves the user at a dead end and feels unpolished compared to the rest of the application.
**Action:** Always replace plain text empty states with the dedicated `<x-ui.empty-state>` component. Ensure you provide a descriptive icon, clear text, and crucially, an actionable CTA (like a button to create a new item) placed in the `<x-slot name="actions">` slot to guide the user to their next logical step.
