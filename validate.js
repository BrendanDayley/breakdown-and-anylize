function ValidateUserForm() {
    var errors = "";
    var focuses = new Array();
    var valid = true;
    if (document.getElementById('name').value == "") {
        errors = errors + "You must enter a valid name";
        valid = false;
        focuses.push('name');
    }
    if (document.getElementById('email').value == "") {
        errors = errors + "You must enter a valid email";
        valid = false;
        focuses.push('email');
    }
    if (document.getElementById('user').value == "") {
        errors = errors + "You must enter a username";
        valid = false;
        focuses.push('user');
    }
    var pass = document.getElementById('passorg');
    var conpass = document.getElementById('conpass');

    if ((pass.value == "") || (conpass.value == "") || (conpass.value != pass.value)) {
        errors = errors + "Passwords must not be empty and must match";
        valid = false;
        focuses.push('passorg');
    }
    if (document.getElementById('zip').value == "") {
        errors = errors + "Due to requirements for lost password resets you must enter a zip code.";
        valid = false;
        focuses.push('zip');
    }

    if (!valid) {
        alert(errors);
        document.getElementById(focuses[0]).focus();
    }

    return valid;
}

$('#name').blur(function(e){
    console.log(e);
})

function ValidateUserForm() {
    var errors = "";
    var focuses = new Array();
    var valid = true;
    if (document.getElementById('name').value == "") {
        errors = errors + "You must enter a valid name";
        valid = false;
        focuses.push('name');
    }
    if (document.getElementById('email').value == "") {
        errors = errors + "You must enter a valid email";
        valid = false;
        focuses.push('email');
    }
    if (document.getElementById('user').value == "") {
        errors = errors + "You must enter a username";
        valid = false;
        focuses.push('user');
    }
    var pass = document.getElementById('passorg');
    var conpass = document.getElementById('conpass');

    if ((pass.value == "") || (conpass.value == "") || (conpass.value != pass.value)) {
        errors = errors + "Passwords must not be empty and must match";
        valid = false;
        focuses.push('passorg');
    }
    if (document.getElementById('zip').value == "") {
        errors = errors + "Due to requirements for lost password resets you must enter a zip code.";
        valid = false;
        focuses.push('zip');
    }

    if (!valid) {
        alert(errors);
        document.getElementById(focuses[0]).focus();
    }

    return valid;
}

if (!/[a-z]/.test($pwd))){
    $errors[] = "Password must include at least one lowercase letter";
}

if (!preg_match("/[A-Z]/", $pwd)) {
    $errors[] = "Password must include at least one uppercase letter";
}

if (!preg_match("/[0-9]/", $pwd)) {
    $errors[] = "Password must include at least one number";
}

if (!preg_match('/[$-\/:-?{-~!"^_`\[\]]/', $pwd)) {
    error_log($pwd);
    $errors[] = "Password must include at least one symbol";
}

return ($errors == $errors_init);

function passOneValid(){
    var password = $('#passorg').val();
    var anUpperCase = /[A-Z]/;
    var aLowerCase = /[a-z]/;
    var aNumber = /[0-9]/;
    var aSpecial = /[$-\/:-?{-~!"^_`\[\]]/;
    valid = true;
    error = [];
    error.push("Passwords must contain: ");

    if(!anUpperCase.test(password)){
        error.push("an uppercase");
        valid = false;
    }

    if(!aLowerCase.test(password)){
        error.push("a lowercase");
        valid = false;
    }

    if(!aNumber.test(password)){
        error.push("a number");
        valid = false;
    }

    if(!aSpecial.test(password)){
        error.push("a special");
        valid = false;
    }

    if(!valid){
        error.join(", ");
    }
}