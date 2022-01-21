<?php

if (isset($_POST['submit'])) {
    require_once "includes/database.php";

    /** @var mysqli $db */

    $email = mysqli_escape_string($db, $_POST['email']);
    $name = mysqli_escape_string($db, $_POST['name']);
    $phoneNumber = mysqli_escape_string($db, $_POST['phone_number']);
    $level = mysqli_escape_string($db, $_POST['level']);
    $password = $_POST['password'];
    $teacher = mysqli_escape_string($db, $_POST['teacher']);

    $errors = [];
    if ($email == '') {
        $errors['email'] = 'Voer een email in';
    }
    if ($name == '') {
        $errors['name'] = 'Voer een naam in';
    }
    if ($email == '') {
        $errors['phone_number'] = 'Voer een telefoonnummer in';
    }
    if ($email == '') {
        $errors['level'] = 'Voer een niveau in';
    }
    if ($password == '') {
        $errors['password'] = 'Voer een wachtwoord in';
    }
    if ($teacher == '') {
        $errors['teacher'] = 'klik een optie aan';
    }

    if (empty($errors)) {
        // hash password
        $password = password_hash($password, PASSWORD_DEFAULT);

        // insert the new user into the database
        $query = "INSERT INTO users (email, name, phone_number, level, password, teacher) 
                VALUES ('$email', '$name', '$phoneNumber', '$level', '$password', '$teacher')";

        $result = mysqli_query($db, $query)
        or die('Db Error: ' . mysqli_error($db) . ' with query: ' . $query);

        if ($result) {
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registreren</title>
</head>
<body>
<h2>Nieuwe gebruiker registeren</h2>
<form action="" method="post">
    <div class="data-field">
        <label for="name">Naam</label>
        <input id="name" type="text" name="name" value="<?= $name ?? '' ?>"/>
        <span class="errors"><?= $errors['name'] ?? '' ?></span>
    </div>
    <div class="data-field">
        <label for="email">Email</label>
        <input id="email" type="text" name="email" value="<?= $email ?? '' ?>"/>
        <span class="errors"><?= $errors['email'] ?? '' ?></span>
    </div>
    <div class="data-field">
        <label for="phone_number">Telefoonnummer</label>
        <input id="phone_number" type="text" name="phone_number" value="<?= $phoneNumber ?? '' ?>"/>
        <span class="errors"><?= $errors['phone_number'] ?? '' ?></span>
    </div>
    <div class="data-field">
        <label for="level">Niveau</label>
        <input id="level" type="text" name="level" value="<?= $level ?? '' ?>"/>
        <span class="errors"><?= $errors['level'] ?? '' ?></span>
    </div>
    <div class="data-field">
        <label for="password">Wachtwoord</label>
        <input id="password" type="password" name="password" value="<?= $password ?? '' ?>"/>
        <span class="errors"><?= $errors['password'] ?? '' ?></span>
    </div>
    <div class="data-field">
        <label for="password">bijlesdocent?</label>
        <input type="radio" id="1" name="teacher" value="1">
        <label for="1">ja</label><br>
        <input type="radio" id="0" name="teacher" value="0">
        <label for="0">nee</label><br>
    </div>
    <div class="data-submit">
        <input type="submit" name="submit" value="Registreren"/>
    </div>
</form>

</body>
</html>
