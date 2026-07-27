## 2024-07-27 - [Avoid N+1 queries in loops for aggregate functions]
**Learning:** Calling `$model->relation()->count()` inside a loop, even when inserting, executes a database query on every iteration.
**Action:** Pre-calculate the count before the loop and use a local PHP variable counter to track the limit, avoiding N+1 queries.
