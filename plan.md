# Gamma Forms Implementation Plan

## Goal

Implement the 29 November 2025 Gamma form workflow in the current CodeIgniter codebase so that:

- administrators can create forms from CSV
- administrators can import form inputs from CSV
- administrators can generate an input form file for a selected `FormID`
- users can open their assigned forms, submit values, and trigger document generation
- generated output files are saved in `4 OutputFiles` and made available for download

## Current Baseline

- Gamma authentication and workspace access already exist through `app/controllers/admin/Auth.php` and `app/controllers/admin/Gamma.php`.
- Gamma storage paths already exist in `app/config/gamma.php` and `app/libraries/Gamma_path_service.php`.
- Gamma tables already exist in `sql/gamma_schema.sql`, but the implementation only covers user notes and a basic form listing.
- The current schema uses `input_form_instructions`; the new requirement is to rename that business meaning to `input_form_divisions`.

## Recommended Delivery Order

### 1. Foundation and schema alignment

Update the Gamma schema and server-side code so the data model matches the new requirements.

- add or align `sma_gamma_forms`, `sma_gamma_form_inputs`, `sma_gamma_input_records`, and `sma_gamma_output_file_logs`
- rename the form input division field from `input_form_instructions` to `input_form_divisions`
- keep backward-safe handling in PHP where needed if older rows still use the old field during transition
- add model methods for:
  - creating forms
  - importing forms from CSV rows
  - importing form inputs from CSV rows
  - fetching inputs by `form_id`
  - creating output file logs
  - storing submitted input records

Critical files:

- `sql/gamma_schema.sql`
- `app/models/admin/Gamma_form_model.php`
- new `app/models/admin/Gamma_document_model.php`

### 2. Admin form management and CSV import

Create an admin-only Gamma forms area that supports:

- listing forms with visible `FormID`
- importing one form row from CSV into `sma_gamma_forms`
- creating the precedent clause file and document creation file after import
- importing multiple form input rows from CSV into `sma_gamma_form_inputs`

Implementation notes:

- follow existing CodeIgniter upload patterns already used in `app/controllers/admin/System_settings.php`
- store uploaded CSVs temporarily under an existing safe upload path
- validate required headers before processing
- create files at the locations stored in the form row, relative to the assigned Gamma user tree

Critical files:

- new `app/controllers/admin/Gamma_forms.php`
- `app/models/admin/Gamma_form_model.php`
- new `themes/default/admin/views/gamma/forms_index.php`
- new `themes/default/admin/views/gamma/import_form.php`
- new `themes/default/admin/views/gamma/import_form_inputs.php`

### 3. Input form generation

Add an admin action that accepts a `FormID`, loads all matching `sma_gamma_form_inputs` rows, and writes a generated PHP view file to `Form.input_form_location`.

The generator should support:

- label text from `input_label`
- input names from `input_name`
- input control type from `input_type`
- default value from `default_value`
- `maxlength` from `max_size`
- grouping by `input_form_divisions`
- same-line grouping using `line_command`
- table rendering using:
  - `table_number`
  - `table_displayed`
  - `table_row`
  - `table_column`

Initial scope decisions:

- treat `allowed_input` as stored metadata but do not enforce custom formulas yet
- support `Textbox`, `Dropbox`, and `Checkbox`
- store generated files under the existing Gamma user interface path structure

Critical files:

- new `app/libraries/Gamma_input_form_builder.php`
- `app/controllers/admin/Gamma_forms.php`
- `app/models/admin/Gamma_form_model.php`

### 4. User-side form usage and submission

Extend the Gamma user flow so a logged-in user can:

- see their assigned forms
- click a form action button
- open the generated input form page
- submit the form values

On submit:

- create a new `output_file_id`
- store one input record row per submitted value in `sma_gamma_input_records`
- call the PHP file at `document_creation_location`
- create the output filename in `YYYYMMDDHHMM OutputFilenameBase.docx` format
- save it in the configured output folder
- show a download link or redirect to a download endpoint

Critical files:

- `app/controllers/admin/Gamma.php`
- `app/models/admin/Gamma_form_model.php`
- new `app/models/admin/Gamma_document_model.php`
- new `app/libraries/Gamma_document_runner.php`
- `themes/default/admin/views/gamma/dashboard.php`

### 5. Verification

Verify the workflow end to end with one seeded Gamma user and one sample form.

- import form CSV
- confirm `FormID` is visible in admin UI
- confirm precedent and document creation files are created
- import form input CSV
- generate input form file by `FormID`
- log in as assigned user and open the form
- submit sample values
- confirm rows are written to `sma_gamma_input_records`
- confirm an output log row is created
- confirm a `.docx` file is saved to `4 OutputFiles`

## First Implementation Slice

Start with the foundation slice in this order:

1. Refresh the plan file and schema naming to `input_form_divisions`
2. Expand `Gamma_form_model` into a real forms/input repository
3. Introduce the admin `Gamma_forms` controller skeleton and route-ready views
4. Add form list UI with visible `FormID`

## Known Risks

- Existing rows or SQL may still refer to `input_form_instructions`; code should tolerate old naming during transition.
- `input_form_location`, `precedent_clause_location`, and `document_creation_location` may be stored as user-relative paths or absolute paths; path normalization should be centralized.
- CSV examples are not present in the current workspace, so header handling should be defensive and explicit.

