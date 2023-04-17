<?php
    require_once("includes/header.php");
    require_once("includes/classes/userPrivileges.php");
    echo `Swal.fire({
        title: 'This user is already assigned!',
        text: "Do you want to update the record?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.replace("assignRoles.php");
                $(x).click();
                Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
            }
        })`;
?>

