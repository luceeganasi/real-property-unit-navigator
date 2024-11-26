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

<main class="signup-container">
    <div class="signup-form">
        <?php if (!empty($errors)) { ?>
            <div class="error-messages">
                <?php foreach($errors as $error) { ?>
                    <p class="error"><?= $error ?></p>
                <?php } ?>
            </div>
        <?php } ?>
        
        <h1>Sign up</h1>
        
        <form method="post">
            <div class="form-group">
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Name"
                    value="<?= htmlspecialchars($_POST['name']) ?>" 
                    required
                />
            </div>
            
            <div class="form-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email"
                    value="<?= htmlspecialchars($_POST['email']) ?>" 
                    required
                />
            </div>
            
            <div class="form-group">
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    required
                />
            </div>
            
            <div class="form-group">
                <input 
                    type="password" 
                    name="confirm_password" 
                    placeholder="Confirm Password"
                    required
                />
            </div>
            
            <button type="submit" name="submit" class="signup-button">
                Sign up
            </button>
            
            <p class="login-link">
                Already have an account? <a href="./login.php">Log in</a>
            </p>
        </form>
    </div>
</main>

<?php include "../includes/footer.php"; ?>