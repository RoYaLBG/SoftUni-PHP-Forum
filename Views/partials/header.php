<?php /* @var $this \ANSR\View */ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Best Forum</title>
    <?php foreach ($this->getStyles() as $style): ?>
        <?= $style; ?>
    <?php endforeach; ?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#logoutButton").click(function () {
                $.post("<?= $this->url('users', 'logout'); ?>", {
                    <?= $this->getCsrfJqueryData(); ?>
                }).done(function () {
                    window.location = "<?= $this->url('welcome', 'index'); ?>";
                });
            });


            var loginRegisterField = $('#loginRegisterField');
            var loginButton = $('#loginButton');
            var registerButton = $('#registerButton');

            loginButton.click(function () {
                loginRegisterField.html('<h2>Login</h2>' +
                    '<label for="userLogin">Username</label>' +
                    '<input type="text" id="userLogin"/>' +
                    '<label for="passLogin">Password</label>' +
                    '<input type="password" id="passLogin"/>' + '<?= $this->getCsrfValidator(); ?>' +
                    '<button id="submit" onclick="login();">Submit</button>');
                $('#response').html('');
                $('#topics').html('');

                $("#userLogin").keypress(function(e) {
                    if (e.keyCode == 13) {
                        $("#submit").click();
                    }
                });

                $("#passLogin").keypress(function(e) {
                    if (e.keyCode == 13) {
                        $("#submit").click();
                    }
                });
            });
            registerButton.click(function () {

                loginRegisterField.html('<h2>Register</h2>' +
                    '<label for="email">Email</label>' +
                    '<input type="email" id="email"/>' +
                    '<label for="userRegister">Username</label>' +
                    '<input type="text" id="userRegister"/>' +
                    '<label for="passRegister">Password</label>' +
                    '<input type="password" id="passRegister"/>' +
                    '<label for="passRepeat">Repeat password</label>' +
                    '<input type="password" id="passRepeat"/>' +
                    '<button id="submit" onclick="register();">Submit</button>');
                $('#response').html('');
                $('#topics').html('');
                $("#userRegister").keypress(function(e) {
                    if (e.keyCode == 13) {
                        $("#submit").click();
                    }
                });

                $("#passRegister").keypress(function(e) {
                    if (e.keyCode == 13) {
                        $("#submit").click();
                    }
                });
                $("#passRepeat").keypress(function(e) {
                    if (e.keyCode == 13) {
                        $("#submit").click();
                    }
                });

            });

        });
        
        function searchTopics() {
            $('#loginRegisterField').html('');
            $('#topics').html('');
            $('#response').html('');

            $.post("<?= $this->url('topics', 'find'); ?>", {
                keyword: $('#searchbox').val()
            }).done(function (response) {
                var json = $.parseJSON(response);
                if (json.success == 0 || $.isEmptyObject(json)) {
                    $('#topics').html('<p class="noResults">No results found</p>')
                } else {
                    $('#topics').html('<h2>Search results</h2>')
                    $.each(json, function (i, item) {
                        var href = "<a href='<?= HOST; ?>topics/view/id/" + item.id + "'>" + item.summary + "</a><br />";
                        $('#topics').append(href);
                    })
                }
            });
        }

        
        function login() {
           $.post("<?= $this->url('users', 'login'); ?>", {
               username: $('#userLogin').val(),
               password: $('#passLogin').val(),
               <?= $this->getCsrfJqueryData() ;?>
           }).done(function (response) {
               var json = $.parseJSON(response);
               if (json.success == 1) {
                   $('#response').html('');
                   window.location = "<?= $this->url('welcome', 'index'); ?>";
               } else {
                   $('#response').html('<h2 class="incorrect">' + 'Incorrect username or password' + '</h2>');
               }
           });
        }

        function register() {
           if ($("#passRegister").val() !== $("#passRepeat").val()) {
               $("#response").html('<h2 class="incorrect">' + 'Password missmatch' + '</h2>')
               return false;
           }

           $.post("<?= $this->url('users', 'register'); ?>", {
               username: $('#userRegister').val(),
               password: $('#passRegister').val(),
               email: $('#email').val()
           }).done(function (response) {
               var json = $.parseJSON(response);
               if (json.success == 1) {
                   $('#response').html('');
                   window.location = "<?= $this->url('welcome', 'index'); ?>";
               } else {
                   $('#response').html('<h2 class="incorrect">' + json.msg + '</h2>');
               }
           });

        }
    </script>
</head>

<body>
<div id="wrapper">

    <header>
        <h1>RoYaL's Forum</h1>

        <h2>Everything in depth</h2>
        <div class="navigation">
            <ul>
                <li><a href="<?= $this->url('welcome', 'index'); ?>">Home</a><span> -></span></li>
                <?php $this->partial(strtolower($this->getFrontController()->getRouter()->getController().'Menu.php'));?>
            </ul>
        </div>

        <?php if (!$this->getFrontController()->getController()->getApp()->UserModel->isLogged()): ?>
            <ul>
                <li>
                    <button id="loginButton">Login</button>
                </li>
                <li>
                    <button id="registerButton">Register</button>
                </li>
            </ul>
            <div id="search">
                <input type="text" id="searchbox" placeholder="search..."/>
                <button onclick="searchTopics()">find</button>
            </div>
        <?php else: ?>
            <ul>
                <li>
                    <button id="logoutButton">Logout</button>
                </li>
            </ul>
            <div id="search">
                <input type="text" id="searchbox" placeholder="search..."/>
                <button onclick="searchTopics()">find</button>
            </div>

            <h2 class="welcomeUser">Welcome <?= htmlspecialchars($_SESSION['username']); ?></h2>
        <?php endif; ?>
        <div id="topics"></div>
    </header>
    <main>
        <section id="loginRegisterField">

        </section>
        <div id="response"></div>