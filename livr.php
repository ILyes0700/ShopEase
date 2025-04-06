<?php
require("connect.php");  // Inclure le fichier de connexion à la base de données

// Inclure PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Autoload de Composer pour PHPMailer

// Vérification si les informations ont été envoyées via POST
if (isset($_POST['date']) && isset($_POST['id'])) {
    // Récupérer la date de livraison soumise par le formulaire et l'ID de l'entreprise
    $date = $_POST['date'];
    $id = $_POST["id"];

    // Préparer la requête SQL pour obtenir les livraisons de cette date et de l'entreprise
    $sql = "SELECT pharmacie_livraison.id, pharmacie_livraison.email_patient, 
                   pharmacie_livraison.medicament_nom, 
                   pharmacie_livraison.medicament_quantite, 
                   pharmacie_livraison.medicament_prix, 
                   pharmacie_livraison.total, 
                   pharmacie_livraison.datee,
                   pharmacie_livraison.frais_livraison,
                   pharmacie_livraison.taille,  
                   pharmacie_livraison.email_sent,
                   phar.nomphar, 
                   phar.tel, 
                   phar.address1, 
                   phar.address2, 
                   phar.statee, 
                   phar.zip 
            FROM pharmacie_livraison 
            INNER JOIN phar ON pharmacie_livraison.pharmacy_id = phar.id 
            INNER JOIN entreprise ON pharmacie_livraison.entreprise = entreprise.nom_de_entreprise 
            WHERE entreprise.id = '$id' 
            AND pharmacie_livraison.datee = '$date'";

    // Exécuter la requête
    $result = mysqli_query($conn, $sql);
}

