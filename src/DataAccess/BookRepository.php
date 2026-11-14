<?php
class BookRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function add(Book $book): void {
        $stmt = $this->db->prepare("INSERT INTO books (title, author_id, year, genre, isbn, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$book->title, $book->authorId, $book->year, $book->genre, $book->isbn, $book->totalCopies, $book->availableCopies]);
    }

    public function findById(int $id): ?Book {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) return null;
        
        $book = new Book();
        $book->id = $data['id'];
        $book->title = $data['title'];
        $book->authorId = $data['author_id'];
        $book->year = $data['year'];
        $book->genre = $data['genre'];
        $book->isbn = $data['isbn'];
        $book->totalCopies = $data['total_copies'];
        $book->availableCopies = $data['available_copies'];
        return $book;
    }

    public function search(string $query): array {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE title LIKE :q OR genre LIKE :q");
        $stmt->execute(['q' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAvailableCopies(int $bookId, int $change): void {
        $stmt = $this->db->prepare("UPDATE books SET available_copies = available_copies + :change WHERE id = :id");
        $stmt->execute(['change' => $change, 'id' => $bookId]);
    }
        public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM books");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}