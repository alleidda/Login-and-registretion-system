<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

//Clean String Values
function clean ($string)
{
   // return htmlentities($string);
}

//Redirection
function redirect($location)
{
    return header("location:$location");
}

//Set Session Message

function set_message($msg)
{
    if(!empty($msg)) {
        $_SESSION['Message'] = $msg;
    }
    else
    {
        $msg ="";
    }
}

// Display Message Function
function display_message()
{
   if (isset($_SESSION['Message'])) {
        echo $_SESSION['Message'];
        unset($_SESSION['Message']);
   }
}


//Generate Token

function Token_Generator()
{
    $token = $_SESSION['token']=md5(uniqid(mt_rand(),true));
    return $token;
}

//Send Email Function
function send_email($email, $sub, $msg, $header, $UserName)
{
    global $mail;
    try {
        //Server settings
        $mail->SMTPDebug = 2;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'mail.flexone.cloud';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'contact@myprojectstart.com';                     //SMTP username
        $mail->Password   = 'azerty';                               //SMTP password
        $mail->SMTPSecure = STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('contact@myprojectstart.com', 'Admin');
        global $cnn;
        $mail->addAddress($email, $UserName);     //Add a recipient
        //$mail->addReplyTo('info@example.com', 'Information');
       // $mail->addCC('cc@example.com');
       // $mail->addBCC('bcc@example.com');
    
        //Attachments
       // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      //  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $sub;
        $mail->Body    = $msg;
       // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    //return mail($email, $sub, $msg, $header);
}

//***********User Validation functions ************ */


//Errors Function
function Error_validation($Error)
{
    return '<div class="alert alert-danger">'.$Error.'</div>';
}

//User Validation Function
function user_validation()
{
   if (isset($_POST['register'])) {
      $FirstName = $_POST['FirstName'];
      $LastName = $_POST['LastName'];
      $UserName = $_POST['UserName'];
      $Email = $_POST['Email'];
      $Pass = $_POST['pass'];
      $CPass = $_POST['cpass'];
      
     $Errors = [];
     $Max = 20;
     $Min = 03;

//Check the First Name Characters

     if (strlen($FirstName)<$Min) {
         $Errors[]="First Name Cannot Be Less Than {$Min} Characters";
     }
     if (strlen($FirstName)>$Max) {
        $Errors[]="First Name Cannot Be More Than {$Max} Characters";
    }
    //Check the First Name Characters
    if (strlen($LastName)<$Min) {
        $Errors[]="Last Name Cannot Be Less Than {$Min} Characters";
    }
    if (strlen($LastName)>$Max) {
        $Errors[]="Last Name Cannot Be More Than {$Max} Characters";
    }
    //Check the User Name Characters
    if(!preg_match("/^[a-zA-Z,0-9]*$/",isset($UserName))) {
        $Errors[]="User Name cannot Accept Those Characters";
    }

//Check the Email Exists Characters
    if (Email_Exists($Email)) {
        $Errors[]="Email Already Registred !";
    
       }

//Check the User Name Characters
    if (User_Exists($UserName)) {
        $Errors[]="User Name Already Registred !";
    
       }
//Check the Password Characters
    if ($Pass != $CPass)
    {
        $Errors[] = "Password Not Matched !";
    }

    if(!empty($Errors)) {
        foreach($Errors as $Error) {
            echo Error_validation($Error);
        }
    } else {
       if( user_registration($FirstName, $LastName, $UserName, $Email, $Pass)) {
        set_message('<p class = "bg-success text-center lead">You Have Successfully Registred ! Please Check Your Activated Link</p>');
            redirect("index.php");
       } else {
        set_message('<p class = "bg-danger text-center lead">Your Account Is Not Registered , Please Try Again </p>');
        redirect("index.php");
       }
    }
 
   }   
}

//Email Exists Function

function Email_Exists($email)
{
     global $cnn;
    $req = $cnn->prepare("SELECT * FROM Users WHERE Email = :Email");
   $req->execute(array("Email"=>$email));
 while($donnees = $req->fetch()) {
 if ($donnees['Email'] == $email) {
           return true;
       } else {
           return false;
       }
 }
}

//User Exists Function

function User_Exists($user)
{
     global $cnn;
    $req = $cnn->prepare("SELECT * FROM Users WHERE UserName = :UserName");
   $req->execute(array("UserName"=>$user));
 while($donnees = $req->fetch()) {
 if ($donnees['UserName'] == $user) {
           return true;
       } else {
           return false;
       }
 }
}

//User Registration Function
function user_registration($FName, $LName, $UName, $Email, $Pass) {
    
    $FirstName = $FName;
    $LastName = $LName;
    $UserName = $UName;

    if (Email_Exists($Email))
    {
        return true;
    }
    else if (User_Exists($UserName)) {
        return true;
    } else {
        $Pass = md5($Pass);
        $Validation_Code = md5($UserName + microtime());
        global $cnn;
        $requete = $cnn->prepare('INSERT INTO Users(FirstName, LastName, UserName, Email, Password, Validation_Code, Active) VALUES(?,?,?,?,?,?,?)');
        $requete->execute(array($FirstName, $LastName, $UserName, $Email, $Pass, $Validation_Code, 0));
        $subject = "Active Your Account";
        $msg = "Please Click the Link Below to Active Your Account https://myprojectstart.com//login_registration_emailActivation/activate.php?Email=$Email&Code=$Validation_Code";
        $header = "From No-Reply contact@myprojectstart.com";

        send_email($Email, $subject, $msg, $header, $UserName);

        return true;
    }
}

//Activation Function

function activation()
{
    if ($_SERVER['REQUEST_METHOD']=="GET")
    {
        $Email = $_GET['Email'];
        $Code = $_GET['Code'];

        global $cnn;
        $requete = $cnn->prepare('SELECT * from Users where Email = :Email AND Validation_Code = :Code');
        $requete->execute(array("Email"=>$Email, "Code"=>$Code));
        $count_line = $requete->rowCount();
        if ($count_line > 0) {
            $requete2 = $cnn->prepare("UPDATE Users SET Active = 1, Validation_Code = 0 where Email = :Email AND Validation_Code = :Code");
            $requete2->execute(array("Email"=>$Email, "Code"=>$Code));
             set_message('<p class = "bg-success text-center lead">Your Account Successfully Activated </p>');
              redirect('login.php');
            } else {
            echo('<p class = "bg-danger text-center lead">Your Account is Not Activated </p>');
        }
    }
}

///User Login Validation Function
function login_validation()
{
    $Errors = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $UserEmail = $_POST['UEmail'];
        $UserPass = $_POST['UPass'];
        $Remember = isset($_POST['remember']);

        if (empty($UserEmail)) {
            $Errors[] = "Please Enter Your Email";
        }
        if (empty($UserPass)) {
            $Errors[] = "Please Enter Your Password";
        }
        if (!empty($Errors))
        {
            foreach ($Errors as $Error) {
                echo Error_validation($Error);
            }
        } else {
            if (user_login($UserEmail, $UserPass, $Remember))
            {
                redirect("admin.php");
            } else {
                echo Error_validation("Please Enter a Correct Email or Password");
            }
        }
    }
}

