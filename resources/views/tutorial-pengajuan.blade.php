<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Pengajuan RAPBS - SIRAPBS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            line-height: 1.7;
        }
        header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
        }
        header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.4rem; }
        header p  { font-size: 1rem; opacity: 0.9; }
        .container { max-width: 820px; margin: 2rem auto; padding: 0 1.25rem 3rem; }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: #1e40af; text-decoration: none; font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .back-link:hover { text-decoration: underline; }
        .card {
            background: white; border-radius: 12px;
            box-shadow: 0 1px 8px rgba(0,0,0,.07);
            padding: 1.75rem; margin-bottom: 1.5rem;
        }
        .card h2 {
            font-size: 1.15rem; font-weight: 700; color: #1e40af;
            margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .step-list { list-style: none; counter-reset: step; }
        .step-list li {
            counter-increment: step;
            display: flex; gap: 1rem;
            margin-bottom: 1.1rem;
            padding-bottom: 1.1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .step-list li:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .step-num {
            flex-shrink: 0;
            width: 2rem; height: 2rem;
            background: #1e40af; color: white;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem;
        }
        .step-content strong { display: block; margin-bottom: 0.25rem; color: #0f172a; }
        .step-content p, .step-content ul { font-size: 0.93rem; color: #475569; }
        .step-content ul { padding-left: 1.2rem; margin-top: 0.3rem; }
        .step-content ul li { list-style: disc; margin-bottom: 0.2rem; }
        .badge {
            display: inline-block; padding: 0.15rem 0.55rem;
            border-radius: 99px; font-size: 0.78rem; font-weight: 600;
        }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .info-box {
            background: #eff6ff; border-left: 4px solid #3b82f6;
            border-radius: 6px; padding: 0.85rem 1rem;
            font-size: 0.9rem; color: #1e40af; margin-top: 0.75rem;
        }
        .warning-box {
            background: #fffbeb; border-left: 4px solid #f59e0b;
            border-radius: 6px; padding: 0.85rem 1rem;
            font-size: 0.9rem; color: #92400e; margin-top: 0.75rem;
        }
        footer {
            text-align: center; font-size: 0.85rem;
            color: #94a3b8; margin-top: 2rem;
        }
        @media (max-width: 600px) {
            header h1 { font-size: 1.35rem; }
            .card { padding: 1.25rem; }
        }
    </style>
</head>
<body>

<header>
    <h1>📋 Tutorial Pengajuan RAPBS</h1>
    <p>Panduan langkah demi langkah untuk pegawai</p>
</header>

<div class="container">
    <a href="{{ url('/admin/login') }}" class="back-link">← Kembali ke Halaman Login</a>

    {{-- Langkah Utama --}}
    <div class="card">
        <h2>🚀 Langkah-langkah Pengajuan RAPBS</h2>
        <ol class="step-list">
            <li>
                <div class="step-num">1</div>
                <div class="step-content">
                    <strong>Login ke Sistem</strong>
                    <p>Masukkan <strong>NIP</strong> dan <strong>Password</strong> Anda pada halaman login.
                       Hubungi administrator jika belum memiliki akun.</p>
                </div>
            </li>
            <li>
                <div class="step-num">2</div>
                <div class="step-content">
                    <strong>Buka Menu Pengajuan RAPBS</strong>
                    <p>Setelah login, klik menu <strong>Pengajuan RAPBS</strong> pada navigasi sebelah kiri.</p>
                </div>
            </li>
            <li>
                <div class="step-num">3</div>
                <div class="step-content">
                    <strong>Buat Pengajuan Baru</strong>
                    <p>Klik tombol <strong>"+ Baru"</strong> di pojok kanan atas, lalu pilih:</p>
                    <ul>
                        <li><strong>Tahun Ajaran</strong> — pilih tahun ajaran yang sedang aktif</li>
                    </ul>
                    <p>Data pegawai (nama, unit kerja) akan terisi otomatis. Klik <strong>Simpan</strong>.</p>
                </div>
            </li>
            <li>
                <div class="step-num">4</div>
                <div class="step-content">
                    <strong>Tambahkan Item Kebutuhan</strong>
                    <p>Setelah pengajuan tersimpan, tambahkan item di bagian <strong>"Item Kebutuhan"</strong>:</p>
                    <ul>
                        <li><strong>Kategori Belanja</strong> — pilih kategori yang sesuai</li>
                        <li><strong>Nama Item</strong> — contoh: Laptop, Printer, Buku Referensi</li>
                        <li><strong>Spesifikasi</strong> — isi spesifikasi teknis bila ada</li>
                        <li><strong>Satuan</strong> — Unit / Buah / Set / dll</li>
                        <li><strong>Volume</strong> — jumlah yang dibutuhkan</li>
                        <li><strong>Harga Satuan</strong> — harga per satuan (Rp)</li>
                        <li><strong>Total Harga</strong> — dihitung otomatis</li>
                    </ul>
                    <p>Ulangi langkah ini untuk setiap item yang dibutuhkan.</p>
                </div>
            </li>
            <li>
                <div class="step-num">5</div>
                <div class="step-content">
                    <strong>Kirim Pengajuan ke Admin</strong>
                    <p>Setelah semua item ditambahkan, klik tombol <strong>"Ajukan"</strong> di bagian atas halaman.
                       Status pengajuan akan berubah menjadi <span class="badge badge-blue">Diajukan</span>.</p>
                    <div class="warning-box">⚠️ Pastikan semua item sudah lengkap sebelum mengajukan. Item tidak dapat ditambah/diedit setelah diajukan.</div>
                </div>
            </li>
            <li>
                <div class="step-num">6</div>
                <div class="step-content">
                    <strong>Pantau Status Pengajuan</strong>
                    <p>Status pengajuan akan diperbarui oleh admin. Berikut arti setiap status:</p>
                    <ul>
                        <li><span class="badge badge-gray">Draft</span> — Pengajuan belum dikirim</li>
                        <li><span class="badge badge-blue">Diajukan</span> — Menunggu review admin</li>
                        <li><span class="badge badge-yellow">Perlu Revisi</span> — Admin meminta perbaikan</li>
                        <li><span class="badge badge-green">Disetujui</span> — Pengajuan diterima</li>
                        <li><span class="badge badge-red">Ditolak</span> — Pengajuan ditolak</li>
                    </ul>
                </div>
            </li>
            <li>
                <div class="step-num">7</div>
                <div class="step-content">
                    <strong>Revisi Jika Diminta</strong>
                    <p>Jika status menjadi <span class="badge badge-yellow">Perlu Revisi</span>, buka pengajuan dan baca catatan dari admin.
                       Lakukan perbaikan pada item yang diperlukan, lalu klik <strong>"Ajukan Ulang"</strong>.</p>
                </div>
            </li>
        </ol>
    </div>

    {{-- Tips --}}
    <div class="card">
        <h2>💡 Tips Penting</h2>
        <ul style="padding-left:1.2rem; color:#475569; font-size:0.93rem;">
            <li style="margin-bottom:.6rem">Satu pegawai hanya dapat memiliki <strong>satu pengajuan per tahun ajaran</strong>.</li>
            <li style="margin-bottom:.6rem">Isi spesifikasi item sejelas mungkin agar admin dapat memverifikasi dengan tepat.</li>
            <li style="margin-bottom:.6rem">Pastikan harga satuan sesuai dengan harga pasar terkini.</li>
            <li style="margin-bottom:.6rem">Jika ada kendala, hubungi administrator sistem.</li>
        </ul>
        <div class="info-box">
            ℹ️ Untuk mengubah foto profil atau password, klik nama Anda di pojok kanan atas → <strong>Profil Saya</strong>.
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} SIRAPBS — Sistem Informasi RAPBS
    </footer>
</div>

</body>
</html>
