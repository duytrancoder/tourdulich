<?php

require_once ROOT . "/core/Model.php";

class UserModel extends Model
{
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM tblusers WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function updateUserProfile($email, $name, $mobile)
    {
        $sql =
            "UPDATE tblusers SET FullName=:name,MobileNumber=:mobile WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":name", $name, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        return $query->execute();
    }

    public function checkPassword($email, $password)
    {
        $sql = "SELECT Password FROM tblusers WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_OBJ);
        
        if (!$user) {
            return false;
        }
        
        // Support both old MD5 and new password_hash
        if (strlen($user->Password) === 32 && ctype_xdigit($user->Password)) {
            // Old MD5 hash - verify and upgrade
            if ($user->Password === md5($password)) {
                // Upgrade to password_hash
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $this->updatePassword($email, $newHash);
                return true;
            }
            return false;
        } else {
            // New password_hash
            return password_verify($password, $user->Password);
        }
    }

    public function updatePassword($email, $newpassword)
    {
        // Ensure password is hashed if it's not already
        if (strlen($newpassword) !== 60 || !str_starts_with($newpassword, '$2y$')) {
            $newpassword = password_hash($newpassword, PASSWORD_DEFAULT);
        }
        
        $sql = "UPDATE tblusers SET Password=:newpassword WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":newpassword", $newpassword, PDO::PARAM_STR);
        return $query->execute();
    }

    public function checkUserByEmailAndMobile($email, $mobile)
    {
        $sql =
            "SELECT EmailId FROM tblusers WHERE EmailId=:email and MobileNumber=:mobile";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0;
    }

    public function resetPassword($email, $mobile, $newpassword)
    {
        // Hash the password
        $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
        
        $sql =
            "UPDATE tblusers SET Password=:newpassword WHERE EmailId=:email and MobileNumber=:mobile";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":mobile", $mobile, PDO::PARAM_STR);
        $query->bindParam(":newpassword", $hashedPassword, PDO::PARAM_STR);
        return $query->execute();
    }

    public function checkEmailAvailability($email)
    {
        $sql = "SELECT EmailId FROM tblusers WHERE EmailId=:email";
        $query = $this->db->prepare($sql);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0;
    }

    public function createUser($fname, $mnumber, $email, $password)
    {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql =
            "INSERT INTO tblusers(FullName,MobileNumber,EmailId,Password) VALUES(:fname,:mnumber,:email,:password)";
        $query = $this->db->prepare($sql);
        $query->bindParam(":fname", $fname, PDO::PARAM_STR);
        $query->bindParam(":mnumber", $mnumber, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $query->execute();
        return $this->db->lastInsertId();
    }
}
