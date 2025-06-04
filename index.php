<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoDrive - Location de Voitures</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Trouvez la voiture parfaite pour vos besoins</h1>
            <p>Des véhicules modernes, confortables et économiques à votre disposition</p>
            <div class="search-box">
                <form action="cars.php" method="GET">
                    <div class="form-group">
                        <label for="marque">Marque</label>
                        <select name="marque" id="marque">
                            <option value="">Toutes les marques</option>
                            <?php
                            $query = "SELECT DISTINCT marque FROM VOITURE ORDER BY marque";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="'.$row['marque'].'">'.$row['marque'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type">
                            <option value="">Tous les types</option>
                            <option value="diesel">Diesel</option>
                            <option value="essence">Essence</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Cars -->
    <section class="featured-cars">
        <div class="container">
            <div class="section-header">
                <h2>Nos véhicules populaires</h2>
                <p>Découvrez notre sélection de voitures les plus demandées</p>
                <a href="cars.php" class="view-all">Voir tous les véhicules <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="cars-grid">
                <?php
                $query = "SELECT * FROM VOITURE WHERE statut = 'disponible' LIMIT 6";
                $result = mysqli_query($conn, $query);
                while ($car = mysqli_fetch_assoc($result)) {
                    include 'includes/car-card.php';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services">
        <div class="container">
            <div class="section-header">
                <h2>Nos Services</h2>
                <p>Des services de qualité pour une expérience de location sans soucis</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Vaste Sélection</h3>
                    <p>Choisissez parmi une large gamme de véhicules pour tous les besoins et budgets.</p>
                </div>
                <div class="service-card">
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3>Tarifs Transparents</h3>
                    <p>Pas de frais cachés, vous ne payez que ce que vous voyez.</p>
                </div>
                <div class="service-card">
                    <div class="icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Assistance 24/7</h3>
                    <p>Notre équipe est disponible à tout moment pour vous aider.</p>
                </div>
                <div class="service-card">
                    <div class="icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Assurance Complète</h3>
                    <p>Roulez en toute tranquillité avec notre assurance tous risques.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners">
        <div class="container">
            <div class="section-header">
                <h2>Nos Partenaires</h2>
                <p>Nous collaborons avec les meilleures marques pour vous offrir des véhicules de qualité</p>
            </div>
            <div class="partners-logos">
                <div class="partner"><img src="https://images.pexels.com/photos/3944374/pexels-photo-3944374.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Renault"></div>
                <div class="partner"><img src="https://images.pexels.com/photos/2127733/pexels-photo-2127733.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Peugeot"></div>
                <div class="partner"><img src="https://images.pexels.com/photos/4514917/pexels-photo-4514917.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Citroen"></div>
                <div class="partner"><img src="https://images.pexels.com/photos/1035108/pexels-photo-1035108.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Volkswagen"></div>
                <div class="partner"><img src="https://images.pexels.com/photos/248687/pexels-photo-248687.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Ford"></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Prêt à prendre la route ?</h2>
                <p>Réservez dès maintenant et profitez de nos offres spéciales</p>
                <a href="cars.php" class="btn btn-light">Réserver une voiture</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>