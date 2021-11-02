<?php require_once('includes/header.php') ?>

    <!-- Navigation Bar -->
    <?php require_once('includes/nav.php') ?>


    <!-- Admin Main Page -->
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card bg-light mt-5 py-2">
                    <h3>
                    <?php 

                        if (logged_in()) {
                            echo "You have Succesfully Logged in";
                        } else {
                            redirect('login.php');
                        }
                    
                    ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('includes/footer.php') ?>
