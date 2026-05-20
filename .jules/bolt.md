## 2024-05-16 - [N+1 query in navigation]
**Learning:** Checking `Auth::user()->unreadNotifications->count()` inside the navigation layout triggers an N+1 query issue since notifications might not be loaded, and `count()` triggers a separate count query on the relation each time it's called (or loads all notifications). Actually, it loads the relation because it uses the property `unreadNotifications` and then calls `count()` on the collection. In this case, `unreadNotifications()->count()` is much more efficient as it performs an aggregate query, but even better is doing it right in a View Composer if needed, or just `unreadNotifications()->count()`. Wait, since it's `Auth::user()->unreadNotifications->count()`, it loads ALL unread notifications into memory just to count them!

**Action:** Replace `$user->unreadNotifications->count()` with `$user->unreadNotifications()->count()`. This executes a simple `COUNT(*)` query without fetching the actual model records.

## 2024-05-20 - [N+1 queries during image upload in ItemController]
**Learning:** In `ItemController::update`, when adding new images to an item, there's a loop over the uploaded images. Inside the loop, it checks if the limit of 10 images has been reached using `$item->images()->count()`. This executes a `COUNT` query in the database for *every* uploaded image.
**Action:** Always cache the initial count of related models before entering a loop and increment the cached value inside the loop to avoid N+1 query performance issues.
