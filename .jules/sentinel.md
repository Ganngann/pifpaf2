## 2025-05-16 - [Fix Mass Assignment Vulnerability in BankAccountController]
**Vulnerability:** A mass assignment vulnerability existed in `BankAccountController.php` because `store()` and `update()` methods used `$request->all()` while the `user_id` attribute was listed in the model's `$fillable` array. This could potentially allow users to manipulate the `user_id` during creation or updates.
**Learning:** Even if `create()` is chained to a relationship like `Auth::user()->bankAccounts()->create($request->all())`, using `$request->all()` remains risky because it includes all payload data, not just the data intentionally allowed by validation logic.
**Prevention:** Always use the validated data array returned by `$request->validate()` (e.g. `$validated = $request->validate(...)`) instead of `$request->all()` when creating or updating models, particularly when foreign keys are mass-assignable.
## 2025-05-19 - [Fix Path Traversal Vulnerabilities in File Reads]
**Vulnerability:** The application had a critical path traversal vulnerability in `AiRequestController.php` and `ItemController.php` where user-controlled input (`image_path`, `original_image_path`) was passed directly to `Storage::disk('public')->path()` and `Storage::disk('public')->move()` without proper sanitization. This allowed attackers to potentially read or move arbitrary files on the server using `../../` sequences.
**Learning:** Even when using Laravel's storage facade, unsanitized user input passed to methods like `path()`, `exists()`, or `move()` is unsafe. Path parameters received from external requests must be strictly validated.
**Prevention:** Always validate file paths from external input using strict regex constraints (e.g., `regex:/^ai_images\/[a-zA-Z0-9_\-\.]+$/`) to ensure they only point to expected directories and do not contain directory traversal sequences.

## 2025-10-24 - IDOR in Foreign Key Assignment
**Vulnerability:** A generic `exists:addresses,id` validation rule was used for foreign key assignment, allowing an attacker to assign an address belonging to another user.
**Learning:** `exists` validation alone is insufficient for resources that are scoped by user. It simply checks if the ID exists in the database table, not if it belongs to the authenticated user.
**Prevention:** Always use a scoped validation rule like `Rule::exists('table', 'id')->where('user_id', Auth::id())` when validating and assigning foreign keys representing user-owned resources from input.
