<?php
    require_once("includes/header.php");
    require_once("includes/classes/userPrivileges.php");

    if(!User::isLoggedIn()){
        header("Location: signIn.php");
        return;
    }
    
    if($userLoggedInObj->getRole() != 'superAdmin'){
        echo "<p style='color: red'>You cannot access this page</p>";
        exit();
    }
    if(isset($_POST['submit'])){
        $rolesInfo = userPrivileges::showPrivileges($con, $_POST['categoryUserId']);
        $flag = false;
        $sql = "INSERT INTO privileges(userId, categoryId, upload, `delete`, approval, role) VALUES(:userId, :categoryId, :upload, :delete, :approval, :role);";
        $query = $con->prepare($sql);
        $deleteQuery = $con->prepare("DELETE FROM privileges WHERE id = :roleId");
        $stmt = $con->query("SELECT * from categories");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $name = str_replace(' ', '', $row["name"]);
            if(isset($_POST[$name])){
                $upload = (isset($_POST['acessUpload'.$name])) ? 1 : 0;
                $delete = (isset($_POST['acessDelete'.$name])) ? 1 : 0;
                //checking if this category is already assigned
                if(array_key_exists($row['id'],$rolesInfo)){
                    $flag = 'update';
                    $demox = $row['id'];
                    $demoy = $_POST['categoryUserId'];
                    $roleIdQuery = $con->query("SELECT id FROM privileges where privileges.categoryId = $demox AND privileges.userId = $demoy");
                    while ($roleRow = $roleIdQuery->fetch(PDO::FETCH_ASSOC)){
                        $roleId = $roleRow['id'];
                    }
                    $deleteQuery->execute(array(
                            ':roleId' => $roleId));
                    $query->execute(array(
                    ':userId' => $_POST['categoryUserId'],
                    ':categoryId' => $row['id'],
                    ':upload' => $upload,
                    ':delete' => $delete,
                    ':approval' => 0,
                    ':role' => 'category'));
                }
                else{
                    $flag = null;
                    $query->execute(array(
                    ':userId' => $_POST['categoryUserId'],
                    ':categoryId' => $row['id'],
                    ':upload' => $upload,
                    ':delete' => $delete,
                    ':approval' => 0,
                    ':role' => 'category'));
                    
                }
            }
        }
        if(isset($_POST['admin'])){
            $sql = "INSERT INTO privileges(userId, role) VALUES(:userId, :role);
                    UPDATE `users` SET `role` = 'admin' WHERE `users`.`id` = :userId;";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(
            ':userId' => $_POST['categoryUserId'],
            ':role' => 'admin'));
            $_SESSION['assignMessage'] = "<p style='color:green'>Record Added Successfully</p>";
        }
        header("Location: assignRoles.php?term=".$_SESSION['searchedNo']);
        return;
    }

    // delete Privilege::STARTS
    $deleteFlag = false;
    $query = $con->query("SELECT * FROM privileges");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)){
        if(isset($_POST['removeRole'.$row['id']]) or isset($_POST[$row['id']])){
            $deleteFlag = true;
            $delete = $con->prepare("DELETE FROM privileges WHERE id = :id");
            $delete->execute(array(
                ':id' => $row['id']));
        }
    }

    $query = $con->query("SELECT * FROM privileges WHERE privileges.role = 'admin'");
    while ($row = $query->fetch(PDO::FETCH_ASSOC)){
        if(isset($_POST['removeRoleAdmin'.$row['userId']]) or isset($_POST['admin'.$row['userId']])){
            $deleteFlag = true;
            $sql = "DELETE FROM privileges WHERE id = :id;
                    UPDATE `users` SET `role` = 'normalUser' WHERE `users`.`id` = :userId;";
            $delete = $con->prepare($sql);
            $delete->execute(array(
                ':userId' => $row['userId'],
                ':id' => $row['id']));
        }
    }
    if($deleteFlag === true){
        header("Location: assignRoles.php?term=".$_SESSION['searchedNo']);
        return;
    }
    // delete Privilege::ENDS

    // checking for the search term
    if(!isset($_GET["term"]) || $_GET["term"] == "") {
        echo "You must enter a number";
        exit();
    }
    $term = $_GET["term"];
    if(!is_numeric($_GET["term"])){
        echo"Search by number";
        return;
    }
    
    $_SESSION['searchedNo'] = $_GET["term"]; 

    // RENDERING CATEGORIES::STARTS
    $stmt = $con->query("SELECT name from categories");
    $out = '';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $x = str_replace(' ', '', $row['name']);
        // CHANGED FINAL::S
        $out .= '
                <tr class="checkboxRow">
                    <td><input onclick="checkError(this, `disabledCheckbox'.$x.'`)" class="categoryCheckbox" type="checkbox" id="'.$x.'" name="'.$x.'" value="'.$x.'"><label for="'.$x.'">'.$x.'</label></td>
                    <td><input data-group="group1'.$x.'" class="disabledCheckbox'.$x.' disabled2" type="checkbox" name="acessUploadFalse'.$x.'" value="acessUpload'.$x.'" disabled><label for="acessUpload'.$x.'">Upload</label>
                    <input hidden="true" data-group="group1'.$x.'" class="activeAccess" type="checkbox" name="acessUpload'.$x.'" value="acessUpload'.$x.'"></td>
                    <td><input data-group="group2'.$x.'" class="disabledCheckbox'.$x.' disabled2" type="checkbox" name="acessDeleteFalse'.$x.'" value="acessDelete'.$x.'" disabled><label for="acessDelete'.$x.'"> Delete</label>
                    <input hidden="true" data-group="group2'.$x.'" class="activeAccess" type="checkbox" name="acessDelete'.$x.'" value="acessDelete'.$x.'"></td>
                </tr>';
        // CHANGED FINAL::E
    }
    // RENDERING CATEGORIES::ENDS


    // RENDERING CARD::STARTS
    $card = '';
    $data = array();
    $query = $con->prepare("SELECT * FROM users WHERE number LIKE CONCAT('%', :term, '%') AND role NOT IN (SELECT role FROM users WHERE role = 'superAdmin')");
    $query->bindParam(":term", $term);
    $query->execute();
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $privilageData = userPrivileges::getEditPrivileges($con,$row['id']);
        
        $card .= 
        '<div class="card mb-2 mr-4 assignRolesCards">
            <img class="card-img-top" src="'.$row["profilePic"].'" alt="Card image" style="width:57%; margin-left:auto; margin-right:auto;">
            <div class="card-body">
                <h4 class="card-title">'.$row["firstName"]." ".$row["lastName"].'</h4>
                <div class="card-text">Username: '.$row["username"].'</div>
                <p class="card-text">Phone number: '.$row["number"].'</p>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#editModal'.$row['id'].'">View Role</button>
                <button onclick="passValue('.$row['id'].')" type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Assign Role</button>
            </div>
        </div>';
        $card .= 
        '<div class="modal fade" id="editModal'.$row['id'].'">
            <div class="modal-dialog">
                <form id="editModalForm'.$row['id'].'" class="modal-content" action="assignRoles.php" method="post">
                    <div class="modal-header text-center">
                        <h4 class="modal-title">Privileges</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body" style="text-align: left !important;">
                        '.$privilageData.'
                    </div>
                    <div class="modal-footer">
                        <a onclick="deletePrivilege(`editModalForm'.$row['id'].'`, `deletePrivileges'.$row['id'].'`)" class="btn btn-danger text-white"><i class="far fa-trash-alt"></i> Delete</a>
                        <button id="deletePrivileges'.$row['id'].'" hidden="hidden" type="submit" name="deletePrivileges" class="btn">Delete</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>';
    }
    
    // RENDERING CARD::ENDS
?>
<div class="mt-4 d-flex text-center">
    <?= $card; ?>
</div>

<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <form id="roleForm" class="modal-content" action="assignRoles.php" method="post">
            <div class="modal-header text-center">
                <h4 class="modal-title">Assign Roles</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="text-align: left !important;">
                <div class="pb-2">
                    <input type="checkbox" id="admin" name="admin" value="admin">
                    <label for="admin">ADMIN</label>
                </div>
                <div id="categoryList" class="category-users">
                    <div id="requestStatusBody" class="collapse show"></div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Upload</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $out ?>
                            </tbody>
                        </table>
                    <input class="categoryUserId" type="hidden" name="categoryUserId" value="">
                    <div class="pt-3">
                        <input type="checkbox" id="checkAllRoles" name="checkAllRoles" value="checkAllRoles">
                        <label for="checkAll">Check All</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button  onclick="addRoles()" class="btn btn-primary" type="button">Submit</button>
                <button id="falseAddBtn" hidden="hidden" class="btn" type="submit" name="submit">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>


