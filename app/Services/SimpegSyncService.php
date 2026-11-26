<?php

namespace App\Services;

use App\Models\BidangModel;
use App\Models\OpdModel;
use Config\Services;
use RuntimeException;

/**
 * Service untuk menarik dan menyinkronkan Data Master (OPD, Bidang, Pegawai)
 * dari REST API SIMPEG / SSO Pemkab Buleleng (`SIMPEG_API_BASE_URL`).
 */
class SimpegSyncService
{
    protected string $baseUrl;
    protected string $token;
    protected OpdModel $opdModel;
    protected BidangModel $bidangModel;

    /** @var callable|null Optional mock fetcher untuk pengujian otomatis */
    protected $customFetcher = null;

    public function __construct(?callable $customFetcher = null)
    {
        $this->baseUrl = rtrim((string) env('SIMPEG_API_BASE_URL', 'https://sso.bllkom.site/api/v1'), '/');
        $this->token   = trim((string) env('SIMPEG_API_TOKEN', ''));
        $this->opdModel      = new OpdModel();
        $this->bidangModel   = new BidangModel();
        $this->customFetcher = $customFetcher;
    }

    /**
     * Memastikan konfigurasi Base URL dan Token API sudah terisi.
     */
    protected function ensureConfigured(): void
    {
        if ($this->baseUrl === '' || $this->token === '') {
            throw new RuntimeException('Konfigurasi SIMPEG_API_BASE_URL atau SIMPEG_API_TOKEN pada .env belum lengkap.');
        }
    }

    /**
     * Menentukan opsi verifikasi SSL (`verify`) yang aman dan portabel di Laragon/Windows maupun Linux.
     *
     * @return bool|string Path file CA bundle (.pem) atau boolean verify
     */
    protected function resolveSslVerifyOption()
    {
        $verifyEnv = env('SIMPEG_API_VERIFY_SSL');
        if ($verifyEnv !== null && in_array(strtolower(trim((string) $verifyEnv)), ['0', 'false', 'off', 'no'], true)) {
            return false;
        }

        $customCa = trim((string) env('SIMPEG_API_CA_BUNDLE', ''));
        if ($customCa !== '' && is_file($customCa)) {
            return $customCa;
        }

        $iniCa = trim((string) ini_get('curl.cainfo')) ?: trim((string) ini_get('openssl.cafile'));
        if ($iniCa !== '' && is_file($iniCa)) {
            return true;
        }

        $fallbackBundles = [
            APPPATH . 'Config' . DIRECTORY_SEPARATOR . 'cacert.pem',
            'C:\\laragon\\etc\\ssl\\cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
        ];
        foreach ($fallbackBundles as $bundle) {
            if (is_file($bundle)) {
                return $bundle;
            }
        }

        return true;
    }

    /**
     * Melakukan HTTP GET ke endpoint API eksternal dengan autentikasi Bearer Token.
     */
    public function requestGet(string $path, array $query = []): array
    {
        if ($this->customFetcher !== null) {
            return (array) call_user_func($this->customFetcher, $path, $query);
        }

        $this->ensureConfigured();

        $url = $this->baseUrl . '/' . ltrim($path, '/');
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $client = Services::curlrequest([
            'timeout'     => 20,
            'http_errors' => false,
            'verify'      => $this->resolveSslVerifyOption(),
        ]);

        $response = $client->get($url, [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        $bodyRaw    = (string) $response->getBody();
        $decoded    = json_decode($bodyRaw, true);

        if ($statusCode < 200 || $statusCode >= 300 || !is_array($decoded)) {
            $errMsg = is_array($decoded) && !empty($decoded['message'])
                ? $decoded['message']
                : "HTTP {$statusCode} saat mengakses {$path}";
            throw new RuntimeException('Gagal mengakses API SIMPEG/SSO: ' . $errMsg);
        }

        if (isset($decoded['success']) && $decoded['success'] === false) {
            throw new RuntimeException('API SIMPEG/SSO mengembalikan error: ' . ($decoded['message'] ?? 'Unknown error'));
        }

        return $decoded;
    }

    /**
     * Mengambil seluruh daftar OPD Utama (`GET /unit-kerja/utama`) lintas halaman.
     */
    public function fetchAllOpd(): array
    {
        $all  = [];
        $page = 1;

        do {
            $resp = $this->requestGet('/unit-kerja/utama', [
                'page'     => $page,
                'per_page' => 50,
            ]);

            $rows = $resp['data'] ?? [];
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $all[] = $row;
                }
            }

            $meta     = $resp['meta'] ?? [];
            $lastPage = (int) ($meta['last_page'] ?? 1);
            $page++;
        } while ($page <= $lastPage);

        return $all;
    }

