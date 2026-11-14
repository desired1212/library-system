<?php
class ReaderRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function add(Reader $reader): void {
        $stmt = $this->db->prepare("INSERT INTO readers (full_name, ticket_number, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$reader->fullName, $reader->ticketNumber, $reader->phone, $reader->email]);
    }
        public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM readers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}