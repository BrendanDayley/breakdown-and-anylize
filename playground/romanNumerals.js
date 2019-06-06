convertToRoman = function(toRoman) {
    if (isNaN(toRoman)) {
        return "NaN";
    }
    if (toRoman > 255 || toRoman < 1) {
        return "NA"
    }
    roman = "";
    // create a table that compares our number to its corresponding Roman numeral.
    // since we have an uppler limit of 255 we can cap out at the Roman numeral C
    var numerals = {C:100, XC:90, L:50, XL:40, X:10, IX:9, V:5, IV:4, I:1}

    // loop through our numerals
    for ( k in numerals ) {
        // compare parameter to the number values in the numerals object
        while (num >= numerals[k]){
            roman += k;
            num -= numerals[k];
        }
    }
    return roman;
}

for (i = 0; i<=256; i++){
    var roman = convertToRoman(i)
    message = "Case #" + i + ": " + i + "=" + roman;
    console.log(message);
}