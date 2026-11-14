classDiagram
    direction TB

    class RoomCategory {
        <<enumeration>>
        STANDARD
        FAMILY
        SUPERIOR
        SUITE
    }

    class BookingStatus {
        <<enumeration>>
        CONFIRMED
        CANCELLED
    }

    class Hotel {
        +int id
        +string name
        +string address
        +List~Room~ rooms
        +getRooms() List~Room~
    }

    class Room {
        +int id
        +string roomNumber
        +RoomCategory category
        +int bedsCount
        +float pricePerNight
        +string description
        +string status
        +getPricePerNight() float
    }

    class Customer {
        +int id
        +string fullName
        +string email
        +string phone
    }

    class Booking {
        +int id
        +Customer customer
        +Room room
        +string checkInDate
        +string checkOutDate
        +float totalPrice
        +BookingStatus status
        +cancel() void
    }

    class RoomRepository {
        +getAllRooms() List~Room~
        +getRoomById(int id) Room
    }

    class BookingRepository {
        +getBookingsByRoom(int roomId) List~Booking~
        +saveBooking(Booking booking) bool
        +cancelBooking(int bookingId) bool
        +getBookingsByCustomer(int customerId) List~Booking~
    }

    class CatalogViewModel {
        +List~Room~ rooms
        +string selectedCategory
        +loadCatalog() void
        +filterByCategory(string category) void
    }

    class RoomSearchViewModel {
        -RoomRepository roomRepo
        -BookingRepository bookingRepo
        +string checkInDate
        +string checkOutDate
        +int guestsCount
        +List~Room~ availableRooms
        +string errorMessage
        +searchRooms() void
        -isRoomFree(int roomId, string inDate, string outDate) bool
    }

    class RoomDetailsViewModel {
        -RoomRepository roomRepo
        +Room room
        +string checkInDate
        +string checkOutDate
        +int nightsCount
        +float totalPrice
        +loadRoom(int roomId) void
        +calculatePrice() void
    }

    class BookingViewModel {
        -BookingRepository bookingRepo
        +Room room
        +string customerName
        +string customerPhone
        +string customerEmail
        +string checkInDate
        +string checkOutDate
        +float totalPrice
        +List~Booking~ myBookings
        +createBooking() bool
        +cancelBooking(int bookingId) void
        +loadMyBookings(int customerId) void
    }

    class CatalogView {
        <<View>>
        +displayCatalog() void
    }

    class RoomSearchView {
        <<View>>
        +displayResults() void
        +showError(string msg) void
    }

    class RoomDetailsView {
        <<View>>
        +displayDetails() void
        +displayPrice() void
    }

    class BookingView {
        <<View>>
        +showConfirmation() void
        +showMyBookings() void
    }

    Hotel "1" *-- "many" Room : содержит
    Booking --> Room : относится к
    Booking --> Customer : оформлен на
    Booking --> BookingStatus : имеет статус
    Room --> RoomCategory : имеет категорию

    CatalogViewModel ..> RoomRepository : использует
    RoomSearchViewModel ..> RoomRepository : использует
    RoomSearchViewModel ..> BookingRepository : использует
    RoomDetailsViewModel ..> RoomRepository : использует
    BookingViewModel ..> BookingRepository : использует

    CatalogViewModel ..> Room : возвращает
    RoomSearchViewModel ..> Room : возвращает
    RoomDetailsViewModel ..> Room : возвращает
    BookingViewModel ..> Booking : возвращает

    CatalogView ..> CatalogViewModel : Binding
    RoomSearchView ..> RoomSearchViewModel : Binding
    RoomDetailsView ..> RoomDetailsViewModel : Binding
    BookingView ..> BookingViewModel : Binding