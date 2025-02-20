<?php
// Connexion à la base de données
require("connect.php");

if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

$response = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomphar = $_POST['nomphar'];
    $statee = $_POST['statee'];
    $nomproduit = $_POST['nomproduit'];

    // Cas où seul le nom du produit est renseigné (et l'entreprise et l'état sont vides)
    if ($nomproduit && !$nomphar && !$statee) {
        // Requête pour récupérer tous les produits avec le même nom
        $sql = "SELECT med.*, phar.nomphar, phar.address1, phar.address2, phar.statee, phar.zip, phar.tel 
                FROM med 
                JOIN phar ON med.id = phar.id 
                WHERE med.nom = ? 
                ORDER BY med.prix ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nomproduit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Tableau pour stocker les produits correspondants
            $produits = [];
            while ($row = $result->fetch_assoc()) {
                $produits[] = [
                    'produit_nom' => $row['nom'],
                    'imagee' => $row['imagee'],
                    'prix' => $row['prix'],
                    'nomphar' => $row['nomphar'],
                    'address1' => $row['address1'],
                    'address2' => $row['address2'],
                    'statee' => $row['statee'],
                    'zip' => $row['zip'],
                    'tel' => $row['tel']
                ];
            }
            // Réponse avec tous les produits
            $response = json_encode(['produits' => $produits]);
        } else {
            $response = json_encode(['error' => 'Aucun produit trouvé']);
        }
    }
    // Cas où l'entreprise et l'état sont renseignés (sans produit)
    elseif (!$nomproduit && $nomphar && $statee) {
        // Requête pour récupérer les informations de l'entreprise
        $sql = "SELECT * FROM phar WHERE nomphar = ? AND statee = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nomphar, $statee);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = json_encode([
                'nomphar' => $row['nomphar'],
                'statee' => $row['statee'],
                'address1' => $row['address1'],
                'address2' => $row['address2'],
                'tel' => $row['tel'],
                'zip' => $row['zip']
            ]);
        } else {
            $response = json_encode(['error' => 'Aucune entreprise trouvée']);
        }
    }
    // Cas où les trois champs sont renseignés (produit + entreprise + état)
    elseif ($nomproduit && $nomphar && $statee) {
        // Requête pour récupérer les informations de l'entreprise et du produit
        $sql = "SELECT phar.*, med.nom AS produit_nom, med.imagee, med.prix FROM phar
                LEFT JOIN med ON phar.id = med.id
                WHERE phar.nomphar = ? AND phar.statee = ? AND med.nom = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nomphar, $statee, $nomproduit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = json_encode([
                'nomphar' => $row['nomphar'],
                'statee' => $row['statee'],
                'address1' => $row['address1'],
                'address2' => $row['address2'],
                'tel' => $row['tel'],
                'zip' => $row['zip'],
                'produit_nom' => $row['produit_nom'],
                'imagee' => $row['imagee'],
                'prix' => $row['prix']
            ]);
        } else {
            $response = json_encode(['error' => 'Aucune entreprise ou produit trouvé']);
        }
    }

    $stmt->close();
    $conn->close();

    // Ajouter l'en-tête pour indiquer que la réponse est en JSON
    header('Content-Type: application/json');
    echo $response;
    exit;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Arrière-plan d'image */
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://source.unsplash.com/1600x900/?pharmacy'); /* Image d'arrière-plan dynamique */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .kanit-regular {
  font-family: "Kanit", serif;
  font-weight: 400;
  font-style: normal;
}
        /* Style pour le container du chatbot */
        .container {
            margin-top: 100px;
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
        
        .chatbox {
            background-color: rgba(255, 255, 255, 0.85); /* Fond semi-transparent */
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 0 auto;
            overflow-y: auto;
        }

        /* En-tête du chatbot */
        .chat-header {
            background-color: #007bff;
            padding: 20px;
            color: #fff;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }

        /* Messages du chatbot */
        .chat-message {
            background-color: #f9f9f9;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
            opacity: 0; /* Initial state for animation */
            animation: fadeIn 1s forwards; /* Animation applied */
        }

        /* Messages du bot */
        .message-bot {
            background-color: #007bff;
            color: #fff;
        }

        /* Messages de l'utilisateur */
        .message-user {
            background-color: #e9e9e9;
            color: #333;
        }

        /* Formulaire de chat */
        .form-control, .btn {
            border-radius: 10px;
            padding: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        /* Animation pour les messages */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Style footer */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px;
            background-color: #007bff;
            color: white;
        }

        .footer-text {
            font-size: 14px;
        }

        /* Style des icônes dans les messages */
        .chat-message i {
            margin-right: 10px;
        }

        .message-bot i {
            color: #fff;
        }

        .message-user i {
            color: #333;
        }

        /* Formulaire de soumission */
        .chat-form {
            margin-top: 20px;
        }

        /* Modifier les champs d'entrée */
        .chat-form input,
        .chat-form button {
            width: 100%;
            margin-bottom: 10px;
        }

        /* Image d'arrière-plan pour un effet plus agréable */
        .chatbox {
            background-color: rgba(255, 255, 255, 0.8); /* Légèrement transparent */
        }
        
    </style>
</head>
<body class="kanit-regular">
<nav class="navbar navbar-expand-lg "  style=" box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); background-color: #f7f7f7; margin-top:-100px;">
        <div class="container">
          <a class="navbar-brand d-flex align-items-center" href="index.html">
            <img src="logtest.webp" alt="Logo PharmFind" class="me-2" style="width: 40px; height: 40px; border-radius:50%;">
            <h3 class="mb-0 kanit-regular" style=" font-size: 30px; color: #002060;">ShopEase</h3>
          </a>
        </div>
      </nav>
      <div class="container mt-5">
    <div class="chatbox">
        <!-- Espace pour le logo -->
        

        <div class="chat-header">
            <h3><i class="fas fa-comment-dots"></i> Chatbot - Informations de l'Entreprise</h3>
        </div>

        <div id="messages" class="mt-4">
            <div class="chat-message message-bot">
                <i class="fas fa-robot"></i> Bonjour ! Comment puis-je vous aider aujourd'hui ?
            </div>
        </div>

        <div class="chat-form">
            <form id="chat-form">
                <div class="form-group">
                    <label for="nomproduit"><i class='fas fa-box input-icon'></i> Nom du Produit</label>
                    <input type="text" class="form-control" id="nomproduit" placeholder="Entrez le nom du produit (facultatif)">
                </div>
                <div class="form-group" style="display: none;">
                    <label for="nomphar"><i class="fas fa-building"></i> Nom de l'Entreprise</label>
                    <input type="text" class="form-control" id="nomphar" placeholder="Nom de l'entreprise (facultatif)">
                </div>
                <div class="form-group" style="display: none;">
                    <label for="statee"><i class="fas fa-map-marker-alt"></i> État</label>
                    <input type="text" class="form-control" id="statee" placeholder="Entrez l'état (facultatif)">
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById("chat-form").addEventListener("submit", function(e) {
        e.preventDefault();

        const nomphar = document.getElementById("nomphar").value;
        const statee = document.getElementById("statee").value;
        const nomproduit = document.getElementById("nomproduit").value;

        // Afficher le message de l'utilisateur
        const userMessage = `<div class="chat-message message-user"><i class="fas fa-user"></i><strong>Vous:</strong> Nom du produit: ${nomproduit}</div>`;
        document.getElementById("messages").innerHTML += userMessage;

        // Vérifier si les champs sont remplis
        // Envoyer la requête AJAX à PHP
        $.ajax({
            type: "POST",
            url: "", // La même page
            data: { 
                nomphar: nomphar,
                statee: statee,
                nomproduit: nomproduit
            },
            dataType: 'json', // Spécifier que la réponse est en JSON
            success: function(response) {
                let botMessage = '';

                if (response.error) {
                    botMessage = `<div class="chat-message message-bot"><i class="fas fa-times-circle"></i> ${response.error}</div>`;
                } else {
                    if (response.produits && response.produits.length > 0) {
                        botMessage = `<div class="chat-message message-bot"><i class="fas fa-info-circle"></i> Informations des produits :</div>`;
                        response.produits.forEach(function(produit) {
                            botMessage += `
                                <div class="chat-message message-bot">
                                    <i class='fas fa-box input-icon'></i> <strong>Nom du produit</strong>: ${produit.produit_nom}<br>
                                    <img src="${produit.imagee}" alt="${produit.produit_nom}" width="100" style='border-radius: 10px;' /><br>
                                    <i class="fas fa-dollar-sign"></i> <strong>Prix</strong>: ${produit.prix} TND<br>
                                    <i class="fas fa-building"></i> <strong>Nom de l'entreprise</strong>: ${produit.nomphar}<br>
                                    <i class="fas fa-phone"></i> <strong>Téléphone</strong>: ${produit.tel}<br>
                                    <i class="fas fa-building"></i> <strong>Adresse</strong>: ${produit.address1} ${produit.address2}<br>
                                    <i class="fas fa-map-marker-alt"></i> <strong>Code Postal</strong>: ${produit.zip}<br>
                                    <i class="fas fa-map"></i> <strong>État</strong>: ${produit.statee}
                                </div>
                            `;
                        });
                    }
                }

                // Ajouter le message du bot avec l'animation d'atterrissage
                const botMessageElement = $(botMessage).hide().appendTo("#messages");
                botMessageElement.fadeIn(1000).addClass('chat-message');

                // Scroll vers le bas
                document.getElementById("messages").scrollTop = document.getElementById("messages").scrollHeight;
            },
            error: function(xhr, status, error) {
                console.error("Erreur AJAX : ", error); // Affiche l'erreur dans la console
                let botMessage = `<div class="chat-message message-bot"><i class="fas fa-times-circle"></i> Désolé, une erreur est survenue lors de la récupération des informations.</div>`;
                document.getElementById("messages").innerHTML += botMessage;
                document.getElementById("messages").scrollTop = document.getElementById("messages").scrollHeight;
            }
        });
    });
</script>

</body>
</html>
