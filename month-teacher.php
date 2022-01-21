<?php
session_start();

//May I even visit this page?
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: Login.php");
    exit;
}

function build_calendar($month, $year){
    /** @var mysqli $db */
    require_once "includes/database.php";
    //Get id from session
    $id = $_SESSION['loggedInUser']['id'];

    //Get the record from the database result
    // is the user the teacher?
    $query = "SELECT id FROM users WHERE teacher = '1'";
    $result = mysqli_query($db, $query)
    or die ('Error: ' . $query );
    $teacher = mysqli_fetch_assoc($result);

    // Create array containing abbreviations of days of week.
    $daysOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

    //What is the first day of the month?
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    // How many days does this month contain?
    $numberDays = date('t', $firstDayOfMonth);

    // Retrieve some information about the first day of the month
    $dateComponents = getdate($firstDayOfMonth);

    // What is the name of the month?
    $monthName = $dateComponents['month'];

    // What is the index value (0-6) of the first day of the month?
    $dayOfWeek = $dateComponents['wday'];
    if($dayOfWeek==0){
        $dayOfWeek==6;
    }else{
        $dayOfWeek = $dayOfWeek-1;
    }

    // Create the table tag opener and day headers
    $datetoday = date('Y-m-d');
    $prevMonth = date('m', mktime(0, 0, 0, $month-1, 1, $year));
    $prevYear = date('Y', mktime(0, 0, 0, $month-1, 1, $year));
    $nextMonth = date('m', mktime(0, 0, 0, $month+1, 1, $year));
    $nextYear = date('Y', mktime(0, 0, 0, $month+1, 1, $year));
    $calendar = "<center><h2>$monthName $year</h2>";
    $calendar .= "<a class='btn btn=primary btn-xs' href='?month=".$prevMonth."&year=".$prevYear."'>prev Month</a> ";
    $calendar .= "<a class='btn btn=primary btn-xs' href='?month=".date('m')."&year=".date('Y')."'>Current Month </a> ";
    $calendar .= "<a class='btn btn=primary btn-xs' href='?month=".$nextMonth."&year=".$nextYear."'>next Month </a></center>";
    $calendar .= "<table class='table table-bordered'>";
    $calendar .= "<tr>";

    // create the calendar headers
    foreach($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }

    // Create the rest of the calendar
    // Initiate the day counter, starting with the 1st.
    $currentDay = 1;
    $calendar .= "</tr><tr>";

    // The variable $dayOfWeek is used to ensure that the calendar display consists of exactly 7 columns.
    if($dayOfWeek > 0) {
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar .= "<td class='empty'></td>";
        }
    }

    $month = str_pad($month, 2, "0", STR_PAD_LEFT);

    while ($currentDay <= $numberDays) {
        //Seventh column (Saturday) reached. Start a new row.
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayName = strtolower(date('l', strtotime($date)));
        $today = $date==date('Y-m-d')? 'today' : '';

        if($date<date('Y-m-d')){
            $calendar.="<td class='$today'><h4>$currentDay</h4><a class='btn btn-danger btn-xs'>NVT</a></td>";
        }else{
            // we have 12 slots in each day
            $totalbookings = checkSlots($db, $date);
            if($totalbookings==12){
                $calendar .="<td class='$today'><h4>$currentDay</h4><a href='#' class='btn btn-danger btn-xs'>All booked</a>";
            }else{
                $availableslots = 12 - $totalbookings;
                if ($id == $teacher['id']) {
                    $calendar .= "<td class='$today'><h4>$currentDay</h4><a href='bookings.php?date=" . $date . "' class='btn btn-success btn-xs'>Book</a><small><i>$availableslots slots left</i></small>";
                } else {
                    $calendar .= "<td class='$today'><h4>$currentDay</h4><a href='book.php?date=" . $date . "' class='btn btn-success btn-xs'>Book</a><small><i>$availableslots slots left</i></small>";
                }

            }
        }

        //Increment counters
        $currentDay++;
        $dayOfWeek++;
    }

//Complete the row of the last week in month, if necessary
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        for($l=0;$l<$remainingDays;$l++){
            $calendar .= "<td></td>";
        }
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";

    echo $calendar;

}

function checkSlots($db, $date){
    $stmt = $db ->prepare('select * from bookings where date = ?');
    $stmt->bind_param('s',$date);
    $totalbookings = 0;
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $totalbookings++;
            }
            $stmt->close();
        }
    }
    return $totalbookings;
}
?>

<html>
<head>
    <meta name="viewport" content="width=device=width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>
<body>
<br>
<nav id="navbar" class="sticky">
    <div><a href="month.php">Agenda</a></div>
    <div><a href="students.php">Leerlingen</a></div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="calendar">
                <?php
                $dateComponents = getdate();

                if(isset($_GET['month']) && isset($_GET['year'])){
                    $month = $_GET['month'];
                    $year = $_GET['year'];
                }else{
                    $month = $dateComponents['mon'];
                    $year = $dateComponents['year'];
                }
                echo build_calendar($month,$year);
                ?>
            </div>
        </div>
    </div>
</div>
<a href="logout.php">Uitloggen</a>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</html>

