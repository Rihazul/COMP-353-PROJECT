<html>
    <head>
        <title>COSN | Log In</title>
    </head>

    <style> 
        #bar{
            height: 100px;
            background-color: #9e34eb;
            color: #3f0b57 ;
            padding: 4px;
        }
        #signup{
            background-color: #eb3480;
            color: white;
            font-size: small;
            padding: 5px;
            border-radius: 5px;
            width:50px;
            margin-top: 8px;
            text-align: center;
            float: right;
        }
        #bar2{
            background-color: white;
            width:800px; 
            height:400px; 
            margin:auto;
            margin-top:50px;
            padding:10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            text-align: center;
        }
        #typed_email{
            width: 200px; 
            height: 30px; 
            border-radius: 5px; 
            border: none; 
            margin-top: 20px; 
            padding: 5px;
        }
        #submit_button{
            width: 200px; 
            height: 30px; 
            border-radius: 5px; 
            border: none; 
            padding: 5px; 
            background-color: #9e34eb; 
            color: white; 
            font-weight: bold;
        }
    </style>
    
    <body style = "font-family: tahoma; 
    background-color:#fac3da">
        <div id="bar">
            <div style = "font-size: 40px;
            font-weight: bold;"> COSN </div>

            <div> 
                <a href="signup.php" id="signup"> Sign Up </a> 
            </div>
        </div>

        <div id="bar2" >
            <div style="font-size: 30px; 
            color: #9e34eb; 
            font-weight: 
            bold; 
            margin-top: 20px;"> Log in to COSN </div>
            <br> <br> 
            <form action="profile.php" method="post">
                <input type="text" 
                id="typed_email" 
                placeholder="Enter your email" 
                name="email"> <br> <br>

                <input type="password" 
                id="typed_email" 
                placeholder="Enter your password" 
                name="password"> <br> <br>

                <input type="submit" 
                id="submit_button" 
                value="Log in"> <br> <br>
            </form>

            <a href="#" style="text-decoration: none; color: #9e34eb; font-size: small;"> Forgot your password? </a>

        </div>
    
    </body>

</html>
<?php


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'admin') {
    header("Location: admin.php"); // Redirect to login page if not admin
    exit();
}
else
header("Location: profile.php");


//Database connection
//include 'db_connection.php';
//$conn = OpenCon();

?>