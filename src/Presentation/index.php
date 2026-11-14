<?php
require_once __DIR__ . '/../Business/Author.php';
require_once __DIR__ . '/../Business/Book.php';
require_once __DIR__ . '/../Business/Reader.php';
require_once __DIR__ . '/../Business/BookLoan.php';
require_once __DIR__ . '/../DataAccess/BookRepository.php';
require_once __DIR__ . '/../DataAccess/ReaderRepository.php';
require_once __DIR__ . '/../DataAccess/LoanRepository.php';
require_once __DIR__ . '/../Application/LibraryService.php';

$dbFile = __DIR__ . '/../../library.sqlite';
$db = new PDO('sqlite:' . $dbFile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$db->exec("
CREATE TABLE IF NOT EXISTS authors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS books (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author_id INTEGER,
    year INTEGER,
    genre TEXT,
    isbn TEXT,
    total_copies INTEGER DEFAULT 1,
    available_copies INTEGER DEFAULT 1
);
CREATE TABLE IF NOT EXISTS readers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    ticket_number TEXT UNIQUE,
    phone TEXT,
    email TEXT
);
CREATE TABLE IF NOT EXISTS book_loans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    book_id INTEGER NOT NULL,
    reader_id INTEGER NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    is_returned BOOLEAN DEFAULT 0
);
");

$service = new LibraryService(
    new BookRepository($db),
    new ReaderRepository($db),
    new LoanRepository($db)
);

$msg = "";

if (isset($_POST['add_book'])) {
    try {
        $book = new Book();
        $book->title = $_POST['title'];
        $book->year = (int)$_POST['year'];
        $book->genre = $_POST['genre'];
        $book->isbn = $_POST['isbn'];
        $book->totalCopies = (int)$_POST['total_copies'];
        $book->availableCopies = $book->totalCopies; // при добавлении все экземпляры доступны
        $service->addBook($book);
        $msg = "Книга успешно добавлена!";
    } catch (Exception $e) { $msg = "Ошибка: " . $e->getMessage(); }
}


if (isset($_POST['add_reader'])) {
    try {
        $reader = new Reader();
        $reader->fullName = $_POST['full_name'];
        $reader->ticketNumber = $_POST['ticket_number'];
        $reader->phone = $_POST['phone'];
        $reader->email = $_POST['email'];
        $service->registerReader($reader);
        $msg = "Читатель успешно зарегистрирован!";
    } catch (Exception $e) { $msg = "Ошибка: " . $e->getMessage(); }
}
if (isset($_POST['issue'])) {
    try {
        $service->issueBook((int)$_POST['book_id'], (int)$_POST['reader_id'], $_POST['due_date']);
        $msg = "Книга выдана!";
    } catch (Exception $e) { $msg = "Ошибка: " . $e->getMessage(); }
}

if (isset($_POST['return'])) {
    try {
        $service->returnBook((int)$_POST['loan_id']);
        $msg = "Книга возвращена!";
    } catch (Exception $e) { $msg = "Ошибка: " . $e->getMessage(); }
}

$overdue = $service->getOverdue();
$allBooks = $service->getAllBooks();
$allReaders = $service->getAllReaders();;
?>
<!DOCTYPE html>
<html>
<head><title>Библиотека</title></head>
<body>
    <h2>Библиотечная система</h2>
    <p><b><?= $msg ?></b></p>
    <h3>Добавить книгу</h3>
    <form method="POST">
        Название: <input type="text" name="title" required><br><br>
        Год: <input type="number" name="year" required><br><br>
        Жанр: <input type="text" name="genre" required><br><br>
        ISBN: <input type="text" name="isbn"><br><br>
        Кол-во экземпляров: <input type="number" name="total_copies" value="1" required><br><br>
        <button type="submit" name="add_book">Добавить книгу</button>
    </form>
    <hr>

    <h3>Зарегистрировать читателя</h3>
    <form method="POST">
        ФИО: <input type="text" name="full_name" required><br><br>
        Номер билета: <input type="text" name="ticket_number" required><br><br>
        Телефон: <input type="text" name="phone"><br><br>
        Email: <input type="email" name="email"><br><br>
        <button type="submit" name="add_reader">Зарегистрировать</button>
    </form>
    <hr>
        <h3>Список книг</h3>
    <table border="1" cellpadding="5">
        <tr><th>ID</th><th>Название</th><th>Доступно</th></tr>
        <?php foreach ($allBooks as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td><?= htmlspecialchars($b['title']) ?></td>
                <td><?= $b['available_copies'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Список читателей</h3>
    <table border="1" cellpadding="5">
        <tr><th>ID</th><th>ФИО</th><th>Билет</th></tr>
        <?php foreach ($allReaders as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= htmlspecialchars($r['ticket_number']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr>
    <h3>Выдать книгу</h3>
    <form method="POST">
        ID книги: <input type="number" name="book_id" required>
        ID читателя: <input type="number" name="reader_id" required>
        Дата возврата: <input type="date" name="due_date" required>
        <button type="submit" name="issue">Выдать</button>
    </form>

    <h3>Вернуть книгу</h3>
    <form method="POST">
        ID выдачи (loan_id): <input type="number" name="loan_id" required>
        <button type="submit" name="return">Вернуть</button>
    </form>
    
    <h3>Просроченные книги</h3>
    <ul>
        <?php foreach ($overdue as $loan): ?>
            <li>Выдача #<?= $loan['id'] ?> (Книга ID: <?= $loan['book_id'] ?>, Дата возврата: <?= $loan['due_date'] ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>