<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");
    
    if (isset($_SESSION['userId'])) 
    {
        $userId = $_SESSION['userId'];

        $user = $con->prepare(
            "SELECT * 
             FROM User 
             WHERE Id = ?"
        );
        $user->execute([$userId]);
        $user = $user->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $userId = '';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Home</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <main>
        <div class="card-main">
        <?php
            if ($userId == '') 
            {
        ?>
            <h1 class="title">Upcoming Courses</h1>
            <?php
                $upcomingCourses = $con->prepare(
                    "SELECT * FROM Course 
                     JOIN CourseProvider ON Course.Id = CourseProvider.CourseId
                     WHERE NOW() < Course.StartTime
                     ORDER BY StartTime
                     LIMIT 3"
                );
                $upcomingCourses->execute();
                if ($upcomingCourses->rowCount() > 0) 
                {
                    echo "<div class=\"short no-gap\">";
                    while ($course = $upcomingCourses->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $courseProvider = $con->prepare(
                            "SELECT * FROM 
                             Provider JOIN User ON ProviderId = User.Id 
                             WHERE ProviderId = ?"
                        );
                        $courseProvider->execute([$course['ProviderId']]);
                        $provider = $courseProvider->fetch(PDO::FETCH_ASSOC);

                        course($course, $provider);
                    }
                    echo "</div>";
                    button("More", "courses.php", "more");
                } 
                else 
                {
                    sorry("We couldn't find any upcoming courses yet.", $gap=false);
                }
            ?>
            

            <h1 class="title">Testimonials</h1>
            <?php
                $testimonials = $con->prepare(
                    "SELECT * FROM Comment 
                     JOIN Course ON Comment.CourseId = Course.Id 
                     ORDER BY CommentDate DESC 
                     LIMIT 3
                ");
                $testimonials->execute();
                if ($testimonials->rowCount() > 0) 
                {
                    echo "<div class=\"short\">";
                    while ($comment = $testimonials->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $commentingStudent = $con->prepare("SELECT * FROM Student JOIN User ON StudentId = User.Id WHERE StudentId = ?");
                        $commentingStudent->execute([$comment['StudentId']]);
                        $student = $commentingStudent->fetch(PDO::FETCH_ASSOC);

                        comment($comment, $student);
                    }
                    echo "</div>";
                }
                else 
                {
                    sorry("We couldn't find any testimonials yet.", $gap=false);
                }
            ?>


            <h1 class="title" id="joinUs">Join Us!</h1>
            <div class="short">
                <div class="card center three gap">
                    <p>Become a <b>Student</b></p>
                    <a href="register_student.php"><button class="button message">Enroll Here</button></a>
                </div>
                <div class="card center three gap">
                    <p>Become a <b>Course Instructor</b></p>
                    <a href="register_instructor.php"><button class="button message">Register Here</button></a>
                </div>
                <div class="card center three gap">
                    <p>Become a <b>Training Provider</b></p>
                    <a href="register_provider.php"><button class="button message">Apply Here</button></a>
                </div>
            </div>


        <?php
            }
            else if ($userId != '') 
            {
                if ($user['Role'] == 'Student')        
                {
                    $_SESSION['studentId'] = $userId;
                    header('Location: dashboard_student.php');
                }
                else if ($user['Role'] == 'Instructor')        
                {
                    $_SESSION['instructorId'] = $userId;
                    header('Location: dashboard_instructor.php');
                }
                else if ($user['Role'] == 'Provider')        
                {
                    $_SESSION['providerId'] = $userId;
                    header('Location: dashboard_provider.php');
                }
            }
        ?>
        </div>
    </main>
<?php include("inc/footer.inc.php") ?>    
<script src="js/script.js"></script>
</body>
</html>