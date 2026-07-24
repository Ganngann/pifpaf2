## 2026-07-24 - Prevent N+1 Query in Loop
**Learning:** Calling aggregate relationships like `$model->relation()->count()` inside a loop causes an N+1 query problem.
**Action:** Always pre-calculate aggregate counts before loops, store in a local PHP variable, and manually track the counter during the loop.
