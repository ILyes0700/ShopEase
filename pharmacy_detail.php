<?php
// Connexion à la base de données
require("connect.php");
session_start();
// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer l'ID de la pharmacie depuis l'URL
$pharmacy_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Sécuriser l'ID pour éviter les injections SQL
$pharmacy_id = $conn->real_escape_string($pharmacy_id);

// Requête SQL pour récupérer les médicaments de la pharmacie par son ID
$sql_meds = "SELECT m.id1, m.id, m.nom, m.imagee, m.disce, m.prix, m.videoe, m.qun, m.taille
            FROM med m
            JOIN phar p ON m.id = p.id
            WHERE p.id = $pharmacy_id
            ORDER BY m.nom ASC";
$result_meds = $conn->query($sql_meds);


// Requête SQL pour récupérer les détails de la pharmacie
$sql_pharmacy = "SELECT * FROM phar WHERE id = $pharmacy_id";
$result_pharmacy = $conn->query($sql_pharmacy);
$pharmacy = $result_pharmacy->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logtest.webp" type="image/png">
    <title>ShopEase</title>
    <link rel="stylesheet" href="medfind.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Include Bootstrap JS (necessary for modals to work) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/font-awesome-line-awesome/css/all.min.css">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .navbar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .navbar img:hover {
            transform: rotate(360deg);
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1, h2 {
            color: #007bff;
            text-align: center;
            text-transform: uppercase;
        }

        .card {
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            transition: color 0.3s ease;
        }

        .card-title:hover {
            color: #007bff;
        }

        .btn-primary {
            background-color: rgb(0, 75, 224);
            border-color: rgb(0, 75, 224);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: rgb(1, 63, 189);
            border-color: rgb(1, 63, 189);
            transform: scale(1.05);
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .quantity-container {
            display: inline-flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .btn-minus, .btn-plus {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #555;
            background-color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .btn-minus:hover, .btn-plus:hover {
            background-color: #007bff;
            color: #fff;
        }

        .num {
            width: 80px;
            height: 40px;
            border: none;
            text-align: center;
            font-size: 16px;
            color: #333;
            background-color: #f9f9f9;
            outline: none;
        }

        .num:focus {
            background-color: #fff;
        }

        .cart-container {
            position: relative;
            display: inline-block;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: #007bff;
            color: white;
            font-size: 14px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        /* Animation pour l'icône du panier */
        .ab {
            animation: bounce 3s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .footer-text {
            color: #b0b0b0;
            font-size: 14px;
            padding-top: 50px;
        }

        .footer-text strong {
            color: #007bff;
        }

        /* Animation de fade pour le titre */
        .animated-title {
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .video-icon {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .video-icon i {
            font-size: 25px;
            color: #007bff;
        }

        .video-icon:hover i {
            transform: scale(1.2);
            color: #0056b3;
        }
        /* Animation du modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-in-out;
}

.modal.fade.show .modal-dialog {
    transform: translate(0, 0);
}

/* Redimensionner la vidéo et ajouter un peu de marges */
.video-container {
    position: relative;
    padding-bottom: 56.25%; /* Pour garder le ratio 16:9 */
    height: 0;
    overflow: hidden;
    max-width: 100%;
}

.video-player {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

/* Modal Header */
.modal-header {
    background-color:rgb(119, 184, 253);
    color: white;
    border-bottom: 2px solid #ddd;
}

.modal-header .modal-title {
    font-size: 1.25rem;
    font-weight: bold;
}

.modal-header .btn-close {
    color: white;
    opacity: 1;
}

/* Modal Body */
.modal-body {
    padding: 15px;
    text-align: center;
}

/* Ajout d'une ombre subtile autour de la vidéo */
.video-player {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Animation d'apparition du modal */
.modal-dialog-centered {
    transform: scale(0.7);
    transition: transform 0.5s ease;
}

.modal.fade.show .modal-dialog-centered {
    transform: scale(1);
}

/* Amélioration de l'interface */
.modal-content {
    border-radius: 15px;
    overflow: hidden;
}

/* Style pour les vidéos sur mobile */
@media (max-width: 767px) {
    .video-player {
        height: auto;
    }
}
.taille-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
}

.taille-label {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.taille-select {
    width: 90%;
    max-width: 250px;
    padding: 5px;
    font-size: 16px;
    font-weight: 400;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.taille-select:hover {
    border-color: #0071dc;
    background-color: #f0f8ff;
}

.taille-select:focus {
    outline: none;
    border-color: #004ba0;
    box-shadow: 0 0 5px rgba(0, 71, 208, 0.5);
}
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="log11.php">
            <img src="logtest.webp" alt="Logo" class="im mt-0" style="width: 40px;height:40px; margin-right: 10px; border-radius:50%;">
            <h3 class="pt-2" style="color: #002060; font-size: 2rem;">ShopEase</h3>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active lie" href="painer.php" style="color: #002060; font-weight: bold;">
                        <div class="cart-container" style="position: relative; display: inline-block;">
                            <!-- Remplacer l'image par une icône Font Awesome -->
                            <i class="fas fa-shopping-cart" style="font-size: 40px; color: #002060; margin-top: 15px; margin-right: 20px;"></i>
                            
                            <?php
                            // Calculer le nombre d'articles dans le panier
                            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                $cartCount = count($_SESSION['cart']); // Nombre d'articles dans le panier
                            } else {
                                $cartCount = 0; // Si le panier est vide
                            }
                            ?>
                            
                            <?php if ($cartCount > 0): ?>
                                <!-- Badge indiquant le nombre d'articles dans le panier -->
                                <span class="cart-badge " style="position: absolute; top: 7px; right: 18px; background-color:rgb(53, 53, 53); color: white; font-size: 6px; padding: 5px 10px; border-radius: 50%;">
                                    <?php echo $cartCount; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clic2.php" style="color: #007bff; font-weight: bold;">
                        <i class="fas fa-comment-dots" style="font-size: 40px; color: #002060; margin-top: 15px;"></i> <!-- Icône de chatbot -->
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Titre de l'Entreprise -->
    <h2 class="animated-title" style="color:#002060;">
        <i class="fas fa-building" style="color: #002060;"></i> Détails de l'Entreprise: <?php echo $pharmacy['nomphar']; ?>
    </h2>

    <!-- Adresse de l'Entreprise -->
    <p style="color: rgb(89, 190, 148); font-weight: bold;">
        <i class="fas fa-map-marker-alt" style="color: rgb(89, 190, 148);"></i> 
        <strong>Adresse:</strong> <?php echo $pharmacy['address1'] . ', ' . $pharmacy['zip']; ?>
    </p>

    <!-- Titre pour Produits Disponibles -->
    <h2 class="animated-title" style="color:rgb(2, 53, 155);">
    <i class="fas fa-box" style="color: rgb(2, 53, 155);"></i> Produits Disponibles:
    </h2>

    <!-- Affichage des Produits -->
    <div class="row">
    <?php
    if ($result_meds->num_rows > 0) {
        while ($row = $result_meds->fetch_assoc()) {
            // Récupération des données du produit
            $videoe = $row['videoe']; // URL de la vidéo
            $imagee = $row['imagee']; // URL de l'image
            $taille = $row['taille']; // Taille du produit

            // Vérification si la taille est définie et la découpe en tableau
            if ($taille) {
                $taille_array = explode(',', $taille); // Si taille définie, découper
            } else {
                $taille_array = []; // Si pas de taille, tableau vide
            }

            // Affichage du produit
            echo "<div class='col-xs-12 col-sm-12 col-md-6 col-lg-3 pt-3'>";

            // Vérifier si le produit a une vidéo
            if ($videoe) {
                if($row['qun']>0){
                    echo "<div class='card'>
                        <img src='" . $imagee . "' class='card-img-top' alt='...' data-bs-toggle='modal'>
                        <div class='card-body'>
                            <h5 class='card-title'>
                                <i class='fas fa-box' style='color: #007bff;'></i>
                                <span>" . $row['nom'] . "</span>
                            </h5>
                            <div class='video-icon' data-bs-toggle='modal' data-bs-target='#videoModal_" . $row['id1'] . "'>
                                <i class='fas fa-play-circle' style='font-size: 25px; margin-top:-3px; color: #007bff;'></i>
                                <span>Voir la vidéo</span>
                            </div>
                            <p class='card-text'>
                                <i class='fas fa-info-circle' style='color: #007bff;'></i> " . $row['disce'] . "
                            </p>
                            <form action='prix.php' method='POST'>
                                <input type='hidden' name='med_name' value='" . $row['nom'] . "'>
                                <input type='hidden' name='idphar' value='" . $pharmacy_id . "'>
                                <input type='hidden' name='med_price' value='" . $row['prix'] . "'>
                                <div class='quantity-container'>
                                    <button type='button' class='btn-minus'><i class='fas fa-minus-circle'></i></button>
                                    <input type='number' name='quantity' class='num' placeholder='Quantité' min='0' required>
                                    <button type='button' class='btn-plus'><i class='fas fa-plus-circle'></i></button>
                                </div>";

                // Afficher le sélecteur de taille si défini
                if ($taille_array) { 
                    echo "<div class='taille-container mb-3'>";
                    echo "<label for='taille' class='taille-label'>Choisissez une taille:</label>";
                    echo "<select name='taille' id='taille' class='taille-select'>";
                    echo "<option disabled selected>Choisir une taille</option>";
                    foreach ($taille_array as $taille_item) {
                        echo "<option value='" . trim($taille_item) . "'>" . trim($taille_item) . "</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                }

                echo "<input type='submit' class='btn btn-primary mt-2' value='Ajoute à panier'>
                        </form>
                        <p class='prix pt-2' style='color:rgb(2, 53, 155);'>
                            <i class='fas fa-tags' style='color:rgb(2, 53, 155);'></i> " . $row['prix'] . " TND
                        </p>
                    </div>
                </div>";

                // Modal pour chaque vidéo (ID unique pour chaque produit)
                echo "<div class='modal fade' id='videoModal_" . $row['id1'] . "' tabindex='-1' aria-labelledby='videoModalLabel_" . $row['id1'] . "' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='videoModalLabel_" . $row['id1'] . "'>" . $row['nom'] . " _ " . $row['prix'] ." TND </h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <div class='video-container'>
                                    <video id='video_" . $row['id1'] . "' class='video-player' controls>
                                        <source src='" . $videoe . "' type='video/mp4'>
                                        Votre navigateur ne supporte pas la vidéo.
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
                }
                // Affichage du produit avec vidéo
                
            } else {
                if($row['qun']>0){
                    echo "<div class='card'>
                        <img src='" . $imagee . "' class='card-img-top' alt='...'>
                        <div class='card-body'>
                            <h5 class='card-title'>
                                <i class='fas fa-box' style='color: #007bff;'></i>
                                <span>" . $row['nom'] . "</span>
                            </h5>
                            <p class='card-text'>
                                <i class='fas fa-info-circle' style='color: #007bff;'></i> " . $row['disce'] . "
                            </p>
                            <form action='prix.php' method='POST'>
                                <input type='hidden' name='med_name' value='" . $row['nom'] . "'>
                                <input type='hidden' name='idphar' value='" . $pharmacy_id . "'>
                                <input type='hidden' name='med_price' value='" . $row['prix'] . "'>
                                <div class='quantity-container'>
                                    <button type='button' class='btn-minus'><i class='fas fa-minus-circle'></i></button>
                                    <input type='number' name='quantity' class='num' placeholder='Quantité' min='0' required>
                                    <button type='button' class='btn-plus'><i class='fas fa-plus-circle'></i></button>
                                </div>";

                // Afficher le sélecteur de taille si disponible
                if ($taille_array) { 
                    echo "<div class='taille-container mb-3'>";
                    echo "<label for='taille' class='taille-label'>Choisissez une taille:</label>";
                    echo "<select name='taille' id='taille' class='taille-select'>";
                    echo "<option disabled selected>Choisir une taille</option>";
                    foreach ($taille_array as $taille_item) {
                        echo "<option value='" . trim($taille_item) . "'>" . trim($taille_item) . "</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                }

                echo "<input type='submit' class='btn btn-primary mt-2' value='Ajoute à panier'>
                        </form>
                        <p class='prix pt-2' style='color:rgb(2, 53, 155);'>
                            <i class='fas fa-tags' style='color:rgb(2, 53, 155);'></i> " . $row['prix'] . " TND
                        </p>
                    </div>
                </div>";
            }
                }
                // Affichage d'un produit sans vidéo
                

            echo "</div>"; // Fermer la div col
        }
    } else {
        echo "<p style='color: rgb(89, 190, 148); font-weight: bold;' >
                <i class='fas fa-exclamation-circle' style='color: rgb(89, 190, 148);'></i> Aucun Produit trouvé dans cette Entreprise.
              </p>";
    }
    ?>
</div>





<!-- Footer -->
<div class="text-center mt-5">
    <p class="footer-text">
        <i class="fas fa-info-circle"></i> ShopEase &copy; 2024 | Designed by <strong>Ilyes</strong>
    </p>
</div>

</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>

<script>
    // Pour chaque modal de vidéo, on joue la vidéo quand il s'affiche
    <?php while ($row = $result_meds->fetch_assoc()) { ?>
        $('#videoModal_<?php echo $row['id1']; ?>').on('shown.bs.modal', function () {
            var video = document.getElementById("video_<?php echo $row['id1']; ?>");
            video.play();
        });

        // Lorsque le modal est fermé, on met la vidéo en pause
        $('#videoModal_<?php echo $row['id1']; ?>').on('hidden.bs.modal', function () {
            var video = document.getElementById("video_<?php echo $row['id1']; ?>");
            video.pause();
            video.currentTime = 0;
        });
    <?php } ?>
    // Gestion des boutons plus et moins pour la quantité
    document.addEventListener('DOMContentLoaded', function () {
        const minusButtons = document.querySelectorAll('.btn-minus');
        const plusButtons = document.querySelectorAll('.btn-plus');

        minusButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const input = button.parentElement.querySelector('.num');
                let value = parseInt(input.value) || 0;
                if (value > 0) {
                    input.value = value - 1;
                }
            });
        });

        plusButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const input = button.parentElement.querySelector('.num');
                let value = parseInt(input.value) || 0;
                input.value = value + 1;
            });
        });
    });
</script>
