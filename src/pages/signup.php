<?php
    include "../models/signup.php";
    include "../session.php";

    $errors = [];

    if(isset($_SESSION['user_id'])) {
        header("Location: profile.php");
    }

    if(isset($_POST['submit'])) {
        if(!$_POST['name']) {
            $errors[] = "Name is required.";
        }
        if(!$_POST['email']) {
            $errors[] = "Email is required.";
        }
        if(!$_POST['password']) {
            $errors[] = "Password is required.";
        }

        if($_POST['password'] != $_POST['confirm_password']) {
            $errors[] = "You must confirm your password.";
        }
        
        if(empty($errors)) {
            if(!check_existing_email($_POST['email'])) {
                $user = save_registration($_POST['name'],$_POST['email'], $_POST['password']);
                if(!empty($user)) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];

                    header("Location: profile.php");
                } else {
                    $errors[] = "There was an error logging in your account.";
                }
            } else {
                $errors[] = "Email address already exist.";
            }
        }
    } else {
        $_POST = [
            'name' => '',
            'password' => '',
            'email' => ''
        ];
    }
?>
<?php include "../includes/header.php"; ?>
    
    <main class="content">
        <section id="signup" class="container">
            <div id="signup-form">
                <?php if (!empty($errors)) { ?>
                    <?php include "../includes/error.php" ?>
                <?php } ?>
                <div class="form card">
                    <h1>Sign up to your account.</h1>
                    <form method="post">
                        <div class="input-control">
                            <label for="name">Name: </label>
                            <input type="text" name="name" class="input-field input-md" value="<?= $_POST['name'] ?>" />
                        </div>
                        <div class="input-control">
                            <label for="email">Email: </label>
                            <input type="email" name="email" class="input-field input-md" value="<?= $_POST['email'] ?>" />
                        </div>
                        <div class="input-control">
                            <label for="password">Password: </label>
                            <input type="password" name="password" class="input-field input-md" value="<?= $_POST['password'] ?>" />
                        </div>
                        <div class="input-control">
                            <label for="name">Confirm Password: </label>
                            <input type="password" name="confirm_password" class="input-field input-md" value="" />
                        </div>
                        <div class="input-control">
                            <input type="submit" name="submit" class="btn btn-sm btn-rounded" value="Register" />
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
<?php include "../includes/footer.php"; ?>