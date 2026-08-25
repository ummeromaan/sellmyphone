
<?php require 'includes/ad-header.php';?>
<div class="login-center">
    <div class="login-box">

        <div class="row g-0">

        
            <div class="col-md-5 left-side">

                <i class="fa-solid fa-user"></i>

                <h2>Admin Login</h2>

                <p>Welcome Back</p>

            </div>


            <div class="col-md-7 right-side">

                <h3 class="text-center mb-4">Login</h3>

                <form action="check-login" method="POST">

                    <input type="text"
                        name="name"
                        class="form-control"
                        placeholder="Enter Name"
                        required>

                        <input type="email "
                        name="email"
                        class="form-control"
                        placeholder="Enter email"
                        required>

                    <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter Password"
                        required>

                        
<?php
if(isset($_GET['error']) && $_GET['error'] == 1)
{
    echo "<p class='error-msg'>Invalid Username or Password!</p>";
}
?>
                    <button type="submit" name="login" class="btn btn-login">
                        Login
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>
</body>
</html>
