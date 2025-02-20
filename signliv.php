<?php
// Inclusion de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
include('connect.php');  // Assurez-vous que ce fichier contient la connexion à la base de données

// Récupération des données envoyées via POST
$email = $_POST['email'];
$tel = $_POST['tel'];
$pass = $_POST['pass'];
$nom = $_POST['nom'];
$prenom = $_POST['pre'];
$date = $_POST['date'];
$address = $_POST['add'];
$address2 = $_POST['add2'];
$state = $_POST['stat'];
$zip = $_POST['zip'];
$gender = $_POST['check1']; // Genre (Homme ou Femme)
$nomphar = $_POST['nomphar']; // Nom de la pharmacie si Pharmacien

// Vérifier la connexion à la base de données
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

// Fonction pour générer un ID unique
function generate_unique_id($conn) {
    // Générer un ID aléatoire de 6 chiffres
    $id = rand(100000, 999999);

    // Vérifier si cet ID existe déjà dans la base de données
    $req7 = mysqli_query($conn, "SELECT * FROM entreprise WHERE id = '$id'");
    
    // Si l'ID existe déjà, appeler la fonction à nouveau pour générer un nouvel ID
    if (mysqli_num_rows($req7) > 0) {
        return generate_unique_id($conn);
    }

    return $id; // Retourner l'ID unique
}

// Générer l'ID unique
$id2 = generate_unique_id($conn);

// Vérifier si le numéro de téléphone existe déjà dans la table phar
$req4 = mysqli_query($conn, "SELECT * FROM entreprise WHERE tel = '$tel'");
if (mysqli_num_rows($req4) == 0) {
    // Insérer les données dans la table 'phar' (pour les pharmaciens)
    $req2 = mysqli_query($conn, "INSERT INTO entreprise (id,email, tel, passworde, nom, prenom, gender, datee, address1, address2, statee, zip, typee, nom_de_entreprise) 
                                    VALUES ('$id2','$email', '$tel', '$pass', '$nom', '$prenom', '$gender', '$date', '$address', '$address2', '$state', '$zip', 'Livreur', '$nomphar')");

    if ($req2) {
        // Afficher un message de succès
        $alertMessage = "$nom ajouté avec succès!";
        $alertType = 'success';
        $id_pharmacie = mysqli_insert_id($conn);

        // Envoi de l'e-mail de confirmation
        require 'vendor/autoload.php'; // Charge PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurer le serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Utilisation du serveur SMTP de Gmail
            $mail->SMTPAuth = true;          // Activation de l'authentification SMTP
            $mail->Username = 'pharfind@gmail.com';  // Remplacez par votre adresse e-mail Gmail
            $mail->Password = 'rfqdlvatmnuklgtb';  // Utilisez le mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Utilisation de TLS pour sécuriser la connexion
            $mail->Port = 587;  // Port utilisé par Gmail pour TLS

            // Informations de l'expéditeur et du destinataire
            $mail->setFrom('pharfind@gmail.com', 'ShopEase');
            $mail->addAddress($email, $nom);  // Remplacez par l'adresse du pharmacien
            $mail->AddEmbeddedImage('logtest.webp', 'logo');
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
        <h2>Bonjour $nom,</h2>
        <p>Votre inscription en tant que Livreur sur <strong>ShopEase</strong> a été confirmée avec succès.</p>
        <p>Nous sommes heureux de vous accueillir parmi nos livreurs.</p>
        
        <div class='info'>
            <strong>ID  :</strong> $id2
        </div>
        
        <p>Merci de vous être inscrit sur ShopEase. Vous êtes maintenant prêt à commencer vos livraisons.</p>
        <p>N'hésitez pas à nous contacter si vous avez des questions.</p>
        
        <p class='footer'>Cordialement,<br>L'équipe ShopEase</p>
    </div>
</body>
</html>
";

            $mail->AltBody = 'Bonjour ' . $nom . ', Votre inscription en tant que pharmacien est confirmée. Telephone : ' . $tel;

            // Envoi de l'e-mail
            $mail->send();
            // La redirection se fera dans le script JS après l'affichage de l'alerte
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}";
        }

    } else {
        // Message d'erreur si l'insertion échoue
        $alertMessage = "Erreur lors de l'ajout du livreur.";
        $alertType = 'error';
    }
} else {
    // Si le numéro de téléphone existe déjà, on prépare l'alerte
    $alertMessage = "Le numéro de téléphone existe déjà  !";
    $alertType = 'error';
}

// Fermer la connexion
mysqli_close($conn);
?>

<!-- Affichage de l'alerte -->
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
                window.location.href = 'logliv.html'; // Rediriger après l'alerte
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
            window.location.href = 'logliv.html'; // Rediriger après fermeture manuelle
        }, 500);
    }

    // Appeler la fonction pour afficher l'alerte avec les messages définis par PHP
    <?php if (isset($alertMessage)): ?>
        showAlert("<?php echo $alertMessage; ?>", "<?php echo $alertType; ?>");
    <?php endif; ?>
</script>
