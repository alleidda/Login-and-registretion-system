<?php require_once('includes/header.php') ?>

    <!-- Navigation Bar -->
    <?php require_once('includes/nav.php') ?>

    <div class="container">
    <div class="row">
        <div class="col-lg-6 m-auto">
    <div class="card bg-light mt-5 py-2">
       <div class="card-title">
       <?php display_message();
            login_validation();
       ?>
       <h2 class="text-center mt-2">Login Form</h2>
       <hr>
       </div>
       <div class="card-body">
        <form method="POST">
        <input type="email" name="UEmail" placeholder="User Email" class="form-control py-2 mb-2">
       <input type="password" name="UPass" placeholder="Password" class="form-control py-2 mb-2">
       <button class="btn btn-dark float-end">Login</button>
       </div>
       <div class="card-footer">
            <input type="checkbox" name="remember"> <span>Remember Me </span>
            <a href="recover.php" class="float-end">Forget Password</a>
            </form>
       </div>
       
    </div>
        </div>
    </div>
    </div>
    <?php require_once('includes/footer.php') ?>
