<?php
// Connexion à la base de données
session_start();
require("connect.php"); // Assurez-vous que ce fichier contient la connexion à la base de données
$idphar=$_GET["id"];
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}
//Récupérer les pharmacies pour un état sélectionné, si l'état est défini dans l'URL
$idphar = isset($_GET['id']) ? $_GET['id'] : '';
// Récupérer les médicaments depuis la table pharmacy_medicaments
$sql = "SELECT * FROM pharmacie_livraison where pharmacy_id=$idphar AND email_sent='1'"; //where pharmacy_id=$idphar";
$result = $conn->query($sql);

$total_quantite = 0;
$total_prix = 0;
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
    <link rel="stylesheet" href="medfind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        nav {
            background-color: rgb(218, 218, 218);
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
        .navbar-nav .nav-link {
            color: #fff;
        }
        .navbar-nav .nav-link.active {
            color: #ffc107;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .btn-print {
            background-color:rgb(243, 169, 58);
            color: #fff;
            border:none;
            border-radius:8px;
        }
        .kanit-regular {
  font-family: "Kanit", serif;
  font-weight: 400;
  font-style: normal;
}
        
        .footer {
            background-color:rgb(227, 235, 243);
            color: #fff;
            padding: 20px 0
            text-align: center;
            margin-top: 50px;
        }
        .footer a {
            color: #ffc107;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .navbar-brand {
            color: #fff;
            font-weight: bold;
            font-size: 30px;
        }
        .im {
            margin-left: 30px;
            margin-top: -10px;
        }
        footer a {
            color: black;
            text-decoration: none;
        }
        .container {
            padding: 15px;
        }
        /* Style Global */
h2 {
    font-size: 2rem;
    color: #007bff;
    margin-bottom: 30px;
    text-align: center;
}

/* Table Styles */
.table th, .table td {
    padding: 12px;
    vertical-align: middle;
    text-align: center;
}

.table-striped tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1);
    transform: scale(1.02);
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table th {
    background-color: #007bff;
    color: white;
}

.table td {
    background-color: #f9f9f9;
}

/* Responsive Table */
.table-responsive {
    overflow-x: auto;
    margin-top: 20px;
}

/* Section Totale */
.total-section {
    background: linear-gradient(145deg, #f0f8ff, #d9eaff);
    border-radius: 12px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-top: 30px;
}

.total-section h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}

/* Buttons */
.btn-primary, .btn-success, .btn-outline-primary, .btn-outline-success {
    font-size: 5px;
    padding: 2px 5px;
    border-radius: 5px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.btn-success {
    background-color: #28a745;
    color: white;
    border: none;
}

.btn-success:hover {
    background-color: #218838;
    transform: scale(1.05);
}

.btn-outline-primary, .btn-outline-success {
    background-color: transparent;
    border: 2px solid;
    color: #007bff;
}

.btn-outline-primary:hover, .btn-outline-success:hover {
    background-color: #007bff;
    color: white;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
}

.btn-lg {
    font-size: 14px;
    font-weight: bold;
    padding: 12px 20px;
}

/* Back Button */
.back-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 20px;
    transition: background-color 0.3s ease;
}

.back-button:hover {
    background-color: #0056b3;
}

.back-button i {
    margin-right: 8px;
}

/* Custom Styling */
.pm {
    padding-left: 0px;
    color: rgba(172, 145, 235, 0.48);
}

.jn {
    padding-right: 20px;
}