    /**
     * Mengambil daftar Bidang aktif suatu OPD (`GET /unit-kerja/{kode}/bidang`).
     */
    public function fetchBidangByOpd(string $kodeOpd): array
    {
        $resp = $this->requestGet('/unit-kerja/' . rawurlencode($kodeOpd) . '/bidang');
        $rows = $resp['data'] ?? [];
        return is_array($rows) ? $rows : [];
    }

    /**
     * Mengambil daftar Pegawai (`GET /pegawai`) dengan filter opsional.
     */
    public function fetchPegawai(array $filters = []): array
    {
        return $this->requestGet('/pegawai', $filters);
    }

    /**
     * Menyinkronkan seluruh Master OPD dari API (`/unit-kerja/utama`) ke tabel `opd`.
     * Menjaga relasi UUID `id_opd` milik data eksisting (`OPD-001` dst.) agar tidak rusak.
     */
    public function syncOpd(): array
    {
        $apiRows  = $this->fetchAllOpd();
        $inserted = 0;
        $updated  = 0;

        // Peta alias untuk mencocokkan nama OPD lama (placeholder OPD-00X) ke OPD resmi API
        $legacyAliases = [
            'dinas komunikasi dan informatika'            => '75010309',
            'dinas pendidikan'                            => '75010320',
            'dinas pertanian dan kehutanan'               => '75010325',
            'dinas arsip dan perpustakaan daerah'         => '75010301',
            'dinas pariwisata'                            => '75010304',
            'dinas kesehatan'                             => '75010307',
        ];

        $existingOpds = $this->opdModel->findAll();
        $byKode       = [];
        $byLegacySlug = [];
        foreach ($existingOpds as $opd) {
            $kode = trim((string) ($opd['kode_opd'] ?? ''));
            if ($kode !== '') {
                $byKode[$kode] = $opd;
            }
            if (preg_match('/^OPD-\d+$/i', $kode)) {
                $slug = strtolower(trim((string) ($opd['nama_opd'] ?? '')));
                if ($slug !== '') {
                    $byLegacySlug[$slug] = $opd;
                }
            }
        }

        foreach ($apiRows as $row) {
            $kodeUkerja = trim((string) ($row['kode_ukerja'] ?? $row['kode_opd'] ?? ''));
            $namaOpd    = trim((string) ($row['ket_ukerja'] ?? $row['ket_uorg'] ?? ''));
            if ($kodeUkerja === '' || $namaOpd === '') {
                continue;
            }

            $payload = [
                'kode_opd'   => $kodeUkerja,
                'nama_opd'   => $namaOpd,
                'nama_jenis' => isset($row['nama_jenis']) ? trim((string) $row['nama_jenis']) : null,
                'alamat'     => isset($row['alamat']) && $row['alamat'] !== '' ? trim((string) $row['alamat']) : null,
                'is_active'  => ((string) ($row['is_active'] ?? '1') === '1') ? 1 : 0,
            ];

            $targetExisting = $byKode[$kodeUkerja] ?? null;

            // Jika belum ada berdasarkan kode SIMPEG, cek apakah ada OPD placeholder lama yang cocok
            if ($targetExisting === null) {
                $namaLower = strtolower($namaOpd);
                if (isset($byLegacySlug[$namaLower])) {
                    $targetExisting = $byLegacySlug[$namaLower];
                } else {
                    foreach ($legacyAliases as $aliasName => $mappedCode) {
                        if ($mappedCode === $kodeUkerja && isset($byLegacySlug[$aliasName])) {
                            $targetExisting = $byLegacySlug[$aliasName];
                            break;
                        }
                    }
                }
            }

            if ($targetExisting !== null) {
                $this->opdModel->skipValidation(true)->update($targetExisting['id_opd'], $payload);
                $byKode[$kodeUkerja] = array_merge($targetExisting, $payload);
                $updated++;
            } else {
                $payload['kuota_storage_mb'] = 1000;
                $this->opdModel->skipValidation(true)->insert($payload);
                $inserted++;
            }
        }

        return [
            'total_api' => count($apiRows),
            'inserted'  => $inserted,
            'updated'   => $updated,
        ];
    }

