## 2026-07-24 - Insecure Direct Object Reference in Bulk Action
**Vulnerability:** IDOR in the ItemImageController's bulk reorder method allowed attackers to modify the order of images belonging to other users' items by including their own authorized image ID as the first element in the request payload.
**Learning:** Checking authorization only against the first element of an array of user-submitted IDs is insufficient for bulk operations. The authorization boundary must cover the entire dataset being operated on.
**Prevention:** For any action processing an array of IDs, ensure that every single ID in the array belongs to the authorized resource model by performing a scoped `whereIn` count check before executing the operation.
