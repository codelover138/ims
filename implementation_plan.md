# Gamma Implementation Plan

## Goal

Implement the client's Gamma requirements on top of the current application without breaking the existing `ims` workflows. Reuse the current authentication, session, user table, and file-manager infrastructure where it is stable, and introduce only the minimum new Gamma tables needed for forms, notes, and document generation.

## Current Codebase Assessment

### What can be reused

- Authentication already exists through Ion Auth and `sma_users`.
- Login, logout, session state, and forgot-password flow already exist.
- The application already creates a per-user folder on user creation.
- elFinder is already integrated and restricted by user path.
- A protected dashboard/user-space pattern already exists.

### What should not be reused for Gamma business logic

- `sma_products` should not be used as Gamma forms.
- `sma_companies` should not be used as Gamma user accounts.
- Existing sales/POS tables should remain separate from Gamma document-generation data.

## Implementation Strategy

### Core approach

Build Gamma as a new module inside the current application first.

- Reuse `sma_users` as the main user table.
- Add new `sma_`-prefixed Gamma tables only for Gamma-specific business data.
- Replace the current document path logic with a Gamma-specific path strategy.
- Reuse login/session/dashboard plumbing.
- Keep legacy modules untouched as much as possible.

### Database strategy

Start by creating `gamma_` tables in the current `ims` database.

Reason:

- lower integration risk
- no immediate need to refactor all database bootstrapping
- faster delivery

Later, if required, Gamma can be moved to a dedicated `Gamma` database connection.

## Data Model

### 1. Users and authentication

Use existing `sma_users` table for:

- login identity
- password management
- session ownership
- remember-me and forgot-password tokens
- primary Gamma user identity

### 2. User fields strategy

Do not create a separate `gamma_user_accounts` table at this stage.

Instead:

- reuse `sma_users`
- keep existing columns that already work
- add only the extra columns that Gamma actually needs now

Suggested field mapping:

- `id` -> `UserID`
- `created_on` -> `DateCreated`
- `username` -> `Username`
- `first_name` -> `FirstName`
- `last_name` -> `LastName`
- `email` -> `Email1`
- `phone` -> `MobilePhone` or `BusinessPhone`

Suggested extra columns to add to `sma_users` only if needed:

- `middle_name`
- `birth_date`
- `business_name`
- `unit_number`
- `street_number`
- `street_name`
- `street_type`
- `suburb`
- `state`
- `country`
- `postcode`
- `email2`
- `mobile_phone`
- `business_phone`
- `security_question`
- `security_answer`
- `departure_date`
- `departure_reason`

If some of these fields are not needed in the first release, do not add them yet.

### 3. Gamma business tables

Create these new tables:

#### `sma_gamma_user_notes`

Purpose:

- stores user notes required by the client

Key fields:

- `user_notes_id`
- `user_id` -> references `sma_users.id`
- `entry_date`
- `note_date`
- `narrative`
- `last_updated`

#### `sma_gamma_forms`

Purpose:

- stores the forms available to a specific user

Key fields:

- `form_id`
- `user_id` -> references `sma_users.id`
- `form_title`
- `description`
- `button_label`
- `input_form_location`
- `precedent_clause_location`
- `document_creation_location`
- `output_filename_base`
- `output_file_location`
- `last_updated`

#### `sma_gamma_form_inputs`

Purpose:

- stores the input definitions that power auto-generated forms

Key fields:

- `form_input_id`
- `form_id`
- `input_name`
- `input_label`
- `input_type`
- `data_type`
- `default_value`
- `allowed_input`
- `max_size`
- `input_form_instructions`
- `line_command`
- `table_number`
- `table_displayed`
- `table_row`
- `table_column`

#### `sma_gamma_output_file_logs`

Purpose:

- stores one row per generated document

Key fields:

- `output_file_id`
- `form_id`
- `output_file`
- `date_created`

#### `sma_gamma_input_records`

Purpose:

- stores submitted values for each generated output file

Key fields:

- `form_input_record_id`
- `output_file_id`
- `input_name`
- `input_value`
- `date_created`

Global Gamma settings should be stored in `sma_settings` instead of creating a separate `sma_gamma_settings` table.

Use `sma_settings` only for global Gamma configuration values, such as:

