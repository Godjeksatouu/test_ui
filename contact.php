<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = sanitize($_POST['nom']);
    $email = sanitize($_POST['email']);
    $sujet = sanitize($_POST['sujet']);
    $message = sanitize($_POST['message']);
    
    // Simple validation
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $error = "Tous les champs sont obligatoires";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide";
    } else {
        // In a real application, you would send an email here
        $success = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - AutoDrive</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Contactez-nous</h1>
            <p>Une question ? N'hésitez pas à nous contacter</p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h2>Nos Coordonnées</h2>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Adresse</h3>
                            <p>123 Rue de la Location<br>75000 Paris, France</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Téléphone</h3>
                            <p>+33 1 23 45 67 89</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>contact@autodrive.com</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Horaires d'ouverture</h3>
                            <p>Lundi - Vendredi: 9h - 18h<br>Samedi: 9h - 12h<br>Dimanche: Fermé</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h2>Envoyez-nous un message</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label for="nom">Nom complet*</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sujet">Sujet*</label>
                            <input type="text" id="sujet" name="sujet" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message*</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Envoyer le message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>