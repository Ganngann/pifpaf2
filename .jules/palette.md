## 2024-05-30 - Empty States CTA duplication prevention
**Learning:** When replacing plain `<p>` empty states with the styling-heavy `x-ui.empty-state` component and embedding a call-to-action (CTA) button within it, the UI can become confusing if a general, top-level "Create" or "Add" button remains visible simultaneously.
**Action:** When implementing an empty state CTA, conditionally wrap any general page-level action buttons (e.g., using `@if($collection->isNotEmpty())`) to hide them when the empty state is displayed, ensuring a single, clear path forward for the user.
