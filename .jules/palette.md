
## 2024-05-26 - Empty State Call-to-Actions and Button Duplication
**Learning:** When using the `x-ui.empty-state` component to replace unstyled text, it's a significant UX improvement to include the primary call-to-action (like "Add [Item]") directly within the empty state itself. However, doing so can lead to confusing duplicate buttons if the top-level "Add" button remains visible.
**Action:** Always include a helpful call-to-action within empty states. Concurrently, conditionally hide any top-level 'Add' or 'Create' buttons when the list is empty to maintain a clean interface and clearly direct the user's focus to the primary empty state action.
