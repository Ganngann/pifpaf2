## 2024-05-16 - [N+1 query in navigation]
**Learning:** Checking `Auth::user()->unreadNotifications->count()` inside the navigation layout triggers an N+1 query issue since notifications might not be loaded, and `count()` triggers a separate count query on the relation each time it's called (or loads all notifications). Actually, it loads the relation because it uses the property `unreadNotifications` and then calls `count()` on the collection. In this case, `unreadNotifications()->count()` is much more efficient as it performs an aggregate query, but even better is doing it right in a View Composer if needed, or just `unreadNotifications()->count()`. Wait, since it's `Auth::user()->unreadNotifications->count()`, it loads ALL unread notifications into memory just to count them!

**Action:** Replace `$user->unreadNotifications->count()` with `$user->unreadNotifications()->count()`. This executes a simple `COUNT(*)` query without fetching the actual model records.

## 2024-05-24 - [N+1 query in file upload loop]
**Learning:** Calling `$item->images()->count()` inside a `foreach` loop creates a hidden N+1 query vulnerability because Laravel executes a `SELECT COUNT(*)` query on the database for every iteration.
**Action:** Pre-calculate relational counts (like `$model->relation()->count()`) before the loop, store them in a local variable, and manually increment that variable inside the loop instead of re-executing the count query.