/* Total Section Styling */
.total-section {
    background-color: #f4f7fb;
    border: 1px solid #e0e5ec;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Additional Button Styling */
.btn-download, .btn2 {
    background-color: #27ae60;
    color: #fff;
    border-color: #27ae60;
}

.btn2:hover {
    background-color: rgb(19, 148, 73);
    border-color: rgb(19, 148, 73);
}

.bt {
    background-color: rgb(34, 108, 219);
    color: #fff;
    margin-left: 440px;
}

/* Miscellaneous Styles */
.table th, .table td {
    vertical-align: middle;
    text-align: center;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

/* Style for Links */
.btn-link {
    font-size: 1.1rem;
    color: #007bff;
    text-decoration: none;
}

.btn-link:hover {
    text-decoration: underline;
}

    </style>
</head>
<body style="background-color: #ffffff; " class="kanit-regular">
<nav class="navbar navbar-expand-lg" style=" background-color: #ffffff; ">
        <div class="container">
          <a class="navbar-brand d-flex align-items-center" href="pharm.html">
            <img src="logtest.webp" alt="Logo PharmFind" class="me-2" style="width: 40px; height: 40px; border-radius:50%;">
            <h3 class="mb-0" style=" color:#002060;">ShopEase</h3>
          </a>
      </nav>
      <div class="container py-5">
    <a href="pharm.html" class="btn btn-link text-dark mb-4 d-flex align-items-center" style="color:#007bff;">
        <i class="fa fa-arrow-left mr-2" style="color:#007bff;"></i>    Retour à la page précédente
    </a>
    <h2 class="display-3 font-weight-light mb-5 text-dark pl-4" >Liste des Produits Acheétés</h2>
    
    <?php
    if ($result->num_rows > 0) {
        echo '<div class="table-responsive shadow-lg rounded-lg overflow-hidden">';
        echo '<table class="table table-hover table-bordered table-light">';
        echo '<thead class="bg-gradient-primary text-white"><tr><th>Nom</th><th>Quantité</th><th>Total</th><th>Date</th></tr></thead><tbody>';
        
        while ($row = $result->fetch_assoc()) {
            $total_item = $row['total'];
            echo "<tr class='table-row'>";
            echo "<td>" . htmlspecialchars($row['medicament_nom']) . "</td>";
            echo "<td>" . $row['medicament_quantite'] . "</td>";
            echo "<td>" . number_format($total_item, 2, ',', ' ') . " TND</td>";
            echo "<td>" . $row['datee'] . "</td>";
            echo "</tr>";
            
            $total_quantite += $row['medicament_quantite'];
            $total_prix += $total_item;
        }
        
        echo '</tbody></table>';
        echo '</div>';
    } else {
        echo "<p class='text-center text-muted'>Aucun médicament disponible.</p>";
    }
    ?>

    <div class="total-section mt-5 p-4 bg-gradient-light rounded-lg shadow-lg">
        <h3 class="font-weight-bold text-dark">Total des Produits</h3>
        <p><strong class="text-primary">Quantité totale :</strong> <?php echo $total_quantite; ?></p>
        <p><strong class="text-primary">Total des prix :</strong> <?php echo number_format($total_prix, 2, ',', ' ') . " TND"; ?></p>
    </div>
    
    <div class="col-12 text-center pt-4">
        <button class="btn btn-primary btn-lg px-5 py-3 rounded-pill" onclick="window.print();">
            <i class="fa fa-print"></i> Imprimer la Facture
        </button>
        <button class="btn btn-success btn-lg px-5 py-3 rounded-pill ml-3" onclick="downloadCart();">
            <i class="fa fa-download"></i> Télécharger en PDF
        </button>
    </div>
</div>

    <div class="text-center mt-4">
            <p class="footer-text" style="color: #b0b0b0; font-size: 14px;">ShopEase &copy; 2024 | Designed by <strong>Ilyes</strong></p>
        </div>

</body>
</html>

<?php
// Fermer la connexion
$conn->close();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
    async function downloadCart() {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        // Ajouter un logo en haut du PDF avec border-radius
        const logo = new Image();
        logo.src = "logtest.webp"; // Remplacez par le chemin de votre logo
        logo.onload = function () {
            // Ajouter le logo avec un border-radius de 50%
            pdf.addImage(logo, 'WEBP', 0, 0, 30, 30); // Positionner le logo en haut à gauche
            pdf.setFont("helvetica", "bold");
            pdf.setFontSize(16);

            // Titre avec couleur
            pdf.setTextColor(0, 32, 96); // Bleu foncé pour le titre
            pdf.text("Liste des Produits Achétés", 60, 20); // Position du titre

            // Récupérer les données du tableau
            const rows = [];
            const table = document.querySelector("table");

            if (table) {
                table.querySelectorAll("tr").forEach((row, rowIndex) => {
                    const cols = Array.from(row.querySelectorAll("th, td")).map((col) => col.innerText.trim());
                    // Ne pas inclure la première ligne si ce sont les en-têtes
                    if (rowIndex !== 0) {
                        rows.push(cols);
                    }
                });
            }

            // Ajouter le tableau avec design
            pdf.autoTable({
                head: [['Nom', 'Quantité', 'Prix Total', 'Date']],
                body: rows,
                startY: 50, // Position après le titre et le logo
                theme: 'grid', // Thème pour les bordures du tableau
                headStyles: {
                    fillColor: [0, 32, 96], // Couleur de fond des en-têtes
                    textColor: [255, 255, 255], // Couleur du texte dans les en-têtes
                    fontSize: 12,
                    font: 'helvetica',
                    halign: 'center',
                    valign: 'middle',
                },
                bodyStyles: {
                    fontSize: 10,
                    font: 'helvetica',
                    halign: 'center', // Alignement du texte dans les cellules
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240], // Couleur de fond des lignes alternées
                },
                columnStyles: {
                    0: { cellWidth: 40 }, // Ajuster la largeur de la première colonne (Nom)
                    1: { cellWidth: 30 }, // Ajuster la largeur de la deuxième colonne (Quantité)
                    2: { cellWidth: 40 }, // Ajuster la largeur de la troisième colonne (Prix Total)
                    3: { cellWidth: 50 }, // Ajuster la largeur de la quatrième colonne (Date)
                },
            });

            // Ajouter les totaux avec un style différent
            const totalSection = document.querySelector(".total-section");
            if (totalSection) {
                const totals = Array.from(totalSection.querySelectorAll("p")).map((total) => total.innerText);
                let y = pdf.lastAutoTable.finalY + 10; // Position sous le tableau
                pdf.setFontSize(12);
                pdf.setFont("helvetica", "bold");
                
                // Mise en valeur du prix total avec une couleur différente
                totals.forEach((total, index) => {
                    if (total.includes("Total des prix")) {
                        pdf.setTextColor(255, 0, 0); // Rouge pour le prix total
                    } else {
                        pdf.setTextColor(0, 0, 0); // Noir pour les autres totaux
                    }
                    pdf.text(total, 10, y);
                    y += 10;
                });
            }

            // Télécharger le fichier PDF
            pdf.save("Liste_Produits.pdf");
        };
    }
</script>
