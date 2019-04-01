function go(url) {
    document.location.href = url;
}

function validate(url) {
    var user = document.getElementById('Username').value;
    var pass = document.getElementById('Password').value;
    if(user != '' && pass != ''){
        go(url);
    } else {
         var errors = [];
        if(user == '') {
            errors.push("must include a username");
        }
        if(pass == '') {
            errors.push("must include a password");
        }
        alert(errors.join("\n"));
    }
}