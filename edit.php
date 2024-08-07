<?php
session_start();
try {
    $db = new PDO("mysql:host=localhost;dbname=examen", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Échec de la connexion : " . $e->getMessage();
    die();
}

// Vérification si l'identifiant du candidat à modifier est passé en paramètre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Récupération de l'ID du candidat à modifier
    $Id_cand = $_POST['id'];
    // Requête pour récupérer les données du candidat avec l'ID spécifié
    $stmt = $db->prepare("SELECT * FROM candidat WHERE id_cand = :id");
    $stmt->bindParam(':id', $Id_cand);
    $stmt->execute();
    $candidat = $stmt->fetch(PDO::FETCH_ASSOC);
    // Vérifier si le candidat a été trouvé
    if ($candidat) {
        // Remplir les variables avec les données du candidat pour pré-remplir le formulaire
        $Nom = $candidat['Nom'];
        $Prenom = $candidat['Prenom'];
        $Datnais = $candidat['Datnais'];
        $ville = $candidat['ville'];
        $sexe = $candidat['sexe'];
        $codfil = $candidat['codfil'];
    } else {
        echo "Aucun candidat trouvé avec l'ID spécifié.";
    }
}

// Si le formulaire de mise à jour est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Récupération des nouvelles données du formulaire
    $Id_cand = $_POST['id'];
    $Nom = $_POST['Nom'];
    $Prenom = $_POST['Prenom'];
    $Datnais = $_POST['Datnais'];
    $ville = $_POST['ville'];
    $sexe = $_POST['sexe'];
    $codfil = $_POST['codfil'];

    // Requête pour mettre à jour les données du candidat
    $stmt_update = $db->prepare("UPDATE candidat SET Nom = :Nom, Prenom = :Prenom, Datnais = :Datnais, ville = :ville, sexe = :sexe, codfil = :codfil WHERE id_cand = :Id_cand");
    try {
        // Exécution de la requête de mise à jour
        $stmt_update->execute([
            'Nom' => $Nom,
            'Prenom' => $Prenom,
            'Datnais' => $Datnais,
            'ville' => $ville,
            'sexe' => $sexe,
            'codfil' => $codfil,
            'Id_cand' => $Id_cand
        ]);
        echo "Données mises à jour avec succès.";
        $_SESSION['update_success'] = true;
        $_SESSION['Nom'] = $Nom;
        $_SESSION['Prenom'] = $Prenom;
        $_SESSION['Datnais'] = $Datnais;
        $_SESSION['ville'] = $ville;
        $_SESSION['sexe'] = $sexe;
        $_SESSION['codfil'] = $codfil;
        // Redirection vers insertion.php
        header("Location: insertion.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification des informations du candidat</title>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($Id_cand); ?>">
    <label for="Nom">Nom:</label>
    <input type="text" id="Nom" name="Nom" value="<?php echo htmlspecialchars($Nom); ?>"><br><br>

    <label for="Prenom">Prénom:</label>
    <input type="text" id="Prenom" name="Prenom" value="<?php echo htmlspecialchars($Prenom); ?>"><br><br>

    <label for="Datnais">Date de naissance:</label>
    <input type="date" id="Datnais" name="Datnais" value="<?php echo htmlspecialchars($Datnais); ?>"><br><br>

    <label for="ville">Ville:</label>
    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($ville); ?>"><br><br>

    <label for="sexe">Sexe:</label>
    <select name="sexe" id="sexe">
        <option value="M" <?php if ($sexe == 'M') echo 'selected'; ?>>Masculin</option>
        <option value="F" <?php if ($sexe == 'F') echo 'selected'; ?>>Féminin</option>
    </select><br><br>

    <label for="codfil">Code filière:</label>
                    <td>
                        <select name="codfil" required>
                            <option value="SIL">SIL</option>
                            <option value="RIT">RIT</option>
                            <option value="AGRO">AGRO</option>
                            <option value="AGE">AGE</option>
                        </select>
                    </td>
                </tr><br>
                
    <input type="submit" name="submit" value="Mettre à jour">
</form>
</body>
</html>