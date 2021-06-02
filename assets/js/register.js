// Show-hide login-register form depending on user selection
$(document).ready(function() {
    // hide login & display register form
    $("#hideLogin").click(function() {
        $("#loginForm").hide();
        $("#registerForm").show();
    });

    // hide register & display login form
    $("#hideRegister").click(function() {
        $("#loginForm").show();
        $('#registerForm').hide();
    });
});