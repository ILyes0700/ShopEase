<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
require("connect.php");
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Traitement du formulaire d'envoi à la pharmacie
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {
    // Vérifiez si les champs sont bien remplis
    if (isset($_POST['email_patient'], $_POST['entreprise']) && !empty($_POST['email_patient']) && !empty($_POST['entreprise'])) {
        // Récupérer les informations du patient
        $email_patient = $_POST['email_patient'];
        $entreprise_id = $_POST['entreprise'];

        // Rechercher les informations du patient (adresse, zip, etc.)
        $sql_patient = "SELECT * FROM passient WHERE email = ?";
        $stmt_patient = $conn->prepare($sql_patient);
        $stmt_patient->bind_param("s", $email_patient);
        $stmt_patient->execute();
        $result_patient = $stmt_patient->get_result();
        
        if ($result_patient->num_rows === 0) {
            echo "<script>alert('Aucun patient trouvé avec cet email.');</script>";
            exit;
        }
        
        $patient = $result_patient->fetch_assoc();

        // Rechercher les informations de l'entreprise (adresse, zip, etc.)
        $sql_entreprise = "SELECT * FROM entreprise WHERE id = ?";
        $stmt_entreprise = $conn->prepare($sql_entreprise);
        $stmt_entreprise->bind_param("i", $entreprise_id);
        $stmt_entreprise->execute();
        $result_entreprise = $stmt_entreprise->get_result();
        
        if ($result_entreprise->num_rows === 0) {
            echo "<script>alert('Aucune entreprise trouvée avec cet ID.');</script>";
            exit;
        }

        $entreprise = $result_entreprise->fetch_assoc();

        // Récupérer les informations de localisation
        $patient_zip = $patient['zip'];
        $entreprise_zip = $entreprise['zip'];

        // Calcul des frais de livraison en fonction du code postal
        $frais_livraison = ($patient_zip === $entreprise_zip) ? 4 : 7;

        // Vérifiez si le panier n'est pas vide
        if (!empty($_SESSION['cart'])) {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $medicament_nom = htmlspecialchars($item['name']);
                $medicament_quantite = (int) $item['quantity'];
                $medicament_prix = (float) $item['price'];  // Le prix du médicament
                $tot = $medicament_prix * $medicament_quantite;  // Total pour cet article
                $taille = (!empty($item['taille'])) ? htmlspecialchars($item['taille']) : "";

                // Ajouter le total au total global
                $total += $tot;
            }

            // Ajouter les frais de livraison au total
            $total += $frais_livraison;

            // Insérer les informations dans la base de données
            $datee = date('Y-m-d');  // Date actuelle au format 'YYYY-MM-DD'

            foreach ($_SESSION['cart'] as $item) {
                $medicament_nom = htmlspecialchars($item['name']);
                $medicament_quantite = (int) $item['quantity'];
                $medicament_prix = (float) $item['price'];
                $tot = $medicament_prix * $medicament_quantite;
                $taille = (!empty($item['taille'])) ? htmlspecialchars($item['taille']) : "";
                $idphar = isset($item['idphar']) ? $item['idphar'] : null;

                // Insertion dans la base de données
                $sql = "INSERT INTO pharmacie_livraison (email_patient, entreprise, medicament_nom, taille, medicament_quantite, medicament_prix, total, datee, pharmacy_id, frais_livraison) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdidsii", $email_patient, $entreprise['nom_de_entreprise'], $medicament_nom, $taille, $medicament_quantite, $medicament_prix, $tot, $datee, $idphar, $frais_livraison);
        
        // Utiliser une requête préparée pour l'UPDATE
        $sql80 = "UPDATE med SET qun = qun - ? WHERE id = ? AND nom = ? AND prix = ?";
        $stmt80 = $conn->prepare($sql80);
        $stmt80->bind_param("iiss", $medicament_quantite, $idphar, $medicament_nom, $medicament_prix);
        $stmt80->execute();
        
                if ($stmt->execute()) {
                    // Envoi de l'email après l'insertion des données
                    $nom_de_entreprise = $conn->real_escape_string($entreprise['nom_de_entreprise']);
                    $req9 = "SELECT email FROM entreprise WHERE nom_de_entreprise = ? LIMIT 1";
                    $stmt_email = $conn->prepare($req9);
                    $stmt_email->bind_param("s", $nom_de_entreprise);
                    $stmt_email->execute();
                    $result_email = $stmt_email->get_result();
                    $row2 = $result_email->fetch_assoc();
                    $emailliv = $row2['email'];
                } else {
                    echo "Erreur lors de l'envoi des données : " . $stmt->error;
                }
            }
            require 'vendor/autoload.php'; // Charge PHPMailer
                    $mail = new PHPMailer(true);

                    try {
                        // Configurer le serveur SMTP
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = '';  // Remplacez par votre adresse email Gmail
                        $mail->Password = '';  // Utilisez le mot de passe d'application
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Informations de l'expéditeur et du destinataire
                        $mail->setFrom('', 'ShopEase');
                        $mail->addAddress($emailliv);

                        // Contenu de l'e-mail
                        $mail->isHTML(true);
                        $mail->Subject = 'Confirmation d\'inscription';
                        $mail->Body = "
                        <html>
                        <head>
                            <title>Confirmation d'inscription</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    background-color: #f4f4f4;
                                    margin: 0;
                                    padding: 0;
                                }
                                .container {
                                    background-color: #ffffff;
                                    width: 80%;
                                    max-width: 600px;
                                    margin: 30px auto;
                                    padding: 20px;
                                    border-radius: 8px;
                                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                }
                                h2 {
                                    color: #ffbe5e;
                                }
                                p {
                                    font-size: 16px;
                                    color: #333333;
                                    line-height: 1.6;
                                }
                                .info {
                                    background-color: #f0f0f0;
                                    padding: 10px;
                                    border-left: 4px solid #ffbe5e;
                                    margin-top: 20px;
                                    font-size: 16px;
                                }
                                .footer {
                                    font-size: 14px;
                                    text-align: center;
                                    color: #777777;
                                    margin-top: 30px;
                                }
                                .logo {
                                    text-align: center;
                                    margin-bottom: 20px;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                            <div class='logo'>
                                    <img src='cid:logo' alt='ShopEase Logo' width='100' height='100' style='border-radius: 50%;'>
                                </div>
                                <h2>Bonjour , </h2>
                                <p>Une nouvelle commande a été effectuée sur ShopEase et est en route vers vous !</p>
                                
                                <div class='info'>
                                    <strong>Vérifiez votre compte sur notre site pour plus de détails. Une commande se dirige vers vous.</strong>
                                </div>
                                
                                <p>Nous vous remercions pour votre service rapide.</p>
                                <p>Si vous avez des questions ou des préoccupations, n'hésitez pas à nous contacter.</p>
                                
                                <p class='footer'>Cordialement,<br>L'équipe ShopEase</p>
                            </div>
                        </body>
                        </html>
                        ";
                        // Envoi de l'e-mail
                        $mail->send();
                    } catch (Exception $e) {
                        echo "Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}";
                    }
            echo "<script>window.location.href = 'painer.php';</script>";
            unset($_SESSION['cart']);  // Vider le panier après l'envoi
        } else {
            echo "<script>alert('Le panier est vide. Veuillez ajouter des produits avant d\'envoyer !')</script>";
        }
    } else {
        echo "<script>alert('L'email du patient et l'entreprise sont obligatoires.');</script>";
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Ajouter FontAwesome -->
    <style>
        /* Réinitialisation et global styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            padding: 20px;
        }
        h3 {
            color: #2c3e50;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .total, .empty-cart {
            font-size: 18px;
            color: #2c3e50;
            margin-top: 20px;
        }

        .empty-cart {
            color: #e74c3c;
            text-align: center;
            font-size: 20px;
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .kanit-regular {
  font-family: "Kanit", serif;
  font-weight: 400;
  font-style: normal;
}
        .input-container {
            position: relative;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            color: #7f8c8d;
        }

        input[type="email"], select {
            width: 100%;
            padding: 14px 40px 14px 40px; /* Ajout de padding pour laisser de la place à l'icône */
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #bdc3c7;
            background-color: #ecf0f1;
            transition: all 0.3s;
        }

        input[type="email"]:focus, select:focus {
            border-color: #3498db;
            background-color: #ffffff;
            outline: none;
        }

        input[type="email"]::placeholder,
        select::placeholder {
            color: #95a5a6;
        }

        /* Ajouter l'icône dans l'input */
        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
        }

        /* Buttons */
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        button {
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-send {
            background-color: #2ecc71;
            color: white;
        }

        .btn-send:hover {
            background-color: #27ae60;
        }

        .btn-back {
            background-color: #3498db;
            color: white;
        }

        .btn-back:hover {
            background-color: #2980b9;
        }

        .btn-back a {
            text-decoration: none;
            color: white;
        }

        .btn-send i, .btn-back i {
            margin-right: 8px;
        }

        /* Panier */
        .cart-item {
            background-color: #f9f9f9;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-item strong {
            color: #3498db;
        }

        .cart-item .price {
            color: #e74c3c;
            font-weight: bold;
        }

        .cart-item .quantity {
            color: #3498db;
        }

        .cart-item .total-price {
            color: #2ecc71;
        }
        .mar{
            margin-top:20px;
        }
        .fas2{
            margin-top:11px;
        }
        .colver{
            color:#2ecc71;
        }
        .aq{
            color: #3498db;
        }
        .bd{
            
        }
    </style>
</head>
<body>

<div class="container kanit-regular">
    <h3 class="bd">Envoyer à livraison</h3>

    <?php
    if (empty($_SESSION['cart'])) {
        echo "<p class='empty-cart'>Votre panier est vide. Veuillez ajouter des produits avant de procéder à la livraison.</p>";
    } else {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            echo "<div class='cart-item kanit-regular'>";
            echo "<p><strong class='aq'>Nom: </strong> " . htmlspecialchars($item['name']) . "</p>";
            echo "<p><strong class='aq'>Prix: </strong> " . number_format($item['price'], 3, ',', ' ') . " TND</p>";
            echo "<p><strong class='aq'>Quantité: </strong> " . $item['quantity'] . "</p>";
            echo "<p><strong class='aq'>Total pour cet article: </strong> " . number_format($item['total_price'], 3, ',', ' ') . " TND</p>";
            echo "</div>";
            $total += $item['total_price'];  // Ajouter chaque article au total global
        }
        echo "<div class='total'>";
        echo "<h4 class='colver'>Total Panier: " . number_format($total, 3, ',', ' ') . " TND</h4>";
        echo "</div>";
    }
    ?>

    <form method="POST" action="livraison.php" class="mar kanit-regular">
        <div class="input-container ">
            <label for="email_patient">Votre Email :</label>
            <i class="fas fas2 fa-envelope"></i>
            <input type="email" id="email_patient" class="kanit-regular" name="email_patient" required placeholder="Entrez votre email avec lequel tu a  inscrit pour m'assurer que la commande arrive correctement">
        </div>

        <div class="input-container">
            <label for="entreprise">Entreprise (Nom de la livraison) :</label>
            <i class="fas fas2 fa-building"></i>
            <select id="entreprise" name="entreprise" class="kanit-regular" required>
                <option value="">Sélectionnez une livraison</option>
                <?php
                // Récupérer les entreprises de la base de données
                $sql = "SELECT id, nom_de_entreprise,statee,tel, address1, zip FROM entreprise";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Afficher les entreprises
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom_de_entreprise']) . "  -  " .  htmlspecialchars($row['statee']) . "  ,  " . htmlspecialchars($row['address1']) . "_ " . htmlspecialchars($row['zip']) . "  ,  "  . htmlspecialchars($row['tel']) .  "</option>";
                    }
                } else {
                    echo "<option value=''>Aucune entreprise disponible</option>";
                }
                ?>
            </select>
        </div>

        <div class="buttons">
            <button type="submit" name="send" class="btn-send kanit-regular">
                <i class="fas fa-paper-plane"></i> Envoyer à la Livreur
            </button>
            <button type="button" class="btn-back kanit-regular">
                <i class="fas fa-arrow-left"></i>
                <a href="painer.php">Retour au Panier</a>
            </button>
        </div>
    </form>
</div>

</body>
</html>
