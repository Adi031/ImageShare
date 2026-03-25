<?php
include 'db.php';

$conn->query("SET FOREIGN_KEY_CHECKS=0;");
$conn->query("TRUNCATE TABLE images;");
$conn->query("SET FOREIGN_KEY_CHECKS=1;");

if ($conn->query("ALTER TABLE images DROP COLUMN file_path, ADD COLUMN image_data LONGBLOB NOT NULL, ADD COLUMN mime_type VARCHAR(255) NOT NULL;")) {
    echo "Migration successful\n";
} else {
    echo "Migration failed: " . $conn->error . "\n";
}
?>
