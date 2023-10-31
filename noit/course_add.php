<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");

    if (isset($_SESSION['providerId'])) 
    {
        $providerId = $_SESSION['providerId'];

        $provider = $con->prepare(
            "SELECT * 
             FROM Provider 
             JOIN User ON ProviderId = User.Id 
             WHERE ProviderId = ?"
        );
        $provider->execute([$providerId]);
        $provider = $provider->fetch(PDO::FETCH_ASSOC);
    }
    else 
    {
        header('Location: login.php');
    }

    if (isset($_POST['submit']))
    {
        $title = $_POST['title'];
        $description = $_POST['description'];

        $cDate = $_POST['date'];
        $date = date("Y-m-d", strtotime($cDate));

        $sTime = $_POST['start-time'];
        $startTime = $date.' '.date("H:i:s",strtotime($sTime));

        $eTime = $_POST['end-time'];
        $endTime = $date.' '.date("H:i:s",strtotime($eTime));

        $link = $_POST['link'];

        $thumb = $_FILES['image']['name'];
        $ext = pathinfo($thumb, PATHINFO_EXTENSION);
        $thumbnail = uniqid().'.'.$ext;
        $thumb_size = $_FILES['image']['size'];
        $thumb_tmp_name = $_FILES['image']['tmp_name'];
        $thumb_folder = 'uploads/'.$thumbnail;


        $insCourse = $con->prepare(
            "INSERT INTO Course (Title, Description, Thumbnail, Date, StartTime, EndTime, Link)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $insCourse->execute([$title, $description, $thumbnail, $date, $startTime, $endTime, $link]);
        move_uploaded_file($thumb_tmp_name, $thumb_folder);

        $courseId = $con->lastInsertId(); 
        $insCourseProvider = $con->prepare(
            "INSERT INTO CourseProvider (CourseId, ProviderId)
            VALUES (?, ?)"
        );
        $insCourseProvider->execute([$courseId, $providerId]);

        $verCourse = $con->prepare(
            "SELECT * 
             FROM Course
             WHERE Id = ?"
        );
        $verCourse->execute([$courseId]);
        if ($verCourse->rowCount() > 0) 
        {
            $_SESSION['success_course_added'] = "Course <b>{$title}</b> has been added.";
        }

        if (!empty($_POST['instructor'])) 
        {
            $instructorIds = $_POST['instructor'];

            foreach($instructorIds as $instructorId)
            {
                $insCourseInstructor = $con->prepare(
                    "INSERT INTO CourseInstructor (CourseId, InstructorId, Availability, Status)
                     VALUES (?, ?, ?, ?)"
                );
                $insCourseInstructor->execute([$courseId, $instructorId, '', 'Invited']);
            }
        }
        else
        {
            $_SESSION['warning_no_instructors'] = "Course <b>{$title}</b> was added without instructors. Please update later.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Add Course</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <div class="container">
    <?php
        if (isset($_SESSION['success_course_added']))
        {
            $message = $_SESSION['success_course_added'];
            unset($_SESSION['success_course_added']);
            success($message, "View", "course.php?id={$courseId}");
        }
        else if (isset($_SESSION['warning_no_instructors']))
        {
            $message = $_SESSION['warning_no_instructors'];
            unset($_SESSION['warning_no_instructors']);
            warning($message, "Ok", "course.php?id={$courseId}");
        }
        else
        {
    ?>
            <div class="card card-header">
                <h1>Add Course</h1>
            </div>
            <div class="card card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="field input">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    <div class="field input">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="5" maxlength="2055" required></textarea>
                    </div>
                    <div class="field input file">
                        <label for="thumbnail">Thumbnail</label>
                        <input type="file" name="image" id="thumbnail" accept="image/*" class="file-input" required>
                    </div>
                    <div class="field input file">
                        <label for="link">Link</label>
                        <input type="text" name="link" id="link" required>
                    </div>
                    <div class="field input">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" class="date-input" required>
                    </div>
                    <div class="field input">
                        <label for="start-time">Start Time</label>
                        <input type="time" name="start-time" id="start-time" class="date-input" required>
                    </div>
                    <div class="field input">
                        <label for="end-time">End Time</label>
                        <input type="time" name="end-time" id="end-time" class="date-input" required>
                    </div>
                    <div class="field">
                        <label for="instructors">Instructors</label>
                        <?php
                            $companyInstructors = $con->prepare(
                                "SELECT * FROM Instructor
                                JOIN User ON Instructor.InstructorId = User.Id
                                WHERE Instructor.Company = ?"
                            );
                            $companyInstructors->execute([$provider['Company']]);

                            if ($companyInstructors->rowCount() > 0)
                            {
                                echo "<div class=\"checkbox\">";
                                while ($instructor = $companyInstructors->fetch(PDO::FETCH_ASSOC))
                                {
                        ?>
                                    <div class="option">
                                        <input type="checkbox" name="instructor[]" value="<?= $instructor['Id']; ?>"/>
                                        <p><?= $instructor['Name'].' — '.$instructor['Company'].' '.$instructor['Profession']; ?></p>
                                    </div>
                        <?php
                                }
                                echo "</div>";
                            }
                            else
                            {
                                echo "<p>Sorry, we couldn't find any instructors from {$provider['Company']}.</p>";
                            }
                        ?>
                    </div>
                    <div class="links">
                        <div class="field">
                            <input class="button submit gap" type="submit" name="submit" value="Add">
                        </div>
                    </div>
                </form>
            </div>
    <?php 
        }
    ?>
    </div>
<?php include("inc/footer.inc.php"); ?>    
<script src="js/script.js"></script>
</body>
</html>