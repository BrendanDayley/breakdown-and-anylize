var cookie = document.getElementById("bigCookie");

function click(){
    var cook = document.getElementById("bigCookie");
    var i = 5000;
    while(i != 0){
        var randNum = Math.floor(Math.random() * 1000);
        randNum += Math.floor(Math.random() *  100);
        setTimeout(function() {
            cook.click();
        }, randNum);

        i--;
    }
}

function clickFast() {
    for(i = 100; i != 0; i--){
        setTimeout(function(){
            click();
        }, 5000)
        i--;
    }
}

function clickUp(){
    var i = 0;
    while(i < 1000000){
        setTimeout(function() {
            Game.ObjectsById[0].levelUp();
            Game.ObjectsById[1].levelUp();
            Game.ObjectsById[2].levelUp();
            Game.ObjectsById[3].levelUp();
            Game.ObjectsById[4].levelUp();
            Game.ObjectsById[5].levelUp();
            Game.ObjectsById[6].levelUp();
            Game.ObjectsById[7].levelUp();
            Game.ObjectsById[8].levelUp();
            Game.ObjectsById[9].levelUp();
            Game.ObjectsById[10].levelUp();
            Game.ObjectsById[11].levelUp();
            Game.ObjectsById[12].levelUp();
            Game.ObjectsById[13].levelUp();
            Game.ObjectsById[14].levelUp();
        }, 1);
        i++;
    }
}
click();