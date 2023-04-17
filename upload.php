<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
if(!isset($_SESSION["userLoggedIn"])){
  $_SESSION['previous-page'] = "upload.php";
  echo '<script>
        Swal.fire({
            title: `Please Sign In`,
            icon: `warning`,
            showCancelButton: true,
            confirmButtonColor: `#3085d6`,
            cancelButtonColor: `#d33`,
            confirmButtonText: `Sign In`
            }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "signIn.php";
            }
            else{
                window.location.href = "index.php";
            }
            })
        </script>';
  return;
}
?>


<div class="column">

    <?php
    $formProvider = new VideoDetailsFormProvider($con);
    echo $formProvider->createUploadForm();
    ?>


</div>

<script>
$("form").submit(function() {
    $("#loadingModal").modal("show");
});
</script>



<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
        Please wait. This might take a while.
        <img src="assets/images/icons/loading-spinner.gif">
      </div>

    </div>
  </div>
</div>




<?php require_once("includes/footer.php"); ?>
                