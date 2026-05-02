<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi | InterviewGuard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <style>
        :root {
            --green: #1f8f5e;
            --green-soft: #38b779;
            --green-deep: #176f49;
            --mint: #dff3e7;
            --ink: #1f2d26;
            --muted: #6d8478;
            --bg: #f3faf6;
            --line: #dceee4;
            --white: #fff;
            --shadow: 0 8px 22px rgba(25, 135, 84, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--ink);
            font-family: Inter, Verdana, Geneva, Tahoma, sans-serif;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(360px, 500px);
        }

        .brand-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(31, 143, 94, 0.95), rgba(56, 183, 121, 0.88)),
                linear-gradient(160deg, #196b47, #23935f);
            color: #fff;
        }

        .visual {
            width: min(560px, 100%);
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 18px;
            font-weight: 800;
        }

        .brand-mark span {
            width: 38px;
            height: 38px;
            display: inline-grid;
            place-items: center;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.24);
        }

        h1 {
            margin: 0;
            font-size: clamp(38px, 5vw, 66px);
            line-height: 1;
        }

        .register-panel {
            display: flex;
            align-items: center;
            padding: 44px;
            background: var(--white);
            overflow-y: auto;
        }

        .register-card {
            width: 100%;
        }

        .register-card h2 {
            margin: 0;
            font-size: 28px;
        }

        .subtitle {
            margin: 10px 0 28px;
            color: var(--muted);
            line-height: 1.6;
        }

        .alert {
            margin-bottom: 20px;
            padding: 13px 15px;
            border-radius: 8px;
            font-size: 14px;
        }

        .alert-error {
            color: #9b2335;
            background: #ffe3e9;
            border: 1px solid #ffc4d0;
        }

        .alert-success {
            color: #1f8f5e;
            background: #e7f5ee;
            border: 1px solid #c9e7d8;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #4f5560;
            font-size: 13px;
            font-weight: 700;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px 14px;
            color: var(--ink);
            font: inherit;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(31, 143, 94, 0.14);
        }

        .field {
            margin-bottom: 16px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .submit {
            width: 100%;
            height: 50px;
            margin-top: 10px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            color: #fff;
            font: inherit;
            font-weight: 800;
            background: linear-gradient(90deg, var(--green), var(--green-soft));
            box-shadow: 0 8px 18px rgba(31, 143, 94, 0.28);
        }

        .footer-links {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: var(--muted);
        }

        .footer-links a {
            color: var(--green);
            text-decoration: none;
            font-weight: 700;
        }

        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .role-option {
            flex: 1;
            padding: 10px;
            text-align: center;
            border: 1px solid var(--line);
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .role-option.active {
            background: var(--mint);
            border-color: var(--green);
            color: var(--green-deep);
        }

        #extra-fields-pegawai {
            display: none;
        }
    </style>
</head>

<body>
    <main class="page">
        <section class="brand-panel">
            <div class="visual">
                <div class="brand-mark"><span>IG</span> InterviewGuard</div>
                <h1>Bergabung dengan Kami</h1>
                <p>Daftarkan diri Anda sebagai HRD atau Pegawai untuk mulai menggunakan platform InterviewGuard.</p>
            </div>
        </section>

        <section class="register-panel">
            <div class="register-card">
                <h2>Registrasi</h2>
                <p class="subtitle">Silakan isi data diri Anda untuk membuat akun baru.</p>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <form action="<?= site_url('register') ?>" method="post">
                    <div class="field">
                        <label>Daftar Sebagai</label>
                        <div class="role-selector">
                            <div class="role-option active" onclick="setRole('pegawai')">Pegawai</div>
                            <div class="role-option" onclick="setRole('hrd')">HRD</div>
                        </div>
                        <input type="hidden" name="role" id="role-input" value="pegawai">
                    </div>

                    <div class="field">
                        <label for="nama">Nama Lengkap</label>
                        <input id="nama" name="nama" type="text" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="row">
                        <div class="field">
                            <label for="username">Username</label>
                            <input id="username" name="username" type="text" placeholder="Username" required>
                        </div>
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" placeholder="Password" required>
                        </div>
                    </div>

                    <div id="extra-fields-pegawai" style="display: block;">
                        <div class="field">
                            <label for="posisi">Posisi</label>
                            <input id="posisi" name="posisi" type="text" placeholder="Contoh: Staff Administrasi">
                        </div>
                        <div class="field">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="2" placeholder="Masukkan alamat lengkap"></textarea>
                        </div>
                    </div>

                    <button class="submit" type="submit">Daftar Sekarang</button>
                </form>

                <div class="footer-links">
                    Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk di sini</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        function setRole(role) {
            document.getElementById('role-input').value = role;
            const options = document.querySelectorAll('.role-option');
            options.forEach(opt => opt.classList.remove('active'));
            
            const selectedOpt = Array.from(options).find(opt => opt.textContent.toLowerCase() === role);
            if (selectedOpt) selectedOpt.classList.add('active');

            const extraFields = document.getElementById('extra-fields-pegawai');
            if (role === 'pegawai') {
                extraFields.style.display = 'block';
            } else {
                extraFields.style.display = 'none';
            }
        }
    </script>
</body>

</html>
