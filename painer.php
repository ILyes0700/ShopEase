<?php
session_start();

// Connexion à la base de données
require("connect.php");
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Traitement du formulaire d'envoi à la pharmacie
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            // Vérifier si 'idphar' existe dans l'élément
            if (isset($item['idphar'])) {
                $idphar = $item['idphar'];
            } else {
                $idphar = null;  // Si l'ID de la pharmacie n'est pas défini, on le met à null
            }
        
            // Récupérer les informations du médicament
            $datee = date('Y-m-d');  // La date d'achat du médicament
            $medicament_nom = htmlspecialchars($item['name']);
            $medicament_quantite = (int) $item['quantity'];
            $medicament_prix = (float) $item['price'];  // Le prix du médicament
            $tot = $medicament_prix * $medicament_quantite;  // Total pour cet article

            // Insertion des informations dans la base de données
            $sql = "INSERT INTO pharmacy_medicaments (nom_medicament, quantite, prix, pharmacy_id, datee) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdids", $medicament_nom, $medicament_quantite, $tot, $idphar, $datee);  // Lier les variables
            $stmt->execute();
        }
        
        // Vider le panier après l'envoi
        unset($_SESSION['cart']);  // Ou $_SESSION['cart'] = []; si vous préférez
    }
}

// Suppression d'un article du panier
if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    // Si l'ID de l'article est présent, on le supprime
    if (isset($_SESSION['cart'][$idToDelete])) {
        unset($_SESSION['cart'][$idToDelete]);
    }
    echo "<script> window.location.href = 'painer.php'; </script>";
    // Redirige vers la page du panier après suppression
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logtest.webp" type="image/png">
    <title>ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .kanit-regular {
            font-family: "Kanit", serif;
            font-weight: 400;
            font-style: normal;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        strong {
            color: rgb(0, 65, 161);
        }
        h2 {
            text-align: center;
            color: rgb(71, 134, 230);
        }
        p {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
        }
        .item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            position: relative;
        }
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            color: red;
            background: transparent;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: rgb(71, 134, 230);
            text-align: right;
            margin-top: 20px;
        }
        .empty-cart {
            font-size: 18px;
            text-align: center;
            color: #e74c3c;
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
        }
        .btn-print {
            background-color: #27ae60;
            color: #fff;
        }
        .bt {
            background-color: rgb(34, 108, 219);
            color: #fff;
        }
        .btn-download {
            background-color: #27ae60;
            color: #fff;
        }
        .tot {
            color: #27ae60;
        }
        .back-button i {
            margin-right: 8px;
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

        /* Image Section with Animation */
        .image-section {
            text-align: center;
            margin-bottom: 20px;
            animation: bounceIn 1s ease-out;
        }

        .image-section img {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 30%;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="kanit-regular">

<div class="container">

    <!-- Image icon -->
    <div class="image-section">
        <img src="cha9a9a.svg" alt="Icone" />
    </div>

    <?php
if (empty($_SESSION['cart'])) {
    echo "<p class='empty-cart'>Votre panier est vide.</p>";
} else {
    echo "<h2>Panier</h2>";
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        echo "<div class='item'>";
        echo "<p><strong>Nom:</strong> " . htmlspecialchars($item['name']) . "</p>";
        
        // Vérifier si 'taille' existe avant de l'afficher
        if (!empty($item['taille'])) {
            echo "<p><strong>Taille:</strong> " . htmlspecialchars($item['taille']) . "</p>";
        }
        
        echo "<p><strong>Prix:</strong> " . number_format($item['price'], 3, ',', ' ') . " TND</p>";
        echo "<p><strong>Quantité:</strong> " . $item['quantity'] . "</p>";
        echo "<p><strong>Total:</strong> " . number_format($item['total_price'], 3, ',', ' ') . " TND</p>";
        
        // Ajouter le bouton de suppression avec un lien qui passe l'ID de l'article à supprimer
        echo "<a href='?delete=" . $id . "'><button class='delete-btn'><i class='fas fa-trash'></i></button></a>";
        echo "</div>";
        
        $total += $item['total_price'];  // Ajouter chaque article au total global
    }
    echo "<div class='total'>";
    echo "<h4>Total Panier: " . number_format($total, 3, ',', ' ') . " TND</h4>";
    echo "</div>";
}
?>

    <div class="buttons">
        <!-- Formulaire pour envoyer à la pharmacie -->
        <form method="POST" action="livraison.php">
            <button type="submit" name="send" class="btn-print kanit-regular">Envoyer à la livraison</button>
        </form>
    </div>

    <div class="buttons">
        <button class="btn-print bt kanit-regular" onclick="window.print();">Imprimer le Panier</button>
    </div>

</div>
</body>
</html>