- `gamma_base_path`
- `gamma_enable_username_recovery`
- `gamma_default_output_folder`
- `gamma_file_manager_root_mode`

Do not use `sma_settings` for per-user or per-form Gamma data.

## Folder Architecture

### Base path

Gamma file storage should be rooted in:

`Gamma/Users/{Username}/`

### Required user-level folders

For each user, create:

- `1 UserInterfaceFiles`
- `2 PrecedentClauses`
- `3 DocumentGeneration`
- `4 OutputFiles`

### Required form folder naming

For each form, create:

`{FormID padded to 5 digits} {FormTitle}`

Example:

`00001 Citizenship Application`

### Recommended final structure

`Gamma/Users/TestUser/`

- `1 UserInterfaceFiles`
- `2 PrecedentClauses`
- `3 DocumentGeneration`
- `4 OutputFiles`
- `00001 Sample Form/`

Or, if form-specific subfolders are required under the form directory:

`Gamma/Users/TestUser/00001 Sample Form/`

- `1 UserInterfaceFiles`
- `2 PrecedentClauses`
- `3 DocumentGeneration`
- `4 OutputFiles`

### Path management rule

Do not hardcode Gamma paths in controllers.

Create one reusable path service responsible for:

- username root path
- form folder naming
- padded FormID generation
- folder creation
- safe folder-name sanitization

## Application Components

### 1. Authentication

Keep current auth flow.

Support:

- login
- logout
- lost password by email

Add:

- forgot username flow using email lookup
- optional security-question recovery flow only if those fields are added to `sma_users`

### 2. Gamma user space

Create a protected Gamma dashboard page that:

- requires login
- shows the logged-in user at the top
- shows a logout button
- lists forms assigned to the logged-in user

Each form row should display:

- label from `sma_gamma_forms.description`
- button text from `sma_gamma_forms.button_label`

### 3. Form engine

Build a renderer that reads `sma_gamma_form_inputs` and outputs the UI dynamically.

Initial supported input types:

- Textbox
- Dropbox
- Checkbox

Validation should be driven by:

- `data_type`
- `allowed_input`
- `max_size`

### 4. File manager integration

Reuse elFinder, but point Gamma users to their Gamma root folder instead of:

`assets/document/{username}`

Permissions:

- admin/owner can browse all Gamma user trees
- regular user can browse only their own Gamma tree

### 5. Document generation

For each form submission:

1. load the relevant precedent/source files
2. load the generation script or template
3. merge submitted input values
4. create a file in the output folder
5. log the output file path
6. log the submitted inputs used to generate it

Output filename format:

`YYYYMMDD TTTT {OutputFilenameBase}`

## File-by-File Coding Plan

### Config

#### `app/config/database.php`

- keep current `ims` connection as primary
- optionally add a future Gamma-specific connection if needed

#### New: `app/config/gamma.php`

Store:

- Gamma base path
- folder names
- allowed file types
- any Gamma-specific feature flags
- values can later be mirrored into `sma_settings` if runtime configuration is needed

### Libraries / Helpers

#### New: `app/libraries/Gamma_path_service.php`

Responsibilities:

- build user root path
- build form path
- create missing folders
- sanitize path components

#### New: `app/helpers/gamma_helper.php`

Responsibilities:

- FormID padding
- safe filename and folder-name normalization

### Models

#### New: `app/models/admin/Gamma_user_model.php`

Handles:

- Gamma-related extensions to `sma_users`
- `sma_gamma_user_notes`

#### New: `app/models/admin/Gamma_form_model.php`

Handles:

- `sma_gamma_forms`
- `sma_gamma_form_inputs`

#### New: `app/models/admin/Gamma_document_model.php`

Handles:

- `sma_gamma_output_file_logs`
- `sma_gamma_input_records`

### Controllers

#### `app/controllers/admin/Auth.php`

Modify user creation flow to:

- create Gamma user root folders
- optionally populate any Gamma-specific user fields added to `sma_users`

#### New: `app/controllers/admin/Gamma_dashboard.php`

Responsibilities:

- protected user space
- user header/logout area
- list of available forms

#### New: `app/controllers/admin/Gamma_users.php`

Responsibilities:

- Gamma profile management
- Gamma note management

