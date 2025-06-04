<?php
// filepath: c:\wamp64\www\ecommerce website\facture.php
require('libs/fpdf.php');
include 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Accès refusé');
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$stmt = $conn->prepare("SELECT * FROM `orders` WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Commande introuvable');
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Facture de commande',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);

$pdf->Cell(0,10,'Date : '.$order['placed_on'],0,1);
$pdf->Cell(0,10,'Nom : '.$order['name'],0,1);
$pdf->Cell(0,10,'Email : '.$order['email'],0,1);
$pdf->Cell(0,10,'Adresse : '.$order['address'],0,1);
$pdf->Cell(0,10,'Methode de paiement : '.$order['method'],0,1);
$pdf->Cell(0,10,'Produits : '.$order['total_products'],0,1);
$pdf->Cell(0,10,'Prix total : $'.$order['total_price'],0,1);
$pdf->Cell(0,10,'Statut du paiement : '.$order['payment_status'],0,1);

$pdf->Output('I', 'facture_'.$order_id.'.pdf');
exit;