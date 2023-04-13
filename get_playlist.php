<?php

require_once './dbconnection.php';

$input = json_decode(file_get_contents('php://input'), true);
$playlist_id = $input['playlist_id'];



$pdo = createDbConnection();
$stmt = $pdo->prepare("SELECT Name, Composer FROM Tracks WHERE TrackId IN (SELECT TrackId FROM playlist_track WHERE PlaylistId = :playlist_id)");
$stmt->bindParam(':playlist_id', $playlist_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    foreach ($stmt as $row) {
        echo '<h3>'.$row['Name'].'</h3>' . '<br>' . '<p>('. $row['Composer'] .')</p>' . '<br>';
    }
} else {
    echo "No tracks found for playlist $playlist_id.";
}
