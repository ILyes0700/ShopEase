<?php
// Connexion à la base de données
include('connect.php');  // Remplace par ton fichier de connexion à la base de données
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $tel = mysqli_real_escape_string($conn, $_POST['tel']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Vérifier si l'email existe déjà dans la base de données
    $checkEmail = "SELECT * FROM entre_liv WHERE email='$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        // Si l'email existe déjà
        $alertMessage = "Erreur lors de l'ajout du Livreur .";
            $alertType = 'error';
    } else {
        // Insertion des données dans la base de données
        $query = "INSERT INTO entre_liv (id,tel, email, nom, prenom) VALUES ('$id','$tel', '$email', '$nom', '$prenom')";
        if (mysqli_query($conn, $query)) {
            require 'vendor/autoload.php'; // Charge PHPMailer
                $mail = new PHPMailer(true);
    
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Utilisation du serveur SMTP de Gmail
                    $mail->SMTPAuth = true;          // Activation de l'authentification SMTP
                    $mail->Username = '';  // Remplacez par votre adresse e-mail Gmail
                    $mail->Password = '';  // Utilisez le mot de passe d'application
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Utilisation de TLS pour sécuriser la connexion
                    $mail->Port = 587;  // Port utilisé par Gmail pour TLS
    
                    // Informations de l'expéditeur et du destinataire
                    $mail->setFrom('', 'ShopEase');
                    $mail->AddEmbeddedImage('logtest.webp', 'logo');
                    $mail->addAddress($email, $nom);  // Remplacez par l'adresse du pharmacien
                    // Contenu de l'e-mail
                    $mail->isHTML(true);
                    $mail->Subject = 'Confirmation d\'inscription';
                    $mail->Body = "
    <html>
    <head>
        <title>Confirmation d'inscription - Livreur</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }
            .logo {
                                text-align: center;
                                margin-bottom: 20px;
                            }
            .container {
                background-color: #ffffff;
                width: 90%;
                max-width: 650px;
                margin: 40px auto;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #007bff; /* Bleu pour un ton plus professionnel */
                font-size: 24px;
                margin-bottom: 10px;
            }
            p {
                font-size: 16px;
                color: #333333;
                line-height: 1.7;
            }
            .info {
                background-color: #f0f9ff;
                padding: 15px;
                border-left: 5px solid #007bff; /* Bordure bleue pour cohérence */
                margin-top: 25px;
                font-size: 16px;
                border-radius: 5px;
            }
            .footer {
                font-size: 14px;
                text-align: center;
                color: #777777;
                margin-top: 40px;
            }
            .footer a {
                color: #007bff;
                text-decoration: none;
            }
            .footer a:hover {
                text-decoration: underline;
            }
            .button {
                background-color: #007bff;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                display: inline-block;
                margin-top: 20px;
            }
            .button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class='container'>
        <div class='logo'>
                <img src='cid:logo' alt='ShopEase Logo' width='100' height='100' style='border-radius: 50%;'>
            </div>
            <h2>Bonjour $nom,</h2>
            <p>Nous avons le plaisir de vous informer que votre inscription en tant que <strong>Livreur</strong> sur <strong>ShopEase</strong> a été confirmée avec succès ! 🎉</p>
            
            <p>Félicitations, vous êtes désormais prêt à rejoindre notre plateforme et à commencer vos livraisons en toute simplicité. Nous sommes heureux de vous compter parmi nous.</p>
            
            <div class='info'>
                <p><strong>Restez connecté, car la livraison sera envoyée par email.</strong></p>
                <p>Nous vous enverrons tous les détails relatifs à vos prochaines livraisons directement par email, y compris les informations sur les commandes à livrer, les horaires, et les adresses. Assurez-vous de vérifier régulièrement votre boîte de réception pour rester à jour !</p>
            </div>
            
            <p>Si vous avez des questions ou besoin d'assistance, notre équipe est à votre disposition pour vous aider.</p>
            
            <p class='footer'>Cordialement,<br>L'équipe <strong>ShopEase</strong></p>
        </div>
    </body>
    </html>
";



    
                    // Envoi de l'e-mail
                    $mail->send();
                    //echo "<script>alert('E-mail de confirmation envoyé avec succès !');</script>";
    
                } catch (Exception $e) {
                    echo "Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}";
                }
            //echo "<script>alert('Le livreur a été ajouté avec succès.');</script>";
            $alertMessage = "$nom ajouté avec succès!";
            $alertType = 'success';
        } else {
            //echo "<script>alert('Erreur lors de l\'ajout du livreur : " . mysqli_error($conn) . "');</script>";
            $alertMessage = "Erreur lors de l'ajout du Livreur .";
            $alertType = 'error';
        }
    }
}
?>
<div id="alertBox" class="alert" style="display:none;">
    <span id="alertMessage"></span>
    <span class="closebtn" onclick="closeAlert()">&times;</span>
