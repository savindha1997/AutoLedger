<?php
include('dbconfig.php');
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die('Invalid invoice request');
}

$query = mysqli_query($con, "SELECT * FROM bykes WHERE id = $id LIMIT 1");
if (!$query || mysqli_num_rows($query) === 0) {
    die('Invoice data not found');
}

$row = mysqli_fetch_assoc($query);
$brn = mysqli_real_escape_string($con, $row['brn']);

$saleQuery = mysqli_query($con, "SELECT * FROM bykesale WHERE brn = '$brn' ORDER BY id DESC LIMIT 1");
if (!$saleQuery || mysqli_num_rows($saleQuery) === 0) {
    die('Sale record not found');
}

$sale = mysqli_fetch_assoc($saleQuery);

$businessName = 'KALHARA AUTO HOUSE';
$businessAddress = '6th Mile Post, Koswathumanana, Karandeniya';
$businessPhone = 'Phone: 077-6434861';
$businessInfoResult = mysqli_query($con, "SELECT * FROM business_info ORDER BY id ASC LIMIT 1");
if ($businessInfoResult && mysqli_num_rows($businessInfoResult) > 0) {
    $businessInfoRow = mysqli_fetch_assoc($businessInfoResult);
    if (!empty($businessInfoRow['b_name'])) {
        $businessName = strtoupper($businessInfoRow['b_name']);
    }
    if (!empty($businessInfoRow['b_address'])) {
        $businessAddress = $businessInfoRow['b_address'];
    }
    if (!empty($businessInfoRow['b_phone'])) {
        $businessPhone = 'Phone: ' . $businessInfoRow['b_phone'];
        if (!empty($businessInfoRow['b_phone_2'])) {
            $businessPhone .= ' / ' . $businessInfoRow['b_phone_2'];
        }
    }
}

function pdfEscape($text) {
    $text = str_replace('\\', '\\\\', $text);
    $text = str_replace('(', '\\(', $text);
    $text = str_replace(')', '\\)', $text);
    return $text;
}

function pdfTextWidth($text, $fontSize) {
    return strlen($text) * ($fontSize * 0.55);
}

function pdfWrapLines($text, $fontSize, $maxWidth) {
    $words = preg_split('/\s+/', trim($text));
    $lines = [];
    $current = '';

    foreach ($words as $word) {
        $candidate = $current === '' ? $word : $current . ' ' . $word;
        if (pdfTextWidth($candidate, $fontSize) <= $maxWidth) {
            $current = $candidate;
        } else {
            if ($current !== '') {
                $lines[] = $current;
            }
            $current = $word;
        }
    }

    if ($current !== '') {
        $lines[] = $current;
    }

    return $lines;
}

$invoiceDate = date('d/m/Y');
$referenceId = 'Ref-S-' . str_pad($sale['id'], 4, '0', STR_PAD_LEFT);
$purchasePrice = 'LKR ' . number_format($row['bprice'], 0, '.', ',');
$salePrice = 'LKR ' . number_format($sale['sprice'], 0, '.', ',');
$saleDate = $sale['sdate'] ?: 'N/A';
$sellerName = $row['bought_from'] ?: 'N/A';
$sellerAddress = $row['address'] ?: 'N/A';
$sellerNic = $row['nic'] ?: 'N/A';
$sellerPhone = $row['phone_number'] ?: 'N/A';
$vehicleModel = $row['year_make_model'] ?: 'N/A';
$vehicleChassis = $row['chassis_number'] ?: 'N/A';
$vehicleEngine = $row['engine_number'] ?: 'N/A';
$vehicleBrn = $row['brn'] ?: 'N/A';

$buyerName = $sale['buyer_name'] ?: 'N/A';
$buyerAddress = $sale['buyer_address'] ?: 'N/A';
$buyerNic = $sale['buyer_nic'] ?: 'N/A';
$buyerPhone = $sale['buyer_phone'] ?: 'N/A';

