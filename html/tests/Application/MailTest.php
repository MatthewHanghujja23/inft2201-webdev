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

    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Alice", "Hello world");
        $this->assertIsInt($id);
        $this->assertEquals(1, $id);
    }

    public function testListMails() {
    $mail = new Mail($this->pdo);

    // Insert two sample mails
    $mail->createMail("Hello", "World");
    $mail->createMail("Foo", "Bar");

    $allMails = $mail->getAllMails(); 
    $this->assertCount(2, $allMails);
    $this->assertEquals("Hello", $allMails[0]['subject']);
}

}