#### New: `app/controllers/admin/Gamma_forms.php`

Responsibilities:

- form CRUD
- form input definition CRUD

#### New: `app/controllers/admin/Gamma_recovery.php`

Responsibilities:

- forgot username flow
- optional security-question recovery

#### `app/controllers/admin/Document.php`

Modify or extend to:

- route Gamma users to Gamma directory roots in elFinder
- preserve existing legacy behavior where necessary

### Views

Create Gamma-specific views for:

- dashboard
- form listing
- form editor
- profile editor
- forgot username flow

### Routes

#### `app/config/routes.php`

Add routes for:

- Gamma dashboard
- Gamma forms
- Gamma users
- Gamma recovery

### SQL / migration files

Create:

- `sql/gamma_schema.sql`
- optional `sql/gamma_seed.sql`

Do not create a separate `sma_gamma_settings` table in the first release.

## Recommended Delivery Order

### Phase 1

- create Gamma schema
- add Gamma config
- add Gamma path service

### Phase 2

- update user-creation flow to build Gamma folder structure
- add any required new columns to `sma_users`

### Phase 3

- build Gamma dashboard
- show logged-in user and logout button
- list forms assigned to user

### Phase 4

- build Gamma forms CRUD
- build Gamma form input definition CRUD

### Phase 5

- implement forgot username flow
- preserve existing forgot-password flow

### Phase 6

- integrate elFinder with Gamma root folders

### Phase 7

- build form renderer from `sma_gamma_form_inputs`

### Phase 8

- implement document generation and output logging

### Phase 9

- seed test data
- create `TestUser`
- create sample form
- create sample folders and files

## Risks and Controls

### Risk: legacy module coupling

Control:

- keep Gamma business data isolated in new `sma_`-prefixed Gamma tables
- keep only global Gamma configuration in `sma_settings`

### Risk: path logic spread across controllers

Control:

- centralize all Gamma path generation in one service

### Risk: forcing Gamma into `products` or `companies`

Control:

- do not reuse those tables for Gamma business logic

### Risk: breaking current file manager behavior

Control:

- add Gamma-specific elFinder root handling instead of rewriting all existing document behavior at once

### Risk: adding too many Gamma fields to `sma_users`

Control:

- add only fields required for the first release
- avoid mirroring the full client specification unless those fields are actually used

## Immediate Next Tasks

1. Create `sql/gamma_schema.sql` without `gamma_user_accounts`
2. Prepare `ALTER TABLE sma_users` for only the required extra fields
3. Add `app/config/gamma.php`
4. Add `app/libraries/Gamma_path_service.php`
5. Update `app/controllers/admin/Auth.php` to create Gamma folders on user creation
6. Add Gamma dashboard controller and view

## Recommendation

Proceed with Gamma as a parallel module inside the existing app, while reusing `sma_users` as the main user table. This keeps the project lighter and fits your goal of using the current application as a skeleton instead of introducing unnecessary account architecture.

## Requirement Coverage Matrix

### File structure requirements

#### 1. Create the new project in a folder named `Gamma`

Status:

- partially covered

Notes:

- current plan covers `Gamma` as the storage root for user documents
- it does not yet decide whether the application code itself must be moved into a project folder named `Gamma`

#### 2. Review code from `Beta`

Status:

- informational only

Notes:

- no direct implementation required

#### 3. Create the required folder structure

Status:

- partially covered

Notes:

- `Gamma/Users` is covered
- `Gamma/Users/TestUser` is covered as a seed/test requirement
- per-form folders are covered conceptually
- the exact final hierarchy still needs one decision:
  - user-level standard folders only, or
  - standard folders repeated under each form folder

#### 3(a). `Gamma/Users`

Status:

- covered

#### 3(b). Create `TestUser`

Status:

- covered in the plan
- not implemented yet

#### 3(c). Form folder naming with 5-digit `FormID`

Status:

- covered conceptually

Notes:

- must be enforced as a strict implementation rule in path generation

#### 3(d). Required folders

- `1 UserInterfaceFiles`
- `2 PrecedentClauses`
- `3 DocumentGeneration`
- `4 OutputFiles`

Status:

- partially covered

Notes:

- included in plan
- final location in folder hierarchy still needs to be locked

### Database requirement

