<?php
// Connexion à la base de données
require("connect.php");

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Vérification que le fichier image a bien été soumis
    if (isset($_FILES["imagee"])) {

        // Récupérer les données envoyées depuis le formulaire
        $id = $_POST["id"];  // ID de la pharmacie
        $nom = $_POST["nom"];  // Nom du médicament
        $prix = $_POST["prix"];  // Prix du médicament
        $disce = $_POST["disc"];  // Description du médicament
        $qun = $_POST["qun"];  // Quantité

        // Gestion des tailles
        $tailles = isset($_POST["taille"]) ? $_POST["taille"] : [];  // Récupère les tailles
        $tailles_str = implode(',', $tailles);  // Convertit les tailles en une chaîne séparée par des virgules

        // Gestion de l'image téléchargée
        $target_dir = "uploads/";  // Dossier où l'image sera stockée
        $target_file_image = $target_dir . basename($_FILES["imagee"]["name"]);  // Chemin du fichier
        $imageFileType = strtolower(pathinfo($target_file_image, PATHINFO_EXTENSION));  // Type du fichier

        // Vérification si le fichier est bien une image
        if (getimagesize($_FILES["imagee"]["tmp_name"]) === false) {
            echo "<script>alert('Le fichier n\\'est pas une image.');</script>";
            echo "<script> window.location.href = 'pharm.html'; </script>";
            exit;
        }

        // Vérification de la taille de l'image (max 500 KB)
        if ($_FILES["imagee"]["size"] > 500000) {
            echo "<script>alert('Le fichier image est trop lourd.');</script>";
            echo "<script> window.location.href = 'pharm.html'; </script>";
            exit;
        }

        // Vérifier les formats d'images autorisés
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif" && $imageFileType != "webp") {
            echo "<script>alert('Seuls les formats JPG, JPEG, PNG, GIF, et WEBP sont autorisés pour l\\'image.');</script>";
            exit;
        }

        // Déplacer le fichier image téléchargé dans le dossier 'uploads'
        if (move_uploaded_file($_FILES["imagee"]["tmp_name"], $target_file_image)) {
            echo "Image téléchargée avec succès.<br>"; // Message de succès

            // Gestion de la vidéo téléchargée (si présente)
            if (isset($_FILES["videoe"]) && $_FILES["videoe"]["error"] == 0) {
                // Si une vidéo est soumise
                $target_file_video = $target_dir . basename($_FILES["videoe"]["name"]);  // Chemin du fichier vidéo
                $videoFileType = strtolower(pathinfo($target_file_video, PATHINFO_EXTENSION));  // Type du fichier vidéo

                // Vérification des formats vidéo autorisés
                if ($videoFileType != "mp4" && $videoFileType != "mov" && $videoFileType != "avi" && $videoFileType != "mkv" && $videoFileType != "webm") {
                    echo "<script>alert('Seuls les formats MP4, MOV, AVI, MKV, et WEBM sont autorisés pour la vidéo.');</script>";
                    exit;
                }

                // Vérification de la taille de la vidéo (max 20 MB)
                if ($_FILES["videoe"]["size"] > 40000000) {
                    echo "<script>alert('Le fichier vidéo est trop lourd.');</script>";
                    exit;
                }

                // Déplacer le fichier vidéo téléchargé dans le dossier 'uploads'
                if (move_uploaded_file($_FILES["videoe"]["tmp_name"], $target_file_video)) {
                    echo "Vidéo téléchargée avec succès.<br>"; // Message de succès
                } else {
                    echo "<script>alert('Erreur lors du téléchargement de la vidéo.');</script>";
                    echo "<script> window.location.href = 'pharm.html'; </script>";
                    exit;
                }
            } else {
                // Si aucune vidéo n'est soumise, on met une chaîne vide pour `videoe`
                $target_file_video = "";  // Valeur vide si pas de vidéo
            }

            // Préparer la requête SQL pour insérer les données dans la base de données
            $query = "INSERT INTO med (id, nom, imagee, disce, prix, videoe, qun, taille) 
                      VALUES ('$id', '$nom', '$target_file_image', '$disce', '$prix', '$target_file_video', '$qun', '$tailles_str')";

            // Affichage de la requête SQL pour le débogage
            //echo "Requête SQL : " . $query . "<br>";  // Débogage

            // Exécuter la requête SQL
            if (mysqli_query($conn, $query)) {
                echo "<script> window.location.href = 'pharm.html'; </script>";
            } else {
                echo "<script>alert('Erreur lors de l\\'ajout du médicament : " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Erreur lors du téléchargement de l\\'image.');</script>";
            echo "<script> window.location.href = 'pharm.html'; </script>";
        }
    } else {
        echo "<script>alert('Aucune image téléchargée.');</script>";
        echo "<script> window.location.href = 'pharm.html'; </script>";
    }
}

// Fermer la connexion à la base de données
mysqli_close($conn);
?>
