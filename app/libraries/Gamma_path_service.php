<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gamma_path_service
{
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->config->load('gamma', true);
        $this->ci->load->helper('gamma');
    }

    protected function item($key, $default = null)
    {
        $value = $this->ci->config->item($key, 'gamma');
        return $value !== null ? $value : $default;
    }

    public function getBaseRelativePath()
    {
        return rtrim(str_replace('\\', '/', $this->item('gamma_base_path', 'Gamma/Users/')), '/') . '/';
    }

    public function getBaseAbsolutePath()
    {
        return rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, trim($this->getBaseRelativePath(), '/'));
    }

    public function getBaseUrl()
    {
        return rtrim(base_url(trim($this->getBaseRelativePath(), '/')), '/') . '/';
    }

    public function getUserFolderName($username)
    {
        return gamma_safe_segment($username);
    }

    public function getUserRootRelativePath($username)
    {
        return $this->getBaseRelativePath() . $this->getUserFolderName($username);
    }

    public function getUserRootAbsolutePath($username)
    {
        return rtrim($this->getBaseAbsolutePath(), '\\/') . DIRECTORY_SEPARATOR . $this->getUserFolderName($username);
    }

    public function getUserRootUrl($username)
    {
        return $this->getBaseUrl() . rawurlencode($this->getUserFolderName($username));
    }

    public function getUserStandardFolders()
    {
        return (array) $this->item('gamma_user_folders', array());
    }

    public function getInputFormFolderName()
    {
        return (string) $this->item('gamma_input_form_folder', '1 InputFile');
    }

    public function buildInputFormFilename($form_title, $form_id)
    {
        return 'IF_' . gamma_safe_compact_segment($form_title) . str_pad((string) ((int) $form_id), 3, '0', STR_PAD_LEFT) . '.php';
    }

    public function buildInputFormRelativePath($form_title, $form_id)
    {
        return $this->normalizeRelativePath($this->getInputFormFolderName() . '/' . $this->buildInputFormFilename($form_title, $form_id));
    }

    public function getFormFolderName($form_id, $form_title)
    {
        return gamma_pad_form_id($form_id) . ' ' . gamma_safe_segment($form_title);
    }

    public function getFormRootAbsolutePath($username, $form_id, $form_title)
    {
        return $this->getUserRootAbsolutePath($username) . DIRECTORY_SEPARATOR . $this->getFormFolderName($form_id, $form_title);
    }

    public function normalizeRelativePath($path)
    {
        $path = trim(str_replace('\\', '/', (string) $path));
        return ltrim($path, '/');
    }

    public function resolvePath($path, $username = null)
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }

        $normalized = str_replace('\\', '/', $path);
        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) || strpos($normalized, '//') === 0) {
            return $path;
        }

        $relative_path = $this->normalizeRelativePath($normalized);
        $base_relative = $this->normalizeRelativePath($this->getBaseRelativePath());
        if ($relative_path === $base_relative || strpos($relative_path, $base_relative . '/') === 0) {
            return rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
        }

        if (strpos($normalized, '/') === 0) {
            if (stripos($relative_path, 'gamma/') === 0 || strtolower($relative_path) === 'gamma') {
                return rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
            }

            return rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
        }

        if ($username) {
            return rtrim($this->getUserRootAbsolutePath($username), '\\/') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
        }

        return rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
    }

    public function ensureParentDirectory($path)
    {
        $parent = dirname($path);
        if ($parent && $parent !== '.' && !is_dir($parent)) {
            mkdir($parent, 0755, true);
        }

        return $parent;
    }

    public function writeFile($path, $contents = '')
    {
        $absolute_path = $this->resolvePath($path);
        $this->ensureParentDirectory($absolute_path);
        file_put_contents($absolute_path, $contents);

        return $absolute_path;
    }

    public function ensureBasePath()
    {
        return $this->ensureDirectory($this->getBaseAbsolutePath());
    }

    public function ensureUserFolders($username)
    {
        $this->ensureBasePath();

        $user_root = $this->getUserRootAbsolutePath($username);
        $this->ensureDirectory($user_root);

        foreach ($this->getUserStandardFolders() as $folder) {
            $this->ensureDirectory($user_root . DIRECTORY_SEPARATOR . $folder);
        }

        return $user_root;
    }

    public function ensureFormFolders($username, $form_id, $form_title)
    {
        $form_root = $this->getFormRootAbsolutePath($username, $form_id, $form_title);
        $this->ensureUserFolders($username);
        $this->ensureDirectory($form_root);

        if ($this->item('gamma_repeat_standard_folders_in_form_root', false)) {
            foreach ($this->getUserStandardFolders() as $folder) {
                $this->ensureDirectory($form_root . DIRECTORY_SEPARATOR . $folder);
            }
        }

        return $form_root;
    }

    protected function ensureDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }
}
