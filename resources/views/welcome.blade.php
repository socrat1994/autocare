<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel 8 Phone Number OTP Auth Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<script>
    window.fbAsyncInit = function() {
        FB.init ({
            appId      : '1058399658124426',
            xfbml      : true,
            version    : 'v2.6'
        });
    };

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    } (document, 'script', 'facebook-jssdk'));

</script>
<div class="container mt-5" style="max-width: 550px">
    <div class="alert alert-danger" id="error" style="display: none;"></div>
    <h3>Add Phone Number</h3>
    <div class="alert alert-success" id="successAuth" style="display: none;"></div>
    <form>
        <label>Phone Number:</label>
        <input type="text" id="number" class="form-control" placeholder="+91 ********">
        <div id="recaptcha-container"></div>
        <button type="button" class="btn btn-primary mt-3" onclick="sendOTP();">Send OTP</button>
    </form>

    <div class="mb-5 mt-5">
        <h3>Add verification code</h3>
        <div class="alert alert-success" id="successOtpAuth" style="display: none;"></div>
        <form>
            <input type="text" id="verification" class="form-control" placeholder="Verification code">
            <button type="button" class="btn btn-danger mt-3" onclick="verify()">Verify code</button>
        </form>
    </div>
    <button onclick = "facebookSignin()">Facebook Signin</button>
    <button onclick = "facebookSignout()">Facebook Signout</button>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>

<script>
    const firebaseConfig = {
        apiKey: "AIzaSyAskaV6bcc2dmpq3JkzmIRcMhYt9psK2OU",

        authDomain: "test-1ceb5.firebaseapp.com",

        projectId: "test-1ceb5",

        storageBucket: "test-1ceb5.appspot.com",

        messagingSenderId: "529719153152",

        appId: "1:529719153152:web:0e18b898c3db18229f7ee5",

        measurementId: "G-JHDLNMQ9ZX"

    };

    firebase.initializeApp(firebaseConfig);
</script>
<script type="text/javascript">
   /* window.onload = function () {
        render();
    };
    function render() {
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container');
        recaptchaVerifier.render();
    }
    function sendOTP() {
        var number = $("#number").val();
        firebase.auth().signInWithPhoneNumber(number, window.recaptchaVerifier).then(function (confirmationResult) {
            window.confirmationResult = confirmationResult;
            coderesult = confirmationResult;
            console.log(coderesult);
            $("#successAuth").text("Message sent");
            $("#successAuth").show();
        }).catch(function (error) {
            $("#error").text(error.message);
            $("#error").show();
        });
    }
    function verify() {
        var code = $("#verification").val();
        coderesult.confirm(code).then(function (result) {
            var user = result.user;
            console.log(user);
            $("#successOtpAuth").text(firebase.auth().currentUser.uid);
            $("#successOtpAuth").show();
        }).catch(function (error) {
            $("#error").text(error.message);
            $("#error").show();
        });
    }*/
    var provider = new firebase.auth.FacebookAuthProvider().addScope('user_link');


    function facebookSignin() {
        firebase.auth().signInWithPopup(provider)
            .then(function(result) {
                var token = result.credential.accessToken;
                var user = result.user;
                console.log(token)
                console.log(user)

            }).catch(function(error) {
            console.log(error.code);
            console.log(error.message);

        });
    }
    function facebookSignout() {

    }
</script>
</body>
</html>
