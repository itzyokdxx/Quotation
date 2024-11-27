<?php
require('includes/fpdf/fpdf.php'); // Ensure this path matches where you've stored FPDF 1.7
include_once('includes/config.php');

// Output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

$id = $_GET["id"];

// Invoice query
$query1 = "SELECT * FROM invoices WHERE invoice = '" . $mysqli->real_escape_string($id) . "'";
$result1 = mysqli_query($mysqli, $query1);

if ($result1) {
    while ($row = mysqli_fetch_assoc($result1)) {
        $invoice_date = $row['invoice_date'];
        $invoice_due_date = $row['invoice_due_date'];
        $subtotal = $row['subtotal'];
        $shipping = $row['shipping'];
        $discount = $row['discount'];
        $vat = $row['vat'];
        $total = $row['total'];
        $notes = $row['notes'];
    }
}

// Customer query
$query2 = "SELECT * FROM customers WHERE invoice = '" . $mysqli->real_escape_string($id) . "'";
$result2 = mysqli_query($mysqli, $query2);

if ($result2) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $name = $row['name'];
        $email = $row['email'];
        $address_1 = $row['address_1'];
        $address_2 = $row['address_2'];
        $town = $row['town'];
        $county = $row['county'];
        $postcode = $row['postcode'];
        $phone = $row['phone'];
        $name_ship = $row['name_ship'];
        $address_1_ship = $row['address_1_ship'];
        $address_2_ship = $row['address_2_ship'];
        $town_ship = $row['town_ship'];
        $county_ship = $row['county_ship'];
        $postcode_ship = $row['postcode_ship'];
    }
}

// Invoice items query
$query3 = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli->real_escape_string($id) . "'";
$result3 = mysqli_query($mysqli, $query3);

$items = [];
if ($result3) {
    while ($row = mysqli_fetch_assoc($result3)) {
        $items[] = array(
            'description' => $row['product'],
            'quantity' => $row['qty'],
            'unit_price' => $row['price'],
        );
    }
}

// Define VAT percentage (adjust based on your requirements)
define('VAT_PERCENTAGE', 0.12); // Example: 12% VAT

// Calculate subtotal and tax
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['quantity'] * $item['unit_price'];
}
$taxAmount = $subtotal * VAT_PERCENTAGE; // Calculate tax
$totalAmountWithTax = $subtotal + $taxAmount; // Add tax to total

// Sample data for the invoice
$invoiceData = [
    'company' => [
        'Rez.N8 Digital Design Studio',
        '#24 Ruby Street',
        'Severina Diamond Subd. Km 18',
        'Paranaque City 1700 Philippines',
        'Telephone Nos. (632) 8821 6793; (632) 8546 4628'
    ],
    'customer' => [
        $name,
        $address_1_ship . ' ' . $address_2_ship,
        $town_ship . ',' . $county_ship . ', ' . $postcode_ship
    ],
    'invoice_number' => 'RF-' . $id,
    'date' => $invoice_date,
    'due_date' => $invoice_due_date,
    'notes' => $notes,
    'items' => $items,
    'logo' => 'images/logo.png' // Path to logo file
];

$company = $invoiceData['company'];
$customer = $invoiceData['customer'];
$invoiceNumber = $invoiceData['invoice_number'];
$date = $invoiceData['date'];
$items = $invoiceData['items'];
$logo = $invoiceData['logo'];

// Create a new PDF document
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add Company Logo
if (file_exists($logo)) {
    $pdf->Image($logo, 10, 10, 50);
}

// Position Company Information
$pdf->SetXY(70, 10);
$pdf->SetFont('Arial', '', 10);
foreach ($company as $line) {
    $pdf->Cell(0, 6, $line, 0, 1, 'R');
}
$pdf->Ln(10);

// Customer Information
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Bill To:', 0, 1);
foreach ($customer as $line) {
    $pdf->Cell(0, 6, $line, 0, 1);
}
$pdf->Ln(5);

// Invoice Details
$pdf->Cell(50, 6, 'Quotation Number:', 0, 0);
$pdf->Cell(50, 6, $invoiceNumber, 0, 1);
$pdf->Cell(50, 6, 'Quotation Date:', 0, 0);
$pdf->Cell(50, 6, $date, 0, 1);
$pdf->Cell(50, 6, 'Due Date:', 0, 0);
$pdf->Cell(50, 6, $invoice_due_date, 0, 1);
$pdf->Cell(50, 6, 'Notes:', 0, 0);
$pdf->Cell(50, 6, $notes, 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Description', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(40, 10, 'Unit Price', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 1, 'C');

// Table Rows
$pdf->SetFont('Arial', '', 10);
foreach ($items as $item) {
    $description = $item['description'];
    $quantity = $item['quantity'];
    $unitPrice = $item['unit_price'];
    $total = $quantity * $unitPrice;

    $pdf->Cell(80, 8, $description, 1, 0, 'L');
    $pdf->Cell(30, 8, $quantity, 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($unitPrice, 2), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($total, 2), 1, 1, 'C');
}

// Subtotal Row
$pdf->Cell(150, 10, 'Subtotal', 1, 0, 'R');
$pdf->Cell(40, 10, number_format($subtotal, 2), 1, 1, 'C');

// Tax Row
$pdf->Cell(150, 10, 'Tax (' . (VAT_PERCENTAGE * 100) . '%)', 1, 0, 'R');
$pdf->Cell(40, 10, number_format($taxAmount, 2), 1, 1, 'C');

// Total Row
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 10, 'Total', 1, 0, 'R');
$pdf->Cell(40, 10, number_format($totalAmountWithTax, 2), 1, 1, 'C');
$pdf->Ln(10);

// Terms and Conditions
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 10, 'Terms and Conditions', 0, 1);
$terms = "1. All quotations and related documents are confidential and should not be disclosed to third parties without prior consent.\n"
    . "2. Any shipping, handling, or assembly fees will be charged to the client unless otherwise stated in the agreement.\n"
    . "3. Orders canceled after confirmation will be subject to a cancellation fee equivalent to 20% of the total order value.\n"
    . "\nWe thank you for this kind opportunity to quote on your requirements.";
$pdf->MultiCell(0, 8, $terms, 0);
$pdf->Ln(5);

// Sign-off
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 10, 'Yours Truly', 0, 1);
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 10, 'Madel Siochi', 0, 1);

// Output PDF as a downloadable file
$pdf->Output('D', 'Invoice_' . $invoiceNumber . '.pdf');
?>