//User Login Function
function user_login($UEmail, $UPass, $Remember)
{
    global $cnn;
    $requete = $cnn->prepare("SELECT * from Users where Email = :Email and Active = 1");
    $requete->execute(array("Email"=>$UEmail));
    if (($requete->rowCount()) == 1) {
        while ($donnees = $requete->fetch()) {
            $db_pass = $donnees['Password'];
        }
        if (md5($UPass) == $db_pass) {
            if ($Remember == true) {
                setcookie('email', $UEmail, time() + 86400);
            }
            $_SESSION['Email'] = $UEmail;
            return true;
        } else {
            return false;
        }
    }

}

//Logged in Function

function logged_in()
{
    if (isset($_SESSION['Email']) || isset($_COOKIE['email'])) {
        return true;
    } else {
        return false;
    }
}

//////////////Recover Function/////////////

function recover_password()
{
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token']) {
            $Email = $_POST['UEmail'];

            if (Email_Exists($Email)) {
                $code = md5($Email + microtime());
                setcookie('temp_code', $code, time()+300);

                global $cnn;
                $requete = $cnn->prepare("UPDATE Users set Validation_Code=:code where Email=:Email");
                $requete->execute(array("code" => $code, "Email" => $Email));

                $Subject = "Please Reset the Password";
                $Message = "Please Follow on Below Link to Reset the Password https://myprojectstart.com/login_registration_emailActivation/code.php?Email='$Email'&Code='$code'";
                $header = "noreply@onlineittuts.com";
                if (send_email($Email, $Subject, $Message, $header)) {
                    echo '<div class="alert alert-success">Please Check Your Email :)</div>';
                } else {
                    echo Error_validation("We Couldn't Send an Email");
                }

            } else {
                echo Error_validation(" Email Not Found...");
            }
        } else {
            redirect("index.php");
        }
    }
}

