<!DOCTYPE html>
<html lang="id" style="scroll-behavior: smooth;">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'InterviewGuard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --green-1: #1f8f5e;
            --green-2: #2ea56f;
            --green-3: #64c79a;
            --bg: #f3faf6;
            --panel: #ffffff;
            --line: #dceee4;
            --ink: #24332b;
            --muted: #6d8478;
            --soft: #f6fbf8;
            --danger: #c64747;
            --success: #1e7e54;
            --shadow: 0 10px 28px rgba(25, 135, 84, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            height: 100%;
            overflow: hidden;
            background: var(--bg);
            color: var(--ink);
            font-family: Inter, system-ui, -apple-system, sans-serif;
        }

        .app-shell {
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            height: 100vh;
            overflow: hidden;
        }

        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 32px;
            scroll-behavior: smooth;
        }

        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .card, .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 24px;
        }

        .btn {
            border: 0;
            min-height: 42px;
            border-radius: 10px;
            padding: 0 20px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(90deg, var(--green-1), var(--green-2));
            box-shadow: 0 8px 20px rgba(31, 143, 94, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(31, 143, 94, 0.3);
        }

        .btn-secondary {
            color: var(--muted);
            background: var(--soft);
            border: 1px solid var(--line);
        }

        .btn-secondary:hover {
            background: #fff;
            color: var(--green-1);
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            border-radius: 10px;
            padding: 12px 14px;
            font: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--green-2);
            box-shadow: 0 0 0 4px rgba(46, 165, 111, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 860px) {
            .app-shell { flex-direction: column; overflow-y: auto; height: auto; }
            .main-container { height: auto; overflow: visible; }
            .content-area { overflow: visible; padding: 20px; }
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <div class="app-shell">
        <?= $this->include('partial/sidebar') ?>

        <div class="main-container">
            <?= $this->include('partial/topbar') ?>

            <main class="content-area">
                <div class="content-wrapper">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>

            <?= $this->include('partial/footer') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