$layout = [
    ['font' => 'F2', 'size' => 18, 'text' => $businessName, 'spacing' => 22],
    ['font' => 'F1', 'size' => 10, 'text' => $businessAddress, 'spacing' => 16],
    ['font' => 'F1', 'size' => 10, 'text' => $businessPhone, 'spacing' => 32],
    ['font' => 'F2', 'size' => 14, 'text' => 'VEHICLE BUYER INVOICE', 'spacing' => 24],
    ['font' => 'F1', 'size' => 11, 'text' => 'Reference ID: ' . $referenceId, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'Sale Date: ' . $saleDate, 'spacing' => 24],
    ['font' => 'F2', 'size' => 12, 'text' => '1. Vehicle Details', 'spacing' => 18],
    ['font' => 'F1', 'size' => 11, 'text' => 'Registration Number: ' . $vehicleBrn, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'Year / Make / Model: ' . $vehicleModel, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'Chassis Number: ' . $vehicleChassis, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'Engine Number: ' . $vehicleEngine, 'spacing' => 24],
    ['font' => 'F2', 'size' => 12, 'text' => '2. Buyer Details', 'spacing' => 18],
    ['font' => 'F1', 'size' => 11, 'text' => 'Full Name: ' . $buyerName, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'Address: ' . $buyerAddress, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'NIC Number: ' . $buyerNic, 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'Phone Number: ' . $buyerPhone, 'spacing' => 28],
    ['font' => 'F2', 'size' => 12, 'text' => '3. Financial Summary', 'spacing' => 18],
    ['font' => 'F1', 'size' => 11, 'text' => 'Buyer Paid Price: ' . $salePrice, 'spacing' => 28],
    ['font' => 'F2', 'size' => 12, 'text' => '4. Declaration & Acknowledgement', 'spacing' => 18],
    ['font' => 'F1', 'size' => 11, 'text' => 'This invoice confirms the sale of the vehicle specified above by ' . $businessName . ' to the buyer.', 'spacing' => 18],
    ['font' => 'F1', 'size' => 11, 'text' => '- The buyer confirms that all necessary ownership documents, registration files, and legal records have been verified.', 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => '- The buyer confirms that the vehicle condition has been inspected and accepted.', 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => '- Both the buyer and the seller agree to the terms of this transaction without any objection or hesitation.', 'spacing' => 16],
    ['font' => 'F1', 'size' => 11, 'text' => 'This is a computer-generated document. No signature or business seal is required.', 'spacing' => 0],
];

$pageWidth = 595; // A4 width in points
$pageHeight = 842; // A4 height in points
$marginLeft = 42.5; // 1.5 cm margin
$marginRight = 42.5;
$maxTextWidth = $pageWidth - $marginLeft - $marginRight;
$marginTop = $pageHeight - 42.5;
$currentY = $marginTop;
$contentLines = [];
foreach ($layout as $rowLine) {
    $font = $rowLine['font'];
    $size = $rowLine['size'];
    $text = $rowLine['text'];
    $wrapLines = pdfWrapLines($text, $size, $maxTextWidth);

    foreach ($wrapLines as $line) {
        $escaped = pdfEscape($line);
        $contentLines[] = "BT /$font $size Tf $marginLeft $currentY Td ($escaped) Tj ET";
        $currentY -= $rowLine['spacing'];
    }
}

$contentStream = implode("\n", $contentLines);
$stream = "{$contentStream}\n";
$streamLength = strlen($stream);

$objects = [];
$objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj";
$objects[] = "2 0 obj\n<< /Type /Pages /Count 1 /Kids [3 0 R] >>\nendobj";
$objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>\nendobj";
$objects[] = "4 0 obj\n<< /Length $streamLength >>\nstream\n$stream\nendstream\nendobj";
$objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj";
$objects[] = "6 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj";

$xref = "xref\n0 " . (count($objects) + 1) . "\n";
$offsets = [sprintf('%010d %05d f ', 0, 65535)];
$offset = strlen("%PDF-1.4\n");

foreach ($objects as $object) {
    $offsets[] = sprintf('%010d %05d n ', $offset, 0);
    $offset += strlen($object) + 1;
}

$pdf = "%PDF-1.4\n";
foreach ($objects as $object) {
    $pdf .= $object . "\n";
}

$pdf .= $xref;
$pdf .= implode("\n", $offsets) . "\n";
$pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $offset . "\n%%EOF";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="buyer_invoice_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $row['brn']) . '.pdf"');

echo $pdf;
