<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | InterviewGuard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login HRD dan pegawai menggunakan username atau id akun.">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <style {csp-style-nonce}>
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
            grid-template-columns: minmax(0, 1fr) minmax(360px, 460px);
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
            max-width: 680px;
            font-size: clamp(38px, 5vw, 66px);
            line-height: 1;
            letter-spacing: 0;
        }

        .visual p {
            max-width: 580px;
            margin: 20px 0 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 17px;
            line-height: 1.7;
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 34px;
        }

        .metric {
            min-height: 112px;
            padding: 18px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .metric strong {
            display: block;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .metric span {
            color: rgba(255, 255, 255, 0.82);
            font-size: 13px;
            line-height: 1.5;
        }

        .login-panel {
            display: flex;
            align-items: center;
            padding: 44px;
            background: var(--white);
        }

        .login-card {
            width: 100%;
        }

        .login-card h2 {
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
            color: #9b2335;
            background: #ffe3e9;
            border: 1px solid #ffc4d0;
            font-size: 14px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #4f5560;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            width: 100%;
            height: 48px;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 0 14px;
            color: var(--ink);
            font: inherit;
            text-transform: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(31, 143, 94, 0.14);
        }

        .field + .field {
            margin-top: 18px;
        }

        .submit {
            width: 100%;
            height: 50px;
            margin-top: 26px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            color: #fff;
            font: inherit;
            font-weight: 800;
            background: linear-gradient(90deg, var(--green), var(--green-soft));
            box-shadow: 0 8px 18px rgba(31, 143, 94, 0.28);
        }

        .account-list {
            display: grid;
            gap: 10px;
            margin-top: 28px;
        }

        .account {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            background: #f6fbf8;
            border: 1px solid var(--line);
            font-size: 13px;
        }

        .account strong {
            display: block;
            margin-bottom: 4px;
        }

        .account span {
            color: var(--muted);
        }

        @media (max-width: 900px) {
            .page {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: 360px;
                align-items: flex-end;
            }

            .login-panel {
                padding: 28px 20px 40px;
            }
        }

        @media (max-width: 640px) {
            .brand-panel {
                padding: 28px 20px;
            }

            .metric-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <section class="brand-panel">
            <div class="visual">
                <div class="brand-mark"><span>IG</span> InterviewGuard</div>
                <h1>Portal HRD dan pegawai</h1>
                <p>Dashboard monitoring, akses tes fullscreen, dan sesi interview memakai akun berbeda berdasarkan username atau id akun.</p>
                <div class="metric-row" aria-hidden="true">
                    <div class="metric">
                        <strong>HRD</strong>
                        <span>Dashboard monitoring dan rekap pelanggaran.</span>
                    </div>
                    <div class="metric">
                        <strong>User</strong>
                        <span>Akses halaman tes dengan identitas akun.</span>
                    </div>
                    <div class="metric">
                        <strong>ID / USERNAME</strong>
                        <span>Login memakai username atau id akun utama.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <h2>Masuk</h2>
                <p class="subtitle">Gunakan username/id akun dan password yang sudah terdaftar.</p>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert" style="color: #1f8f5e; background: #e7f5ee; border-color: #c9e7d8;"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <?php if (! empty($error)) : ?>
                    <div class="alert"><?= esc($error) ?></div>
                <?php endif; ?>

                <form action="<?= esc($loginUrl) ?>" method="post">
                    <div class="field">
                        <label for="id_user">Username / ID Akun</label>
                        <input id="id_user" name="id_user" type="text" value="<?= esc($lastIdUser) ?>" autocomplete="username" autofocus required>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                    <button class="submit" type="submit">Masuk</button>
                    <div style="margin-top: 20px; text-align: center; font-size: 14px; color: var(--muted);">
                        Belum punya akun? <a href="<?= site_url('register') ?>" style="color: var(--green); text-decoration: none; font-weight: 700;">Daftar di sini</a>
                    </div>
                </form>

                <div class="account-list">
                    <div class="account">
                        <div>
                            <strong>HRD001 / HRD002</strong>
                            <span>Username: hrd001 / hrd002</span><br>
                            <span>Password: hrd123</span>
                        </div>
                        <span>HRD</span>
                    </div>
                    <div class="account">
                        <div>
                            <strong>PGW001 / PGW002</strong>
                            <span>Username: pgw001 / pgw002</span><br>
                            <span>Password: pegawai123</span>
                        </div>
                        <span>User</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
