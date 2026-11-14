<?php
class BookLoan {
    public int $id;
    public int $bookId;
    public int $readerId;
    public string $issueDate;
    public string $dueDate;
    public bool $isReturned = false;
}