<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Veuillez vous connecter pour effectuer un paiement', 'error');
}

// Check if location ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirectWithMessage('reservations.php', 'Identifiant de location invalide', 'error');
}

$locationId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

// Get location and reservation details
$query = "SELECT l.*, r.id_client, r.date_debut, r.date_fin, r.id_voiture, v.marque, v.modele, v.image, v.prix_par_jour 
          FROM LOCATION l 
          JOIN RESERVATION r ON l.id_reservation = r.id_reservation 
          JOIN VOITURE v ON r.id_voiture = v.id_voiture 
          WHERE l.id_location = ? AND r.id_client = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $locationId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    redirectWithMessage('reservations.php', 'Location non trouvée ou non autorisée', 'error');
}

$location = mysqli_fetch_assoc($result);

// Check if location is already paid
if ($location['ETAT_PAIEMENT']) {
    redirectWithMessage('reservations.php', 'Cette location a déjà été payée', 'info');
}

$errors = [];

// Calculate total price
$start = new DateTime($location['date_debut']);
$end = new DateTime($location['date_fin']);
$interval = $start->diff($end);
$days = $interval->days + 1;
$totalPrice = $location['prix_par_jour'] * $days;

// Process payment form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $modePaiement = sanitize($_POST["mode_paiement"]);
    
    // Validate inputs
    if (empty($modePaiement)) {
        $errors[] = "Mode de paiement est requis";
    }
    
    // If no errors, process payment
    if (empty($errors)) {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update location status
            $query = "UPDATE LOCATION SET ETAT_PAIEMENT = 1 WHERE id_location = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $locationId);
            mysqli_stmt_execute($stmt);
            
            // Create payment record
            $today = date('Y-m-d');
            $query = "INSERT INTO PAIEMENT (id_location, date_paiement, montant, mode_paiement) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "isds", $locationId, $today, $totalPrice, $modePaiement);
            mysqli_stmt_execute($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            redirectWithMessage('reservations.php', 'Paiement effectué avec succès', 'success');
        } catch (Exception $e) {
            // Rollback transaction
            mysqli_rollback($conn);
            $errors[] = "Erreur lors du paiement: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - AutoDrive</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="payment-section">
        <div class="container">
            <div class="payment-content">
                <div class="section-header">
                    <h2>Paiement de votre location</h2>
                    <p>Veuillez compléter les informations de paiement ci-dessous</p>
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
                
                <div class="payment-details">
                    <div class="reservation-summary">
                        <h3>Résumé de la réservation</h3>
                        <div class="car-details">
                            <div class="car-image">
                                <?php
                                $image = $location['image'] ? $location['image'] : 'https://images.pexels.com/photos/170811/pexels-photo-170811.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1';
                                ?>
                                <img src="<?php echo $image; ?>" alt="<?php echo $location['marque'] . ' ' . $location['modele']; ?>">
                            </div>
                            <div class="car-info">
                                <h4><?php echo $location['marque'] . ' ' . $location['modele']; ?></h4>
                                <div class="reservation-dates">
                                    <p><i class="fas fa-calendar-alt"></i> <?php echo formatDate($location['date_debut']); ?> - <?php echo formatDate($location['date_fin']); ?></p>
                                    <p><i class="fas fa-clock"></i> <?php echo $days; ?> jour(s)</p>
                                </div>
                            </div>
                        </div>
                        <div class="price-details">
                            <div class="price-row">
                                <span>Prix par jour:</span>
                                <span><?php echo $location['prix_par_jour']; ?> €</span>
                            </div>
                            <div class="price-row">
                                <span>Nombre de jours:</span>
                                <span><?php echo $days; ?></span>
                            </div>
                            <div class="price-row total">
                                <span>Total à payer:</span>
                                <span><?php echo $totalPrice; ?> €</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-form">
                        <h3>Méthode de paiement</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $locationId; ?>" method="POST">
                            <div class="form-group payment-methods">
                                <div class="payment-method">
                                    <input type="radio" id="espece" name="mode_paiement" value="espèce" required>
                                    <label for="espece">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Espèces</span>
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="cheque" name="mode_paiement" value="par chèque">
                                    <label for="cheque">
                                        <i class="fas fa-money-check"></i>
                                        <span>Chèque</span>
                                    </label>
                                </div>
                                <div class="payment-method">
                                    <input type="radio" id="virement" name="mode_paiement" value="virement">
                                    <label for="virement">
                                        <i class="fas fa-university"></i>
                                        <span>Virement</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-note">
                                <p>Note: Pour cette démo, aucune information de paiement réelle n'est requise.</p>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary btn-block">Payer maintenant</button>
                                <a href="reservations.php" class="btn btn-outline btn-block">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>