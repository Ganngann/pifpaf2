## 2026-07-23 - Prevent N+1 queries with aggregate methods inside loops
**Learning:** Calling aggregate relationship methods like count() inside loops triggers an N+1 query bottleneck.
**Action:** Pre-calculate the count outside the loop and use a local PHP variable counter to track dynamic changes.
