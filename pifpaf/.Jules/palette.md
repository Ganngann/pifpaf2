
## 2024-07-25 - Laravel default pagination components UI mismatch
**Learning:** Default Laravel pagination components (`vendor:publish --tag=laravel-pagination`) often use generic colors (like `gray-200` for active states and `white` backgrounds without proper focus rings) that can clash with the application's primary color scheme and fail to meet accessibility contrast ratios or proper focus visibility.
**Action:** When working with Laravel pagination in a new project, always evaluate the published `tailwind.blade.php` (or equivalent) and ensure active states use the primary brand color (e.g., `bg-indigo-600`), and that hover, focus, and disabled states are properly styled for both aesthetics and accessibility.
