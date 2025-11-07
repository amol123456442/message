<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
<form action="<?= base_url('auth/register_action') ?>" method="post">
    <label>Full Name:</label><br>
    <input type="text" name="full_name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Employee ID:</label><br>
    <input type="text" name="employee_id" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>
<p>Already registered? <a href="<?= base_url('auth/login') ?>">Login here</a></p>
</body>
</html>
