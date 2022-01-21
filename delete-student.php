<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

/** @var mysqli $db */

//Require music data & image helpers to use variable in this file
require_once "includes/database.php";

if (isset($_POST['submit'])) {
    // DELETE DATA
    // Remove the album data from the database with the existing albumId
    $id = mysqli_escape_string($db, $_POST['id']);
    $query = "DELETE FROM users WHERE id = '$id'";
    $result = mysqli_query($db, $query) or die ('Error: ' . $query);

    //Close connection
    mysqli_close($db);

    //Redirect to homepage after deletion & exit script
    header("Location: students.php");
    exit;

} else if (isset($_GET['id']) || $_GET['id'] != '') {
    //Retrieve the GET parameter from the 'Super global'
    $id = mysqli_escape_string($db, $_GET['id']);

    //Get the record from the database result
    $query = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($db, $query) or die ('Error: ' . $query);

    if (mysqli_num_rows($result) == 1) {
        $student = mysqli_fetch_assoc($result);
    } else {
        // redirect when db returns no result
        header('Location: students.php');
        exit;
    }
} else {
    // Id was not present in the url OR the form was not submitted

    // redirect to index.php
    header('Location: students.php');
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
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <title>Verwijder <?= $student['name'] ?></title>
</head>
<body>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="students.php">Leerlingen</a></div>
</nav>
<br><br>
<h2>Verwijder <?= $student['name'] ?></h2>
<form action="" method="post">
    <p>
        Weet u zeker dat u de leerling '<?= $student['name'] ?>'  wilt verwijderen?
    </p>
    <input type="hidden" name="id" value="<?= $student['id'] ?>"/>
    <input type="submit" name="submit" value="Verwijderen"/>
</form>
</body>
</html>


