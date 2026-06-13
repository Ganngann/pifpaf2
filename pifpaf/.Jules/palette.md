
## 2026-06-13 - Empty State Improvements
**Learning:** When adding empty states using the existing `x-ui.empty-state` component, relying on just the text without an action leaves users at a dead end.
**Action:** Always wrap plain text empty states with `x-ui.empty-state` and include a relevant Call-To-Action (CTA) link in the `actions` slot (styled like a primary button) to guide users towards their next logical step (e.g. from an empty purchases list to the dashboard to find items).
