<?php
session_start();
include '../includes/config.php';
include '../includes/functions.php';

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Get all payments
$query = "SELECT p.*, l.id_reservation, r.date_debut, r.date_fin, c.nom, c.prénom, v.marque, v.modele
          FROM PAIEMENT p
          JOIN LOCATION l ON p.id_location = l.id_location
          JOIN RESERVATION r ON l.id_reservation = r.id_reservation
          JOIN CLIENT c ON r.id_client = c.id_client
          JOIN VOITURE v ON r.id_voiture = v.id_voiture
          ORDER BY p.date_paiement DESC";
$payments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Paiements - AutoDrive Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/admin-header.php'; ?>

    <section class="admin-dashboard">
        <div class="container">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Gestion des Paiements</h2>
                </div>
                <div class="admin-card-body">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Voiture</th>
                                    <th>Dates location</th>
                                    <th>Date paiement</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($payments) > 0): ?>
                                    <?php while ($payment = mysqli_fetch_assoc($payments)): ?>
                                        <tr>
                                            <td>#<?php echo $payment['id_paiement']; ?></td>
                                            <td><?php echo $payment['nom'] . ' ' . $payment['prénom']; ?></td>
                                            <td><?php echo $payment['marque'] . ' ' . $payment['modele']; ?></td>
                                            <td><?php echo formatDate($payment['date_debut']) . ' - ' . formatDate($payment['date_fin']); ?></td>
                                            <td><?php echo formatDate($payment['date_paiement']); ?></td>
                                            <td><?php echo $payment['montant']; ?> €</td>
                                            <td><?php echo ucfirst($payment['mode_paiement']); ?></td>
                                            <td class="actions">
                                                <a href="payment-details.php?id=<?php echo $payment['id_paiement']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun paiement trouvé</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/admin-footer.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>