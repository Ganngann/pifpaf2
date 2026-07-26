## 2024-06-25 - Avoid relationship aggregate queries inside loops
**Learning:** Calling aggregate relationship methods like `$model->relation()->count()` inside a loop causes N+1 query problems. In `ItemController::update`, this was happening for image count checks.
**Action:** Always pre-calculate relationship counts outside loops when iterating over related records and use local PHP variables to track dynamic counts.
