<?php
session_start();

include 'contact.php'; // Include the Contact class

$contacts = include 'data.php'; // Include contacts data
$contactObjects = [];

foreach ($contacts as $contactData) {
    $contactObjects[] = new Contact($contactData);
}


$id = $title = $name = $surname = $birthDate = $phone = $email = '';
$types = $errors = []; // Initialize arrays


if (isset($_GET['id'])) {
    foreach ($contactObjects as $contact) {
        if ($contact->getId() == $_GET['id']) {
            $id = $contact->getId();
            $title = $contact->getTitle();
            $name = $contact->getName();
            $surname = $contact->getSurname();
            $birthDate = $contact->getBirthdate()->format('d-m-Y'); // Format the DateTime object
            $phone = $contact->getPhone();
            $email = $contact->getEmail();
            $types = ['Favorite' => $contact->isFavourite(), 'Important' => $contact->isImportant(), 'Archived' => $contact->isArchived()];
            break; // Stop the loop once the contact is found
        }
    }
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? 'Mr.';
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $birthDate = $_POST['birth_date'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $types = $_POST['types'] ?? [];



    $contact = new Contact(); // Create a new Contact object for validation
    $errors = $contact->validateContactForm($title, $name, $surname, $birthDate, $phone, $email);

    var_dump($birthDate);

    if (empty($errors)) {
        if (!empty($id)) {
            foreach ($contactObjects as $contact) {
                if ($contact->getId() == $id) {
                    // Update the contact details
                    $contact->setTitle($title);
                    $contact->setName($name);
                    $contact->setSurname($surname);
                    $contact->setBirthdate($birthDate);
                    $contact->setPhone($phone);
                    $contact->setEmail($email);
                    $contact->setFavourite(in_array('Favorite', $types));
                    $contact->setImportant(in_array('Important', $types));
                    $contact->setArchived(in_array('Archived', $types));
                    break;
                }
            }
        } else {
            $newId = count($contactObjects) + 1;
            $newContact = new Contact([
                'id' => $newId,
                'title' => $title,
                'name' => $name,
                'surname' => $surname,
                'birthdate' => $birthDate,
                'phone' => $phone,
                'email' => $email,
                'favourite' => in_array('Favorite', $types),
                'important' => in_array('Important', $types),
                'archived' => in_array('Archived', $types)
            ]);
            $contactObjects[] = $newContact;
        }

        // Update contact data file
        file_put_contents('data.php', '<?php return ' . var_export(array_map(function ($contact) {
            return [
                'id' => $contact->getId(),
                'title' => $contact->getTitle(),
                'name' => $contact->getName(),
                'surname' => $contact->getSurname(),
                'birthdate' => $contact->getBirthdate()->format('d-m-Y'),
                'phone' => $contact->getPhone(),
                'email' => $contact->getEmail(),
                'favourite' => $contact->isFavourite(),
                'important' => $contact->isImportant(),
                'archived' => $contact->isArchived(),
            ];
        }, $contactObjects), true) . ';');

        $_SESSION['contact_data'] = $_POST; // Store the form data into session
        header('Location: checkdata.php'); // Redirect to check data page
        exit();
    }
}



function displayFieldError($field, $errors) {
    if (isset($errors[$field])) {
        echo "<div class='text-danger'><strong>" . htmlspecialchars($errors[$field]) . "</strong></div>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Contact Form</h2>
        <form method="POST" action="contact_form.php" novalidate >
            <div class="form-group mb-3">
                <label for="id">ID:</label>
                <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" readonly class="form-control">
            </div>
            <div class="form-group mb-3">
                <label>Title:</label><br>
                <input type="radio" id="mr" name="title" value="Mr." <?php echo ($title == 'Mr.') ? 'checked' : ''; ?>>
                <label for="mr">Mr.</label>
                <input type="radio" id="mrs" name="title" value="Mrs." <?php echo ($title == 'Mrs.') ? 'checked' : ''; ?>>
                <label for="mrs">Mrs.</label>
                <input type="radio" id="miss" name="title" value="Miss" <?php echo ($title == 'Miss') ? 'checked' : ''; ?>>
                <label for="miss">Miss</label>
                <?php displayFieldError('title', $errors); ?>
            </div>
            <div class="form-group mb-3">
                <label for="name">Name:</label> 
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="form-control" onfocus="if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                <?php displayFieldError('name', $errors); ?> 
            </div>
            <div class="form-group mb-3">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($surname); ?>" class="form-control" onfocus="if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                <?php displayFieldError('surname', $errors); ?>
            </div>
            <div class="form-group mb-3">
                <label for="birth_date">Birth Date (DD-MM-YYYY):</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($birthDate); ?>"  class="form-control" onfocus="if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                <?php displayFieldError('birth_date', $errors); ?> 
            </div>
            <div class="form-group mb-3">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control" onfocus="if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                <?php displayFieldError('phone', $errors); ?>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" onfocus="if(this.nextElementSibling) this.nextElementSibling.style.display='none';">
                <?php displayFieldError('email', $errors); ?>
            </div>
            <div class="form-group mb-3">
                <label>Type:</label><br>
                <input type="checkbox" id="favorite" name="types[]" value="Favorite"  <?php echo in_array('Favorite', $types) ? 'checked' : '';?>>
                <label for="favorite">Favorite</label>
                <input type="checkbox" id="important" name="types[]" value="Important" <?php echo in_array('Important', $types) ? 'checked' : ''; ?>>
                <label for="important">Important</label>
                <input type="checkbox" id="archived" name="types[]" value="Archived" <?php echo in_array('Archived', $types) ? 'checked' : ''; ?>>
                <label for="archived">Archived</label>
            </div>


            
            <button type="submit" name="submit" class="btn btn-primary">Save</button>
            <button type="submit" name="update" class="btn btn-warning" <?php echo empty($id) ? 'disabled' : ''; ?>>Update</button>
            <button type="button" class="btn btn-danger" <?php echo empty($id) ? 'disabled' : ''; ?>>Delete</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
