## 2024-05-16 - [N+1 query in navigation]
**Learning:** Checking `Auth::user()->unreadNotifications->count()` inside the navigation layout triggers an N+1 query issue since notifications might not be loaded, and `count()` triggers a separate count query on the relation each time it's called (or loads all notifications). Actually, it loads the relation because it uses the property `unreadNotifications` and then calls `count()` on the collection. In this case, `unreadNotifications()->count()` is much more efficient as it performs an aggregate query, but even better is doing it right in a View Composer if needed, or just `unreadNotifications()->count()`. Wait, since it's `Auth::user()->unreadNotifications->count()`, it loads ALL unread notifications into memory just to count them!

**Action:** Replace `$user->unreadNotifications->count()` with `$user->unreadNotifications()->count()`. This executes a simple `COUNT(*)` query without fetching the actual model records.

## 2026-06-01 - [N+1 query in image upload loop]
**Learning:** Calling `$item->images()->count()` inside a loop iterates over the file upload array, which triggers a separate COUNT database query for each uploaded image. This can cause significant N+1 issues when uploading many files.
**Action:** Always cache the aggregate result before the loop (e.g. `$currentImageCount = $item->images()->count();`) and increment it manually inside the loop.
