<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

/** @var mysqli $db */

require_once "includes/database.php"; // Using database connection file here

$id = $_GET['id']; // get id through query string

if(isset($id)){
    $query = mysqli_query($db, "SELECT * FROM users WHERE id='$id'"); // select query
    while($row = mysqli_fetch_assoc($query)){
        $name = $row['name'];
        $email = $row['email'];
        $phoneNumber = $row['phone_number'];
        $level = $row['level'];
    }
} else {
    $errors['db'] = "Er is een fout opgetreden...";
}

if(isset($_POST['update'])) // when click on Update button
{
    $name_update  = mysqli_escape_string($db, $_POST['name']);
    $email_update   = mysqli_escape_string($db, $_POST['email']);
    $phone_update  = mysqli_escape_string($db, $_POST['phone_number']);
    $level_update  = mysqli_escape_string($db, $_POST['level']);

    require_once("includes/errors-edit.php");

    if(empty($errors)){
        //Save the record to the database
        $update_query = "UPDATE users SET name='$name_update', email='$email_update', phone_number='$phone_update', level='$level_update' 
                         WHERE id = '$id' ";
        $result = mysqli_query($db, $update_query);
        if($result)
        {
            mysqli_close($db); // Close connection
            header("location:profile.php"); // redirects to all records page
            exit;
        } else {
            $errors['db'] = 'Something went wrong in your database query: ' . mysqli_error($db);
        }
    }

}

?>

<!doctype html>
<html lang="en">
<head>
    <title>Edit profile</title>
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
<h2>Wijzig profiel</h2>

<form action="" method="post" enctype="multipart/form-data">
    <div class="data-field">
        <label for="name">naam</label>
        <input id="name" type="text" name="name"
               value="<?php if(isset($name_update)){ echo $name_update; } else{ echo $name; } ?>" />
        <span class="errors"><?php echo $errors['first_name'] ?? ''; ?></span>
    </div>
    <div class="data-field">
        <label for="email">email</label>
        <input id="email" type="text" name="email"
               value="<?php if(isset($email_update)){ echo $email_update; } else{ echo $email; } ?>" />
        <span class="errors"><?php echo $errors['email'] ?? ''; ?></span>
    </div>
    <div class="data-field">
        <label for="phone_number">Telefoonnummer</label>
        <input id="phone_number" type="text" name="phone_number"
               value="<?php if(isset($phone_update)){ echo $phone_update; } else{ echo $phoneNumber; } ?>"/>
        <span class="errors"><?php echo $errors['phone_number'] ?? ''; ?></span>
    </div>
    <div class="data-field">
        <label for="level">Niveau</label>
        <input id="level" type="level" name="level"
               value="<?php if(isset($level_update)){ echo $level_update; } else{ echo $level; } ?>" />
        <span class="errors"><?php echo $errors['level'] ?? ''; ?></span>
    </div>
    <div>
        <input type="submit" name="update" value="Update"/>
    </div>
</form>
</body>
</html>