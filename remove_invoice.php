<?php

require_once './dbconnection.php';

$input_data = json_decode(file_get_contents('php://input'), true);
$invoice_id = $input_data['invoice_id'];

$pdo = createDbConnection();
$stmt = $pdo->prepare("DELETE FROM invoice_item WHERE InvoiceId = :invoice_id");
$stmt->bindParam(':invoice_id', $invoice_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
  echo "Invoice items for invoice $invoice_id removed successfully.";
} else {
  echo "No invoice items found for invoice $invoice_id.";
}