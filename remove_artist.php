<?php

require_once './dbconnection.php';

/* 
$artist_id = $_POST['artist_id']; */
$artist_id = 5;

try {
    $pdo = createDbConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("DELETE FROM playlist_track WHERE TrackId IN (SELECT TrackId FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id))");
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->execute();

    // Poistetaan artistin kappaleet myös invoice_items-taulusta
    $stmt = $pdo->prepare("DELETE FROM invoice_items WHERE TrackId IN (SELECT TrackId FROM tracks WHERE Composer = :artist_id)");
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->execute();

    $stmt = $pdo->prepare("DELETE FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id)");
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->execute();

      // Poista tiedot invoice_items-taulusta, joiden kappale kuuluu poistetun artistin albumeihin
      $stmt = $pdo->prepare("DELETE FROM invoice_items WHERE TrackId IN (SELECT TrackId FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id))");
      $stmt->bindParam(':artist_id', $artist_id);
      $stmt->execute();

    $stmt = $pdo->prepare("DELETE FROM albums WHERE ArtistId = :artist_id");
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->execute();

    $stmt = $pdo->prepare("DELETE FROM artists WHERE ArtistId = :artist_id");
    $stmt->bindParam(':artist_id', $artist_id);
    $stmt->execute();

    $pdo->commit();

    echo "Artist and related data removed successfully.";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error removing artist: " . $e->getMessage();
}
