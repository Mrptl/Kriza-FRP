<?php
/**
 * Kriza FRP — Catalog PDF Request Handler
 * ----------------------------------------
 * Sends two emails via Zoho Mail SMTP (PHPMailer):
 *  1. Admin notification  → info@krizafrp.co.in  (visitor details)
 *  2. Visitor confirmation → visitor email        (thank-you + PDF attached)
 *
 * SETUP REQUIRED before uploading to cPanel:
 *  1. Download PHPMailer from https://github.com/PHPMailer/PHPMailer/releases
 *     Extract and place ONLY these 3 files in a folder called "phpmailer/" next to this file:
 *       phpmailer/PHPMailer.php
 *       phpmailer/SMTP.php
 *       phpmailer/Exception.php
 *  2. Replace YOUR_ZOHO_MAIL_PASSWORD below with your Zoho Mail password.
 *     (If 2FA is enabled on Zoho, generate an App Password in Zoho Mail settings.)
 *  3. Upload this file, phpmailer/ folder, and assets/ folder to GoDaddy cPanel root.
 */

// ── CORS & headers ────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ── CONFIG — Edit only these values ──────────────────────────────────────────
define('ZOHO_HOST',    'smtp.zoho.in');
define('ZOHO_PORT',    465);
define('ZOHO_USER',    'info@krizafrp.co.in');
define('ZOHO_PASS',    'YOUR_ZOHO_MAIL_PASSWORD');   // ← Replace this
define('ADMIN_EMAIL',  'info@krizafrp.co.in');
define('SENDER_NAME',  'Kriza FRP Products');
define('PDF_PATH',     __DIR__ . '/assets/Kriza FRP Products.pdf');
define('PDF_FILENAME', 'Kriza FRP Products Catalog.pdf');

// ── Load PHPMailer ────────────────────────────────────────────────────────────
$phpmailerDir = __DIR__ . '/phpmailer/';
if (!file_exists($phpmailerDir . 'PHPMailer.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'PHPMailer not configured. See setup instructions inside send-catalog.php']);
    exit;
}
require_once $phpmailerDir . 'Exception.php';
require_once $phpmailerDir . 'PHPMailer.php';
require_once $phpmailerDir . 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── Parse & validate input ────────────────────────────────────────────────────
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request body']);
    exit;
}

$fullName = trim(htmlspecialchars($input['name']    ?? '', ENT_QUOTES, 'UTF-8'));
$company  = trim(htmlspecialchars($input['company'] ?? '', ENT_QUOTES, 'UTF-8'));
$phone    = trim(htmlspecialchars($input['phone']   ?? '', ENT_QUOTES, 'UTF-8'));
$desc     = trim(htmlspecialchars($input['desc']    ?? '', ENT_QUOTES, 'UTF-8'));
$dateStr  = trim(htmlspecialchars($input['date']    ?? '', ENT_QUOTES, 'UTF-8'));
$email    = filter_var(trim($input['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$fullName || !$email || !$phone || !$company) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Required fields are missing or invalid']);
    exit;
}

