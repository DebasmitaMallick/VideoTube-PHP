<html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    </head>
    <body>
        <script>
            Swal.fire({
            text: "Are you sure you want to logout now?",
            icon: `warning`,
            showCancelButton: true,
            confirmButtonColor: `#d33`,
            cancelButtonColor: `#5cb85c`,
            confirmButtonText: `Yes`
            }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "logout.php";
            }
            else{
                window.location.href = "index.php";
            }
            })
        </script>
    </body>
</html>