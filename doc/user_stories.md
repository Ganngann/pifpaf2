# User Stories for Pifpaf

## User Story 1: User Registration

**As a** new user,
**I want to** create an account on Pifpaf
**So that** I can list items for sale and buy items from other users.

### Acceptance Criteria:

* A user can register with a username, email, and password.
* The password must be securely hashed and stored.
* After successful registration, the user is automatically logged in.
* The user is redirected to their dashboard after registration.

## User Story 2: User Login

**As a** registered user,
**I want to** log in to my account
**So that** I can access my dashboard and manage my listings.

### Acceptance Criteria:

* A user can log in with their email and password.
* The system provides clear error messages for incorrect login attempts.
* A "Remember Me" option is available to keep the user logged in.

## User Story 3: AI-Powered Item Listing

**As a** seller,
**I want to** list an item for sale by providing a few details and a photo
**So that** the AI can automatically generate a title, description, and suggest a price.

### Acceptance Criteria:

* A user can upload a photo of the item.
* A user can provide a short description of the item.
* The AI (Gemini 2.5 Flash) processes the image and text to generate a title, a detailed description, and a suggested price range.
* The user can review and edit the AI-generated content before publishing the listing.
* The new listing is saved in the database and is visible on the marketplace.
