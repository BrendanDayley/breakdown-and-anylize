function myFunc(exp, out){
    var calc = document.getElementById(exp).value;

    if(calc.indexOf("^") != -1){
        vars = calc.split('^');
        for(var i=0; i<vars.length; i++) {
            vars[i] = +vars[i];
        }
        document.getElementById(out).innerHTML = Math.pow(vars[0], vars[1]);
    } else {
        document.getElementById(out).innerHTML = eval(calc);
    }
}