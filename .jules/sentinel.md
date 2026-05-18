## 2025-05-18 - Prevent Path Traversal in Storage Operations
**Vulnerability:** User-controlled `image_path` parameters were passed directly to `Storage::disk('public')->path()`, `exists()`, and `move()` without validation, allowing path traversal attacks via inputs like `../../../.env`.
**Learning:** Laravel's `Storage` methods do not inherently protect against path traversal if the input itself contains directory traversal characters like `..`. File paths must be strictly validated.
**Prevention:** Always validate file paths from requests using a strict regex (e.g., `regex:/^ai_images\/[a-zA-Z0-9_\-\.]+$/`) to restrict them to specific expected directories and prevent traversal sequences.
