<?php

    function success($message, $value, $return) 
    {
        echo "
                <div class=\"card card-header header-success\">
                    <h1>Success!</h1>
                </div>
                <div class=\"card card-body gap\">
                    <p>$message</p>
                    <a href=\"$return\"><button class=\"button message\">$value</button></a>
                </div>";
    }

    function warning($message, $value, $return) 
    {
        echo "
                <div class=\"card card-header header-warning\">
                    <h1>Warning!</h1>
                </div>
                <div class=\"card card-body gap\">
                    <p>$message</p>
                    <a href=\"$return\"><button class=\"button message\">$value</button></a>
                </div>";
    }

    function error($message, $value, $return) 
    {
        echo "
                <div class=\"card card-header header-error\">
                    <h1>Error!</h1>
                </div>
                <div class=\"card card-body gap\">
                    <p>$message</p>
                    <a href=\"$return\"><button class=\"button message\">$value</button></a>
                </div>";
    }

    function oops($message, $value, $return) 
    {
        echo "
                <div class=\"card card-header header-warning\">
                    <h1>Oops!</h1>
                </div>
                <div class=\"card card-body gap\">
                    <p>$message</p>
                    <a href=\"$return\"><button class=\"button message\">$value</button></a>
                </div>";
    }

    function sorry($message, $gap) 
    {
        if (!$gap)
        {
            echo "
                <div class=\"long\">
                    <div class=\"card\">
                        <p>Sorry! $message</p>
                    </div>
                </div>
            ";
        }
        else
        {
            echo "
                <div class=\"long no-gap\">
                    <div class=\"card\">
                        <p>Sorry! $message</p>
                    </div>
                </div>
            ";
        }
    }
    
    function course($course, $provider) 
    {
        $courseThumb = $course['Thumbnail'];
        $courseTitle = $course['Title'];
        $courseLink = 'course.php?id='.$course['Id'];
        $courseDate = date("d-m-y", strtotime($course['Date']));

        $providerPic = $provider['ProfilePicture'];
        $providerCompany = $provider['Company'];

        echo "
                <div class=\"card course three\">
                    <img src=\"uploads/$courseThumb\" alt=\"\" class=\"thumbnail\">
                    <div class=\"provider\">
                        <img src=\"uploads/$providerPic\" alt=\"\" class=\"profile-pic\">
                        <div>
                            <p>$providerCompany</p>
                        </div>
                    </div>
                    <a href=\"$courseLink\">
                        <h2>$courseTitle</h2>
                    </a>
                    <p>
                        <small>$courseDate</small>
                    </p>
                </div>
        ";
    }
    
    function instructor($instructor) 
    {
        $instructorName = $instructor['Name'];
        $instructorProfession = $instructor['Profession'];
        $instructorPic = $instructor['ProfilePicture'];
        
        echo "
                <div class=\"card instructor three\">
                    <img src=\"uploads/$instructorPic\" alt=\"\" class=\"profile-pic\">
                    <div>
                        <h2>$instructorName</h2>
                        <p><small>$instructorProfession</small></p>
                    </div>
                </div>
        ";
    }

    function comment($comment, $student)
    {
        $testimonial = $comment['Comment'];
        $studentPic = $student['ProfilePicture'];
        $studentName = $student['Name'];

        echo "
                <div class=\"card comment three\">
                    <p>$testimonial</p>
                    <div class=\"student\">
                        <img src=\"uploads/$studentPic\" alt=\"\">
                        <div>
                            <p>$studentName</p>
                        </div>
                    </div>
                </div>
        ";
    }
    
    function feedback($comment, $student)
    {
        $feedback = $comment['Comment'];
        $studentName = $student['Name'];
        $studentPic = $student['ProfilePicture'];
        $commentDate = $comment['CommentDate'];
        echo "
            <div class=\"card comment\">
                <div class=\"field input\">
                    <textarea name=\"feedback\" cols=\"30\" rows=\"7\" maxlength=\"2055\" readonly>$feedback</textarea>
                </div>
                <div class=\"student\">
                    <img src=\"uploads/$studentPic\" alt=\"\">
                    <div>
                        <p><b>$studentName</b> </p>
                        <p><small>$commentDate</small></p>
                    </div>
                </div>
            </div>
        ";
    }

    function button($value, $href, $class)
    {
        echo "
                <div class=\"long\">
                    <a href=\"$href\"><button class=\"button $class\">$value</button></a>
                </div>
        ";
    }
?>