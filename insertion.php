<?php

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: connexion.php"); // Redirection vers la page de connexion si non connecté
    exit();
}
try {
    $db = new PDO("mysql:host=localhost;dbname=examen", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Suppression d'un candidat
if (isset($_GET['delete'])) {
    $candidatId = $_GET['delete'];
    try {
        // Supprimer le candidat avec l'id spécifié
        $stmt = $db->prepare("DELETE FROM candidat WHERE Id_cand = :id");
        $stmt->bindParam(':id', $candidatId);
        $stmt->execute();
       
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Ajout d'un nouveau candidat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Nom = $_POST['Nom'];
    $Prenom = $_POST['Prenom'];
    $Datnais = $_POST['Datnais'];
    $ville = $_POST['ville'];
    $sexe = $_POST['sexe'];
    $codfil = $_POST['codfil'];

    try {
        $stmt = $db->prepare("INSERT INTO candidat (Nom, Prenom, Datnais, ville, sexe, codfil) VALUES (:Nom, :Prenom, :Datnais, :ville, :sexe, :codfil)");
        $stmt->bindParam(':Nom', $Nom);
        $stmt->bindParam(':Prenom', $Prenom);
        $stmt->bindParam(':Datnais', $Datnais);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':sexe', $sexe);
        $stmt->bindParam(':codfil', $codfil);
        $stmt->execute();
        // Redirection après l'ajout
        header("Location: insertion.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Récupération de la liste des candidats
try {
    $stmt = $db->prepare("SELECT Id_cand, Nom, Prenom, Datnais, ville, sexe, codfil FROM candidat");
    $stmt->execute();
    $candidat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enregistrement des données</title>
</head>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }
</style>
<body>
    <h1>Interface 1</h1>
    <form action="insertion.php" method="post">
        <fieldset>
            <legend><b>Vos coordonnées</b></legend>
            <table>
                <tr><td>Nom:</td><td><input type="text" name="Nom" size="50" maxlength="50"></td></tr>
                <tr><td>Prénom:</td><td><input type="text" name="Prenom" size="50" maxlength="50"></td></tr>
                <tr><td>Date de naissance:</td><td><input type="date" name="Datnais" size="50" maxlength="50"></td></tr>
                <tr><td>Ville:</td><td><input type="text" name="ville" size="50" maxlength="50"></td></tr>
                <tr><td>Sexe:</td>
                    <td>
                        <select name="sexe" required>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </td>
                </tr>
				<tr><td>
				<label for="Code_filière"> Code filière: </label>
				<select id="Code_filière" name="codfil">
				<?php
				try{
					$db = new PDO("mysql:host=localhost;dbname=examen", "root", "");
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
					//Requête SQL pour récupérer les codes de filière
					$stmt = $db->prepare("SELECT codfil FROM filiere");
                    $stmt->execute();
					//Vérication du nombre de lignes récupérées
                    $rowCount = $stmt->rowCount();

					if ($rowCount > 0) {
						//Affichage des options de la liste déroulante
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							echo "<option value= '". $row["codfil"]. "'>". $row["codfil"]. "</option>";
						}
					}else{
						echo "<option>Aucune filière trouvée</option>";
					
					} 
				} catch (PDOException $e) {
					echo "Connection failed: " . $e->getMessage();
					die();
				}
				?>
				</select><br><br>
				</tr></td>
				
                <tr><td><input type="reset" name="effacer" value="Effacer" style="background-color: red; color: white; border: white;"></td>
                    <td><input type="submit" name="enregistrer" value="Enregistrer" style="background-color: green; color: white; border: white;"></td>
                </tr>
            </table>
        </fieldset>
    </form>

    <h1>Interface 2</h1>

    <table>
        <thead>
            <tr>
                <th>Id_cand</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de naissance</th>
                <th>Ville</th>
                <th>Sexe</th>
                <th>Code filière</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidat as $cand): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cand['Id_cand']); ?></td>
                    <td><?php echo htmlspecialchars($cand['Nom']); ?></td>
                    <td><?php echo htmlspecialchars($cand['Prenom']); ?></td>
                    <td><?php echo htmlspecialchars($cand['Datnais']); ?></td>
                    <td><?php echo htmlspecialchars($cand['ville']); ?></td>
                    <td><?php echo htmlspecialchars($cand['sexe']); ?></td>
                    <td><?php echo htmlspecialchars($cand['codfil']); ?></td>
                    <td>
                        <form style="display:inline;" action="insertion.php" method="get">
                            <input type="hidden" name="delete" value="<?php echo $cand['Id_cand']; ?>">
                            <input type="submit" value="Supprimer" style="color: white; background-color: red; border: none; padding: 5px 10px;">
                        </form>
                        <form style="display:inline;" action="edit.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $cand['Id_cand']; ?>">
                            <input type="submit" name="update" value="Modifier" style="color: white; background-color: blue; border: none; padding: 5px 10px;">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