// Envoi de l'email lorsque le patient est sélectionné
if (isset($_POST['send_email']) && isset($_POST['email'])) {
$email = $_POST['email'];
$medicamentNom = $_POST['medicament_nom'];
$taille = $_POST['taille'];
$prix_liv = $_POST['prix_liv'];
$medicamentQuantite = $_POST['medicament_quantite'];
$medicamentPrix = $_POST['medicament_prix'];
$total = $_POST['total'];
$dateLivraison = $_POST['date_livraison'];
$pharmacieNom = $_POST['pharmacie_nom'];
$livraisonId = $_POST['livraison_id'];
$tellir = $_POST["livreur_" . $livraisonId];
$fo="select tel,nom,prenom,address1,address2,statee,zip from passient WHERE email='$email' ";
$res2 = mysqli_query($conn, $fo);
$req20 = "SELECT address1, address2, statee, zip, tel FROM phar WHERE nomphar = '$pharmacieNom' LIMIT 1";
$result10 = mysqli_query($conn, $req20);
$total2=$total+$prix_liv;
$row6 = mysqli_fetch_assoc($result10);
$j1 = $row6['address1'];
$j2 = $row6['address2'];
$j3 = $row6['statee'];
$j4= $row6['zip'];
$j5 = $row6['tel'];
if ($res2 && mysqli_num_rows($res2) > 0) {
    $patient = mysqli_fetch_assoc($res2);
    $patientTel = $patient['tel'];
    $patientNom = $patient['nom'];
    $patientPrenom = $patient['prenom'];
    $patientAddress1 = $patient['address1'];
    $patientAddress2 = $patient['address2'];
    $patientStatee = $patient['statee'];
    $patientZip = $patient['zip'];
} else {
    $patientNom = "Inconnu";
    $patientPrenom = "Inconnu";
    $patientTel = "Inconnu";
    $patientAddress1 = "Inconnu";
    $patientAddress2 = "Inconnu";
    $patientStatee = "Inconnu";
    $patientZip = "Inconnu";
}
if (isset($_POST['liv_em'])) {
    $livreurEmail = $_POST['liv_em'];
} else {
    //echo "<script>alert('liv_em n\'est pas défini dans la requête POST.');</script>";
}
$livt="select tel FROM entre_liv WHERE email='$livreurEmail'";
$res3=mysqli_query($conn, $livt);
$num1 = mysqli_fetch_assoc($res3);
$nem = $num1['tel'];
$checkEmailSentQuery = "SELECT email_sent FROM pharmacie_livraison WHERE id = '$livraisonId'";
$resultCheck = mysqli_query($conn, $checkEmailSentQuery);
$rowCheck = mysqli_fetch_assoc($resultCheck);
if ($rowCheck['email_sent'] == 0) {
    $mailPatient = new PHPMailer(true);
    try {
        $mailPatient->isSMTP();
        $mailPatient->Host = 'smtp.gmail.com'; 
        $mailPatient->SMTPAuth = true; 
        $mailPatient->Username = '';
        $mailPatient->Password = ''; 
        $mailPatient->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mailPatient->Port = 587;
        $mailPatient->setFrom('', 'ShopEase');
        $mailPatient->addAddress($email); 
        $mailPatient->isHTML(true);
        $mailPatient->Subject = 'Confirmation de livraison';
        $mailPatient->Body = "
<html>
<head>
    <title>Confirmation de Livraison</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #4CAF50;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .header .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
        }
        .header .date {
            font-size: 16px;
            color: #7F8C8D;
        }
        h2 {
            font-size: 26px;
            color: #34495E;
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-details {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .nb{
        color:rgb(101, 142, 184);
        }
        .invoice-details h3 {
            font-size: 22px;
            color: #34495E;
            margin-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .invoice-details .detail {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }
        .invoice-details .label {
            font-weight: bold;
            color: #4CAF50;
            width: 45%;
        }
        .invoice-details .value {
            color: #555;
            width: 45%;
            text-align: right;
        }
        .summary {
            background-color: #e8f5e8;
            padding: 17px;
            border-left: 4px solid #4CAF50;
            margin-top: 30px;
        }
        .summary h4 {
            font-size: 16px;
            color: #34495E;
            margin-bottom: 15px;
        }
        .summary .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
        }
        .summary .total-line span {
            font-weight: bold;
            color: #333;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-top: 40px;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header' style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;'>
    <div class='logo' style='font-size: 28px; font-weight: bold; color: #4CAF50;'>
        ShopEase
    </div>
    <div class='date' style='font-size: 16px; color: #7F8C8D; margin-top: 5px; margin-left:20px;'> <!-- Ajout d'un margin-top -->
        Date de Livraison: $dateLivraison
    </div>
</div>

<h2 style='font-size: 26px; color: #34495E; text-align: center; margin-top: 40px;'>Confirmation de Livraison</h2>

        <p>Bonjour,</p>
        <p>Nous avons le plaisir de vous informer que votre livraison est prête pour acceptation. Voici les détails de votre livraison :</p>

        <div class='invoice-details'>
            <h3>Détails de la Livraison</h3>

            <div class='detail'>
                <span class='label'>Nom du Produit</span>
                <span class='value'>$medicamentNom</span>
            </div>
            <div class='detail'>
                <span class='label'>Taille</span>
                <span class='value'>$taille</span>
            </div>
            <div class='detail'>
                <span class='label'>Quantité</span>
                <span class='value'>$medicamentQuantite</span>
            </div>
            <div class='detail'>
                <span class='label'>Nom du Entreprise</span>
                <span class='value'>$pharmacieNom</span>
            </div>
            <div class='detail'>
                <span class='label'>Livrée par</span>
                <span class='value'>$nem</span>
            </div>
        </div>

        <div class='summary'>
    <h4 style='font-size: 18px; color: #34495E; margin-bottom: 25px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;'>Récapitulatif de la Facture :</h4>

    <div class='total-line' style='margin-bottom: 18px; display: flex; justify-content: space-between; font-size: 15px; color: #555;'>
        <span style='font-weight: bold; color: #4CAF50;'>Montant Total des Produits :</span>
        <span style='font-weight: bold; color: #333;'>$total TND</span>
    </div>
    <div class='total-line' style='margin-bottom: 18px; display: flex; justify-content: space-between; font-size: 15px; color: #555;'>
        <span style='font-weight: bold; color: #4CAF50;'>Frais de Livraison :</span>
        <span style='font-weight: bold; color: #333;'>$prix_liv TND</span>
    </div>
    <div class='total-line' style='margin-bottom: 30px; display: flex; justify-content: space-between; font-size: 15px; color: #555;'>
        <span style='font-weight: bold; color: #4CAF50; font-size: 15px;'>Total à Payer :</span>
        <span style='font-weight: bold; color: #4CAF50; font-size: 15px;'>$total2 TND</span>
    </div>
    
    <!-- Add a separator for visual clarity -->
    <div style='border-top: 2px solid #ddd; margin-top: 30px; padding-top: 15px;'></div>
</div>

        <p>Nous vous remercions pour votre confiance et restons à votre disposition pour toute information complémentaire.</p>
        <p>Cordialement,</p>
        <p>L'équipe ShopEase</p>

        <p class='footer'>Si vous avez des questions, n'hésitez pas à <a href='mailto:pharfind@gmail.com'>nous contacter</a>.</p>
    </div>
</body>
</html>
";


        


        $mailPatient->send();
        $mailLivreur = new PHPMailer(true);
        $mailLivreur->isSMTP();
        $mailLivreur->Host = 'smtp.gmail.com';
        $mailLivreur->SMTPAuth = true;       
        $mailLivreur->Username = ;  
        $mailLivreur->Password = '';  // Utilisez le mot de passe d'application
        $mailLivreur->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Utilisation de TLS pour sécuriser la connexion
        $mailLivreur->Port = 587;  // Port utilisé par Gmail pour TLS

        // Informations de l'expéditeur et du destinataire
        $mailLivreur->setFrom('', 'ShopEase');
        $mailLivreur->addAddress($livreurEmail);  // L'adresse du livreur

        // Contenu de l'e-mail avec un joli tableau HTML pour le livreur
        $mailLivreur->isHTML(true);
        $mailLivreur->Subject = 'Confirmation de livraison';

        // Corps du message avec un joli tableau HTML
        $mailLivreur->Body = "
<html>
<head>
    <title>Confirmation de Livraison</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 700px;
            background-color: #ffffff;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 28px;
            color: #4CAF50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        h3 {
            font-size: 22px;
            color: #333;
            margin-top: 40px;
            margin-bottom: 15px;
            color: #007bff; /* Blue for headings */
        }
        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .details-section {
            margin-bottom: 25px;
        }
        .details-section .item {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .details-section .item span {
            font-weight: bold;
            color: #333;
        }
        .highlight {
            background-color: #f9f9f9;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .highlight h3 {
            color: #4CAF50;
        }
        .important {
            background-color: #ffe9e9;
            padding: 12px;
            border-left: 4px solid #f44336;
            color: #f44336;
            font-weight: bold;
            margin-top: 30px;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-top: 30px;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        /* Styling for the items section */
        .item {
            background-color: #f1f8e9;
            padding: 10px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 16px;
        }
            .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        .header .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
        }
        .header .date {
            font-size: 16px;
            color: #7F8C8D;
        }
        .nb{
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class='container'>
    <div class='header' style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;'>
    <div class='logo' style='font-size: 28px; font-weight: bold; color: #4CAF50;'>
        ShopEase
    </div>
    <div class='date' style='font-size: 16px; color: #7F8C8D; margin-top: 5px; margin-left:20px;'> <!-- Ajout d'un margin-top -->
        Date de Livraison: $dateLivraison
    </div>
</div>
        <h2>Confirmation de Livraison</h2>
        <p>Bonjour,</p>
        <p>Nous avons le plaisir de vous confirmer que les détails de votre livraison sont les suivants :</p>

        <div class='details-section'>
            <h3>Détails de la Livraison</h3>
            <div class='highlight'>
                <div class='item'>
                    <span class='nb'>Nom du Produit :</span> $medicamentNom
                </div>
                <div class='item'>
                    <span class='nb'>Taille :</span> $taille
                </div>
                <div class='item'>
                    <span class='nb'>Quantité :</span> $medicamentQuantite
                </div>
                <div class='item'>
                    <span class='nb'>Frais de Livraison :</span> $prix_liv TND
                </div>
                <div class='item'>
                    <span class='nb'>Livré à :</span> $email
                </div>
                <div class='item'>
                    <span class='nb'>Total avec Livraison :</span> $total2  TND
                </div>
            </div>
        </div>

        <div class='details-section'>
            <h3>Détails de l'Entreprise</h3>
            <div class='highlight'>
                <div class='item'>
                    <span class='nb'>Nom de l'Entreprise :</span> $pharmacieNom
                </div>
                <div class='item'>
                    <span class='nb'>Adresse 1 :</span> $j1
                </div>
                <div class='item'>
                    <span class='nb'>Adresse 2 :</span> $j2
                </div>
                <div class='item'>
                    <span class='nb'>État :</span> $j3
                </div>
                <div class='item'>
                    <span class='nb'>Code Postal :</span> $j4
                </div>
                <div class='item'>
                    <span class='nb'>Téléphone de l'Entreprise :</span> $j5
                </div>
            </div>
        </div>

        <div class='details-section'>
            <h3>Détails du Patient</h3>
            <div class='highlight'>
                <div class='item'>
                    <span class='nb'>Nom :</span> $patientNom
                </div>
                <div class='item'>
                    <span class='nb'>Prénom :</span> $patientPrenom
                </div>
                <div class='item'>
                    <span class='nb'>Téléphone :</span> $patientTel
                </div>
                <div class='item'>
                    <spa class='nb'n>Adresse :</span> $patientAddress1 $patientAddress2
                </div>
                <div class='item'>
                    <span class='nb'>Code Postal :</span> $patientZip
                </div>
                <div class='item'>
                    <span class='nb'>État :</span> $patientStatee
                </div>
            </div>
        </div>

        <div class='important'>
            <p>Nous vous encourageons à confirmer la réception de cette livraison dès que possible.</p>
        </div>

        <p>Cordialement,</p>
        <p>L'équipe ShopEase</p>

        <p class='footer'>Pour toute question, <a href='mailto:pharfind@gmail.com'>contactez-nous</a>.</p>
    </div>
</body>
</html>
";




        // Envoi de l'email au livreur
        $mailLivreur->send();

        // Mise à jour du champ email_sent à 1 dans la base de données après les deux envois
        $updateEmailSentQuery = "UPDATE pharmacie_livraison SET email_sent = 1 WHERE id = '$livraisonId'";
        mysqli_query($conn, $updateEmailSentQuery);

       // echo "<script>alert('E-mails de confirmation envoyés avec succès !');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Erreur lors de l\'envoi des e-mails: {$mailPatient->ErrorInfo}');</script>";
    }
} else {
    echo "<script>alert('L\'email a déjà été envoyé pour cette livraison.');</script>";
}
// Récupérer les informations du client à partir de la base de données
// Récupérer les informations du client




}
// Récupérer les livraisons avec email_sent = 1 pour la même date
if (isset($_POST['date']) && isset($_POST['id'])) {
    $date = $_POST['date'];
    $id = $_POST['id'];
    
    $sql_sent = "SELECT pharmacie_livraison.id, pharmacie_livraison.email_patient, 
                        pharmacie_livraison.medicament_nom, 
                        pharmacie_livraison.medicament_quantite, 
                        pharmacie_livraison.medicament_prix, 
                        pharmacie_livraison.total, 
                        pharmacie_livraison.datee, 
                        pharmacie_livraison.email_sent,
                        phar.nomphar, 
                        phar.tel, 
                        phar.address1, 
                        phar.address2, 
                        phar.statee, 
                        phar.zip 
                 FROM pharmacie_livraison 
                 INNER JOIN phar ON pharmacie_livraison.pharmacy_id = phar.id 
                 INNER JOIN entreprise ON pharmacie_livraison.entreprise = entreprise.nom_de_entreprise 
                 WHERE entreprise.id = '$id' 
                 AND pharmacie_livraison.datee = '$date' 
                 AND pharmacie_livraison.email_sent = 1";

    $result_sent = mysqli_query($conn, $sql_sent);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="icon" href="logtest.webp" type="image/png">
<title>ShopEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        .container {
            margin-top: 30px;
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
  



.card-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
}

.card {
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 280px;
    padding: 20px;
    text-align: left;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
}

.card-header {
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;
}

.card-body {
    font-size: 14px;
    color: #333;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.card-footer {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
    padding-top: 20px;
}

.select-container {
    width: 100%;
    position: relative;
}

.select-container select {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background-color: #f4f4f4;
    color: #333;
    transition: all 0.3s ease;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.select-container select:focus {
    border-color: #4CAF50;
    background-color: #fff;
    outline: none;
    box-shadow: 0px 4px 12px rgba(76, 175, 80, 0.2);
}

.button-container {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.sendEmailBtn, .declineOrderBtn {
    padding: 8px 18px;
    font-size: 14px;
    border-radius: 50px;
    text-align: center;
    width: auto;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.sendEmailBtn {
    background-color: #4CAF50;
    color: white;
    border-color:#4CAF50;
}

.sendEmailBtn:hover {
    transform: translateY(-3px);
    background-color:#45a049 ;
    border-color:#45a049;
}

.declineOrderBtn {
    background-color:rgb(243, 96, 96) ;
    color: white;
    border-color:rgb(243, 96, 96);
}

.declineOrderBtn:hover {
    transform: translateY(-3px);
    background-color: rgb(247, 83, 83);
    border-color:rgb(247, 83, 83);
}

.ab {
    font-size: 14px;
    color: #555;
    font-style: italic;
    text-align: center;
}



        .form-group {
            margin-bottom: 20px;
        }

        .navbar {
            margin-top: -30px;
            
        }

        .navbar-brand {
            color: white;
        }

        .navbar-brand:hover {
            color: #fff;
        }
        .ab{
            display: inline-block;
            padding: 10px 10px;
        }
        .ac{
            display: flex;
            gap: 10px; /* Espacement entre les boutons */
            align-items: center;
        }
        .an{
            color: black;
        }
        .pl{
            color:rgb(252, 139, 34) ;
        }
        .pc{
            color:rgb(0, 158, 53) ;
        }
        .kanit-regular {
  font-family: "Kanit", serif;
  font-weight: 400;
  font-style: normal;
}
            </style>
</head>
<body  class="kanit-regular">
<nav class="navbar navbar-expand-lg fixed-top" style=" box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); background-color: #f7f7f7;">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="livr.php">
      <img src="logtest.webp" alt="Logo PharmFind" class="me-2" style="width: 40px; height: 40px; border-radius:50%;">
      <h3 class="mb-0 kanit-regular" style=" font-size: 30px; color: #002060;">ShopEase</h3>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="ajliv.html" style="color: #75b1da; font-weight: bold; text-transform: uppercase; padding: 8px 15px; border-radius: 5px;">
             <!-- Icône ajoutée ici -->
            <h5 class="mb-0 kanit-regular" style="font-size: 17px; color: #002060;"><i class="fas fa-plus me-2"></i>Ajouter Un livreur</h5>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>


    <div class="container mt-5 pt-5">
    <!-- Titre avec animation et icône -->
    <div class="animation-container text-center mb-4">
        <h2 class="animated-title" style="color: #002060; font-weight: bold; padding: 10px; background: linear-gradient(45deg, #ffbe5e, #f7a52e); color: white; border-radius: 8px;">
            <i class="fas fa-truck-loading"></i> Recherche de Livraisons
        </h2>
    </div>

    <!-- Formulaire de soumission de la date et ID de l'entreprise -->
    <form method="POST" action="" class="mb-4 mt-5">
        <div class="form-group">
            <label for="date"><i class="fas fa-calendar-alt"></i> Date de livraison :</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label for="id"><i class="fas fa-building"></i> ID de l'entreprise :</label>
            <input type="number" name="id" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3" style="background-color:rgb(248, 176, 67); color: white; border: none; padding: 10px 20px; border-radius: 5px;">
            <i class="fas fa-search"></i> Afficher les Livraisons
        </button>
    </form>

    <?php if (isset($result)): ?> 
    <h3 class="mt-4 text-center" style="color: rgb(0, 0, 0); font-weight: bold;">
        <i class="fas fa-box"></i> Livraisons à envoyer
    </h3>
    <div class="card-container">
        <?php 
        // Récupérer les livreurs une seule fois avant de parcourir les lignes
        $sql_livreurs = "SELECT id, nom, prenom, tel, email FROM entre_liv WHERE id = '$id'";
        $result_livreurs = mysqli_query($conn, $sql_livreurs);
        $livreurs = [];
        while ($livreur = mysqli_fetch_assoc($result_livreurs)) {
            $livreurs[$livreur['email']] = $livreur; // Utiliser l'email comme clé
        }

        // Boucle sur les livraisons
        while ($row = mysqli_fetch_assoc($result)):  
            $pasres = mysqli_query($conn, "SELECT statee, address1 FROM passient WHERE email = '" . $row['email_patient'] . "' LIMIT 1");
            $row2= mysqli_fetch_assoc($pasres);
            $statee = $row2['statee'];
            $address1 = $row2['address1'];
        ?>
            <div class="card">
                <div class="card-header pl">
                    <i class="fas fa-box"></i> <?= $row['medicament_nom'] ?>
                </div>
                <div class="card-body">
    <p><i class="fas fa-calendar-day"></i> <strong class='pc pl-3'>Date:</strong> <?= $row['datee'] ?></p>
    <p><i class="fas fa-envelope"></i> <strong class='pc'>Email:</strong> <?= $row['email_patient'] ?></p>
    <p><i class="fas fa-map"></i> <strong class='pc'>État:</strong> <?= $statee ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <strong class='pc'>Adresse:</strong> <?= $address1 ?></p>
    <p><i class="fas fa-building"></i> <strong class='pc'>Nom du Entreprise:</strong> <?= $row['nomphar'] ?></p>
    <p><i class="fas fa-dollar-sign"></i> <strong class='pc'>Prix Total:</strong> <?= $row['total'] ?> TND</p>
</div>
                <div class="card-footer">
    <div class="select-container">
        <select name="livreur_<?= $row['id'] ?>" class="form-control">
            <option value="">Choisir un livreur</option>
            <?php foreach ($livreurs as $livreur): ?>
                <option value="<?= $livreur['email'] ?>"><?= $livreur['prenom'] . " " . $livreur['nom'] . " - Tel: " . $livreur['tel'] . " - Email: " . $livreur['email'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="button-container">
        <?php if ($row['email_sent'] == 0): ?>
            <button class="sendEmailBtn" data-email="<?= $row['email_patient'] ?>" 
                    data-medicament-nom="<?= $row['medicament_nom'] ?>"
                    data-prix-liv="<?= $row['frais_livraison'] ?>"
                    data-taille="<?= $row['taille'] ?>" 
                    data-medicament-quantite="<?= $row['medicament_quantite'] ?>" 
                    data-medicament-prix="<?= $row['medicament_prix'] ?>"
                    data-total="<?= $row['total'] ?>" 
                    data-date-livraison="<?= $row['datee'] ?>"
                    data-pharmacie-nom="<?= $row['nomphar'] ?>" 
                    data-livraison-id="<?= $row['id'] ?>">
                <i class="fas fa-check"></i> Accepter
            </button>
            <button class="declineOrderBtn" data-email="<?= $row['email_patient'] ?>" 
                    data-medicament-nom="<?= $row['medicament_nom'] ?>" 
                    data-medicament-quantite="<?= $row['medicament_quantite'] ?>"
                    data-pharmacie-nom="<?= $row['nomphar'] ?>"
                    data-livraison-id="<?= $row['id'] ?>">
                <i class="fas fa-times"></i> Réfusée
            </button>
        <?php else: ?>
            <span class="ab">Email déjà envoyé</span>
        <?php endif; ?>
    </div>
</div>


            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>


    <script>
   document.querySelectorAll('.declineOrderBtn').forEach(button => {
    button.addEventListener('click', function() {
        const email = this.getAttribute('data-email');
        const medicamentNom = this.getAttribute('data-medicament-nom');
        const medicamentQuantite = this.getAttribute('data-medicament-quantite');
        const pharmacieNom = this.getAttribute('data-pharmacie-nom');
        const livraisonId = this.getAttribute('data-livraison-id');  // Id de livraison
        
        // Création de la requête POST pour envoyer l'email
        fetch('decline_order.php', {
            method: 'POST',
            body: new URLSearchParams({
                decline_order: '1',
                email_patient: email,
                medicament_nom: medicamentNom,
                medicament_quantite: medicamentQuantite,
                pharmacie_nom: pharmacieNom,
                livraison_id: livraisonId
            })
        })
        .then(response => response.text())
        .then(data => {
            // Afficher un message à l'utilisateur après la réponse
            //alert('Commande refusée de ' + email + ' (ID: ' + livraisonId + ')');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la demande.');
        });
    });
});

        // Ajouter un événement click à chaque bouton "Envoyer Email"
        document.querySelectorAll('.sendEmailBtn').forEach(button => {
    button.addEventListener('click', function () {
        // Vérifier si les attributs data-* existent avant de les utiliser
        let email = this.getAttribute('data-email');
        let medicamentNom = this.getAttribute('data-medicament-nom');
        let prix_liv = this.getAttribute('data-prix-liv');
        let taille = this.getAttribute('data-taille');
        let medicamentQuantite = this.getAttribute('data-medicament-quantite');
        let medicamentPrix = this.getAttribute('data-medicament-prix');
        let total = this.getAttribute('data-total');
        let dateLivraison = this.getAttribute('data-date-livraison');
        let pharmacieNom = this.getAttribute('data-pharmacie-nom');
        let tellivr = this.getAttribute('data-tel-liv');
        let livraisonId = this.getAttribute('data-livraison-id');  // Id de livraison

        // Sélectionner le select correspondant au livreur
        const livreurSelect = document.querySelector(`select[name="livreur_${livraisonId}"]`);

        // Vérifier si le select existe et si une valeur est sélectionnée
        const livreurEmail = livreurSelect ? livreurSelect.value : null;

        // Vérifier si toutes les données nécessaires sont présentes
        if (!email || !livreurEmail) {
            alert("Il manque des informations importantes (email ou livreur).");
            return;  // Arrêter l'exécution si une donnée est manquante
        }

        // Créer une requête AJAX pour envoyer l'email
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                //alert('Email envoyé avec succès');
            } else {
                alert('Erreur lors de l\'envoi de l\'email');
            }
        };

        // Envoyer la requête avec les données
        xhr.send('send_email=true' +
         '&email=' + encodeURIComponent(email) +
         '&medicament_nom=' + encodeURIComponent(medicamentNom) +
         '&prix_liv=' + encodeURIComponent(prix_liv) +
         '&taille=' + encodeURIComponent(taille) +
         '&medicament_quantite=' + encodeURIComponent(medicamentQuantite) +
         '&medicament_prix=' + encodeURIComponent(medicamentPrix) +
         '&total=' + encodeURIComponent(total) +
         '&date_livraison=' + encodeURIComponent(dateLivraison) +
         '&pharmacie_nom=' + encodeURIComponent(pharmacieNom) +
         '&liv_em=' + encodeURIComponent(livreurEmail) +
         '&livraison_id=' + encodeURIComponent(livraisonId));  // envoyer l'id de la livraison
 // envoyer l'id de la livraison
    });
});




    </script>
    <div class="text-center mt-5 mb-0">
    <p class="footer-text mt-5" style="color: #b0b0b0; font-size: 14px; padding-top: 200px;">ShopEase &copy; 2024 | Designed by <strong>Ilyes</strong></p>
</div>
</body>
</html>
