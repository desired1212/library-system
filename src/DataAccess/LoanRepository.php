<?php
class LoanRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(int $bookId, int $readerId, string $dueDate): void {
        $stmt = $this->db->prepare("INSERT INTO book_loans (book_id, reader_id, issue_date, due_date) VALUES (?, ?, CURRENT_DATE, ?)");
        $stmt->execute([$bookId, $readerId, $dueDate]);
    }

    public function close(int $loanId): int {
        $stmt = $this->db->prepare("SELECT book_id FROM book_loans WHERE id = :id");
        $stmt->execute(['id' => $loanId]);
        $bookId = $stmt->fetchColumn();

        $stmt = $this->db->prepare("UPDATE book_loans SET is_returned = TRUE WHERE id = :id");
        $stmt->execute(['id' => $loanId]);
        
        return (int)$bookId;
    }

    public function getByReader(int $readerId): array {
        $stmt = $this->db->prepare("SELECT * FROM book_loans WHERE reader_id = :id");
        $stmt->execute(['id' => $readerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHistory(): array {
        $stmt = $this->db->query("SELECT * FROM book_loans ORDER BY issue_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOverdue(): array {
        $stmt = $this->db->query("SELECT * FROM book_loans WHERE due_date < CURRENT_DATE AND is_returned = FALSE");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}