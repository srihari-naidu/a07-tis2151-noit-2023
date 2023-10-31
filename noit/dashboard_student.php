<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");

    if (isset($_SESSION['studentId'])) 
    {
        $studentId = $_SESSION['studentId'];

        $student = $con->prepare(
            "SELECT * FROM Student 
             JOIN User ON StudentId = User.Id 
             WHERE StudentId = ?"
        );
        $student->execute([$studentId]);
        $student = $student->fetch(PDO::FETCH_ASSOC);

        $courses = $con->prepare(
            "SELECT * FROM StudentCourse 
             WHERE StudentId = ?"
        );
        $courses->execute([$studentId]);
        $courseCount = $courses->rowCount();

        $happeningCourses = $con->prepare(
            "SELECT * FROM Course
             JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
             WHERE StudentCourse.StudentId = ?
             AND DATE(NOW()) = DATE(Course.Date)
             AND NOW() > Course.StartTime AND NOW() < Course.EndTime
             ORDER BY StartTime"
        );
        $happeningCourses->execute([$studentId]);
        $happeningCourseCount = $happeningCourses->rowCount();

        $upcomingCourses = $con->prepare(
            "SELECT * FROM Course 
             JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
             WHERE StudentCourse.StudentId = ?
             AND NOW() < Course.StartTime"
        );
        $upcomingCourses->execute([$studentId]);
        $upcomingCourseCount = $upcomingCourses->rowCount();

        $pastCourses = $con->prepare(
            "SELECT * FROM Course
             JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
             WHERE StudentCourse.StudentId = ?
             AND NOW() > Course.EndTime"
        );
        $pastCourses->execute([$studentId]);
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
                    <a href="#happeningCourses">
                        <h1><?= $happeningCourseCount ?></h1>
                    </a>
                    Happening Courses
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
                    <a href="courses.php#allCourses"><button class="button button-add">&#x2b;</button></a>
                </div>
                <div class="card center four">
                    <a href="courses.php#happeningCourses"><button class="button button-view">&#128065;</button></a>
                </div>
                <div class="card center four">
                    <a href="courses.php#upcomingCourses"><button class="button button-view">&#128065;</button></a>
                </div>
                <div class="card center four">
                    <a href="courses.php#pastCourses"><button class="button button-view">&#128065;</button></a>
                </div>
            </div>

            <?php
                $unreviewedCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                     JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                     WHERE StudentCourse.StudentId = ?
                     AND NOW() > Course.EndTime
                     AND StudentCourse.CourseId NOT IN (
                        SELECT CourseId 
                        FROM Comment 
                        WHERE Comment.StudentId = ?
                     )
                     LIMIT 3"
                );
                $unreviewedCourses->execute([$studentId, $studentId]);

                if ($unreviewedCourses->rowCount() > 0)
                {
            ?>
                    <h1 class="title" id="unreviewedCourses">Leave a Feedback</h1>
                    <p><i>Leave a feedback on these courses and add a certificate to your collection!</i></p>
                    <?php
                        if ($unreviewedCourses->rowCount() > 0) 
                        {
                            echo "<div class=\"short no-gap\">";
                            while ($course = $unreviewedCourses->fetch(PDO::FETCH_ASSOC)) 
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
                            button("More", "courses.php#unreviewedCourses", "more");
                        } 
                        else {
                            sorry("We couldn't find any of your unreviewed courses yet.", $gap=false);
                        }
                    ?>
            <?php
                }
            ?>


            <h1 class="title" id="happeningCourses">Happening Courses</h1>
            <p><i>Courses you've enrolled in that are happening now.</i></p>
            <?php
                $happeningCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                     WHERE StudentCourse.StudentId = ?
                     AND DATE(NOW()) = DATE(Course.Date)
                     AND NOW() > Course.StartTime AND NOW() < Course.EndTime
                     ORDER BY StartTime
                     LIMIT 3"
                );
                $happeningCourses->execute([$studentId]);

                if ($happeningCourses->rowCount() > 0) 
                {
                    echo "<div class=\"short no-gap\">";
                    while ($course = $happeningCourses->fetch(PDO::FETCH_ASSOC)) 
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
                    button("More", "courses.php#happeningCourses", "more");
                } 
                else {
                    sorry("We couldn't find any of your happening courses yet.", $gap=false);
                }
            ?>


            <h1 class="title" id="upcomingCourses">Upcoming Courses</h1>
            <p><i>Courses you've enrolled in that are upcoming.</i></p>
            <?php
                $upcomingCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                     WHERE StudentCourse.StudentId = ?
                     AND NOW() < TIME(Course.StartTime)
                     ORDER BY StartTime
                     LIMIT 3"
                );
                $upcomingCourses->execute([$studentId]);

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
                else {
                    sorry("We couldn't find any of your upcoming courses yet. Start enrolling now!", $gap=true);
                    button("Enroll Now!", "courses.php#allCourses", "more");
                }
            ?>


            <h1 class="title" id="pastCourses">Past Courses</h1>
            <p><i>Courses you've enrolled in that have ended.</i></p>
            <?php
                $pastCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId 
                     JOIN StudentCourse ON Course.Id = StudentCourse.CourseId 
                     WHERE StudentCourse.StudentId = ?
                     AND NOW() > Course.EndTime
                     ORDER BY EndTime DESC
                     LIMIT 3"
                );
                $pastCourses->execute([$studentId]);
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
                else {
                    sorry("We couldn't find any of your past courses yet.", $gap=false);
                }
            ?>
        </div>
    </main>

<?php include("inc/footer.inc.php") ?>    
<script src="js/script.js"></script>
</body>
</html>