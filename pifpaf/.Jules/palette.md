## 2024-05-18 - Missing focus styles in welcome page form

**Learning:** Forms constructed using basic Tailwind classes often lack visual feedback for keyboard navigation unless `focus:` modifiers are explicitly added. The welcome search form was particularly impacted as it didn't use `x-text-input` components which typically provide this out-of-the-box. Additionally, fields logically grouped without individual labels (e.g. min/max price, location/distance) require `aria-label`s for screen readers.

**Action:** Add explicit `focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none` classes to raw `<input>` and `<select>` elements that don't inherit from base form components. Provide `aria-label` attributes for inputs grouped under a single visual label.
