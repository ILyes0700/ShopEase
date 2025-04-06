<?php
// Inclusion de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
include('connect.php');  // Assurez-vous que ce fichier contient la connexion à la base de données

// Récupération des données envoyées via POST
$email = $_POST['email'];
$tel = $_POST['tel'];
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}
$req4 = mysqli_query($conn, "SELECT id FROM phar WHERE tel = '$tel' LIMIT 1");
if (mysqli_num_rows($req4) > 0) {
    $row = mysqli_fetch_assoc($req4);
    $id_entreprise = $row['id'];
    require 'vendor/autoload.php'; // Charge PHPMailer
                $mail = new PHPMailer(true);
    
                try {
                    // Configurer le serveur SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Utilisation du serveur SMTP de Gmail
                    $mail->SMTPAuth = true;          // Activation de l'authentification SMTP
                    $mail->Username = '';  // Remplacez par votre adresse e-mail Gmail
                    $mail->Password = '';  // Utilisez le mot de passe d'application
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Utilisation de TLS pour sécuriser la connexion
                    $mail->Port = 587;  // Port utilisé par Gmail pour TLS
    
                    // Informations de l'expéditeur et du destinataire
                    $mail->setFrom('', 'ShopEase');
                    $mail->addAddress($email);  // Remplacez par l'adresse du pharmacien
                    $mail->AddEmbeddedImage('logtest.webp', 'logo');
                    // Contenu de l'e-mail
                    $mail->isHTML(true);
                    $mail->Subject = 'Mot de passe ! ';
                    $mail->Body = "
    <html>
    <head>
        <title>Rappel de mot de passe</title>
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
                color: #3751ff;
            }
            p {
                font-size: 16px;
                color: #333333;
                line-height: 1.6;
            }
            .info {
                background-color: #f0f0f0;
                padding: 10px;
                border-left: 4px solid #3751ff;
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
            <h2>Bonjour,</h2>
            <p>Vous avez demandé un rappel de votre ID pour votre compte sur <strong>ShopEase</strong>.</p>
            
            <div class='info'>
                <strong>Votre ID actuel :</strong> $id_entreprise
            
            <p>Si vous n'avez pas demandé ce rappel, nous vous recommandons de sécuriser votre compte immédiatement en changeant ID.</p>
            
            <p class='footer'>Cordialement,<br>L'équipe ShopEase</p>
        </div>
    </body>
    </html>
";


                    $mail->AltBody = 'Bonjour , Votre inscription en tant que Entreprise est confirmée. Telephone : ' . $tel;
    
                    // Envoi de l'e-mail
                    $mail->send();
                    //echo "<script>alert('E-mail de confirmation envoyé avec succès !');</script>";
                    echo "<script> window.location.href = 'logphar.html'; </script>";
    
                } catch (Exception $e) {
                    echo "Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}";
                }
    
}
else {
    echo "<script>alert('Le numéro de téléphone ou email n'existe pas  !');</script>";
    echo "<script> window.location.href = 'logphar.html'; </script>";
}
mysqli_close($conn);
?>