</div>

<style>
.alert {
    padding: 15px;
    background-color: #f44336; /* Rouge pour les erreurs */
    color: white;
    margin-bottom: 15px;
    border-radius: 5px;
    position: fixed;
    top: 50%; /* Centrer verticalement */
    left: 50%; /* Centrer horizontalement */
    transform: translate(-50%, -50%); /* Ajustement pour un vrai centrage */
    z-index: 1000;
    height: 100px; /* Hauteur fixée à 100px */
    width: 300px; /* Largeur fixée à 100px */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    opacity: 0;
    transition: opacity 0.5s, transform 0.5s; /* Animation de l'apparition */
    display: flex;
    justify-content: center; /* Centrer le contenu horizontalement */
    align-items: center; /* Centrer le contenu verticalement */
    text-align: center; /* Centrer le texte */
}

.alert.success {
    background-color: #4CAF50; /* Vert pour le succès */
}

.alert.warning {
    background-color: #ff9800; /* Jaune pour les avertissements */
}

.alert .closebtn {
    position: absolute;
    top: 5px;
    right: 10px;
    color: white;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
}

.alert .closebtn:hover {
    color: black;
}

.alert.show {
    opacity: 1;
    transform: translate(-50%, -50%); /* Position normale après l'animation */
}

.alert.hide {
    opacity: 0;
    transform: translate(-50%, -60%); /* Légèrement décalé lors de la disparition */
}


</style>

<script>
    // Fonction pour afficher l'alerte
    function showAlert(message, type) {
        const alertBox = document.getElementById('alertBox');
        const alertMessage = document.getElementById('alertMessage');

        // Configurer le message et le type d'alerte
        alertMessage.textContent = message;

        // Ajouter la classe du type d'alerte (success, error, etc.)
        alertBox.classList.add(type); // type pourrait être success, error, etc.
        
        // Ajouter la classe 'show' pour activer l'animation
        alertBox.classList.add('show');
        alertBox.style.display = 'block'; // Afficher l'alerte

        // Masquer l'alerte après 5 secondes
        setTimeout(() => {
            alertBox.classList.remove('show');
            alertBox.classList.add('hide');
            setTimeout(() => {
                alertBox.style.display = 'none'; // Masquer définitivement après l'animation
                window.location.href = 'livr.php'; // Rediriger après l'alerte
            }, 500); // Attendre que l'animation de disparition soit terminée
        }, 700); // Durée d'affichage de l'alerte
    }

    // Fonction pour fermer l'alerte manuellement
    function closeAlert() {
        const alertBox = document.getElementById('alertBox');
        alertBox.classList.remove('show');
        alertBox.classList.add('hide');
        setTimeout(() => {
            alertBox.style.display = 'none';
            window.location.href = 'livr.php'; // Rediriger après fermeture manuelle
        }, 500);
    }

    // Appeler la fonction pour afficher l'alerte avec les messages définis par PHP
    <?php if (isset($alertMessage)): ?>
        showAlert("<?php echo $alertMessage; ?>", "<?php echo $alertType; ?>");
    <?php endif; ?>
</script>


<!-- Tu peux rediriger l'utilisateur vers une autre page après insertion ou afficher un message de succès -->
