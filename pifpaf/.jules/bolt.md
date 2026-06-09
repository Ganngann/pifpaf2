## 2026-06-09 - N+1 query via aggregate functions in loops
**Learning:** Calling an aggregate function like `->count()` on a relationship (e.g. `$model->relation()->count()`) inside a loop triggers a new database query on each iteration, causing N+1 query performance issues.
**Action:** Always cache the aggregate result before the loop and track it locally inside the loop to avoid redundant database queries.
