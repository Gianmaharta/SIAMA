<?php

namespace App\Controllers;

use App\Models\WebsiteSettingModel;

class WebsiteSettings extends BaseController
{
    protected WebsiteSettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new WebsiteSettingModel();
        helper(['setting', 'logger']);
    }

    public function index()
    {
        if (session()->get('nama_role') !== 'Super_Admin') {
            if ($this->wantsJson()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Akses Ditolak: Hanya Super Admin yang dapat mengelola Pengaturan Website.',
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak: Hanya Super Admin yang dapat mengelola Pengaturan Website.');
        }

        $settings = $this->settingModel->getAllMap(true);
        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'settings' => $settings,
            ]);
        }

        $data = [
            'title'    => 'Pengaturan Website - SIAMA',
            'settings' => $settings,
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        if (session()->get('nama_role') !== 'Super_Admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak: Hanya Super Admin yang dapat mengelola Pengaturan Website.');
        }

        $defaultPassword = trim((string) $this->request->getPost('default_password'));
        if ($defaultPassword !== '' && strlen($defaultPassword) < 6) {
            return redirect()->back()->withInput()->with('error', 'Password default minimal harus 6 karakter.');
        }

        $loginMode = trim((string) $this->request->getPost('login_mode'));
        if (!in_array($loginMode, ['both', 'local', 'sso'], true)) {
            $loginMode = 'both';
        }

        $passwordMode = trim((string) $this->request->getPost('password_mode'));
        if (!in_array($passwordMode, ['default', 'random'], true)) {
            $passwordMode = 'default';
        }

        $settingsToSave = [
            'site_name'        => trim((string) $this->request->getPost('site_name')) ?: 'SIAMA',
            'site_tagline'     => trim((string) $this->request->getPost('site_tagline')) ?: 'Sistem Informasi Alih Media Arsip',
            'instansi_name'    => trim((string) $this->request->getPost('instansi_name')) ?: 'Pemerintah Kabupaten Buleleng',
            'site_description' => trim((string) $this->request->getPost('site_description')),
            'contact_address'  => trim((string) $this->request->getPost('contact_address')),
            'contact_phone'    => trim((string) $this->request->getPost('contact_phone')),
            'contact_email'    => trim((string) $this->request->getPost('contact_email')),
            'social_instagram' => trim((string) $this->request->getPost('social_instagram')),
            'social_facebook'  => trim((string) $this->request->getPost('social_facebook')),
            'social_youtube'   => trim((string) $this->request->getPost('social_youtube')),
            'social_website'   => trim((string) $this->request->getPost('social_website')),
            'login_mode'       => $loginMode,
            'password_mode'    => $passwordMode,
        ];

        if ($defaultPassword !== '') {
            $settingsToSave['default_password'] = $defaultPassword;
        }

        // Upload Logo Website (jika ada file baru)
        $logoFile = $this->request->getFile('site_logo_file');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $ext = strtolower((string) $logoFile->getClientExtension());
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'], true)) {
                return redirect()->back()->withInput()->with('error', 'Format logo tidak didukung. Gunakan PNG, JPG, SVG, atau WEBP.');
            }
            $uploadDir = FCPATH . 'uploads/branding/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newLogoName = 'logo_' . time() . '.' . $ext;
            $logoFile->move($uploadDir, $newLogoName);
            $settingsToSave['site_logo'] = 'uploads/branding/' . $newLogoName;
        }

        $oldSettings = $this->settingModel->getAllMap();
        $this->settingModel->saveSettings($settingsToSave);

        log_activity('Settings', 'UPDATE', 'Super Admin memperbarui Pengaturan Website & Branding', $oldSettings, $settingsToSave);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Pengaturan Website berhasil disimpan dan diterapkan.',
                'settings' => $this->settingModel->getAllMap(true),
            ]);
        }

        return redirect()->to('/settings')->with('success', 'Pengaturan Website berhasil disimpan dan diterapkan.');
    }
}
