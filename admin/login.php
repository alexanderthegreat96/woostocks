<?php
session_start();
require "../class.loader.php";
$session = \LexSystems\Login::returnSession();

if($session['status'])
{
    \LexSystems\Admin::redirect('index.php');
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Woostocks BETA - Login</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom styles for this template -->
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: -webkit-box;
            display: flex;
            -ms-flex-align: center;
            -ms-flex-pack: center;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: center;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }
        .form-signin .checkbox {
            font-weight: 400;
        }
        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>

<body class="text-center">
<form class="form-signin" method="POST" action="<?php echo $_SERVER["REQUEST_URI"];?>">
    <h2><i class="text-danger fas fa-5x fa-database"></i></h2>
    <hr/>
    <h1 class="h3 mb-3 font-weight-normal">Intrare in platforma</h1>
    <label for="inputEmail" class="sr-only">Username</label>
    <input type="text" name="username" id="inputEmail" class="form-control" placeholder="Numele de utilizator" required autofocus>
    <label for="inputPassword" class="sr-only">Parola</label>
    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
    <?php
        if(isset($_POST['login']))
        {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $try = \LexSystems\Login::login($username,$password);
                if($try['status'])
                {
                    \LexSystems\Admin::redirect('index.php');
                }
                else
                {
                    ?>
                    <div class="alert alert-danger"><?php echo $try['error'];?></div>
                    <?php
                }
        }
    ?>
    <button class="btn btn-lg btn-primary btn-block" name="login" type="submit"><i class="fas fa-sign-in-alt"></i> Continua</button>
    <hr/>
    <p class="mt-5 mb-3 text-muted">&copy; <?php echo date("Y");?> <a href="dthdevops.net">Alexandru Lupaescu</a></p>
</form>
</body>
</html>