#### 4. Create a new MySQL database named `Gamma`

Status:

- not covered literally

Notes:

- current plan intentionally uses the existing `ims` database for faster integration
- this is a deliberate deviation from the literal requirement
- if the client insists on a separate DB, this must be changed

### Table requirements

#### 5. `UserAccount`

Status:

- partially covered

Notes:

- current plan reuses `sma_users`
- this covers authentication and basic identity fields
- not all requested profile fields are guaranteed yet
- final list of extra columns to add to `sma_users` still needs to be confirmed

#### 6. `UserNotes`

Status:

- covered

Notes:

- planned as `sma_gamma_user_notes`

#### 7. `Form`

Status:

- covered

Notes:

- planned as `sma_gamma_forms`

#### 8. `FormInput`

Status:

- covered

Notes:

- planned as `sma_gamma_form_inputs`

#### 9. `InputRecord`

Status:

- covered

Notes:

- planned as `sma_gamma_input_records`

#### 10. `OutputFileLog`

Status:

- covered

Notes:

- planned as `sma_gamma_output_file_logs`

#### 11. `Settings`

Status:

- covered

Notes:

- plan reuses `sma_settings` for global Gamma configuration

#### 12. Duplicate `UserNotes` requirement

Status:

- covered

Notes:

- same requirement as item 6
- no separate table needed

### Interface requirements

#### 13. Login component connected to user account data

Status:

- partially covered

Notes:

- login already exists through `sma_users`
- this satisfies the requirement if `sma_users` is treated as the effective Gamma user account table

#### 13(a). Lost password

Status:

- covered

Notes:

- already exists in current authentication flow

#### 13(b). Forgotten username

Status:

- covered in the plan
- not implemented yet

#### 14. User space

Status:

- covered

#### 14(a). Show logged-in user and logout button

Status:

- covered in the plan

#### 14(b). Nothing available unless logged in

Status:

- partially covered

Notes:

- covered conceptually
- must be enforced consistently across dashboard, forms, file manager, generation routes, and downloads

#### 15. Show list of forms available to logged-in `UserID`

Status:

- covered

#### 15(a). Label uses `Form.Description`

Status:

- covered

#### 15(b). Button uses `Form.ButtonLabel`

Status:

- covered

## Open Decisions

### 1. Separate `Gamma` database or current `ims` database

Current plan:

- use current `ims` database

Why this matters:

- this is the largest deviation from the literal client wording

### 2. Final folder hierarchy

Still needs final decision:

- whether the four standard folders live directly under each user
- or inside each form folder

### 3. Meaning of `InputFormLocation`

Still needs final decision:

- real uploaded file path
- generated form path
- optional legacy-compatible file reference

### 4. Meaning of `PrecedentClauseLocation`

Still needs final decision:

- real uploaded file path
- template source path

### 5. Meaning of `DocumentCreationLocation`

Still needs final decision:

- PHP generator file
- template file
- internal handler or script reference

### 6. Final list of extra user fields

Still needs final decision:

- which client-requested `UserAccount` fields should actually be added to `sma_users` in the first release

## Current Implementation Scope Note

The current implementation does not provide full field-by-field parity with the client's complete `UserAccount` specification.

### What is implemented now

- existing `sma_users` is reused for authentication and primary user identity
- Gamma storage structure under `Gamma/Users/...`
- Gamma dashboard entry point
- Gamma form storage model through:
  - `sma_gamma_forms`
  - `sma_gamma_form_inputs`
  - `sma_gamma_output_file_logs`
  - `sma_gamma_input_records`
  - `sma_gamma_user_notes`
- existing lost-password flow
- planned forgot-username flow

### What is intentionally deferred

The full set of client-requested `UserAccount` profile fields is not implemented yet unless those columns are later added to `sma_users` or another profile table.

Deferred fields include items such as:

- `MiddleName`
- `BirthDate`
- `BusinessName`
- detailed address fields
- `Email2`
- separate `MobilePhone`
- separate `BusinessPhone`
- `SecurityQuestion`
- `SecurityAnswer`
- `DepartureDate`
- `DepartureReason`

### Practical interpretation

The current build covers the Gamma skeleton and document workflow foundation, but it should not be described as full `UserAccount` field compliance with the client document.
