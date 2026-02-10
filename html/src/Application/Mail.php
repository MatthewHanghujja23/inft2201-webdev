<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    // 1. Implement createMail to insert a new mail entry and return its ID
    public function createMail($subject, $body) {
        $stmt = $this->pdo->prepare("INSERT INTO mail (subject, body) VALUES (?, ?) RETURNING id");
        $stmt->execute([$subject, $body]);

        return $stmt->fetchColumn();
    }
    
    // 2. Implement getAllMails to retrieve all mail entries
    public function getAllMails() {
    $stmt = $this->pdo->query("SELECT * FROM mail ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 3. Implement getMailById to retrieve a single mail entry by its ID
    public function getMailById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM mail WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 4. Implement updateMail to update an existing mail entry by its ID
    public function updateMail($id, $subject, $body) {
    $stmt = $this->pdo->prepare("UPDATE mail SET subject = ?, body = ? WHERE id = ?");
    $stmt->execute([$subject, $body, $id]);
    return $stmt->rowCount() > 0; 
    }
    
    // 5. Implement deleteMail to delete a mail entry by its ID
    public function deleteMail($id) {
    $stmt = $this->pdo->prepare("DELETE FROM mail WHERE id = :id");
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0; // true if deleted, false if ID did not exist
}


    
}

