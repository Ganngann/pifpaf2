## 2025-05-25 - Improved Empty State Components

**Learning:** Using simple paragraph tags for empty lists creates poor UX and dead ends. When replacing them with `x-ui.empty-state`, always include an actionable Call-To-Action (CTA) in the `<x-slot name="actions">`. Additionally, conditionally hide any top-level "Add" or "Create" buttons when the empty state is visible to prevent confusing duplicate actions.

**Action:** Whenever implementing a list view, check if an empty state exists and ensure it uses the `x-ui.empty-state` pattern with a clear CTA guiding the user to their next logical action.