<?php
    require_once("includes/header.php");
    require_once("includes/classes/ProfileGenerator.php");
    require_once("includes/classes/notificationsVideosProvider.php");
    require_once("includes/classes/deleteVideoApproved.php");
    if(!User::isLoggedIn()){
        header("Location: signIn.php");
        return;
    }
    if($userLoggedInObj->getRole() == 'normalUser'){
        echo "<p style='color: red'>You cannot access this page</p>";
        exit();
    }
    $stmt = $con->query("SELECT name from categories");
    $categoryData = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        array_push($categoryData, $row['name']);
    }
    $out = '';
    foreach ($categoryData as $categoryItem) {
        $item = str_replace(" ","",$categoryItem);
        $x = str_replace(array("#", "'", ";", "!", "&", "-", "@", " "), '', $categoryItem);
        $out .= '<tr><td>';
        $out .= '<input ondblclick="editAction(this, hidelabel'.$x.',save'.$x.')" class="disableCheck" type="checkbox" id="'.$x.'" name="'.$x.'" value="'.$categoryItem.'"><label id="hidelabel'.$x.'" for="'.$x.'"> '.$categoryItem.'</label>';
        $out .= '<button id="save'.$x.'" type="submit" class="btn btn-sm text-primary edit-tools" name="save'.$x.'"><i class="fas fa-arrow-up"></i></button>';
        $out .= '<button onclick="cancelAction('.$x.', hidelabel'.$x.',save'.$x.',cancel'.$x.')" id="cancel'.$x.'" type="button" class="btn btn-sm text-danger edit-tools" name="cancel'.$x.'"><i class="fas fa-times"></i></button>';
        $out .= '<button onclick="editAction('.$x.', hidelabel'.$x.',save'.$x.',cancel'.$x.')" type="button" class="btn categoryFeature1" name="edit'.$x.'"><i class="far fa-edit"></i></button>';
        $out .= '<a onclick="removeCategory(special'.$x.')" class="btn categoryFeature2"><i class="far fa-trash-alt"></i></a>';
        $out .= '<button hidden="hidden" id="special'.$x.'" type="submit" class="btn deleteCategorybtn" name="delete'.$x.'"></button>';
        $out .= '</td></tr>';
    }

    if(isset($_POST['addCategory'])){
        if($_POST['newCategory'] && !ctype_space($_POST['newCategory']) && !in_array($_POST['newCategory'], $categoryData)){
            $sql = "INSERT INTO categories(name) VALUES(:name)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(
            ':name' => htmlentities($_POST['newCategory'])));
            $_SESSION['editCategoryMessage'] = '<p style="color:green">Added Successfully</p>';
        }
        elseif(!$_POST['newCategory'] or ctype_space($_POST['newCategory'])){
            $_SESSION['editCategoryMessage'] = '<p style="color:red">You cannot add a blank category</p>';
        }
        else{
            $_SESSION['editCategoryMessage'] = '<p style="color:red">This category is already present</p>';
        }
        header("Location: admin.php");
        return;
    }


    $flag = false;
    if(isset($_POST['deleteCategory'])){
        $stmt1 = $con->query("SELECT id,name from categories");
        $stmt2 = $con->prepare("DELETE FROM categories WHERE name = :name; DELETE FROM videos WHERE category = :categoryId");
        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
            $x = str_replace(array("#", "'", ";", "!", "&", "-", "@", " "), '', $row['name']);
            if(isset($_POST[$x])){
                $flag = true;
                $stmt2->execute(array(
                    ':name' => $row['name'],
                    ':categoryId' => $row['id']
                ));
            }
        }
        if($flag === false){
            $_SESSION['editCategoryMessage'] = '<p style="color:red">Please select any category to delete</p>';
        }
        else{
            $_SESSION['editCategoryMessage'] = '<p style="color:green">Deleted Successfully</p>';
            $flag = false;
        }
        header("Location: admin.php");
        return;
    }


    $stmt1 = $con->query("SELECT * from categories");
    $stmt2 = $con->prepare("DELETE FROM categories WHERE name = :name; DELETE FROM videos WHERE category = :categoryId");
    $stmt3 = $con->prepare("UPDATE categories SET name = :name WHERE id = :id");
    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
        $x = str_replace(array("#", "'", ";", "!", "&", "-", "@", " "), '', $row['name']);
        if(isset($_POST['delete'.$x])){
            $stmt2->execute(array(
                ':name' => $row['name'],
                ':categoryId' => $row['id']
            ));
            $_SESSION['editCategoryMessage'] = '<p style="color:green">Deleted Successfully</p>';
            header("Location: admin.php");
            return;
        }
        if(isset($_POST['save'.$x])){
            if($_POST[$x] && !ctype_space($_POST[$x]) && !in_array($_POST[$x], $categoryData)){
                $stmt3->execute(array(
                    ':name' => $_POST[$x],
                    ':id' => $row['id']
                ));
                $_SESSION['editCategoryMessage'] = '<p style="color:green">Updated Successfully</p>';
            }
            elseif(!$_POST['save'.$x] && ctype_space($_POST[$x])){
                $_SESSION['editCategoryMessage'] = '<p style="color:red">You cannot add a blank category</p>';
            }
            else{
                $_SESSION['editCategoryMessage'] = '<p style="color:red">Please write a new category to update</p>';
            }
            header("Location: admin.php");
            return;
        }
    }


    $videoProvider = new notificationsVideosProvider($con, $userLoggedInObj);
    $videos = $videoProvider->getVideos();

    $videoGrid = new VideoGrid($con, $userLoggedInObj);

    $query = $con->prepare("SELECT * FROM delete_request");
    $query->execute();
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
        if(isset($_POST['approve'.$row['id']])){
            $sql = $con->prepare("UPDATE `delete_request` SET `status` = '1', `approvedBy` = :approvedBy  WHERE `delete_request`.`id` = :requestId;");
            $sql->bindParam(":requestId", $row['id']);
            $sql->bindParam(":approvedBy", $userLoggedInObj->getUsername());
            $sql->execute();
            deleteVideoApproved::videoDeleteApproved($con, $row["videoId"]);
        }
        elseif(isset($_POST['reject'.$row['id']])){
            $sql = $con->prepare("UPDATE `delete_request` SET `status` = '0' WHERE `delete_request`.`id` = :requestId;");
            $sql->bindParam(":requestId", $row['id']);
            $sql->execute();
            header("Location: admin.php");
            return;
        }
    }
