sequenceDiagram
    autonumber
    actor User as Клиент
    participant View as SearchView / BookingView
    participant SearchVM as RoomSearchViewModel
    participant DetailsVM as RoomDetailsViewModel
    participant BookingVM as BookingViewModel
    participant BookRepo as BookingRepository
    participant RoomRepo as RoomRepository

    User->>View: Вводит даты (checkIn, checkOut) и жмет «Найти»
    View->>SearchVM: searchRooms()
    activate SearchVM
    SearchVM->>RoomRepo: getAllRooms()
    RoomRepo-->>SearchVM: Список всех номеров
    loop Для каждого номера
        SearchVM->>BookRepo: getBookingsByRoom(roomId)
        BookRepo-->>SearchVM: Список существующих броней
        SearchVM->>SearchVM: isRoomFree(roomId, checkIn, checkOut)
    end
    SearchVM-->>View: Обновление списка availableRooms
    deactivate SearchVM
    View-->>User: Показ доступных номеров

    User->>View: Выбирает номер
    View->>DetailsVM: loadRoom(roomId) + calculatePrice()
    activate DetailsVM
    DetailsVM->>RoomRepo: getRoomById(roomId)
    RoomRepo-->>DetailsVM: Данные номера
    DetailsVM->>DetailsVM: nights = checkOut - checkIn
    DetailsVM->>DetailsVM: totalPrice = nights * room.pricePerNight
    DetailsVM-->>View: Обновление полей nightsCount и totalPrice
    deactivate DetailsVM
    View-->>User: Показ цены

    User->>View: Вводит контакты и нажимает «Забронировать»
    View->>BookingVM: createBooking()
    activate BookingVM
    BookingVM->>BookRepo: getBookingsByRoom(roomId)
    BookRepo-->>BookingVM: Актуальные брони
    BookingVM->>BookingVM: Проверка отсутствия коллизий
    alt Номер уже занят
        BookingVM-->>View: Ошибка: "Номер успели забронировать"
        View-->>User: Сообщение об ошибке
    else Номер свободен
        BookingVM->>BookRepo: saveBooking(newBooking)
        BookRepo-->>BookingVM: Успешно сохранено
        BookingVM-->>View: Бронь подтверждена
        deactivate BookingVM
        View-->>User: Сообщение об успешном бронировании!
    end