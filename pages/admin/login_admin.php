<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/login_admin.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="container">
        <form class="login_container" action="../../action/act_login_admin.php" method="post">
            <div class="login_title">
                <img src="../../assets/images/lg.png" alt="Logo" class="logo">
                <img src="../../assets/images/hll.png" alt="Logo" class="logo">
                <img src="../../assets/images/lgsi.png" alt="Logo" class="logo">
            </div>
            <div class="title">
                <a href="../../index.php" class="title-link">
                    <h4>SISTEM INFORMASI PEMETAAN SERTIFIKASI HALAL</h4>
                </a>
            </div>
            <div class="input_wrapper">
                <input type="text" id="email" class="input_field" name="email" required>
                <label for="email" class="label">Email</label>
                <i class="fas fa-user icon"></i>
            </div>
            <div class="input_wrapper">
                <input type="password" id="password" class="input_field" name="password" required>
                <label for="password" class="label">Password</label>
                <i class="fas fa-lock icon"></i>
            </div>
            <div class="input_wrapper">
                <input type="submit" class="input-submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
  
