<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Alih Media (SIAMA)</title>
</head>
<body>
    <h2>Login SIAMA</h2>

    <?php if (session()->getFlashdata('error')) : ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('/login/process') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div style="margin-bottom: 10px;">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
</body>
</html>