    /**
     * Menyinkronkan Master Bidang dari API (`/unit-kerja/{kode}/bidang`) ke tabel `bidang`.
     * Jika `$targetIdOpd` null, menyinkronkan seluruh OPD berkode SIMPEG di tabel `opd`.
     */
    public function syncBidang(?string $targetIdOpd = null): array
    {
        if ($targetIdOpd !== null && $targetIdOpd !== '') {
            $opds = $this->opdModel->where('id_opd', $targetIdOpd)->findAll();
        } else {
            $opds = $this->opdModel->findAll();
            // Jika belum ada satupun OPD dengan kode SIMPEG (angka >= 4 digit), sinkronkan OPD dulu
            $hasSimpegOpd = false;
            foreach ($opds as $o) {
                if (preg_match('/^\d{4,}$/', (string) ($o['kode_opd'] ?? ''))) {
                    $hasSimpegOpd = true;
                    break;
                }
            }
            if (!$hasSimpegOpd) {
                $this->syncOpd();
                $opds = $this->opdModel->findAll();
            }
        }

        $opdsProcessed = 0;
        $totalApi      = 0;
        $inserted      = 0;
        $updated       = 0;

        foreach ($opds as $opd) {
            $kodeOpd = trim((string) ($opd['kode_opd'] ?? ''));
            if (!preg_match('/^\d{4,}$/', $kodeOpd)) {
                continue;
            }

            $opdsProcessed++;
            $apiBidangs = $this->fetchBidangByOpd($kodeOpd);
            $totalApi  += count($apiBidangs);

            $existingRows = $this->bidangModel->where('id_opd', $opd['id_opd'])->findAll();
            $byKode = [];
            $byNama = [];
            foreach ($existingRows as $eb) {
                if (!empty($eb['kode_bidang'])) {
                    $byKode[(string) $eb['kode_bidang']] = $eb;
                }
                $byNama[strtolower(trim((string) ($eb['nama_bidang'] ?? '')))] = $eb;
            }

            foreach ($apiBidangs as $bRow) {
                $kodeBidang = trim((string) ($bRow['kode_ukerja'] ?? ''));
                $namaBidang = trim((string) ($bRow['ket_uorg'] ?? ''));
                if ($namaBidang === '') {
                    continue;
                }

                $target = ($kodeBidang !== '' && isset($byKode[$kodeBidang]))
                    ? $byKode[$kodeBidang]
                    : ($byNama[strtolower($namaBidang)] ?? null);

                $saveData = [
                    'id_opd'      => $opd['id_opd'],
                    'kode_bidang' => $kodeBidang !== '' ? $kodeBidang : null,
                    'nama_bidang' => $namaBidang,
                ];

                if ($target !== null) {
                    $this->bidangModel->skipValidation(true)->update($target['id_bidang'], $saveData);
                    $updated++;
                } else {
                    $this->bidangModel->skipValidation(true)->insert($saveData);
                    $inserted++;
                }
            }
        }

        return [
            'opds_processed' => $opdsProcessed,
            'total_api'      => $totalApi,
            'inserted'       => $inserted,
            'updated'        => $updated,
        ];
    }
}
