<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>
<?php if($this->session->flashdata('error')): ?>
    <p style="color:red;"><?= $this->session->flashdata('error') ?></p>
<?php endif; ?>
<form action="<?= base_url('auth/login_action') ?>" method="post">
    <label>Employee ID:</label><br>
    <input type="text" name="employee_id" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
<p>New user? <a href="<?= base_url('auth/register') ?>">Register here</a></p>
</body>
</html>
