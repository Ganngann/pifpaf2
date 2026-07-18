## 2026-07-18 - Avoid Aggregate Relationship Methods Inside Loops
**Learning:** Calling aggregate relationship methods like `$model->relation()->count()` inside a loop causes an N+1 query problem, as each iteration triggers a new database query.
**Action:** Pre-calculate the count before the loop and use a local PHP variable counter to track the value dynamically.
