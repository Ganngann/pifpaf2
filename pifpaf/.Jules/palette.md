## 2026-07-13 - Gallery Thumbnail Accessibility
**Learning:** Icon-only or image-only interactive elements like gallery thumbnails (<button>) require explicit aria-labels, even if the inner <img> has an alt attribute, to clarify the interaction purpose for screen readers.
**Action:** Add descriptive aria-labels to all image-only buttons to explain what activating the button will do (e.g., "View full image").
