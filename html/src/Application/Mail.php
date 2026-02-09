<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createMail($subject, $body) {
        $stmt = $this->pdo->prepare("INSERT INTO mail (subject, body) VALUES (?, ?) RETURNING id");
        $stmt->execute([$subject, $body]);

        return $stmt->fetchColumn();
    }

    public function getAllMails() {
    $stmt = $this->pdo->query("SELECT * FROM mail ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}