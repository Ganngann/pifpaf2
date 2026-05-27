## 2026-05-27 - Adding actions inside Empty State components
**Learning:** It is crucial to conditionally hide top-level action buttons when a list is empty and the empty state component () incorporates its own primary Call to Action button. Leaving both buttons visible simultaneously clutters the UI and confuses the user with duplicated primary actions on the screen.
**Action:** When replacing unstyled empty states with a dedicated component containing a CTA, always ensure the original page-level action button is hidden using conditional logic (e.g., `@if($collection->isNotEmpty())`).
## 2026-05-27 - Adding actions inside Empty State components
**Learning:** It is crucial to conditionally hide top-level action buttons when a list is empty and the empty state component (x-ui.empty-state) incorporates its own primary Call to Action button. Leaving both buttons visible simultaneously clutters the UI and confuses the user with duplicated primary actions on the screen.
**Action:** When replacing unstyled empty states with a dedicated component containing a CTA, always ensure the original page-level action button is hidden using conditional logic (e.g., @if($collection->isNotEmpty())).
