<?php
require 'includes/db.php';
require 'includes/auth.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass  = md5($_POST['password']);

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->execute([$email,$pass]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        loginUser($user);
        header("Location: " . redirectByRole($user['role']));
        exit;
    } else {
        $error = "Invalid login!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<!-- ✅ GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

<!-- ✅ YOUR CSS -->
<link rel="stylesheet" href="assets/style.css">
s
</head>
<body>

<!-- 🔥 BACKGROUND EFFECT -->
<div class="bg-glow1"></div>
<div class="bg-glow2"></div>

<!-- 🔥 LOGIN CARD -->
<div class="card">
<h2>Library Noise Monitoring</h2>

<?php if($error) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>

</div>

</body>
</html>