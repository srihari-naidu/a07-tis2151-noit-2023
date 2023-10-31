<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");

    if (isset($_SESSION['instructorId'])) 
    {
        $instructorId = $_SESSION['instructorId'];

        $instructor = $con->prepare(
            "SELECT * 
             FROM Instructor 
             JOIN User ON InstructorId = User.Id 
             WHERE InstructorId = ?"
        );
        $instructor->execute([$instructorId]);
        $instructor = $instructor->fetch(PDO::FETCH_ASSOC);


        $courses = $con->prepare(
            "SELECT * 
             FROM CourseInstructor 
             WHERE InstructorId = ?
             AND Availability = ?"
        );
        $courses->execute([$instructorId, 'Available']);
        $courseCount = $courses->rowCount();

        $invitedCourses = $con->prepare(
            "SELECT * FROM Course 
             JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
             WHERE CourseInstructor.InstructorId = ?
             AND CourseInstructor.Status = ?
             AND NOW() < Course.StartTime
             ORDER BY StartTime"
        );
        $invitedCourses->execute([$instructorId, 'Invited']);
        $invitedCourseCount = $invitedCourses->rowCount();

        $upcomingCourses = $con->prepare(
            "SELECT * FROM Course 
             JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
             JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
             WHERE CourseInstructor.InstructorId = ?
             AND Availability = ?
             AND NOW() < Course.StartTime"
        );
        $upcomingCourses->execute([$instructorId, 'Available']);
        $upcomingCourseCount = $upcomingCourses->rowCount();

        $pastCourses = $con->prepare(
            "SELECT * FROM Course 
             JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
             JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
             WHERE  CourseInstructor.InstructorId = ?
             AND Availability = ?
             AND NOW() > Course.EndTime"
        );
        $pastCourses->execute([$instructorId, 'Available']);
        $pastCourseCount = $pastCourses->rowCount();
    }
    else {
        header('Location: login.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Dashboard</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <main>
        <div class="card-main">
            <h1 class="title">Quick Options</h1>
            <div class="short">
                <div class="card center four">
                    <a href="courses.php#yourCourses">
                        <h1><?= $courseCount ?></h1> 
                    </a>
                    Courses
                </div>
                <div class="card center four">
                    <a href="#invitedCourses">
                        <h1><?= $invitedCourseCount ?></h1> 
                    </a>
                    Invites
                </div>
                <div class="card center four">
                    <a href="#upcomingCourses">
                        <h1><?= $upcomingCourseCount ?></h1> 
                    </a>
                    Upcoming Courses
                </div>
                <div class="card center four">
                    <a href="#pastCourses">
                        <h1><?= $pastCourseCount ?></h1> 
                    </a>
                    Past Courses
                </div>
                <div class="card center four">
                    <a href="courses.php#invitedCourses"><button class="button button-add">&#x2b;</button></a>
                </div>
                <div class="card center four">
                    <a href="courses.php#invitedCourses"><button class="button button-view">&#128065;</button></a>
                </div>
                <div class="card center four">
                    <a href="courses.php#upcomingCourses"><button class="button button-view">&#128065;</button></a>
                </div>
                <div class="card center four">
                    <a href="courses.php#pastCourses"><button class="button button-view">&#128065;</button></a>
                </div>
            </div>


            <h1 class="title" id="upcomingCourses" >Upcoming Courses</h1>
            <p><i>Courses you're teaching that are upcoming.</i></p>
            <?php
                $upcomingCourses = $con->prepare(
                    "SELECT * FROM Course
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
                     WHERE CourseInstructor.InstructorId = ?
                     AND Availability = ?
                     AND NOW() < Course.StartTime
                     ORDER BY StartTime
                     LIMIT 3"
                );
                $upcomingCourses->execute([$instructorId, 'Available']);

                if ($upcomingCourses->rowCount() > 0) 
                {
                    echo "<div class=\"short no-gap\">";
                    while ($course = $upcomingCourses->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $courseProvider = $con->prepare(
                            "SELECT * FROM Provider 
                             JOIN User ON ProviderId = User.Id 
                             WHERE ProviderId = ?"
                        );
                        $courseProvider->execute([$course['ProviderId']]);
                        $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                        course($course, $provider);
                    }
                    echo "</div>";
                    button("More", "courses.php#upcomingCourses", "more");
                } 
                else 
                {
                    sorry("We couldn't find any upcoming courses yet.", $gap=false);
                }
            ?>
            
            
            <h1 class="title" id="pastCourses">Past Courses</h1>
            <p><i>Courses you've thought that have ended.</i></p>
            <?php
                $pastCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId 
                     WHERE CourseInstructor.InstructorId = ?
                     AND Availability = ?
                     AND NOW() > Course.EndTime
                     ORDER BY EndTime DESC 
                     LIMIT 3"
                );
                $pastCourses->execute([$instructorId, 'Available']);
                if ($pastCourses->rowCount() > 0) 
                {
                    echo "<div class=\"short no-gap\">";
                    while ($course = $pastCourses->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $courseProvider = $con->prepare(
                            "SELECT * FROM Provider 
                             JOIN User ON ProviderId = User.Id 
                             WHERE ProviderId = ?"
                        );
                        $courseProvider->execute([$course['ProviderId']]);
                        $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                        course($course, $provider);
                    }
                    echo "</div>";
                    button("More", "courses.php#pastCourses", "more");
                } 
                else 
                {
                    sorry("We couldn't find any past courses yet.", $gap=false);
                }
            ?>


            <h1 class="title" id="invitedCourses" >Invited Courses</h1>
            <p><i>Courses you're invited to teach.</i></p>
            <?php
                $invitedCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId
                     WHERE CourseInstructor.InstructorId = ?
                     AND Status = ?
                     AND NOW() < Course.StartTime
                     ORDER BY StartTime
                     LIMIT 3"
                );
                $invitedCourses->execute([$instructorId, 'Invited']);

                if ($invitedCourses->rowCount() > 0) 
                {
                    echo "<div class=\"short no-gap\">";
                    while ($course = $invitedCourses->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $courseProvider = $con->prepare(
                            "SELECT * FROM Provider 
                             JOIN User ON ProviderId = User.Id 
                             WHERE ProviderId = ?"
                        );
                        $courseProvider->execute([$course['ProviderId']]);
                        $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                        course($course, $provider);
                    }
                    echo "</div>";
                    button("More", "courses.php#invitedCourses", "more");
                } 
                else 
                {
                    sorry("We couldn't find any invites yet.", $gap=false);
                }
            ?>


            <h1 class="title" id="unavailableCourses" >Unavailable Courses</h1>
            <p><i>Courses you've responded that you were unavailable to teach.</i></p>
            <?php
                $unavailableCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId
                     WHERE CourseInstructor.InstructorId = ?
                     AND Availability = ?
                     AND Status = ?
                     AND NOW() < Course.StartTime
                     ORDER BY StartTime
                     LIMIT 3"
                );
                $unavailableCourses->execute([$instructorId, 'Unavailable', 'Responded']);

                if ($unavailableCourses->rowCount() > 0) 
                {
                    echo "<div class=\"short no-gap\">";
                    while ($course = $unavailableCourses->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $courseProvider = $con->prepare(
                            "SELECT * FROM Provider 
                                JOIN User ON ProviderId = User.Id 
                                WHERE ProviderId = ?"
                        );
                        $courseProvider->execute([$course['ProviderId']]);
                        $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                        course($course, $provider);
                    }
                    echo "</div>";
                    button("More", "courses.php#unavailableCourses", "more");
                } 
                else 
                {
                    sorry("We couldn't find any invites that you're unavailable for.", $gap=false);;
                }
            ?>
        </div>
    </main>

<?php include("inc/footer.inc.php") ?>    
<script src="js/script.js"></script>
</body>
</html>