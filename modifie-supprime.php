<?php
// Connexion à la base de données
include('connect.php');

// Variables pour afficher les messages
$message = "";
$message_type = "";

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $id1 = $_GET['delete'];
    $delete_query = "DELETE FROM med WHERE id1 = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id1);
    $stmt->execute();
    $stmt->close();
    $message = "Produit supprimé avec succès";
    $message_type = "success";  
}

// Gestion de la modification
if (isset($_POST['modify'])) {
    $id1 = $_POST['id1'];
    $nom = $_POST['nom'];
    $imagee = $_POST['imagee'];
    $disce = $_POST['disce'];
    $prix = $_POST['prix'];
    $videoe = $_POST['videoe'];
    $qun = $_POST['qun'];

    $update_query = "UPDATE med SET nom = ?, imagee = ?, disce = ?, prix = ?, videoe = ?, qun = ? WHERE id1 = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssi", $nom, $imagee, $disce, $prix, $videoe, $qun, $id1);
    $stmt->execute();
    $stmt->close();
    $message = "Produit mis à jour avec succès";
    $message_type = "success";  
}

// Recherche des produits par ID d'entreprise
$products = [];
if (isset($_POST['search'])) {
    $search_id = $_POST['search_id'];
    $search_query = "SELECT * FROM med WHERE id = ?";
    $stmt = $conn->prepare($search_query);
    $stmt->bind_param("i", $search_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Style Global */
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 15px;
        }

        h2 {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
        }

        /* Alert Message */
        .alert-custom {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            padding: 15px;
            border-radius: 10px;
            z-index: 1000;
            display: none;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-custom-success {
            background-color: #28a745;
            color: white;
        }

        .alert-custom-error {
            background-color: #dc3545;
            color: white;
        }

        .alert-custom-info {
            background-color: #17a2b8;
            color: white;
        }

        .close-btn {
            color: white;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            float: right;
            margin-left: 15px;
        }

        /* Recherche */
        .search-form .input-group {
            margin-bottom: 30px;
        }

        .search-form .form-control {
            border-radius: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-form button {
            border-radius: 25px;
            background-color: #007bff;
            color: white;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        /* Cartes Produits */
        .product-card {
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background-color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            border-radius: 12px 12px 0 0;
            max-height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }

        .card-text {
            font-size: 1rem;
            color: #666;
            margin-bottom: 10px;
        }

        .card-text strong {
            font-weight: 700;
            color: #007bff;
        }

        .action-icons i {
            font-size: 22px;
            color: #007bff;
            cursor: pointer;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .action-icons i:hover {
            color: #dc3545;
        }

        /* Footer */
        .footer-text {
            color: #b0b0b0;
            font-size: 14px;
            text-align: center;
            margin-top: 50px;
        }

        /* Modal */
        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-body .form-label {
            font-weight: bold;
            color: #333;
        }

        .modal-body .form-control {
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .modal-footer .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .modal-footer .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Gestion des Produits</h2>

        <!-- Affichage du message de confirmation -->
        <?php if ($message): ?>
            <div class="alert-custom <?php echo "alert-custom-" . $message_type; ?>" id="alertMessage">
                <span class="close-btn" onclick="closeAlert()">&times;</span>
                <?php echo $message; ?>
            </div>
            <script>
                document.getElementById('alertMessage').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('alertMessage').style.display = 'none';
                }, 5000);
            </script>
        <?php endif; ?>

        <!-- Formulaire de recherche -->
        <form method="POST" class="search-form mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search_id" placeholder="Entrez l'ID de l'entreprise" required>
                <button class="btn btn-primary" name="search">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
        </form>

        <!-- Affichage des produits -->
        <?php if ($products): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="product-card">
                            <img src="<?php echo $product['imagee']; ?>" class="card-img-top" alt="Produit">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['nom']; ?></h5>
                                <p class="card-text"><?php echo $product['disce']; ?></p>
                                <p class="card-text"><strong><?php echo $product['prix']; ?> TND</strong></p>
                                <p class="card-text">Quantité: <?php echo $product['qun']; ?></p>

                                <div class="action-icons">
                                    <a href="modifie-supprime.php?delete=<?php echo $product['id1']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $product['id1']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de modification -->
                    <div class="modal fade" id="editModal_<?php echo $product['id1']; ?>" tabindex="-1" aria-labelledby="editModalLabel_<?php echo $product['id1']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel_<?php echo $product['id1']; ?>">Modifier le produit</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="modifie-supprime.php">
                                        <input type="hidden" name="id1" value="<?php echo $product['id1']; ?>">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom</label>
                                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $product['nom']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="imagee" class="form-label">Image URL</label>
                                            <input type="text" class="form-control" placeholder='uploads/...' id="imagee" name="imagee" value="<?php echo $product['imagee']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="disce" class="form-label">Description</label>
                                            <textarea class="form-control" id="disce" name="disce" required><?php echo $product['disce']; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="prix" class="form-label">Prix</label>
                                            <input type="number" class="form-control" id="prix" name="prix" value="<?php echo $product['prix']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="videoe" class="form-label">Vidéo URL</label>
                                            <input type="text" class="form-control" id="videoe" placeholder='uploads/...' name="videoe" value="<?php echo $product['videoe']; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="qun" class="form-label">Quantité</label>
                                            <input type="number" class="form-control" id="qun" name="qun" value="<?php echo $product['qun']; ?>" required>
                                        </div>
                                        <button type="submit" name="modify" class="btn btn-primary">Mettre à jour</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">Aucun produit trouvé pour cet ID d'entreprise.</p>
        <?php endif; ?>
    </div>


    <!-- Footer -->
    <div class="text-center mt-5 mb-0">
        <p class="footer-text">ShopEase &copy; 2024 | Designed by <strong>Ilyes</strong></p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
