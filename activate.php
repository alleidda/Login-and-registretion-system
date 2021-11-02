<?php
require_once('includes/header.php');
require_once('includes/nav.php');
?>

<!-- Activate Main Page -->
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card bg-light mt-5 py-2">
            <?php activation(); ?>
                <h3 class="text-center">Activation</h3>
            </div>
        </div>
    </div>
</div>

<?php
require_once('includes/footer.php');
?>