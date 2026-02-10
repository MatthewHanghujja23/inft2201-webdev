<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

// Connect to production DB
$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');
try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$mail = new Mail($pdo);
$page = new Page();

// Extract ID from URL
$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

// GET: Single mail by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && is_numeric($id)) {
    $singleMail = $mail->getMailById((int)$id);

    if ($singleMail) {
        $page->item($singleMail);
    } else {
        $page->notFound();
    }
    exit;
}

// PUT: Update a mail by ID
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && is_numeric($id)) {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!isset($data['subject']) || !isset($data['body'])) {
        $page->badRequest();
        exit;
    }

    if ($mail->updateMail((int)$id, $data['subject'], $data['body'])) {
        $page->item($mail->getMailById((int)$id));
    } else {
        $page->notFound();   // 404 if ID does not exist
    }
    exit;
}

// DELETE: Delete a mail by ID
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && is_numeric($id)) {
    if ($mail->deleteMail((int)$id)) {
        $page->item(["message" => "Deleted successfully"]);
    } else {
        $page->notFound();
    }
    exit;
}

// If none matched
$page->badRequest();
