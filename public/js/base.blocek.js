function handleFlashMessage(classes, message) {
    let div = document.createElement('div');
    div.className = classes;
    div.innerHTML = message;
    document.getElementById('flashMessageField').appendChild(div);
    setTimeout(function() {
        div.remove();
    }, 3000);
}
function leftSidePanelSwitch() {
    if (document.getElementById('side_panel').style.width === "280px"){
        document.getElementById('side_panel').style.width = "40px";
        document.getElementById('side_open').style.display = "";
        document.getElementById('side_close').style.display = "none";
        document.getElementById('side_content').style.display = "none";
    } else {
        document.getElementById('side_panel').style.width = "280px";
        document.getElementById('side_open').style.display = "none";
        document.getElementById('side_close').style.display = "";
        document.getElementById('side_content').style.display = "";
    }
}