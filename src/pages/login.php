<?php 
    include "../models/login.php";
    include "../session.php";

    $errors = []; 

    if(isset($_SESSION['user_id'])) {
        header("Location: profile.php");
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
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                header("Location: profile.php");
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

<main class="login-container">
    <div class="login-form">
        <?php if (!empty($errors)) { ?>
            <div class="error-messages">
                <?php foreach($errors as $error) { ?>
                    <p class="error"><?= $error ?></p>
                <?php } ?>
            </div>
        <?php } ?>
        
        <h1>Log in</h1>
        
        <form method="post">
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
            
            <button type="submit" name="submit" class="login-button">
                Log in
            </button>
            
            <p class="signup-link">
                Don't have an account? <a href="./signup.php">Signup</a>
            </p>
        </form>
    </div>
</main>

<?php include "../includes/footer.php"; ?>