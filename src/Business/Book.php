<?php
class Book {
    public int $id;
    public string $title;
    public ?int $authorId = null;
    public ?int $year = null;
    public ?string $genre = null;
    public ?string $isbn = null;
    public int $totalCopies;
    public int $availableCopies;

    public function isAvailable(): bool {
        return $this->availableCopies > 0;
    }
}