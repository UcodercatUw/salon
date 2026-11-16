<?php
/**
 * Run this file once to create the ratings table.
 * Navigate to: http://localhost/salon/install_ratings.php
 */
require_once __DIR__ . '/config/database.php';

$database = new Database();
$conn = $database->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    rating TINYINT NOT NULL,
    review TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_service (user_id, service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    $conn->exec($sql);
    echo "<h2 style='color:green;font-family:sans-serif;'>&#10004; Table 'ratings' created successfully!</h2>";
    echo "<p style='font-family:sans-serif;'><a href='index.php'>Go to site</a> | <a href='admin/index.php'>Go to admin</a></p>";
} catch (PDOException $e) {
    echo "<h2 style='color:red;font-family:sans-serif;'>Error: " . $e->getMessage() . "</h2>";
}
?>
