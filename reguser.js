function changetab(next,previous){

    form = document.getElementsByClassName("regform");

    for (i = 0; i < form.length; i++) {
        form[i].style.display = "none";
    }


    document.getElementById(next).style.display = "flex";
    document.getElementById(previous).style.display = "none"



}