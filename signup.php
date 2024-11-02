<html>
    <head>
        <title>COSN | Sign Up</title>
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
            height:auto; 
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
            margin-top: 5px; 
            padding: 5px;
            border: 1px solid black;
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
                <a href="login.php" id="signup"> Log In </a> 
            </div>
        </div>

        <div id="bar2" >
            <div style="font-size: 30px; 
            color: #9e34eb; 
            font-weight: 
            bold; 
            margin-top: 20px;"> Sign Up to COSN </div>
            <br> <br> 
            <form action="signup_handler.php" method="post">
            <input type="text" 
                id="typed_email" 
                placeholder="Enter your first name" 
                name="first_name"> <br> <br>

                <input type="text" 
                id="typed_email" 
                placeholder="Enter your last name" 
                name="last_name"> <br> <br>

                Gender:
                <select name="gender">
                    <option>male</option>
                    <option>female</option>
                </select>

                <br> <br>
                <input type="date" 
                id="typed_email" 
                name="date_of_birth"> <br> <br>


                <input type="text" 
                id="typed_email" 
                placeholder="Enter your email" 
                name="email"> <br> <br>

                <input type="password" 
                id="typed_email" 
                placeholder="Enter your password" 
                name="password"> <br> <br>

                <input type="password" 
                id="typed_email" 
                placeholder="Retype your password" 
                name="retyped_password"> <br> <br>

                <input type="submit"
                id="submit_button" 
                value="Sign Up"> <br> <br>
            </form>

        </div>
    
    </body>

</html>
