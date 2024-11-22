<?php 
    include "../models/login.php";
    include "../session.php";

    $errors = []; 

    if(isset($_SESSION['id'])) {
        // header("Location: account");
    }

    if(isset($_POST['submit'])) {
        if(!$_POST['email']) {
            $errors[] = "Email is required.";
        }
        if(!$_POST['password']) {
            $errors[] = "Password is required.";
        }
        if(empty($errors)) {
            $user = login_account($_POST['email'], $_POST['password']);
            if(!empty($user)) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];

                header("Location: buy.php");
                echo "bilat";
            } else {    
                $errors[] = "The email that you've entered does not match any account.";
            }
        }
    } else {
        $_POST = [
            'email' => '',
            'password' => '',
        ];
    }
?>
<?php include "../includes/header.php"; ?>
    
    <main class="content">
        <section id="signin" class="container">
            <div id="signin-form">
                <?php if (!empty($errors)) { ?>
                    <?php include "../includes/error.php" ?>
                <?php } ?>
                <div class="form card">
                    <h1>Log in to your account.</h1>
                    <form  method="post">
                        <div class="input-control">
                            <label for="name">Email: </label>
                            <input type="email" name="email" class="input-field input-md" value="<?= $_POST['email'] ?>" />
                        </div>
                        <div class="input-control">
                            <label for="name">Password: </label>
                            <input type="password" name="password" class="input-field input-md" value="<?= $_POST['password'] ?>" />
                        </div>
                        <div class="input-control">
                            <input type="submit" name="submit" class="btn btn-md btn-rounded" value="Login" />
                        </div>
                        <div id="signup-account">
                            <p>Don't have an account? <a href="./signup.php">Signup</a> </p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
<?php include "../includes/footer.php"; ?>