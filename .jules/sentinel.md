## 2025-05-16 - [Fix Mass Assignment Vulnerability in BankAccountController]
**Vulnerability:** A mass assignment vulnerability existed in `BankAccountController.php` because `store()` and `update()` methods used `$request->all()` while the `user_id` attribute was listed in the model's `$fillable` array. This could potentially allow users to manipulate the `user_id` during creation or updates.
**Learning:** Even if `create()` is chained to a relationship like `Auth::user()->bankAccounts()->create($request->all())`, using `$request->all()` remains risky because it includes all payload data, not just the data intentionally allowed by validation logic.
**Prevention:** Always use the validated data array returned by `$request->validate()` (e.g. `$validated = $request->validate(...)`) instead of `$request->all()` when creating or updating models, particularly when foreign keys are mass-assignable.
## 2025-05-19 - [Fix Path Traversal Vulnerabilities in File Reads]
**Vulnerability:** The application had a critical path traversal vulnerability in `AiRequestController.php` and `ItemController.php` where user-controlled input (`image_path`, `original_image_path`) was passed directly to `Storage::disk('public')->path()` and `Storage::disk('public')->move()` without proper sanitization. This allowed attackers to potentially read or move arbitrary files on the server using `../../` sequences.
**Learning:** Even when using Laravel's storage facade, unsanitized user input passed to methods like `path()`, `exists()`, or `move()` is unsafe. Path parameters received from external requests must be strictly validated.
**Prevention:** Always validate file paths from external input using strict regex constraints (e.g., `regex:/^ai_images\/[a-zA-Z0-9_\-\.]+$/`) to ensure they only point to expected directories and do not contain directory traversal sequences.
## 2025-06-26 - [Fix IDOR in bulk image reordering]
**Vulnerability:** IDOR vulnerability in `ItemImageController@reorder` because authorization was only checked on the first ID in the provided array.
**Learning:** Attackers could bypass authorization by placing their own resource ID at the start of the array and appending victim resource IDs.
**Prevention:** Always verify authorization for every item when processing an array of IDs for bulk operations, and eager load relationships to avoid N+1 queries.
