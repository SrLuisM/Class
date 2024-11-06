<?php

class Contact {
    private int $id;
    private string $title;
    private string $name;
    private string $surname;
    private DateTime $birthdate;
    private string $phone;
    private string $email;
    private bool $favourite;
    private bool $important;
    private bool $archived;

    // Constructor
    public function __construct(array $contactArray = [
        "id" => 0,
        "title" => "Mr.",
        "name" => "",
        "surname" => "",
        "birthdate" => "now", 
        "phone" => "",
        "email" => "",
        "favourite" => false,
        "important" => false,
        "archived" => false
    ]) {
        $this->id = $contactArray['id'] ?? 0;
        $this->title = $contactArray['title'] ?? "Mr.";
        $this->name = $contactArray['name'] ?? "";
        $this->surname = $contactArray['surname'] ?? "";
        try {
            $this->birthdate = new DateTime($contactArray['birthdate'] ?? "now");
        } catch (Exception $e) {
            $this->birthdate = new DateTime(); // Default to current time if invalid
        }
        $this->phone = $contactArray['phone'] ?? "";
        $this->email = $contactArray['email'] ?? "";
        $this->favourite = $contactArray['favourite'] ?? false;
        $this->important = $contactArray['important'] ?? false;
        $this->archived = $contactArray['archived'] ?? false;
    }

public function validateContactForm($title, $name, $surname, &$birthDate, $phone, $email): array {
    $errors = [];

    // Validación de nombre
    if (empty($name)) { 
        $errors['name'] = "First Name is required."; 
    } elseif (!preg_match('/^[a-zA-Z]+$/', $name)) { 
        $errors['name'] = "First Name must contain only letters."; 
    }

    // Validación de apellido
    if (empty($surname)) {
        $errors['surname'] = "Surname is required.";
    } elseif (!preg_match('/^[a-zA-Z]+$/', $surname)) {
        $errors['surname'] = "Surname must contain only letters.";
    }

    // Validación de teléfono
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errors['phone'] = "Invalid phone number format. It should contain 7 to 15 digits, with an optional '+' at the start.";
    }

    // Validación de correo electrónico
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($birthDate)) {
        $errors['birth_date'] = "Birth date is required."; 
    } else {
        $dateValidationResult = $this->checkContactDate($birthDate);
        if ($dateValidationResult !== true) {
            $errors['birth_date'] = $dateValidationResult; 
        }
    }

    

    return $errors;
}




public function checkContactDate(string $date): string|bool {
    

    // Verifica que la fecha esté en el formato YYYY-MM-DD
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
        return "La fecha debe estar en formato DD-MM-YYYY";
    }

    // Separa año, mes y día
    [$year, $month, $day] = explode('-', $date);
    $year = (int)$year;
    $month = (int)$month;
    $day = (int)$day;

    // Valida el rango del año
    if ($year < 1900 || $year > 2100) {
        return "El año debe estar entre 1900 y 2100.";
    }

    // Valida el rango del mes
    if ($month < 1 || $month > 12) {
        return "El mes debe estar entre 1 y 12.";
    }

    // Determina si el año es bisiesto
    $isLeapYear = ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);

    // Validar el día según el mes y si es bisiesto o no
    if ($day < 1 || $day > 31) {
        return "El día debe estar entre 1 y 31.";
    }

    // Validar días en febrero
    if ($month == 2) {
        if ($isLeapYear && $day > 29) {
            return "Día inválido para febrero en un año bisiesto.";
        } elseif (!$isLeapYear && $day > 28) {
            return "Día inválido para febrero en un año no bisiesto.";
        }
    }

    // Validar días para meses que tienen 30 días
    if (in_array($month, [4, 6, 9, 11]) && $day > 30) {
        return "El mes especificado solo tiene 30 días.";
    }

    return true; // Fecha válida
}
        
    

public function checkBirthday(string $birthDate): ?int {
    if (!$this->checkContactDate($birthDate)) {
        return null; // Return null if the date is invalid
    }

    $today = new DateTime();
    $birthday = DateTime::createFromFormat('Y-m-d', $birthDate);
    $currentYear = (int)$today->format('Y');
    $birthdayThisYear = clone $birthday;
    $birthdayThisYear->setDate($currentYear, (int)$birthday->format('m'), (int)$birthday->format('d'));

    if ($birthdayThisYear < $today) {
        $birthdayThisYear->modify('+1 year');
    }

    $interval = $today->diff($birthdayThisYear);
    return (int)$interval->days; // Return the number of days until the next birthday
}




// Getters y setters
public function getId(): int {
    return $this->id;
}

public function setId(int $id): void {
    $this->id = $id;
}

public function getTitle(): string {
    return $this->title;
}

public function setTitle(string $title): void {
    $this->title = $title;
}

public function getName(): string {
    return $this->name;
}

public function setName(string $name): void {
    $this->name = $name;
}

public function getSurname(): string {
    return $this->surname;
}

public function setSurname(string $surname): void {
    $this->surname = $surname;
}

public function getBirthdate(): DateTime {
    return $this->birthdate;
}


public function setBirthdate(string $birthdate): void {
    $this->birthdate = new DateTime($birthdate);
}

public function getPhone(): string {
    return $this->phone;
}

public function setPhone(string $phone): void {
    $this->phone = $phone;
}

public function getEmail(): string {
    return $this->email;
}

public function setEmail(string $email): void {
    $this->email = $email;
}

public function isFavourite(): bool {
    return $this->favourite;
}

public function setFavourite(bool $favourite): void {
    $this->favourite = $favourite;
}

public function isImportant(): bool {
    return $this->important;
}

public function setImportant(bool $important): void {
    $this->important = $important;
}

public function isArchived(): bool {
    return $this->archived;
}

public function setArchived(bool $archived): void {
    $this->archived = $archived;
}

public function __toString(): string {
    return sprintf(
        "ID: %d, Title: %s, Name: %s, Surname: %s, Birthdate: %s, Phone: %s, Email: %s, Favourite: %s, Important: %s, Archived: %s",
        $this->id,
        $this->title,
        $this->name,
        $this->surname,
        $this->birthdate->format('Y-m-d'), 
        $this->phone,
        $this->email,
        $this->favourite ? 'Yes' : 'No',
        $this->important ? 'Yes' : 'No',
        $this->archived ? 'Yes' : 'No'
    );
}
}
?>
