# Gamma System — Requirements and Implementation Reference

Generated: 2026-04-06. Source: client brief dated 29 November 2025.

---

## 1. System Overview

The purpose of the Gamma module is to allow users to:

1. Log in and view a list of documents they can generate (each document = one row in `sma_gamma_forms`, linked to a `user_id`).
2. Click on a form to open an auto-generated input form (textboxes, dropboxes, checkboxes).
3. Submit the form to:
   a. Record values in `sma_gamma_input_records` under a new unique `output_file_id`.
   b. Call a custom PHP document-creation file.
4. The document-creation PHP file:
   a. Applies logic to the submitted values (e.g. gender → he/him/his pronoun).
   b. Copies blocks of text from a precedent clause file (using marker-based extraction).
   c. Saves the resulting `.docx` to the output folder.
5. The generated file is available for download. Filename format: `YYYYMMDDHHMM OutputFilenameBase.docx` (24-hour time).

---

## 2. Database Schema

All tables use the `sma_` prefix. Source file: [sql/gamma_schema.sql](sql/gamma_schema.sql).

### `sma_gamma_forms`

| Column | Type | Notes |
|---|---|---|
| `form_id` | int AUTO_INCREMENT PK | Unique form identifier; visible to admin |
| `user_id` | int FK → sma_users.id | User to whom the form is assigned |
| `form_title` | varchar(255) | Display title |
| `description` | text | Optional description shown on dashboard |
| `button_label` | varchar(100) | Label for the form's submit button |
| `input_form_location` | varchar(500) | Relative path to the generated input form HTML file |
| `precedent_clause_location` | varchar(500) | Relative path to the precedent clause source file |
| `document_creation_location` | varchar(500) | Relative path to the custom PHP document-generation file |
| `output_filename_base` | varchar(255) | Base name used in the output `.docx` filename |
| `output_file_location` | varchar(500) | Folder where output `.docx` files are saved (default: `4 OutputFiles`) |

### `sma_gamma_form_inputs`

| Column | Type | Notes |
|---|---|---|
| `form_input_id` | int AUTO_INCREMENT PK | |
| `form_id` | int FK → sma_gamma_forms | |
| `input_name` | varchar(100) | HTML field `name` attribute |
| `input_label` | varchar(255) | Label text shown next to the input |
| `input_type` | enum('Textbox','Dropbox','Checkbox') | Control type |
| `data_type` | varchar(100) | HTML input type for Textbox (text, date, email, number, datetime-local) |
| `default_value` | varchar(500) | Pre-filled value. For Dropbox: first selected option if it matches a key |
| `allowed_input` | varchar(500) | For Dropbox: pipe/comma-separated `value=Label` or plain options. Deferred: not enforced as validation yet |
| `max_size` | int | `maxlength` attribute for Textbox inputs |
| `input_form_divisions` | text | Grouping info: `Heading1=Title` and/or `Subheading1=Subtitle` (semicolon/pipe/newline separated) |
| `line_command` | text | Inputs with the same `line_command` value appear on the same row |
| `table_number` | int | Inputs sharing the same `table_number` are rendered in a repeating table |
| `table_displayed` | int | Number of rows visible by default in the table |
| `table_row` | int | Row position within the table |
| `table_column` | int | Column position within the table |

### `sma_gamma_output_file_logs`

| Column | Type | Notes |
|---|---|---|
| `output_file_id` | int AUTO_INCREMENT PK | Unique ID per generated document |
| `form_id` | int FK → sma_gamma_forms | |
| `output_file` | varchar(500) | Web-relative path to the generated `.docx` |
| `date_created` | datetime | |

### `sma_gamma_input_records`

| Column | Type | Notes |
|---|---|---|
| `form_input_record_id` | int AUTO_INCREMENT PK | |
| `output_file_id` | int FK → sma_gamma_output_file_logs | Groups records per document generation run |
| `input_name` | varchar(100) | Field name (dot-notation for nested/table values) |
| `input_value` | text | Submitted value |
| `date_created` | datetime | |

### `sma_gamma_user_notes` (existing, not in current workflow)

Stores freeform notes per user. Not part of the form generation workflow.

---

## 3. File Structure and Paths

The Gamma path service ([app/libraries/Gamma_path_service.php](app/libraries/Gamma_path_service.php)) resolves user-relative paths against the configured `gamma_base_path` (stored in `sma_settings`).

Key path conventions:
- `input_form_location` → generated HTML form file (written by admin, read by user)
- `precedent_clause_location` → text/HTML file with clause blocks delimited by markers
- `document_creation_location` → custom PHP file written by the developer
- `output_file_location` → folder for `.docx` output (default: `4 OutputFiles`)

