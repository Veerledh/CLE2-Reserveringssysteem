<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

/** @var mysqli $db */


require_once "includes/database.php";

if (isset($_POST['submit'])) {
    // DELETE DATA
    // Remove the data from the database with the existing booking id
    $bookingId = mysqli_escape_string($db, $_POST['id']);
    $query = "DELETE FROM bookings WHERE bookings.id = '$bookingId'";
    $result = mysqli_query($db, $query) or die ('Error: ' . $query);

    //Close connection
    mysqli_close($db);

    //Redirect to homepage after deletion & exit script
    header("Location: month-teacher.php");
    exit;

} else if (isset($_GET['id']) || $_GET['id'] != '') {
    //Retrieve the GET parameter from the 'Super global'
    $bookingId = mysqli_escape_string($db, $_GET['id']);

    //Get the record from the database result
    $query = "SELECT bookings.id, users.name, users.email, date, timeslot, subject, notes FROM bookings 
              INNER JOIN users ON bookings.user_id = users.id WHERE bookings.id = '$bookingId'";
    $result = mysqli_query($db, $query) or die ('Error: ' . $query);

    if (mysqli_num_rows($result) == 1) {
        $booking = mysqli_fetch_assoc($result);
    } else {
        // redirect when db returns no result
        header('Location: month-teacher.php');
        exit;
    }
} else {
    // Id was not present in the url OR the form was not submitted

    // redirect to index.php
    header('Location: bookings.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verwijder afspraak op <?= $booking['date'] ?></title>
</head>
<body>
<h2>Verwijder afspraak op <?= $booking['date'] ?></h2>
<form action="" method="post">
    <p>
        Weet u zeker dat u de afspraak met <?= $booking['name'] ?> om <?= $booking['timeslot'] ?> wilt verwijderen?
    </p>
    <input type="hidden" name="id" value="<?= $booking['id'] ?>"/>
    <input type="submit" name="submit" value="Verwijderen"/>
</form>
</body>
</html>

