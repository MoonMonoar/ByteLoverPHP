const f_title = $("#problemTitle"),
    f_body = $("#problemContent"),
    b_insert_code = $("#insC"),
    b_remove_code = $("#rmvC"),
    d_save = $(".prob_draft");


//Editor functions
function downloadFile() {
    const fileName = window.prompt("Enter filename (e.g., file.c):");
    if (fileName) {
        const blob = new Blob([current_code], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.click();
        URL.revokeObjectURL(url);
    }
}

window.pendingDraft = false;
const draft = () => {
    if (window.pendingDraft) {
        return;
    }
    d_save.html(' <i class="fa fa-clock mr2"></i> Saving...');
    window.pendingDraft = true;
    setTimeout(() => {
        localStorage.setItem("NEW_PROB_TITLE", f_title.val());
        localStorage.setItem("NEW_PROB_BODY", f_body.val());
        d_save.html('<i class="fa fa-check-circle mr2"></i> Drafted');
        window.pendingDraft = false;
    }, 1000);
}
let code_cache = [];
let cache_editor_code, current_code, current_lang;
const openCoder = (e) => {
    const cd = e.clipboardData || window.clipboardData;
    cache_editor_code = cd.getData('text');
    e.preventDefault();
    b_insert_code.click();
    return null;
}
b_remove_code.click(() => {
    if (code_cache.length > 0) {
        f_body.val(f_body.val().replace(code_cache[code_cache.length - 1], ""));
        code_cache.pop();
        draft();
    }
    else {
        iziToast.info({
            title: 'No code',
            message: 'You did not paste anymore code!'
        });
    }
});

b_insert_code.click(() => {
    if (!window.editor_markup) {
        const o = $(".code_editor");
        const markup = o.html();
        window.editor_markup = markup;
        o.remove();
        prompt(markup, false, false);
    }
    else {
        prompt(window.editor_markup, false, false);
    }

    $("#editor").click(() => {
        $("#mask").click();
    });

    //Dropdowns
    $(".dt").click(function () {
        $(".dt").not(this).each(function () {
            $(this).removeClass("show");
            $(this).parent().find("ul").removeClass("show");
        });
    });

    $("#editor_down").click(() => {
        downloadFile();
    });

    window.editor_lis = $(document).keydown(function (e) {
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
            e.preventDefault();
            downloadFile();
        }
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 73) {
            e.preventDefault();
            $("#insertCode").click();
        }
    });

    //Tooltip
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    window.tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    const editorElement = document.getElementById('editor');
    const editor = ace.edit(editorElement);

    let t;
    editor.setTheme(t = (localStorage.getItem("ce_t") ? localStorage.getItem("ce_t") : 'ace/theme/monokai'));
    $('#editor-themes option[value="' + t + '"]').prop('selected', true);
    $("#editor-themes").change(function () {
        const theme = $("#editor-themes").val();
        localStorage.setItem("ce_t", theme);
        editor.setTheme(theme);
    });

    let s = (localStorage.getItem("ce_s") ? localStorage.getItem("ce_s") : 14);
    editor.setFontSize(s + "px");
    $('#editor-fontsize option[value="' + s + '"]').prop('selected', true);
    $("#editor-fontsize").change(function () {
        const size = $("#editor-fontsize").val();
        localStorage.setItem("ce_s", size);
        editor.setFontSize(size + "px");
    });

    let l;
    editor.session.setMode(l = (localStorage.getItem("ce_l") ? localStorage.getItem("ce_l") : 'ace/mode/c_cpp'));
    let lang = $('#editor-lang option[value="' + l + '"]');
    lang.prop('selected', true);
    $("#lang-name").html('<i class="fa-solid fa-code mr5"></i>' + lang.text());
    current_lang = l;
    $("#editor-lang").change(function () {
        const mode = $("#editor-lang").val();
        localStorage.setItem("ce_l", mode);
        editor.session.setMode(mode);
        current_lang = mode;
        $("#lang-name").html('<i class="fa-solid fa-code mr5"></i>' + $("#editor-lang option:selected").text());
    });

    editor.session.on('change', function () {
        current_code = editor.getValue();
    });

    editor.setValue((cache_editor_code && cache_editor_code.length > 0) ? cache_editor_code : "");

    $("#closeCoder").click(() => {
        prompt_dismiss();
        cache_editor_code = null;
        $(document).off("keydown", window.editor_lis);
    });

    $("#insertCode").click(() => {
        if (current_code && current_code.length > 0) {
            current_lang = current_lang.replace("ace/mode/", "");
            current_code = '[CODE-' + current_lang + ']' + current_code + '[/CODE-' + current_lang + ']';
            code_cache[code_cache.length] = current_code;
            document.getElementById('problemContent').value += current_code;
            current_code = '';
            draft();
        }
        prompt_dismiss();
    });
});

const makeID = (length) => {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let randomId = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        randomId += characters.charAt(randomIndex);
    }
    return randomId;
}

window.forum_files = {};

const del = (i)=>{
    const o = $(i);
    prompt('<div style="background: var(--white);color: var(--black);padding: 15px; max-width:600px"><div style="font-size: large;margin-bottom: 5px;"><i class="fa-solid fa-trash mr5"></i>Remove file?</div><div class="tsm text-muted" style="margin: 10px 0 10px 0px;">Confirm if you wish to remove the photo.</div><div><button class="btn btn-danger" onclick="rem(\''+o.attr('id')+'\')">Remove</button></div></div>', false, true);
}

const rem = (id)=>{
    $("#"+id).remove();
    delete window.forum_files['file_'+id];
    prompt_dismiss();
}

$(window).ready(() => {
    const l = localStorage.getItem("NEW_PROB_TITLE"),
        b = localStorage.getItem("NEW_PROB_BODY");
    if (l && l.length > 0) {
        f_title.val(l)
    }
    if (b && b.length > 0) {
        f_body.val(b)
    }
    new Tags('#tags', window.tags = ["Programming"]);

    $("#prob-file").on("change", function () {
        var files = this.files;
        for (var i = 0; i < files.length; i++) {
            var image = new Image();
            image.src = URL.createObjectURL(files[i]);
            image.id = makeID(4);
            image.title = "Click to remove";
            image.onclick = ()=>{
                del(image);
            };
            $("#prob-imgs").append(image);
            window.forum_files['file_'+image.id] = files[i];
            if(!window.prohib_notice){
                setTimeout(() => {
                    nude.load(image.id);
                    nude.scan(function (r) {
                        if(!r){
                            prompt('<div style="background: var(--white);color: var(--black);padding: 15px; max-width:600px"><div style="font-size: large;margin-bottom: 5px;"><i class="fa fa-exclamation-triangle mr5"></i>Terms violation disclaimer</div><div>The system has predicted you might be trying to upload content that is not accepted by our community. Please make sure your selected content is not supporting nudity, vulgarity and holds importance for better understanding of your problem only, if anything unusual found further you will loose all your reputation and access to account. In addition please check our <a href="/policies/terms/?ref=footer#prohibited" target="_blank">Terms</a> to learn more.</div><div></div></div>', false, true);
                            window.prohib_notice = true;
                        }
                    });
                }, 500);
            }
        }
        $("#prob-file").val("");
    });
    $(".fnsb").click(() => {
        console.log("clicked");
    });
});