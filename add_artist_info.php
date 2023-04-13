<?php

require_once './dbconnection.php';

// Lue POST-pyynnön JSON-tietoja
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$pdo = createDbConnection();

try {
    $pdo->beginTransaction();

    // Lisää uusi artisti
    $stmt = $pdo->prepare("INSERT INTO artists (Name) VALUES (:name)");
    $stmt->bindParam(':name', $data['artist_name']);
    $stmt->execute();
    $artist_id = $pdo->lastInsertId();

    // Lisää uusi albumi
    $stmt = $pdo->prepare("INSERT INTO albums (Title, ArtistId) VALUES (:title, :artist_id)");
    $stmt->bindParam(':title', $data['album_title']);
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->execute();
    $album_id = $pdo->lastInsertId();

    // Lisää uusi kappale
    $stmt = $pdo->prepare("
        INSERT INTO tracks (Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice)
        VALUES (:name, :album_id, :media_type_id, :genre_id, :composer, :milliseconds, :bytes, :unit_price)
    ");
    $stmt->bindParam(':name', $data['track_name']);
    $stmt->bindParam(':album_id', $album_id);
    $stmt->bindParam(':media_type_id', $data['media_type_id']);
    $stmt->bindParam(':genre_id', $data['genre_id']);
    $stmt->bindParam(':composer', $data['composer']);
    $stmt->bindParam(':milliseconds', $data['milliseconds']);
    $stmt->bindParam(':bytes', $data['bytes']);
    $stmt->bindParam(':unit_price', $data['unit_price']);
    $stmt->execute();

    $pdo->commit();

    // Palauta vastaus onnistuneesta lisäyksestä
    $response = array(
        'status' => 'success',
        'message' => 'Artist, album and track added successfully'
    );
    echo json_encode($response);
} catch (PDOException $e) {
    // Palauta virheilmoitus, jos lisäys epäonnistui
    $response = array(
        'status' => 'error',
        'message' => $e->getMessage()
    );
    echo json_encode($response);
    $pdo->rollBack();
}
