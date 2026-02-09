<?php
use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Clean and reinitialize the table
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }
    // Test 1: Creating a mail entry
    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Alice", "Hello world");
        $this->assertIsInt($id);
        $this->assertEquals(1, $id);
    }

    // Test 2: Retrieving all mail entries
    public function testListMails() {
    $mail = new Mail($this->pdo);

    // Two sample mails
    $mail->createMail("Hello", "World");
    $mail->createMail("Foo", "Bar");

    $allMails = $mail->getAllMails(); 
    $this->assertCount(2, $allMails);
    $this->assertEquals("Hello", $allMails[0]['subject']);
    }

    // Test 3: Getting mail by ID
    public function testGetMailById() {
    $mail = new Mail($this->pdo);
    $id = $mail->createMail("Alice", "Hello");
    $mailData = $mail->getMailById($id);
    $this->assertEquals("Alice", $mailData['subject']);
}


}