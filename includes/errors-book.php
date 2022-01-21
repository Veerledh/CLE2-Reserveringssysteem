<?php

$errors = [];

if ($notes == "") {
    $errors['notes'] = "Vul notities in";
}

if ($subject == "") {
    $errors['subject'] = "Vul het vak in";
}

