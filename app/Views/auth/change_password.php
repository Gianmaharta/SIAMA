<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="max-width: 500px; margin: 0 auto; padding-top: 50px;">
    <h2>Ganti Password Default</h2>
    <p>Demi keamanan, Anda diwajibkan untuk mengganti password default yang diberikan oleh administrator sebelum dapat mengakses sistem.</p>
    
    <?php if (session()->getFlashdata('error')) : ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('/change-password/process') ?>" method="post">
        <div style="margin-bottom: 15px;">
            <label for="new_password">Password Baru</label><br>
            <input type="password" name="new_password" id="new_password" required minlength="8" style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="confirm_password">Konfirmasi Password Baru</label><br>
            <input type="password" name="confirm_password" id="confirm_password" required minlength="8" style="width: 100%; padding: 8px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <input type="checkbox" id="show_password" onclick="togglePassword()">
            <label for="show_password">Tampilkan Password</label>
        </div>

        <button type="submit" style="padding: 10px 20px;">Ganti Password</button>
    </form>
    
    <div style="margin-top: 20px;">
        <a href="<?= base_url('/logout') ?>">Atau Logout</a>
    </div>
</div>

<script>
    function togglePassword() {
        var pw1 = document.getElementById("new_password");
        var pw2 = document.getElementById("confirm_password");
        if (pw1.type === "password") {
            pw1.type = "text";
            pw2.type = "text";
        } else {
            pw1.type = "password";
            pw2.type = "password";
        }
    }
</script>
<?= $this->endSection() ?>
