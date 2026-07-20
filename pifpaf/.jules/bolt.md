## 2026-07-20 - Pre-calculate count to avoid N+1 queries in loops
**Learning:** Calling aggregate relationship methods like `$model->relation()->count()` inside a loop causes an N+1 query problem, as it triggers a new database query in every iteration.
**Action:** Extract the count calculation before the loop into a local variable and increment it manually within the loop if necessary.
