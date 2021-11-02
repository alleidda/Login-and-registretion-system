<?php 

require_once('includes/header.php');

?>

    <div class="container">
    <div class="row">
        <div class="col-lg-6 m-auto">
    <div class="card bg-light mt-5 py-2">
       <div class="card-title">
       <h2 class="text-center mt-2">Recover Password</h2>
       <hr>
       <?php recover_password();
             display_message();
       ?>
       </div>
       <div class="card-body">
            <form method="POST">
                <input type="email" name="UEmail" placeholder="User Email" class="form-control py-2 mb-2">
                <input type="hidden" name="token" value="<?php echo Token_Generator();
                ?>">
       </div>
       <div class="card-footer">
            <button class="btn btn-danger float-end">Cancel</button>
            <button class="btn btn-success float-start">Send Password</button>
       </div>
       </form>
       
    </div>
        </div>
    </div>
    </div>
    <?php require_once('includes/footer.php') ?>
