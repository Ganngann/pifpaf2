
## 2024-06-15 - Cached Relationship Count in Loop
**Learning:** Checking a relationship count (like `$item->images()->count()`) inside a loop causes a new database query on each iteration. This is a common N+1 performance issue when dealing with arrays of inputs (like image uploads).
**Action:** Always cache aggregate functions like `count()` outside the loop and increment/decrement the cached variable internally if needed.
