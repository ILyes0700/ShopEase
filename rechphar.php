<?php
// Connexion à la base de données
require("connect.php");

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer le type sélectionné dans l'URL
$typee = isset($_GET['typee']) ? $_GET['typee'] : '';

// Requête SQL pour récupérer les types uniques dans la table 'phar'
$sql_types = "SELECT DISTINCT typee FROM phar";
$result_types = $conn->query($sql_types);

// Requête SQL pour récupérer les pharmacies correspondant au type sélectionné
$sql_pharmacies = "";
if ($typee) {
    $typee = $conn->real_escape_string($typee);  // Sécuriser le type
    $sql_pharmacies = "SELECT * FROM phar WHERE typee = '$typee' ORDER BY statee ASC";
    $result_pharmacies = $conn->query($sql_pharmacies);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logtest.webp" type="image/png">
    <title>ShopEase</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Lien vers Bootstrap pour le style de base -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style de base */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            color: #007bff;
            font-size: 32px;
            margin-bottom: 30px;
            text-align: center;
            text-transform: uppercase;
        }

        h2 {
            color: #28a745;
            margin-top: 30px;
            font-size: 24px;
        }

        /* Styles des boutons et sélection */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
            transform: scale(1.05);
        }

        .form-control {
            border-radius: 5px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .list-group-item {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #e9ecef;
        }

        /* Animation pour le select */
        select {
            background-color: #f1f3f5;
            border: 1px solid #ced4da;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        select:focus {
            background-color: #e9ecef;
            transform: scale(1.05);
        }

        .animation-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .animated-title {
            font-size: 36px;
            color: #333;
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideIn {
        0% { transform: translateX(-100%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }

    /* Ajout d'une animation et effet de transition */
    .animated-title {
        font-family: 'Arial', sans-serif;
        padding: 15px;
        background: linear-gradient(135deg, #6a11cb, #2575fc); /* Dégradé violet/bleu */
        color: transparent;
        -webkit-background-clip: text;
        text-align: center;
        animation: slideIn 1s ease-out;
        text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.2);
    }

    .animated-title i {
        color: #fff;
    }
    #typee {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: #f5f9fc;
        border: 2px solid #002060;
        border-radius: 10px;
        padding: 10px;
        font-size: 1rem;
        color: #333;
        width: 60%;
        margin: 0 auto;
        outline: none;
        transition: all 0.3s ease;
    }

    /* Effet focus pour l'élément select */
    #typee:focus {
        border-color: #3751ff;
        background-color: #e8f0fe;
        box-shadow: 0 0 5px rgba(104, 151, 231, 0.5);
    }

    /* Style pour l'option (défini un fond et une couleur sur hover) */
    #typee option {
        padding: 10px;
        background-color: #ffffff;
        color: #333;
        font-size: 1rem;
    }

    #typee option:hover {
        background-color: #f0f8ff;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #typee {
            width: 80%;  /* Agrandir le select sur les petits écrans */
        }
    }
</style>
</head>
<body>

<div class="container mt-5">
    <!-- Animation de titre -->
    <div class="animation-container text-center mb-4">
    <h2 class="animated-title" style="font-size: 2.5rem; font-weight: 700; background: linear-gradient(135deg, rgb(185, 185, 185), rgb(116, 116, 116)); color: transparent; -webkit-background-clip: text; animation: slideIn 1s ease-out;">
        <i class="fas fa-search-plus" style="font-size: 2rem; margin-right: 10px; color:rgb(2, 82, 167);"></i>
        Rechercher des Entreprises par Type
    </h2>
</div>

    <!-- Formulaire pour choisir le type -->
    <form action="" method="GET" class="text-center">
    <div class="mb-4">
    <label for="typee" class="form-label" style="color:rgb(2, 88, 180); font-weight: bold;">Sélectionner un Type</label>
    <select name="typee" id="typee" class="form-control" style="width: 60%; margin: 0 auto; border: 2px solid #002060; border-radius: 10px; padding: 10px; font-size: 1rem; color: #333; background-color: #f5f9fc; outline: none; transition: all 0.3s ease;">
        <option value="">Choisir un type</option>
        <?php
        if ($result_types->num_rows > 0) {
            while ($row = $result_types->fetch_assoc()) {
                $selected = ($row['typee'] == $typee) ? 'selected' : '';
                echo "<option value='" . $row['typee'] . "' $selected>" . $row['typee'] . "</option>";
            }
        }
        ?>
    </select>
</div>
        <button type="submit" class="btn btn-primary btn-lg" style="background-color:rgb(0, 62, 185); border: none; border-radius: 10px; padding: 10px 20px;"><i class="fas fa-search-plus" style="font-size: 1rem; margin-right: 10px; color:rgb(255, 255, 255);"></i>Rechercher</button>
    </form>

    <!-- Affichage des pharmacies pour le type sélectionné -->
    <?php
    if ($typee && $result_pharmacies && $result_pharmacies->num_rows > 0) {
        echo "<h2 class='text-center mt-4' style='background: linear-gradient(45deg,rgb(70, 119, 216), #007bff); color: white; padding: 10px; border-radius: 10px;'>Entreprise pour le type <strong>$typee</strong></h2>";
        echo "<ul class='list-group mt-3'>";
        while ($row = $result_pharmacies->fetch_assoc()) {
            echo "<li class='list-group-item d-flex align-items-center' style='border-left: 5px solid rgb(1, 93, 192);'>
                    <a style='text-decoration: none; color: #333;' href='pharmacy_detail.php?id=" . $row['id'] . "' class='d-flex justify-content-between w-100'>
                        <div>
                            <i class='fas fa-building' style='color: rgb(1, 93, 192);'></i>
                            <strong>Nom:</strong> " . $row['nomphar'] . "  
                            <span class='mx-2'>|</span>
                            <i class='fas fa-map-marker-alt' style='color:rgb(1, 93, 192);'></i>
                            <strong>Etat:</strong> " . $row['statee'] . "  
                            <span class='mx-2'>|</span>
                            <i class='fas fa-map-signs' style='color: rgb(1, 93, 192);'></i>
                            <strong>Adresse:</strong> " . $row['address1'] . " (" . $row['zip'] . ")  
                            <span class='mx-2'>|</span>
                            <i class='fas fa-phone' style='color: rgb(1, 93, 192);'></i>
                            <strong>Téléphone:</strong> " . $row['tel'] . "  
                            <span class='mx-2'>|</span>
                            <i class='fas fa-cogs' style='color: rgb(1, 93, 192);'></i>
                            <strong>Type:</strong> " . $row['typee'] . "
                        </div>
                    </a>
                  </li>";
        }
        echo "</ul>";
    } elseif ($typee) {
        echo "<p class='text-center' style='color: rgb(1, 93, 192);'>Aucune Entreprise trouvée pour ce type.</p>";
    }
    ?>
</div>


</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
