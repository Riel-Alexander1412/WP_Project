function changetab(previous,next){

    // form = document.getElementsByClassName("regform");

    // for (i = 0; i < form.length; i++) {
    //     form[i].style.display = "none";
    // }

    document.getElementById(previous).style.display = "none";
    document.getElementById(next).style.display = "flex";

}