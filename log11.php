<?php
session_start();  // Cette ligne doit être en premier dans votre fichier PHP
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logtest.webp" type="image/png">
    <title>ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="medfind.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/font-awesome-line-awesome/css/all.min.css">
    <style>
        .kanit-regular {
  font-family: "Kanit", serif;
  font-weight: 400;
  font-style: normal;
}
 .cart-container {
    position: relative;
    display: inline-block;
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
.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    font-size: 14px;
    padding: 2px 6px;
    border-radius: 50%;
}
nav{
    padding-top: 20px;
    margin-bottom:10px;
}
.ab{
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

    </style>
</head>
<body class="kanit-regular" style="background-color: #ffffff;">
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="log11.php">
            <img src="logtest.webp" alt="Logo" class="im mt-0" style="width: 40px;height:40px; margin-right: 10px; border-radius:50%;">
            <h3 class="pt-0 mb-0" style="color: #002060; font-size: 2rem;">ShopEase</h3>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active lie" href="painer.php" style="color: #007bff; font-weight: bold;">
                        <!-- Conteneur de l'icône du panier avec un badge -->
                        <div class="cart-container" style="position: relative; display: inline-block;">
                            <!-- Remplacer l'image par une icône Font Awesome -->
                            <i class="fas fa-shopping-cart" style="font-size: 40px; color: #002060; margin-top: 15px; margin-right: 20px;"></i>

                            <!-- Badge du nombre d'articles -->
                            <?php
                            // Calculer le nombre d'articles dans le panier
                            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                $cartCount = count($_SESSION['cart']); // Nombre d'articles dans le panier
                            } else {
                                $cartCount = 0; // Si le panier est vide
                            }
                            ?>

                            <!-- Si il y a des articles, afficher le badge -->
                            <?php if ($cartCount > 0): ?>
                                <span class="cart-badge " style="position: absolute; top: -9px; right: 18px; background-color:rgb(2, 63, 185); color: white; font-size: 10px; padding: 5px 10px; border-radius: 50%;">
                                    <?php echo $cartCount; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
                <!-- Li avec icône de chatbot -->
                <li class="nav-item">
                    <a class="nav-link" href="clic2.php" style="color: #007bff; font-weight: bold;">
                        <i class="fas fa-comment-dots" style="font-size: 40px; color: #002060; margin-top: 15px;"></i> <!-- Icône de chatbot -->
                    </a>
                </li>



    </ul>
</div>

    </div>
</nav>
<section class="container">
    <?php include("rechphar.php"); ?>
</section>
<div class="text-center mt-5 mb-0">
    <p class="footer-text mt-5" style="color: #b0b0b0; font-size: 14px; padding-top: 50px;">ShopEase &copy; 2024 | Designed by <strong>Ilyes</strong></p>
</div>
</body>
</html>
