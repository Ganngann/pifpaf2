## 2026-07-19 - N+1 query in image limit check
**Learning:** Checking an aggregate relationship count like $model->relation()->count() inside a loop causes an N+1 query bottleneck because it triggers a new SQL query on every iteration.
**Action:** Always pre-calculate the relationship count before the loop and track it dynamically using a local PHP variable counter.
