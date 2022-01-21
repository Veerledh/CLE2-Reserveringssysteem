<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: Login.php");
    exit;
}

/** @var mysqli $db */

//Require DB settings with connection variable
require_once "includes/database.php";

//Get the result set from the database with a SQL query
$query = "SELECT * FROM users WHERE teacher = 0";
$result = mysqli_query($db, $query) or die ('Error: ' . $query );

//Loop through the result to create a custom array
$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

//Close connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<header>
</header>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="students.php">Leerlingen</a></div>
</nav>
<br>
<section>
    <br><br>
    <table class="center">
        <h2>Leerlingen</h2>
        <thead>
        <tr>
            <th>Naam</th>
            <th>Email</th>
            <th>Telefoonnummer</th>
            <th>Niveau</th>
            <th>Verwijderen</th>
            <th colspan="3"></th>
        </tr>
        </thead>
        <?php foreach ($students as $students) { ?>
            <tr>
                <td><?= $students['name'] ?></td>
                <td><?= $students['email'] ?></td>
                <td><?= $students['phone_number'] ?></td>
                <td><?= $students['level'] ?></td>
                <td><a href="delete-student.php?id=<?= $students['id'] ?>">Delete</a></td>
            </tr>
        <?php } ?>
</section>
</tbody>
</table>
</body>
</html>

