<?php
    require "../config/database.php";

    function login_account($email, $password) {
        global $conn;
        $user = [];

        $query = "SELECT * FROM `users` WHERE `email` = '".$conn->real_escape_string($email)."'";

        if ($result = $conn->query($query)) {
            $row = $result->fetch_array(MYSQLI_ASSOC);
        }

        if(!empty($row)) {
            $hashed_password = md5(md5($row['user_id'].$password));
            if ($hashed_password == $row['password_hash']) {
                $user = [
                    'user_id' => $row['user_id'],
                    'name' => $row['username']
                ];
            }
        }

        return $user;
    }
?>