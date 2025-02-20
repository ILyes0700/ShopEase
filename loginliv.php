<?php
require("connect.php");
$id = $_POST["id"];
$query = "SELECT * FROM entreprise WHERE id = '$id'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    echo "<script> window.location.href = 'livr.php'; </script>";
} else {
    echo "<script> window.location.href = 'logliv.html'; </script>";
    $alertMessage = "Votre ID incorrect !";
    $alertType = 'error';
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
                window.location.href = 'logliv.html'; // Rediriger après l'alerte
            }, 500); // Attendre que l'animation de disparition soit terminée
        }, 1000); // Durée d'affichage de l'alerte
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

