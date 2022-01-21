<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['loggedInUser']['id'];

/** @var mysqli $db */
//Require database in this file
require_once "includes/database.php";


//Get the information from the database result
$query = "SELECT * FROM users WHERE id = '$userId'";
$result = mysqli_query($db, $query)
or die ('Error: ' . $query );


if(mysqli_num_rows($result) != 1)
{
    // redirect when db returns no result
    header('Location: month.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

//Close connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en">
<head>
    <title>Profile</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="bookings-student.php">Afspraken</a></div>
    <div><a href="profile.php">Profiel</a></div>
</nav>
<br><br>
<h3> <?= $user['name'] ?></h3>
<ul>
    <li>Email:  <?= $user['email'] ?></li>
    <li>Telefoonnummer:   <?= $user['phone_number'] ?></li>
    <li>niveau: <?= $user['level'] ?></li>
    <br>
    <td><a href="edit.php?id=<?= $user['id'] ?>">Wijzigen</a></td>
</ul>
</body>
</html>
