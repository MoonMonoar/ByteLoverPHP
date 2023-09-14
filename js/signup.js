$("#showpass").click(()=>{
    if($("#pass1").attr("type") == "password"){
        $("#pass1, #pass2").attr("type", "text");
    }
    else {
        $("#pass1, #pass2").attr("type", "password");
    }
});