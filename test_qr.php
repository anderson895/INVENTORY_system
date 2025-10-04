<?php
require_once __DIR__ . '/vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

// Path for saving QR codes
$qrDir = __DIR__ . '/qr_codes/';
if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);

// Generate QR code
$qrCode = new QrCode(
    data: (string)$product_id,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 300,
    margin: 10,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    foregroundColor: new Color(0, 0, 0),
    backgroundColor: new Color(255, 255, 255),
);

$writer = new PngWriter();
$result = $writer->write($qrCode);

$qrPath = $qrDir . $product_id . '.png';
$result->saveToFile($qrPath);