Support files (precedent clause file and document creation PHP stub) are auto-created on CSV import in [Gamma_forms.php:139](app/controllers/admin/Gamma_forms.php#L139).

---

## 4. Admin Workflow

### 4.1 Import Form (CSV)

- Route: `admin/gamma_forms/import_forms_csv`
- Controller: [Gamma_forms.php:187](app/controllers/admin/Gamma_forms.php#L187)
- Accepts: horizontal header CSV or vertical key=value CSV (auto-detected)
- Required CSV columns (case-insensitive, spaces/underscores interchangeable):
  - `UserID`, `FormTitle`, `InputFormLocation`, `PrecedentClauseLocation`, `DocumentCreationLocation`, `OutputFilenameBase`, `OutputFileLocation`
  - Optional: `Description`, `ButtonLabel`
- After insert: creates the precedent clause file and document creation PHP stub at the specified locations.
- The new `FormID` is displayed in the admin form list.

### 4.2 Import Form Inputs (CSV)

- Route: `admin/gamma_forms/import_inputs_csv`
- Controller: [Gamma_forms.php:228](app/controllers/admin/Gamma_forms.php#L228)
- If CSV rows do not include a `FormID` column, a Default Form ID field in the UI can be used (auto-populated from the most recently imported form).
- Required CSV columns: `InputName`, `InputLabel`, `InputType`
- Optional: `DataType`, `DefaultValue`, `AllowedInput`, `MaxSize`, `InputFormDivisions`, `LineCommand`, `TableNumber`, `TableDisplayed`, `TableRow`, `TableColumn`
- `InputFormDivisions` also accepted as legacy `InputFormInstructions` in CSV header.

### 4.3 Generate Input Form

- Route: `admin/gamma_forms/generate_input_form/{form_id}`
- Controller: [Gamma_forms.php:284](app/controllers/admin/Gamma_forms.php#L284)
- Reads all `sma_gamma_form_inputs` rows for the `FormID`.
- Calls `Gamma_input_form_builder::build()`.
- Writes generated HTML to the absolute path resolved from `input_form_location`.

---

## 5. Input Form Builder

Library: [app/libraries/Gamma_input_form_builder.php](app/libraries/Gamma_input_form_builder.php)

### Grouping and layout rules

| DB field | Effect in generated form |
|---|---|
| `input_form_divisions` with `Heading1=X` | Bold `<h4>` heading before the group |
| `input_form_divisions` with `Subheading1=X` | Italic `<div>` subheading |
| Same `line_command` value | Fields placed side-by-side in Bootstrap grid row |
| Non-null `table_number` | Fields grouped into a repeating table |
| `table_displayed` | Number of visible rows initially |
| `table_row` / `table_column` | Position within table |

### Dropbox options

Options are parsed from `allowed_input`. Format: pipe, comma, or newline separated.
- `value=Label` format: `M=Male|F=Female`
- Plain format: `Yes|No|Maybe`
- If `allowed_input` is null, fallback to `default_value` for options.
- **Important**: if both are null for a Dropbox input, the dropdown will render empty.

### Add/Remove Row (tables)

JavaScript is embedded in the generated HTML to clone/remove table rows. The new row's field names are reindexed: `gamma_tables[table_number][row_index][input_name]`.

---

## 6. User Workflow

### 6.1 Dashboard

- Route: `admin/gamma`
- Shows assigned forms and previously generated documents.
- Admin/Owner users see a "Manage Forms" button linking to the admin form management area.

### 6.2 Open Form

- Route: `admin/gamma/open_form/{form_id}`
- Controller: [Gamma.php:68](app/controllers/admin/Gamma.php#L68)
- Reads the generated HTML file from `input_form_location` and renders it in the site layout.
- If the file does not exist, redirects with an error (admin must generate it first).

### 6.3 Submit Form

- Route: `admin/gamma/submit_form/{form_id}` (POST)
- Controller: [Gamma.php:94](app/controllers/admin/Gamma.php#L94)
- Creates a new `output_file_id` row in `sma_gamma_output_file_logs`.
- Stores all submitted values (flattened) in `sma_gamma_input_records`.
- Calls `Gamma_document_runner::run()`.
- Updates the log row with the relative file path.
- Redirects to dashboard with a download link in the flash message.

### 6.4 Download

- Route: `admin/gamma/download/{output_file_id}`
- Users can only download files from their own forms; admins can download any.

---

## 7. Document Runner

Library: [app/libraries/Gamma_document_runner.php](app/libraries/Gamma_document_runner.php)

### How the custom PHP file is called

The runner calls `include $creation_file` where `$creation_file` is the absolute path of `document_creation_location`. The included PHP file inherits the runner's local scope and has access to:

| Variable | Type | Contents |
|---|---|---|
| `$gamma_form` | object | The form DB row (all columns) |
| `$gamma_output_file_id` | int | The newly created output file ID |
| `$gamma_output_file_path` | string | Absolute path where the `.docx` must be saved |
| `$gamma_input_values` | array | Full submitted POST array (nested for tables) |
| `$gamma_flat_input_values` | array | Flattened `input_name => value` (dot-notation for nested) |
| `$gamma_precedent_clause_path` | string | Relative path to the precedent clause file |
| `$gamma_precedent_clause_text` | string | Full text content of the precedent clause file |
| `$this` | Gamma_document_runner | Gives access to `$this->ci` (CodeIgniter instance) |

### Custom PHP file responsibilities

The custom PHP file should:
1. Read values from `$gamma_flat_input_values` or `$gamma_input_values`.
2. Apply any transformation logic (e.g. gender pronouns, conditional clauses).
3. Extract blocks from `$gamma_precedent_clause_text` using marker search.
4. Create the `.docx` file at `$gamma_output_file_path`.
5. The file should use a library such as **PhpWord** for rich formatting.

### Fallback behaviour

If the custom PHP file does NOT create the file at `$gamma_output_file_path`, the runner creates a minimal plain-text `.docx` using PHP's `ZipArchive` containing the submitted values and precedent clause text. This fallback is useful for testing.

---

## 8. Output File Naming

Format: `YYYYMMDDHHMM {OutputFilenameBase}.docx`

- `HHMM` is 24-hour format.
- Special characters (`\ / : * ? " < > |`) in the base name are replaced with spaces.
- The file is saved in the folder specified by `output_file_location` (default: `4 OutputFiles`), resolved relative to the user's Gamma workspace root.

---

## 9. Routes Summary

| URL | Controller method | Access |
|---|---|---|
| `admin/gamma` | `Gamma::index` | Any logged-in user |
| `admin/gamma/open_form/{id}` | `Gamma::open_form` | Any logged-in user (own forms only) |
| `admin/gamma/submit_form/{id}` | `Gamma::submit_form` | Any logged-in user (own forms only) |
| `admin/gamma/download/{id}` | `Gamma::download` | Own files; admin can download any |
| `admin/gamma_forms` | `Gamma_forms::index` | Admin / Owner only |
| `admin/gamma_forms/import_forms_csv` | `Gamma_forms::import_forms_csv` | Admin / Owner only |
| `admin/gamma_forms/import_inputs_csv` | `Gamma_forms::import_inputs_csv` | Admin / Owner only |
| `admin/gamma_forms/generate_input_form/{id}` | `Gamma_forms::generate_input_form` | Admin / Owner only |

---

## 10. Implementation Completeness

### Fully implemented

- All database tables with correct schema and `input_form_divisions` naming
- CSV import for forms (creates DB row + support files)
- CSV import for form inputs (supports both CSV layouts, legacy header names)
- Input form builder (headings, subheadings, line grouping, tables, add/remove row)
- Admin form list with visible FormID
- User dashboard (assigned forms + generated documents)
- User form open / submit flow
- Document runner (calls custom PHP, passes all variables, has fallback docx creator)
- Output filename format `YYYYMMDDHHMM Base.docx`
- Download endpoint with user-ownership check

### Deferred / not yet implemented

| Item | Notes |
|---|---|
| `allowed_input` server-side validation | Stored in DB; not yet enforced during form submission |
| PhpWord library | Not in `composer.json`. Required for real formatted `.docx`. Run: `composer require phpoffice/phpword` |
| CSRF token in generated form HTML | The builder generates plain HTML (not CI's `form_open()`). If `$config['csrf_protection']` is `TRUE`, form submission will fail. Needs: inject CI CSRF field into generated HTML, or disable CSRF for this route. |
| `assets/uploads/csv/` directory | Must exist on disk for CSV upload to succeed |

---

## 11. Developer Notes for Document Creation PHP Files

When writing a `document_creation_location` PHP file:

```php
<?php
// Available: $gamma_form, $gamma_output_file_id, $gamma_output_file_path,
//            $gamma_input_values, $gamma_flat_input_values,
//            $gamma_precedent_clause_path, $gamma_precedent_clause_text, $this

// Example: pronoun logic
$gender = $gamma_flat_input_values['gender'] ?? '';
$pronoun = ($gender === 'Male') ? 'he' : 'she';
$possessive = ($gender === 'Male') ? 'his' : 'her';
$object = ($gender === 'Male') ? 'him' : 'her';

// Example: extract block between markers in precedent clause file
$marker_start = '<!-- CLAUSE:EXECUTOR_APPOINTMENT:START -->';
$marker_end   = '<!-- CLAUSE:EXECUTOR_APPOINTMENT:END -->';
$pos_start = strpos($gamma_precedent_clause_text, $marker_start);
$pos_end   = strpos($gamma_precedent_clause_text, $marker_end);
if ($pos_start !== false && $pos_end !== false) {
    $clause_text = substr(
        $gamma_precedent_clause_text,
        $pos_start + strlen($marker_start),
        $pos_end - $pos_start - strlen($marker_start)
    );
}

// Example: create docx with PhpWord
$phpWord = new \PhpOffice\PhpWord\PhpWord();
$section = $phpWord->addSection();
$section->addText($pronoun . ' is the testator.');
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($gamma_output_file_path);
```

The file must save the final document to `$gamma_output_file_path`. If it does not, the runner creates a plain fallback.