/// Validation Code Function
function validation_code()
{
    if (isset($_COOKIE['temp_code'])) {
            if (!isset($_GET['Email']) && !isset($_GET['Code'])) {
                redirect('index.php');
            }
            else if (empty($_GET['Email']) && empty($_GET['Code'])) {
                redirect("index.php");
            } else {
                if (isset($_POST['recover-code'])) {
                    $Code = $_POST['recover-code'];
                    $Email = $_GET['Email'];
                    
                    global $cnn;
                    $requete = $cnn->prepare("SELECT * from Users where Validation_Code = :Code and Email = :Email");
                    $requete->execute(array("Code" => $Code, "Email" => $Email));
                    // echo ($requete->rowCount());
                    if (($requete->rowCount()) > 0) {
                        setcookie('temp_code', $Code, time()+300);
                        redirect("reset.php?Email=$Email&Code=$Code");
                    } else {
                        echo Error_validation("Query Failed");
                    }
                }
            }
    } else {
        set_message('<div class="alert alert-danger"> Your Code Has Been Expired :) </div>');
        redirect("recover.php");
    }
}

/////////////////////Reset Password Function//////////////////////////////

function reset_password()
{
if($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_COOKIE['temp_code'])) 
    {
        if (isset($_GET['Email']) && isset($_GET['Code'])) 
        {
                if (isset($_SESSION['token']) && isset($_POST['token'])) 
                {
                        if ($_SESSION['token'] == $_POST['token']) 
                        {
                           if ($_POST['reset-pass'] == $_POST['reset-c-pass'])
                           {
                               $Password = md5($_POST['reset-pass']);
                               global $cnn;
                               $requete = $cnn->prepare("UPDATE Users set Password = :Password, Validation_Code = 0 where Email = :Email");
                               $requete->execute(array(":Password"=>$Password, ":Email"=>$_GET['Email']));
                               if (($requete->rowCount()) > 0) {
                                   set_message('<div class="alert alert-succes">Your Password Has Benn Updated :)</div>');
                                   redirect("login.php");
                               } else {
                                   set_message('<div class="alert alert-danger"> Something Went Wrong :) </div>');
                               }

                           }
                           else
                           {
                            set_message('<div class = "alert alert-danger">Your Password Not Matched :) </div>');
                           }
                        } 
                        else 
                        {
                            set_message('<div class = "alert alert-danger">Your Code  Not Matched :) </div>');
                        }
                } 
                else 
                {
                    set_message('<div class = "alert alert-danger">Your Code Not Matched :)" </div>');
                }
        } 
        else 
        {
            set_message('<div class="alert alert danger">Your Code or Your Email Not Matched</div>');
        }
    } 
    else 
    {
        set_message('<div class = "alert alert-danger">Your Time Period Has Been Expired </div>');
    }
}
}

?>