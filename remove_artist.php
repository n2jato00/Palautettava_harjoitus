<?php

require_once './dbconnection.php';

// get the artist id from the request parameters
/* $artist_id = $_GET['artist_id']; */
$artist_id = 5;

try {
  $conn = createDbConnection();

  // start a transaction
  $conn->beginTransaction();

  // delete all invoice items associated with the artist's tracks

  // delete playlist_track entries associated with the artist's tracks
  $sql = "DELETE FROM playlist_track WHERE TrackId IN (SELECT TrackId FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id))";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':artist_id', $artist_id, PDO::PARAM_INT);
  $stmt->execute();

  $sql = "DELETE FROM invoice_items
            WHERE TrackId IN (SELECT TrackId FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id))";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':artist_id', $artist_id, PDO::PARAM_INT);
  $stmt->execute();

  // delete all tracks associated with the artist's albums
  $sql = "DELETE FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':artist_id', $artist_id, PDO::PARAM_INT);
  $stmt->execute();



  // delete all albums associated with the artist
  $sql = "DELETE FROM albums WHERE ArtistId = :artist_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':artist_id', $artist_id, PDO::PARAM_INT);
  $stmt->execute();

  // delete the artist
  $sql = "DELETE FROM artists WHERE ArtistId = :artist_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':artist_id', $artist_id, PDO::PARAM_INT);
  $stmt->execute();

  // commit the transaction
  $conn->commit();

  echo "Artist and associated data removed successfully.";
} catch (PDOException $e) {
  // rollback the transaction if an error occurred
  $conn->rollback();

  echo "Error: " . $e->getMessage();
}

// close the database connection
$conn = null;
