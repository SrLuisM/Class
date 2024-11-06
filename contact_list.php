<?php
session_start(); 

$contactsFile = 'data.php'; 
$contacts = file_exists($contactsFile) ? include $contactsFile : []; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Contact List</h2>
        <a href="contact_form.php" class="btn btn-success mb-3">Create new contact</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No contacts found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contact['id']); ?></td>
                        <td><?php echo htmlspecialchars($contact['name'] . ' ' . $contact['surname']);  ?></td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td>
                            <a href="contact_form.php?id=<?php echo htmlspecialchars($contact['id']); ?>" class="btn btn-info">Edit/View</a>
                        </td>



                        
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
