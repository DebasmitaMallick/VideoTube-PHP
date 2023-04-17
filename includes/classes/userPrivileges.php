<?php

class userPrivileges {

    private $con, $sqlData;

    public function __construct($con, $userId) {
        $this->con = $con;

        $query = $this->con->prepare("SELECT * FROM privileges WHERE userId = :userId");
        $query->bindParam(":userId", $userId);
        $query->execute();
        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function isAssigned() {
        if($this->sqlData){
            return true;
        }
        else{
            return false;
        }
        
    }

    public static function hasUploadAccess($con, $userId, $categoryId){
        $sql = "SELECT upload FROM privileges WHERE categoryId = $categoryId and userId = $userId";
        $query = $con->query($sql);
        $output = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $output = $row['upload'];
        }
        if($output == '1'){
            return true;
        }
        else{
            return false;
        }
    }

    public static function hasDeleteAccess($con, $userId, $categoryId){
        $sql = "SELECT privileges.delete FROM privileges WHERE privileges.userId = $userId AND privileges.categoryId = $categoryId";
        $query = $con->query($sql);
        $output = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $output = $row['delete'];
        }
        if($output == '1'){
            return true;
        }
        else{
            return false;
        }
    }
    
    public static function getPrivileges($con,$userId){
        $flag = false;
        $sql = "SELECT name, upload, `delete`
                FROM categories INNER JOIN privileges
                WHERE (privileges.userId = $userId AND categories.id = privileges.categoryId)";
        $query = $con->query($sql);
        $privilageData = '<table class="table table-bordered table-hover"><thead><tr><th>Category</th><th>Access</th></tr></thead><tbody>';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $flag = true;
            $access = ($row['upload']=='1') ? (($row['delete'] == '0') ? 'upload' : 'upload, delete') : 'delete';
            
            $privilageData .= '<tr><td>'.$row['name'].'</td><td>'.$access.'</td></tr>';
        }
        $privilageData .= '</tbody></table>';
        if($flag === false){
            $privilageData = '<p class="text-center">Currently not assigned to any categories</p>';
        }
        return $privilageData;
    }

    public static function getEditPrivileges($con,$userId){
        $flag = false;

        //checking role
        $roleQuery = $con->query("SELECT role FROM users WHERE users.id = $userId");
        while ($rowQuery = $roleQuery->fetch(PDO::FETCH_ASSOC)){
            $userRole = $rowQuery['role'];
        }

        $sql = "SELECT privileges.id as privilegeId,categories.id as categoryId,name, upload, `delete`
                FROM categories INNER JOIN privileges
                WHERE (privileges.userId = $userId AND categories.id = privileges.categoryId)";
        $query = $con->query($sql);
        $privilageData = '<table class="table table-bordered table-hover"><thead><tr><th>Category</th><th>Access</th><th> </th></tr></thead><tbody>';
        if($userRole == "admin"){
            $privilageData .= '<tr><td><input class="mr-2" type="checkbox" id="admin'.$userId.'" name="admin'.$userId.'" value="admin'.$userId.'"><label for="admin'.$userId.'"> ADMIN</label></td><td>all</td>';
            $privilageData .= '<td><a onclick="removeCategory(removeRoleAdmin'.$userId.')" class="btn text-danger" style="position: absolute;transform: translate(-34%, -12%);"><i class="far fa-trash-alt"></i></a>';
            $privilageData .= '<button hidden="hidden" id="removeRoleAdmin'.$userId.'" type="submit" class="btn deleteCategorybtn" name="removeRoleAdmin'.$userId.'"></button></td></tr>';
        }
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $flag = true;
            if(($row['upload'] == '0') and ($row['delete'] == '0')){
                $access = "NULL";
            }
            else{
                $access = ($row['upload']=='1') ? (($row['delete'] == '0') ? 'upload' : 'upload, delete') : 'delete';
            }
            
            $privilageData .= '<tr><td><input class="mr-2" type="checkbox" id="'.$row['privilegeId'].'" name="'.$row['privilegeId'].'" value="'.$row['privilegeId'].'"><label id="role'.$row['privilegeId'].'" for="'.$row['privilegeId'].'"> '.$row['name'].'</label></td><td>'.$access.'</td>';
            $privilageData .= '<td><a onclick="removeCategory(removeRole'.$row['categoryId'].')" class="btn text-danger" style="position: absolute;transform: translate(-34%, -12%);"><i class="far fa-trash-alt"></i></a>';
            $privilageData .= '<button hidden="hidden" id="removeRole'.$row['categoryId'].'" type="submit" class="btn deleteCategorybtn" name="removeRole'.$row['privilegeId'].'"></button>';
            $privilageData .='</td></tr>';
        }
        $privilageData .= '</tbody></table>';
        if($flag === false){
            $privilageData = '<p class="text-center">Currently not assigned to any categories</p>';
        }
        return $privilageData;
    }

    public static function showPrivileges($con, $id){
        $stmtCheck = $con->prepare("SELECT * FROM privileges WHERE userId = :userId");
        $stmtCheck->bindParam(":userId", $id);
        $stmtCheck->execute();
        $rolesInfo = array();
        while ($rowCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC)){
            $newdata = array(
                'role' => $rowCheck['role'],
                'upload' => $rowCheck['upload'],
                'delete' => $rowCheck['delete']
            );
            $rolesInfo[$rowCheck['categoryId']] = $newdata;
        }
        return $rolesInfo;
    }
}
?>