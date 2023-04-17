<?php
header("Content-Type: text/javascript");
require_once("includes/config.php");
$stmt = $con->query("SELECT name FROM categories");
$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $data[] = $row["name"];
}
// print_r($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <meta charset="UTF-8"> -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
        myFunction();
        function myFunction(){
            var categoryList1 = <?php echo json_encode($data); ?>;
            var categoryList = [
            {
                'test': 'envelope',
                'notification': '4 new messages',
                'time': '3 mins'
            },
            {
                'test': 'users',
                'notification': '8 friend requests',
                'time': '12 hours'
            },
            {
                'test': 'file',
                'notification': '3 new reports',
                'time': '2 days'
            }
        ];
            // Display the array elements
            // for(var i = 0; i < categoryList.length; i++){
            //     document.write(categoryList[i]);
            // }
            renderCategories(categoryList);
        }
        function renderCategories(categoryList){
            var template = $('#handlebars-categories').html();
            var hbcontent = Handlebars.compile(template)(
                {  
                    "categoryList" : categoryList
                });
            $("#categoryList").append(hbcontent);
        }
    </script>
</body>
</html>