?>
<div class="container d-flex flex-column">
    <div>
        <?php
            require("profile.php");
        ?>
    </div>

    <div class="mt-3">
        <div class="row pb-3">

            <div class="col">
                <div id="notificationCard" class="card">
                    <div class="card-header bg-info text-white text-center">
                        <div class="card-title">Delete Requests</div>
                        <div class="card-tools">
                            <button onclick="plusMinus(this)" type="button" class="btn btn-info btn-sm text-white" data-toggle="collapse" data-target="#notificationBody">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div id="notificationBody" class="collapse show">
                        <div class="card-body">
                            <div class="largeVideoGridContainer">
                                <?php
                                if(sizeof($videos) > 0) {
                                    echo $videoGrid->createLargeNotifications($videos, null, true);
                                }
                                else {
                                    echo "No more requests";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            if(isset($_SESSION['editCategoryMessage'])){
                echo $_SESSION['editCategoryMessage'];
                unset($_SESSION['editCategoryMessage']);
            } 
        ?>
        <div class="pb-3">
            <div id="editCategoryCard" class="card">
                <div class="card-header bg-info text-white text-center">
                    <div class="card-title">CATEGORY</div>
                    <div class="card-tools">
                        <button onclick="plusMinus(this)" type="button" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#categoryBody">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div id="categoryBody" class="collapse show">
                    <div class="card-body">
                        <form method="POST" id="categoryForm">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="newCategory" id="newCategory" placeholder="Enter a new category...">
                                <div class="input-group-append">
                                    <button style="z-index:0" class="btn btn-primary" type="submit" name="addCategory">ADD</button>
                                </div>
                            </div>
                            <table class='table table-hover'>
                                <tbody>
                                    <?= $out ?>
                                </tbody>
                            </table>
                            <div class="pt-3">
                                <input type="checkbox" id="checkAll" name="checkAll" value="checkAll"><label for="checkAll"> Check All</label>
                                <a id="removeListItem" class="btn btn-danger text-white categoryTool2"><i class="far fa-trash-alt"></i> Delete</a>
                                <button id="deleteCategory" hidden="hidden" type="submit" name="deleteCategory" class="btn">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
            <!-- edit -->
        </div>
        <?php
            if($userLoggedInObj->getRole() == 'superAdmin'){
                echo 
                '<div>
                    <div id="assignRolesCard" class="card text-center bg-danger">
                        <div class="card-header text-white text-center">
                            <div class="card-title">Assign Role
                            </div>
                            <div class="card-tools">
                                
                                    <a href="assignRoles.php" class="btn text-white"><i class="fas fa-external-link-alt"></i></a>
                                
                            </div>
                        </div>
                    </div>
                </div>';
            }
        ?>


    </div>
</div>
