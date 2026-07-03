## 2024-05-16 - [N+1 query in navigation]
**Learning:** Checking `Auth::user()->unreadNotifications->count()` inside the navigation layout triggers an N+1 query issue since notifications might not be loaded, and `count()` triggers a separate count query on the relation each time it's called (or loads all notifications). Actually, it loads the relation because it uses the property `unreadNotifications` and then calls `count()` on the collection. In this case, `unreadNotifications()->count()` is much more efficient as it performs an aggregate query, but even better is doing it right in a View Composer if needed, or just `unreadNotifications()->count()`. Wait, since it's `Auth::user()->unreadNotifications->count()`, it loads ALL unread notifications into memory just to count them!

**Action:** Replace `$user->unreadNotifications->count()` with `$user->unreadNotifications()->count()`. This executes a simple `COUNT(*)` query without fetching the actual model records.

## 2026-07-03 - [N+1 query in image upload loop]
**Learning:** Using `$item->images()->count()` inside a `foreach` loop for file uploads causes an N+1 DB query problem because Laravel executes a `SELECT COUNT(*)` for every single image processed, creating an unnecessary DB bottleneck when saving multiple files.
**Action:** Pre-calculate the relationship count outside the loop (`$count = $model->relation()->count();`) and manually increment the local variable inside the loop.
