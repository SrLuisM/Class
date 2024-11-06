<?php
session_start(); 

// Check if there is contact data in the session
if (!isset($_SESSION['contact_data'])) {
    header('Location: contact_form.php'); // Redirect if no data
    exit();
}

// Retrieve contact data from the session
$contactData = $_SESSION['contact_data'];

// Load existing contact data
$contacts = include 'data.php';

// Check if a contact with the same phone or email already exists
foreach ($contacts as $key => $contact) {
    // If the phone or email matches, remove the existing contact
    if ($contact['phone'] == $contactData['phone'] || $contact['email'] == $contactData['email']) {
        unset($contacts[$key]); // Remove existing contact with the same phone or email
    }
}

// Assign a new ID to the contact (assuming IDs are sequential)
$newId = max(array_column($contacts, 'id')) + 1; // Get the maximum existing ID and add 1
$contactData['id'] = $newId; // Set the new ID

// Add the new contact to the existing contacts
$contacts[] = $contactData;

// Save the updated contacts list to the file
file_put_contents('data.php', '<?php return ' . var_export(array_values($contacts), true) . ';'); // Use array_values to reset keys.

unset($_SESSION['contact_data']);

// Calculate days until birthday if birth date exists.
$daysUntilBirthday = null;
if (!empty($contactData['birth_date'])) {
    // Create a DateTime object for the birth date
    $birthDate = new DateTime($contactData['birth_date']);
    $today = new DateTime();

    // Calculate the next birthday
    $nextBirthday = (clone $birthDate)->setDate($today->format('Y'), $birthDate->format('m'), $birthDate->format('d'));

    // If the birthday has already passed this year, set it to next year
    if ($nextBirthday < $today) {
        $nextBirthday->modify('+1 year');
    }

    
    // Calculate the difference in days
    $daysUntilBirthday = $today->diff($nextBirthday)->days + 1;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Contact Data Saved Successfully</h2>
        <div class="mb-3">
            <strong>ID:</strong> <?php echo htmlspecialchars($contactData['id']); ?><br>
            <strong>Title:</strong> <?php echo htmlspecialchars($contactData['title']); ?><br>
            <strong>Name:</strong> <?php echo htmlspecialchars($contactData['name']); ?><br>
            <strong>Surname:</strong> <?php echo htmlspecialchars($contactData['surname']); ?><br>
            <strong>Birth Date:</strong> <?php echo htmlspecialchars($contactData['birth_date']); ?><br>
            <strong>Phone:</strong> <?php echo htmlspecialchars($contactData['phone']); ?><br>
            <strong>Email:</strong> <?php echo htmlspecialchars($contactData['email']); ?><br>
            <strong>Types:</strong> <?php echo htmlspecialchars(implode(', ', $contactData['types'])); ?><br>

            <?php if ($daysUntilBirthday !== null): ?>
                <strong>Dias hasta el próximo cumpleaños:</strong> <?php echo htmlspecialchars($daysUntilBirthday); ?><br>
            <?php else: ?>
                <strong>Dias hasta el próximo cumpleaños:</strong> N/A<br>
            <?php endif; ?>
        </div>
        <a href="contact_form.php" class="btn btn-secondary">Add Another Contact</a>
        <a href="contact_list.php" class="btn btn-primary">View Contact List</a> 
    </div>
</body>
</html>
