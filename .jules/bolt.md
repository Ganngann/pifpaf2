## 2024-05-16 - [N+1 query in navigation]
**Learning:** Checking `Auth::user()->unreadNotifications->count()` inside the navigation layout triggers an N+1 query issue since notifications might not be loaded, and `count()` triggers a separate count query on the relation each time it's called (or loads all notifications). Actually, it loads the relation because it uses the property `unreadNotifications` and then calls `count()` on the collection. In this case, `unreadNotifications()->count()` is much more efficient as it performs an aggregate query, but even better is doing it right in a View Composer if needed, or just `unreadNotifications()->count()`. Wait, since it's `Auth::user()->unreadNotifications->count()`, it loads ALL unread notifications into memory just to count them!

**Action:** Replace `$user->unreadNotifications->count()` with `$user->unreadNotifications()->count()`. This executes a simple `COUNT(*)` query without fetching the actual model records.

## 2026-07-06 - [N+1 Query inside File Upload Loop]
**Learning:** Using `$item->images()->count()` inside a `foreach` loop for file uploads causes an N+1 query issue, executing a `COUNT(*)` SQL query on the database for every single image in the upload batch.
**Action:** Pre-calculate relational counts (like `$model->relation()->count()`) outside the loop and manually increment a local variable inside, rather than executing `count()` on every iteration.
