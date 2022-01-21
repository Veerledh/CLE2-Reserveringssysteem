<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

/** @var mysqli $db */

// redirect when uri does not contain a id
if(!isset($_GET['id']) || $_GET['id'] == '') {
    // redirect to index.php
    header('Location: bookings.php');
    exit;
}

//Require database in this file
require_once "includes/database.php";

//Retrieve the GET parameter from the 'Super global'
$bookingId = mysqli_escape_string($db, $_GET['id']);



//Get the record from the database result
$query = "SELECT bookings.id, users.name, users.email, date, timeslot, subject, notes FROM bookings INNER JOIN users ON bookings.user_id = users.id WHERE bookings.id = '$bookingId'";
$result = mysqli_query($db, $query)
or die ('Error: ' . $query );


if(mysqli_num_rows($result) != 1)
{
    // redirect when db returns no result
    header('Location: bookings.php');
    exit;
}

$booking = mysqli_fetch_assoc($result);

//Close connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en">
<head>
    <title>Music Collection Details</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="students.php">Leerlingen</a></div>
</nav>
<br><br>
<h3><?= $booking['date'] . ' om ' . $booking['timeslot'] ?></h3>

<ul>
    <li>Naam:  <?= $booking['name'] ?></li>
    <li>Email:   <?= $booking['email'] ?></li>
    <li>Vak: <?= $booking['subject'] ?></li>
    <li>Notitie: <?= $booking['notes'] ?></li>
</ul>
<br>
<div>

    <a href="month-teacher.php">Ga terug naar het het maandoverzicht</a>
</div>
</body>
</html>

