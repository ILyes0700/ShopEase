<?php
session_start();  // Démarrer la session pour gérer le panier

// Vérifier si un produit a été ajouté au panier (via POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les données nécessaires existent et ne sont pas vides
    $med_name = isset($_POST['med_name']) ? $_POST['med_name'] : '';
    $med_price = isset($_POST['med_price']) ? $_POST['med_price'] : 0;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;  // Valeur par défaut de 1 si non spécifiée
    $idphar = isset($_POST["idphar"]) ? $_POST["idphar"] : '';
    $taille = isset($_POST['taille']) ? $_POST['taille'] : '';

    // Vérifier si les données sont valides avant de continuer
    if ($med_name !== '' && $med_price > 0 && $quantity > 0) {
        // Calculer le prix total pour l'article
        $total_price = $med_price * $quantity;

        // Initialiser le panier s'il n'est pas encore défini dans la session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Ajouter l'article au panier
        $_SESSION['cart'][] = [
            'name' => $med_name,
            'price' => $med_price,
            'quantity' => $quantity,
            'total_price' => $total_price,
            'idphar' => $idphar,
            'taille' => $taille
        ];

        // Optionnellement, afficher un message ou rediriger vers la page panier
        //echo "<script>alert('Produit ajouté au panier!');</script>";
        echo "<script>window.location.href = 'painer.php';</script>";  // Rediriger vers painer.php pour afficher le panier
    } else {
        // Si des données manquent ou sont invalides, afficher une erreur
        //echo "<script>alert('Veuillez vérifier les informations du produit.');</script>";
    }
}
?>