// Honeypot spam check
if (!empty($input['botcheck'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bot detected']);
    exit;
}

if (!file_exists(PDF_PATH)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Catalog PDF not found on server. Please upload assets/Kriza FRP Products.pdf']);
    exit;
}

// ── Helper: create configured PHPMailer ───────────────────────────────────────
function makeMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = ZOHO_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = ZOHO_USER;
    $mail->Password   = ZOHO_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = ZOHO_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(ZOHO_USER, SENDER_NAME);
    return $mail;
}

// ── Email bodies ──────────────────────────────────────────────────────────────
$reqRow = $desc
    ? "<tr style=\"border-bottom:1px solid #e0e0e0;\">
         <td style=\"padding:13px 10px;font-weight:700;color:#1a1a1a;vertical-align:top;\">Requirements:</td>
         <td style=\"padding:13px 10px;color:#333333;\">{$desc}</td>
       </tr>"
    : '';

// Admin email — Image 1 format
$adminBody = "
<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:28px 24px;background:#ffffff;border-radius:8px;'>
  <h2 style='font-size:26px;font-weight:700;color:#1a1a1a;margin:0 0 12px;'>New Catalog Request</h2>
  <p style='color:#555555;font-size:15px;margin:0 0 28px;line-height:1.5;'>You have received a new catalog request from your website.</p>
  <h3 style='color:#2e7d32;font-size:15px;font-weight:700;margin:0 0 14px;'>Inquiry Details</h3>
  <table style='width:100%;border-collapse:collapse;font-size:14px;'>
    <tr style='border-bottom:1px solid #e0e0e0;'>
      <td style='padding:13px 10px;font-weight:700;color:#1a1a1a;width:38%;vertical-align:top;'>Full Name:</td>
      <td style='padding:13px 10px;color:#333333;'>{$fullName}</td>
    </tr>
    <tr style='border-bottom:1px solid #e0e0e0;'>
      <td style='padding:13px 10px;font-weight:700;color:#1a1a1a;vertical-align:top;'>Company:</td>
      <td style='padding:13px 10px;color:#333333;'>{$company}</td>
    </tr>
    <tr style='border-bottom:1px solid #e0e0e0;'>
      <td style='padding:13px 10px;font-weight:700;color:#1a1a1a;vertical-align:top;'>Phone Number:</td>
      <td style='padding:13px 10px;color:#333333;'>{$phone}</td>
    </tr>
    <tr style='border-bottom:1px solid #e0e0e0;'>
      <td style='padding:13px 10px;font-weight:700;color:#1a1a1a;vertical-align:top;'>Email:</td>
      <td style='padding:13px 10px;'><a href='mailto:{$email}' style='color:#1565c0;text-decoration:none;'>{$email}</a></td>
    </tr>
    <tr style='border-bottom:1px solid #e0e0e0;'>
      <td style='padding:13px 10px;font-weight:700;color:#1a1a1a;vertical-align:top;'>Date Submitted:</td>
      <td style='padding:13px 10px;color:#333333;'>{$dateStr}</td>
    </tr>
    {$reqRow}
  </table>
  <p style='margin-top:28px;font-size:13px;color:#888888;'>
    The catalog PDF has been automatically sent to <a href='mailto:{$email}' style='color:#1565c0;'>{$email}</a>.
  </p>
</div>";

// Visitor email — Image 2 format (with PDF attached)
$visitorBody = "
<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:48px 32px;background:#ffffff;border-radius:8px;'>
  <h2 style='font-size:30px;font-weight:700;color:#1a1a1a;margin:0 0 20px;line-height:1.25;'>Thank You for Requesting Our Catalog!</h2>
  <p style='color:#333333;font-size:15px;margin:0 0 16px;'>Dear {$fullName},</p>
  <p style='color:#555555;font-size:15px;line-height:1.7;margin:0 0 16px;'>
    We've received your catalog request and appreciate your interest in Kriza FRP Products.
    Please find the <strong>Kriza FRP Products Catalog</strong> attached to this email.
    You can download and save it for your reference.
  </p>
  <p style='color:#555555;font-size:15px;line-height:1.7;margin:0 0 28px;'>
    If you have any queries or would like to discuss your requirements further, feel free to
    reach us at <a href='mailto:info@krizafrp.co.in' style='color:#1565c0;text-decoration:none;'>info@krizafrp.co.in</a>
    or call us directly. We'd be happy to help.
  </p>
  <p style='color:#888888;font-size:13px;margin:0;'>
    Warm regards,<br>
    <strong style='color:#1a1a1a;'>Team Kriza FRP Products</strong>
  </p>
</div>";

// ── Send emails ───────────────────────────────────────────────────────────────
try {
    // 1. Admin notification
    $adminMail = makeMailer();
    $adminMail->addAddress(ADMIN_EMAIL, SENDER_NAME . ' Admin');
    $adminMail->addReplyTo($email, $fullName);
    $adminMail->isHTML(true);
    $adminMail->Subject = "Catalog Request from {$fullName} — {$company}";
    $adminMail->Body    = $adminBody;
    $adminMail->AltBody = "New catalog request from {$fullName} ({$company}). Email: {$email}. Phone: {$phone}. Submitted: {$dateStr}.";
    $adminMail->send();

    // 2. Visitor confirmation + PDF attachment
    $visitorMail = makeMailer();
    $visitorMail->addAddress($email, $fullName);
    $visitorMail->isHTML(true);
    $visitorMail->Subject = 'Kriza FRP Products Catalog — Your Requested PDF';
    $visitorMail->Body    = $visitorBody;
    $visitorMail->AltBody = "Dear {$fullName}, please find the Kriza FRP Products Catalog attached. Contact us at info@krizafrp.co.in for queries. Warm regards, Team Kriza FRP Products.";
    $visitorMail->addAttachment(PDF_PATH, PDF_FILENAME);
    $visitorMail->send();

    echo json_encode(['success' => true, 'message' => 'Catalog sent successfully']);

} catch (Exception $e) {
    error_log('[KrizaFRP] Mailer error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please contact us at info@krizafrp.co.in']);
}
