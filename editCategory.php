<?php
    require_once("includes\header.php");
    $stmt = $con->query("SELECT name from categories");
    $out = '';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $item = htmlentities($row['name']);
        $x = str_replace(' ', '', $item);
        $out .= '<tr><td>';
        $out .= '<input ondblclick="editAction(this, hidelabel'.$x.',save'.$x.')" class="disableCheck" type="checkbox" id="'.$x.'" name="'.$x.'" value="'.$item.'"><label id="hidelabel'.$x.'" for="'.$x.'"> '.$item.'</label>';
        $out .= '<button style="display:none;font-size: 13px;" id="save'.$x.'" type="submit" class="btn btn-sm btn-primary" name="save'.$x.'">SAVE</button>';
        $out .= '<button onclick="editAction('.$x.', hidelabel'.$x.',save'.$x.')" type="button" class="btn categoryFeature1" name="edit'.$x.'"><i class="far fa-edit"></i></button>';
        $out .= '<a onclick="removeCategory(special'.$x.')" class="btn categoryFeature2"><i class="far fa-trash-alt"></i></a>';
        $out .= '<button hidden="hidden" id="special'.$x.'" type="submit" class="btn deleteCategorybtn" name="delete'.$x.'"></button>';
        $out .= '</td></tr>';
    }

    if(isset($_POST['addCategory'])){
        if($_POST['newCategory']){
            $sql = "INSERT INTO categories(name) VALUES(:name)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(
            ':name' => $_POST['newCategory']));
            $_SESSION['editCategoryMessage'] = '<p style="color:green">Added Successfully</p>';
        }
        else{
            $_SESSION['editCategoryMessage'] = '<p style="color:red">Please write any category to add</p>';
        }
        header("Location: editCategory.php");
        return;
    }


    $flag = false;
    if(isset($_POST['deleteCategory'])){
        $stmt1 = $con->query("SELECT id,name from categories");
        $stmt2 = $con->prepare("DELETE FROM categories WHERE name = :name; DELETE FROM videos WHERE category = :categoryId");
        while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
            $x = str_replace(' ', '', $row['name']);
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
        header("Location: editCategory.php");
        return;
    }


    $stmt1 = $con->query("SELECT * from categories");
    $stmt2 = $con->prepare("DELETE FROM categories WHERE name = :name; DELETE FROM videos WHERE category = :categoryId");
    $stmt3 = $con->prepare("UPDATE categories SET name = :name WHERE id = :id");
    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)){
        $x = str_replace(' ', '', $row['name']);
        if(isset($_POST['delete'.$x])){
            $stmt2->execute(array(
                ':name' => $row['name'],
                ':categoryId' => $row['id']
            ));
            $_SESSION['editCategoryMessage'] = '<p style="color:green">Deleted Successfully</p>';
            header("Location: editCategory.php");
            return;
        }
        if(isset($_POST['save'.$x])){
            $stmt3->execute(array(
                ':name' => $_POST[$x],
                ':id' => $row['id']
            ));
            $_SESSION['editCategoryMessage'] = '<p style="color:green">Updated Successfully</p>';
            header("Location: editCategory.php");
            return;
        }
    }


    // EDIT FUNCTIONALITY::STARTS

    
    
?>
<div class="container">
    <div id="editCategoryCard" class="card">
        <div class="card-header bg-primary text-white text-center">
            <div class="card-title">CATEGORY</div>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#categoryBody">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div id="categoryBody" class="collapse">
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
                        <button type="button" name="editCategory" class="btn btn-warning text-white categoryTool1" data-toggle="collapse" data-target="#categoryEditBody"><i class="far fa-edit"></i> Edit</button>
                        <a id="removeListItem" class="btn btn-danger text-white categoryTool2"><i class="far fa-trash-alt"></i> Delete</a>
                        <button id="deleteCategory" hidden="hidden" type="submit" name="deleteCategory" class="btn">Delete</button>
                    </div>
                    <div id="categoryEditBody" class="collapse">
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="editedCategory" id="editedCategory" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit" name="saveCategory">GO</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> 
    <?php 
        if(isset($_SESSION['editCategoryMessage'])){
            echo $_SESSION['editCategoryMessage'];
            unset($_SESSION['editCategoryMessage']);
        } 
    ?>

    <div class="container">
        <h4 class="text-center my-4" style="padding-top:3rem;">Welcome to Sanjivani you are logged in as <?php  echo $usernameLoggedIn ?></h4>
        <p class="ab-2 text-center"><strong>For assigning roles click below:</strong></p>
        <div class="container text-center">
            <a href="assignRoles.php" class="btn btn-dark mx-2">Assign Role +</a>
        </div>
    </div>
</div>