<?php

session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}
/** @var mysqli $db */
require_once "includes/database.php";
//Get email from session
$email = $_SESSION['loggedInUser']['email'];
$userId = $_SESSION['loggedInUser']['id'];


if(isset($_GET['date'])){
    $date = $_GET['date'];
    // select all the bookings from the date
    $stmt = $db ->prepare("select * from bookings where date = ?");
    $stmt->bind_param('s', $date);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $bookings[] = $row['timeslot'];
            }
            $stmt->close();
        }
    }
}

if(isset($_POST['submit'])){
    $subject = $db -> real_escape_string($_POST['subject']);
    $notes = $db -> real_escape_string($_POST['notes']);
    $timeslot = $db -> real_escape_string($_POST['timeslot']);

    // insert bookings into the bookings table
    $query = "INSERT INTO bookings (timeslot, notes, user_id, date, subject) VALUES ('$timeslot', '$notes', '$userId', '$date', '$subject')";
    $result = mysqli_query($db, $query) or die('Error: '.mysqli_error($db). ' with query ' . $query);

    if ($result) {
        $msg = "<div class='alert alert-success'>Booking Successfull</div>";
        $bookings[] = $timeslot;
    } else {
        $errors['db'] = 'Something went wrong in your database query: ' . mysqli_error($db);
    }

    mysqli_close($db);
}

$duration = 60; // specifies the duration of a timeslot
$cleanup = 0; // add gap between two timeslots
$start = "07:00"; // specifies the start of the timeslots
$end = "19:00"; // specifies the end of the timeslots

// function that will return with an array of the timeslots
function timeslots($duration, $cleanup, $start, $end){
    // take the 4 parameters which we initialized earlier
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();

    for($intStart = $start; $intStart<$end; $intStart->add($interval)->add($cleanupInterval)){
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if($endPeriod>$end){
            break;
        }

        $slots[] = $intStart->format("H:iA")." - ". $endPeriod->format("H:iA");

    }

    return $slots;
}
?>

<html>
<head>
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<header>
</header>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="bookings-student.php">Afspraken</a></div>
    <div><a href="profile.php">Profiel</a></div>
</nav>
<br><br><br>
<div class="container">
    <p class="text-center">Afspraak maken voor <?php echo date('d F, Y', strtotime($date));?></p>
    <br>
    <div class="row">

        <?php echo isset($msg)?$msg:"";?>

        <?php $timeslots = timeslots($duration, $cleanup, $start, $end);
        foreach($timeslots as $ts){
            ?>

            <div class="col-md-2">
                <div class="form-group">
                    <?php if(in_array($ts, $bookings)){ ?>
                        <button class="btn btn-danger"><?= isset($ts) ? htmlentities($ts) : '' ?></button>
                    <?php }else{ ?>
                        <button class="btn btn-success book" data-timeslot="<?= isset($ts) ? htmlentities($ts) : '' ?>">
                            <?= isset($ts) ? htmlentities($ts) : '' ?></button>
                    <?php }  ?>
                </div>
            </div>
        <?php }  ?>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Booking for: <span id="slot"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="data-field">
                                    <label for="">Tijdslot</label>
                                    <input readonly type="text" class="form-control" id="timeslot" name="timeslot"
                                           value="<?= isset($timeslot) ? htmlentities($timeslot) : '' ?>">
                                </div>
                                <div class="data-field">
                                    <label for="">Email</label>
                                    <input readonly type="email" class="form-control"
                                           value="<?= isset($email) ? htmlentities($email): '' ?>"  name="email">
                                </div>
                                <div class="data-field">
                                    <label for="">Vak</label>
                                    <input required type="subject" class="form-control" name="subject"
                                           value="<?= isset($subject) ? htmlentities($subject) : '' ?>">
                                </div>
                                <div class="data-field">
                                    <label for="">Notitie</label>
                                    <input required type="notes" class="form-control" name="notes"
                                           value="<?= isset($notes) ? htmlentities($notes) : '' ?>">
                                </div>
                                <div class="data-submit">
                                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script>
    // show form when clicked on a timeslot
    $(".book").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        $("#slot").html(timeslot);
        $("#timeslot").val(timeslot);
        $("#myModal").modal("show");
    });
</script>
</body>

</html>
