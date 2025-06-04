<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$email = $password = "";
$errors = [];

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST["email"]);
    $password = $_POST["password"];
    
    // Validate inputs
    if (empty($email)) {
        $errors[] = "Email est requis";
    }
    
    if (empty($password)) {
        $errors[] = "Mot de passe est requis";
    }
    
    // If no errors, check credentials
    if (empty($errors)) {
        // Check for admin
        $query = "SELECT * FROM ADMIN WHERE nom_utilisateur = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($admin = mysqli_fetch_assoc($result)) {
            // For simplicity, we're using plain text password. In a real application, use password_hash/password_verify
            if ($password === $admin['mot_de_passe']) {
                $_SESSION['admin_id'] = $admin['id_admin'];
                
                // Redirect to admin dashboard
                header("Location: admin/dashboard.php");
                exit();
            }
        }
        
        // Check for regular user
        $query = "SELECT * FROM CLIENT WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Verify password using password_verify
            if (isset($user['mot_de_passe']) && password_verify($password, $user['mot_de_passe'])) {
                $_SESSION['user_id'] = $user['id_client'];
                
                // Redirect to requested page or homepage
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header("Location: $redirect");
                exit();
            }
        }
        
        $errors[] = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - AutoDrive</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="auth-section">
        <div class="container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Connexion</h1>
                    <p>Connectez-vous pour réserver une voiture</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required>
                            <i class="fas fa-eye toggle-password" data-target="password"></i>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                    </div>
                    
                    <div class="auth-links">
                        <p>Vous n'avez pas de compte ? <a href="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">Inscrivez-vous</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePasswordButtons = document.querySelectorAll('.toggle-password');
        
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    });
    </script>
</body>
</html>