<?php

require_once './dbconnection.php';

/* $artist_id = $_GET['artist_id']; */
$artist_id = 1;

$pdo = createDbConnection();

// Haetaan artistin nimi
$stmt = $pdo->prepare("SELECT Name FROM artists WHERE ArtistId = :artist_id");
$stmt->bindParam(':artist_id', $artist_id);
$stmt->execute();
$artist_name = $stmt->fetch(PDO::FETCH_COLUMN);

// Haetaan artistin albumit ja albumien kappaleet
$stmt = $pdo->prepare("
    SELECT albums.Title AS album_title, tracks.Name AS track_name
    FROM albums
    JOIN tracks ON albums.AlbumId = tracks.AlbumId
    WHERE albums.ArtistId = :artist_id
    ORDER BY albums.Title, tracks.Name
");
$stmt->bindParam(':artist_id', $artist_id);
$stmt->execute();
$albums = array();
$current_album_title = null;
$current_album = null;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $album_title = $row['album_title'];
    $track_name = $row['track_name'];
    if ($album_title != $current_album_title) {
        if ($current_album !== null) {
            $albums[] = $current_album;
        }
        $current_album_title = $album_title;
        $current_album = array(
            'title' => $album_title,
            'tracks' => array()
        );
    }
    $current_album['tracks'][] = $track_name;
}
if ($current_album !== null) {
    $albums[] = $current_album;
}

// Palautetaan vastaus JSON-muodossa
$response = array(
    'artist_name' => $artist_name,
    'albums' => $albums
);
echo json_encode($response);
