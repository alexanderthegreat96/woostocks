<?php
session_start();
require "../class.loader.php";
$session = \LexSystems\Login::returnSession();
if(!$session['status'])
{
    \LexSystems\Admin::redirect('login.php');
}
$admin = new \LexSystems\Admin();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>WooStocks BETA - Log-uri</title>
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
            background-color: #f5f5f5;
        }

    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light border border-dark">
    <a class="navbar-brand" href="#"><i class="fas fa-database"></i> WooStocks BETA</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="logs.php"><i class="fas fa-clock"></i> Log-uri</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</nav>
<section class="mt-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header  bg-dark text-white">
                        <h6><i class="fas fa-server"></i> Log-uri</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $logs = $admin->getLogs('1000000');
                        if($logs)
                        {
                            echo $admin->build_html_table($logs,'inline','data-view');
                        }
                        else
                        {
                            echo "Nu sunt intrari.";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/datatables.min.js"></script>
<script src="js/page.js"></script>
</html>
