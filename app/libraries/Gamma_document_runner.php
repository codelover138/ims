<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_document_runner
{
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('gamma_path_service');
    }

    protected function xml($value)
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    protected function stringify($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }

    protected function flattenInputValues(array $values, $prefix = '')
    {
        $flattened = array();

        foreach ($values as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                $flattened += $this->flattenInputValues($value, $path);
                continue;
            }

            $flattened[$path] = $this->stringify($value);
        }

        return $flattened;
    }

    protected function loadPrecedentClauseText($form, $username)
    {
        $path = trim((string) $form->precedent_clause_location);
        if ($path === '') {
            return '';
        }

        $absolute_path = $this->ci->gamma_path_service->resolvePath($path, $username);
        if (!is_file($absolute_path)) {
            return '';
        }

        return (string) file_get_contents($absolute_path);
    }

    protected function applyTemplateValues($text, array $flat_values)
    {
        if ($text === '') {
            return '';
        }

        $replacements = array();
        foreach ($flat_values as $key => $value) {
            $replacements['{{' . $key . '}}'] = $value;
            $replacements['[[' . $key . ']]'] = $value;
        }

        return strtr($text, $replacements);
    }

    protected function relativeToWebPath($absolute_path)
    {
        $absolute_path = str_replace('\\', '/', $absolute_path);
        $base_path = str_replace('\\', '/', rtrim(FCPATH, '\\/')) . '/';

        if (strpos($absolute_path, $base_path) === 0) {
            return ltrim(substr($absolute_path, strlen($base_path)), '/');
        }

        return $absolute_path;
    }

    protected function createMinimalDocx($absolute_path, array $lines)
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('ZipArchive is not available for DOCX generation.');
        }

        $zip = new ZipArchive();
        $this->ci->gamma_path_service->ensureParentDirectory($absolute_path);

        if ($zip->open($absolute_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create output document.');
        }

        $content_types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';

        $doc_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>';
        $app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Gamma</Application></Properties>';
        $core = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:title>Gamma Output</dc:title></cp:coreProperties>';

        $paragraphs = '';
        foreach ($lines as $line) {
            if (is_array($line)) {
                if ($line['type'] === 'title') {
                    $text = $this->xml($line['text']);
                    $paragraphs .= '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="240"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="48"/><w:szCs w:val="48"/><w:color w:val="1F3864"/></w:rPr><w:t xml:space="preserve">' . $text . '</w:t></w:r></w:p>';
                } elseif ($line['type'] === 'heading') {
                    $text = $this->xml($line['text']);
                    $paragraphs .= '<w:p><w:pPr><w:spacing w:before="360" w:after="120"/><w:pBdr><w:bottom w:val="single" w:sz="6" w:space="1" w:color="D9D9D9"/></w:pBdr></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/><w:color w:val="2F5496"/></w:rPr><w:t xml:space="preserve">' . $text . '</w:t></w:r></w:p>';
                } elseif ($line['type'] === 'meta') {
                    $text = $this->xml($line['text']);
                    $paragraphs .= '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:i/><w:sz w:val="20"/><w:szCs w:val="20"/><w:color w:val="7F7F7F"/></w:rPr><w:t xml:space="preserve">' . $text . '</w:t></w:r></w:p>';
                } elseif ($line['type'] === 'key_value') {
                    $key = $this->xml($line['key'] . ': ');
                    $val = $this->xml(trim((string)$line['value']) === '' ? ' ' : $line['value']);
                    $paragraphs .= '<w:p><w:pPr><w:spacing w:after="100"/></w:pPr><w:r><w:rPr><w:b/><w:color w:val="404040"/></w:rPr><w:t xml:space="preserve">' . $key . '</w:t></w:r><w:r><w:t xml:space="preserve">' . $val . '</w:t></w:r></w:p>';
                }
            } else {
                $text = $this->xml($line === '' ? ' ' : $line);
                $paragraphs .= '<w:p><w:r><w:t xml:space="preserve">' . $text . '</w:t></w:r></w:p>';
            }
        }

        $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" mc:Ignorable="w14 wp14">'
            . '<w:body>' . $paragraphs . '<w:sectPr><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440"/></w:sectPr></w:body></w:document>';

        $zip->addFromString('[Content_Types].xml', $content_types);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('word/document.xml', $document);
        $zip->addFromString('word/_rels/document.xml.rels', $doc_rels);
        $zip->addFromString('docProps/app.xml', $app);
        $zip->addFromString('docProps/core.xml', $core);
        $zip->close();
    }

    protected function createLegacyWordDocument($absolute_path, array $lines)
    {
        $this->ci->gamma_path_service->ensureParentDirectory($absolute_path);

        $html_lines = array();
        foreach ($lines as $line) {
            if (is_array($line)) {
                if ($line['type'] === 'title') {
                    $html_lines[] = '<h1 style="text-align:center;color:#1F3864;">' . $this->xml($line['text']) . '</h1>';
                } elseif ($line['type'] === 'heading') {
                    $html_lines[] = '<h2 style="color:#2F5496;border-bottom:1px solid #d9d9d9;padding-bottom:4px;">' . $this->xml($line['text']) . '</h2>';
                } elseif ($line['type'] === 'meta') {
                    $html_lines[] = '<p style="text-align:center;color:#7f7f7f;"><em>' . $this->xml($line['text']) . '</em></p>';
                } elseif ($line['type'] === 'key_value') {
                    $html_lines[] = '<p><strong>' . $this->xml($line['key']) . ':</strong> ' . $this->xml(trim((string) $line['value']) === '' ? ' ' : $line['value']) . '</p>';
                }
            } else {
                $html_lines[] = '<p>' . nl2br($this->xml($line === '' ? ' ' : $line)) . '</p>';
            }
        }

        $document = '<html><head><meta charset="UTF-8"></head><body style="font-family:Arial,sans-serif;font-size:12pt;">'
            . implode('', $html_lines)
            . '</body></html>';

        if (file_put_contents($absolute_path, $document) === false) {
            throw new RuntimeException('Unable to create output document.');
        }
    }

    public function run($form, $username, $output_file_id, array $input_values)
    {
        $base_name = trim((string) ($form->output_filename_base ?: $form->form_title ?: 'Output'));
        $extension = class_exists('ZipArchive') ? '.docx' : '.doc';
        $filename = date('YmdHi') . ' ' . preg_replace('/[\\\\\\/:\*\?"<>\|]+/', ' ', $base_name) . $extension;

        // Always save into the logged-in user's own 4 OutputFiles folder,
        // regardless of what path is stored in the form row.
        $output_folder = '4 OutputFiles';
        $absolute_folder = $this->ci->gamma_path_service->getUserRootAbsolutePath($username)
            . DIRECTORY_SEPARATOR . $output_folder;
        if (!is_dir($absolute_folder)) {
            mkdir($absolute_folder, 0755, true);
        }

        $absolute_output_path = rtrim($absolute_folder, '\\/') . DIRECTORY_SEPARATOR . $filename;
        $gamma_generated_at = date('Y-m-d H:i:s');
        $gamma_flat_input_values = $this->flattenInputValues($input_values);
        $gamma_precedent_clause_path = trim((string) $form->precedent_clause_location);
        $gamma_precedent_clause_text = $this->loadPrecedentClauseText($form, $username);
        $document_creation = trim((string) $form->document_creation_location);
        if ($document_creation !== '') {
            $creation_file = $this->ci->gamma_path_service->resolvePath($document_creation, $username);
            if (is_file($creation_file)) {
                $gamma_form = $form;
                $gamma_output_file_id = $output_file_id;
                $gamma_output_file_path = $absolute_output_path;
                $gamma_input_values = $input_values;
                include $creation_file;
            }
        }

        if (!is_file($absolute_output_path)) {
            $lines = array(
                ['type' => 'title', 'text' => $form->form_title],
                ['type' => 'meta', 'text' => 'Generated: ' . $gamma_generated_at],
                ['type' => 'meta', 'text' => 'Output File ID: ' . $output_file_id],
                '',
            );

            $resolved_clause_text = $this->applyTemplateValues($gamma_precedent_clause_text, $gamma_flat_input_values);
            if (trim($resolved_clause_text) !== '') {
                $lines[] = ['type' => 'heading', 'text' => 'Document Base & Clauses'];
                $lines[] = '';
                foreach (preg_split('/\r\n|\r|\n/', $resolved_clause_text) as $clause_line) {
                    $lines[] = $clause_line;
                }
            }

            $lines[] = ['type' => 'heading', 'text' => 'Submitted Form Data'];
            foreach ($gamma_flat_input_values as $key => $value) {
                $lines[] = ['type' => 'key_value', 'key' => $key, 'value' => $value];
            }
            if ($extension === '.docx') {
                $this->createMinimalDocx($absolute_output_path, $lines);
            } else {
                $this->createLegacyWordDocument($absolute_output_path, $lines);
            }
        }

        return array(
            'absolute_path' => $absolute_output_path,
            'relative_path' => $this->relativeToWebPath($absolute_output_path),
            'filename' => $filename,
        );
    }
}
