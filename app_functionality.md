# Application Functionality Guide

## Overview

This application is a CodeIgniter-based management system with a custom `Gamma` module for dynamic document generation.

At a high level, the app supports:

- user authentication and role-based access
- user profile management
- file management through a protected document workspace
- Gamma document template management
- dynamic form generation from database metadata
- DOCX document creation and download

## Main Purpose of the Gamma Module

The `Gamma` module turns the application into a metadata-driven document generator.

Instead of hardcoding each form manually, administrators define forms and input fields in the database, usually through CSV import. The system then:

1. shows a user only the forms assigned to that user
2. generates an input form dynamically
3. accepts submitted values
4. stores the submission as input records
5. generates a DOCX output file
6. lets the user download the generated document

## User Roles

### Admin / Owner

Admins and owners can:

- access the Gamma forms management area
- import form definitions from CSV
- import form input definitions from CSV
- generate input form files for a selected `FormID`
- access broader Gamma file storage through the file manager
- manage users and profiles

### Regular Logged-in User

Regular users can:

- log in to the application
- open the Gamma workspace
- see only the forms assigned to their account
- open a generated form
- fill and submit form data
- download generated documents
- access only their own Gamma file tree in the file manager

## Gamma Workflow

### 1. Login

Users authenticate through the existing application login system.

Relevant behavior:

- protected Gamma pages require login
- session handling is reused from the main app
- Gamma uses the existing `users` table for identity

### 2. Gamma Workspace

After login, the user can open the Gamma workspace.

The workspace shows:

- logged-in user information
- assigned forms
- previously generated documents
- download links for generated files

### 3. Form Assignment

Each Gamma form belongs to a specific user through the `gamma_forms` table.

This means:

- one user sees only their own allowed document templates
- forms are filtered by `user_id`

### 4. Dynamic Form Generation

The form UI is not hardcoded.

It is built from rows stored in the `gamma_form_inputs` table.

Each input row can define:

- input name
- input label
- input type
- data type
- default value
- max size
- allowed input options
- grouping and layout metadata

The form builder supports:

- headings and subheadings using `InputFormDivisions`
- same-line field grouping using `LineCommand`
- grouped table sections using `TableNumber`
- repeating table rows with `Add Row` and `Remove Row`

### 5. Form Submission

When a user submits a form:

1. the app creates a new `output_file_id`
2. submitted values are stored in `gamma_input_records`
3. the document runner processes the form
4. an output DOCX file is created
5. the file path is stored in `gamma_output_file_logs`

Nested table values are flattened before storage so each value can still be linked back to the generated output.

### 6. Document Generation

Document generation is handled by the Gamma document runner.

It supports:

- loading the configured document creation script
- exposing submitted values to that script
- loading precedent clause text
- replacing placeholders in the fallback generation flow
- generating a DOCX file if the custom script does not create one

Output files are saved using this naming style:

`YYYYMMDDHHMM OutputFilenameBase.docx`

Example:

`202603261430 EmploymentAgreement.docx`

### 7. Download

Generated files appear in the Gamma workspace and can be downloaded through a protected endpoint.

Access control ensures:

- admins can access broader output history
- regular users can only download their own generated files

## Admin CSV Features

### Import Forms CSV

Admins can upload a CSV file to create one or more form records.

This process:

- inserts rows into `gamma_forms`
- creates support files for precedent clauses
- creates support files for document generation scripts

### Import Inputs CSV

Admins can upload a CSV file to create input definitions for a form.

This process inserts rows into:

- `gamma_form_inputs`

### Generate Input Form

Admins can generate the dynamic input form file for a selected `FormID`.

This reads the database metadata and writes the generated HTML form file to the path stored in the form record.

## Gamma File Structure

Each Gamma user gets a dedicated folder tree under:

`Gamma/Users/{Username}/`

Standard folders include:

- `1 UserInterfaceFiles`
- `2 PrecedentClauses`
- `3 DocumentGeneration`
- `4 OutputFiles`

This structure is created automatically for users when needed.

## Authentication and Recovery Features

The app includes:

- login
- logout
- forgot password
- reset password
- forgot username

The forgot-username flow lets a user submit their email address and receive their username by email if a matching account exists.

## File Manager Integration

The document/file manager is integrated with Gamma storage.

Behavior:

- admins can access the broader Gamma root
- regular users are restricted to their own Gamma folder

## Database Areas Used by Gamma

The Gamma module primarily uses:

- `gamma_forms`
- `gamma_form_inputs`
- `gamma_input_records`
- `gamma_output_file_logs`
- `gamma_user_notes`
- `users`

## Current Functional Status

The app already supports the core end-to-end Gamma workflow:

- form assignment
- admin CSV import
- dynamic form generation
- user submission
- input record logging
- DOCX output creation
- protected download

## Known Limitations

The app is functional, but some advanced behavior is still dependent on project-specific business logic.

Examples:

- complex client-specific DOCX logic must still be written inside each document creation script
- advanced template processing can be expanded further if needed
- table layout supports repeating rows, but a fully advanced spreadsheet-like rendering model is not implemented

## Summary

This app is both:

- a general management system with users and protected file access
- a Gamma-powered dynamic document generator

Its core value is that administrators can define document workflows through metadata and CSV imports instead of manually coding every form from scratch.
