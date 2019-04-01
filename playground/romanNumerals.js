convertToRoman = function(num) {
    if (isNaN(num)) {
        return "NaN";
    }
    if (num > 255 || num < 1) {
        return "NA"
    }
    roman = "";
    // create a table that compares our number to its corresponding Roman numeral.
    // since we have an uppler limit of 255 we can cap out at the Roman numeral C
    var numerals = {C:100, XC:90, L:50, XL:40, X:10, IX:9, V:5, IV:4, I:1}

    // loop through our numerals
    for ( k in numerals ) {
        // compare our parameter to the number values in our numerals object
        while (num >= numerals[k]){
            // add the numeral to our return string.
            roman += k;
            //subtract from our number so that we don't get more numerals than needed.
            // once we hit zero then the loop will end.
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