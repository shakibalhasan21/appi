
<?php
include_once('function.php');

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get API ID from the query parameter sent by JavaScript
    $apiId = isset($_GET['apiId']) ? intval($_GET['apiId']) : 0;

    // Fetch FullName based on the given API ID
    $stmt = $pdo->prepare("SELECT FullName FROM tblusers WHERE ID = :apiId");
    $stmt->execute(['apiId' => $apiId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the result as JSON
    echo json_encode($row);

} catch (PDOException $e) {
    // Return error in case of failure
    echo json_encode(['error' => $e->getMessage()]);
}
?>
