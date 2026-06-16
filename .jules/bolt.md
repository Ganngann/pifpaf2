## 2024-05-16 - [N+1 query in navigation]
**Learning:** Checking `Auth::user()->unreadNotifications->count()` inside the navigation layout triggers an N+1 query issue since notifications might not be loaded, and `count()` triggers a separate count query on the relation each time it's called (or loads all notifications). Actually, it loads the relation because it uses the property `unreadNotifications` and then calls `count()` on the collection. In this case, `unreadNotifications()->count()` is much more efficient as it performs an aggregate query, but even better is doing it right in a View Composer if needed, or just `unreadNotifications()->count()`. Wait, since it's `Auth::user()->unreadNotifications->count()`, it loads ALL unread notifications into memory just to count them!

**Action:** Replace `$user->unreadNotifications->count()` with `$user->unreadNotifications()->count()`. This executes a simple `COUNT(*)` query without fetching the actual model records.

## 2024-06-16 - [Fix N+1 query in images count loop]
**Learning:** Calling $item->images()->count() inside a foreach loop executes a separate SELECT COUNT(*) query for every iteration. This causes a massive N+1 query problem proportional to the number of elements being processed.
**Action:** Always cache the results of relationship aggregate queries like ->count() before the loop to execute only a single query and increment the cached variable inside the loop if necessary.
