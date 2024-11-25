<?php
    require "../config/database.php";

    function check_existing_email($email) {
        global $conn;
        $flag = false;

        $query = "SELECT `user_id` FROM `users` WHERE `email` = '".$conn->real_escape_string($email)."' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $flag = true;
        }
        
        return $flag;
    }

    function save_registration($name, $email, $password) {
        global $conn;
        $user = [];

        $query = "INSERT INTO `users` (`username`, `email`, `password_hash`, `created_at`) VALUES ('".$conn->real_escape_string($name)."', '".$conn->real_escape_string($email)."', '".$conn->real_escape_string($password)."', '".date('Y-m-d H:i:s')."')";

        if ($conn->query($query)) {
            $id = $conn->insert_id;
            $encrypted_password = md5(md5($id . $password));

            $query = "UPDATE `users` SET password_hash = '".$encrypted_password."' WHERE `users`.`user_id` = ".$id." LIMIT 1";
            
            if($conn->query($query)) {
                $query = "SELECT * FROM `users` WHERE `users`.`user_id` = '".$id."' AND `users`.`password_hash` = '".$conn->real_escape_string($encrypted_password)."'  LIMIT 1";
    
                if($result = $conn->query($query)) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $user = [
                        'user_id' => $row['user_id'],
                        'name' => $row['username'],
                        'email' => $row['email']
                    ];
                }
            }
        }
        return $user;
    } 
?>