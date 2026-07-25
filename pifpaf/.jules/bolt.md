## 2026-07-25 - Avoid N+1 in loops with dynamic aggregations
**Learning:** Calling aggregate relationship methods like `$model->relation()->count()` inside a loop triggers a new database query on every iteration, leading to an N+1 query bottleneck.
**Action:** Pre-calculate the count before the loop and use a local PHP variable counter to track the value dynamically if it changes inside the loop.
