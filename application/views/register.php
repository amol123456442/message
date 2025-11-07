<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI'; }
        .card { max-width: 420px; margin: 50px auto; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2 class="text-center mb-4">Register</h2>
        <form action="<?= base_url('auth/register_action') ?>" method="post">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Employee ID</label>
                <input type="text" name="employee_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Department</label>
                <select name="department" class="form-control" required>
                    <option value="">Select Department</option>
                    <option value="Developer">Developer</option>
                    <option value="Designer">Designer</option>
                    <option value="HR">HR</option>
                    <option value="Manager">Manager</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Sales">Sales</option>
                    <option value="Support">Support</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3">Already registered? <a href="<?= base_url('auth/login') ?>">Login here</a></p>
    </div>
</div>
</body>
</html>