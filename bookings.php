<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

/** @var mysqli $db */
// redirect when uri does not contain a id
if(!isset($_GET['date']) || $_GET['date'] == '') {
    // redirect to index.php
    header('Location: month.php');
    exit;
}

//Require database in this file
require_once "includes/database.php";

//Retrieve the GET parameter from the 'Super global'
$bookingDate = mysqli_escape_string($db, $_GET['date']);

//Get the record from the database result
$query = "SELECT bookings.id, users.name, users.email, date, timeslot, notes FROM bookings 
          INNER JOIN users ON bookings.user_id = users.id WHERE date = '$bookingDate' ";
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
    <div><a href="students.php">Leerlingen</a></div>
</nav>
<br><br>
<h1>Afspraken op <?php echo $bookings['date'] ?></h1>
<br><br>
<table class="center">
    <thead>
    <tr>
        <th>Naam</th>
        <th>Email</th>
        <th>Tijdslot</th>
        <th>Details</th>
        <th>Verwijderen</th>
    </tr>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    <?php foreach ($bookings as $booking) { ?>
        <tr>
            <td><?= $booking['name'] ?></td>
            <td><?= $booking['email'] ?></td>
            <td><?= $booking['timeslot'] ?></td>
            <td><a href="details.php?id=<?= $booking['id'] ?>">Details</a></td>
            <td><a href="delete.php?id=<?= $booking['id'] ?>">Verwijderen</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br><br>
<a href="month-teacher.php">Terug naar het maandoverzicht</a>
</body>
</html>
