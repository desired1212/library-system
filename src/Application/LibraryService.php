<?php
class LibraryService {
    private BookRepository $bookRepo;
    private ReaderRepository $readerRepo;
    private LoanRepository $loanRepo;

    public function __construct(BookRepository $bookRepo, ReaderRepository $readerRepo, LoanRepository $loanRepo) {
        $this->bookRepo = $bookRepo;
        $this->readerRepo = $readerRepo;
        $this->loanRepo = $loanRepo;
    }

    public function addBook(Book $book): void {
        $this->bookRepo->add($book);
    }

    public function registerReader(Reader $reader): void {
        $this->readerRepo->add($reader);
    }

    public function searchBooks(string $query): array {
        return $this->bookRepo->search($query);
    }

    public function issueBook(int $bookId, int $readerId, string $dueDate): void {
        $book = $this->bookRepo->findById($bookId);
        if (!$book) throw new Exception("Книга не найдена");
        if (!$book->isAvailable()) throw new Exception("Нет доступных экземпляров");

        $this->bookRepo->updateAvailableCopies($bookId, -1);
        $this->loanRepo->create($bookId, $readerId, $dueDate);
    }

    public function returnBook(int $loanId): void {
        $bookId = $this->loanRepo->close($loanId);
        if ($bookId) {
            $this->bookRepo->updateAvailableCopies($bookId, 1);
        }
    }

    public function getReaderBooks(int $readerId): array {
        return $this->loanRepo->getByReader($readerId);
    }

    public function getHistory(): array {
        return $this->loanRepo->getHistory();
    }

    public function getOverdue(): array {
        return $this->loanRepo->getOverdue();
    }
        public function getAllBooks(): array {
        return $this->bookRepo->getAll();
    }

    public function getAllReaders(): array {
        return $this->readerRepo->getAll();
    }
}