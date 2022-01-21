<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['loggedInUser']['id'];

/** @var mysqli $db */
// redirect when uri does not contain a id

//Require database in this file
require_once "includes/database.php";

//Get the record from the database result
$query = "SELECT bookings.id, users.name, users.email, date, timeslot, notes, subject FROM bookings INNER JOIN users ON bookings.user_id = users.id  WHERE bookings.user_id = '$userId'";
$result = mysqli_query($db, $query)
or die ('Error: ' . $query );

//Loop through the result to create a custom array
$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = $row;
}

//Close connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en">
<head>
    <title>Bookings</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="bookings-student.php">Afspraken</a></div>
    <div><a href="profile.php">Profiel</a></div>
</nav>
<br><br>
<h1>Afspraken op <?php echo $bookings['date'] ?></h1>
<br><br>
<table class="center">
    <thead>
    <tr>
        <th>Tijdslot</th>
        <th>Datum</th>
        <th>Vak</th>
        <th>Notitie</th>
        <th>Verwijderen</th>
    </tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    <?php foreach ($bookings as $booking) { ?>
        <tr>
            <td><?= $booking['timeslot'] ?></td>
            <td><?= $booking['date'] ?> </td>
            <td><?= $booking['subject'] ?> </td>
            <td><?= $booking['notes'] ?></td>
            <td><a href="delete-booking.php?id=<?= $booking['id'] ?>">Verwijderen</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>

