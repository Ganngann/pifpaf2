## 2026-07-05 - Use semantic buttons for toggles
**Learning:** Interactive accordion headers built with Alpine.js often use `<div>` elements with `@click` handlers, making them inaccessible to keyboard users and screen readers.
**Action:** Always replace clickable `<div>` elements with `<button type="button">`, and ensure they include `:aria-expanded` and keyboard focus styles (e.g., `focus:ring-2`).
