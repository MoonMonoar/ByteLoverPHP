$("#showpass").click(()=>{
    if($("#pass").attr("type") == "password"){
        $("#pass").attr("type", "text");
        $("#pass").focus();
    }
    else {
        $("#pass").attr("type", "password");
        $("#pass").focus();
    }
});