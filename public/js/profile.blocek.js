function completeEvent(href){
    document.location.href = href
}
function editEvent(href) {
    document.location.href = href
}
function askToDelete(href) {
    if (confirm('Opravdu chceš smazat event?')) {
        document.location.href = href
    }
}
function filterPanelSwitch() {
    if (document.getElementById('filter_panel').style.width === "280px"){
        document.getElementById('filter_panel').style.width = "40px";
        document.getElementById('filter_open').style.display = "none";
        document.getElementById('filter_close').style.display = "";
        document.getElementById('filter_content').style.display = "none";
    } else {
        document.getElementById('filter_panel').style.width = "280px";
        document.getElementById('filter_open').style.display = "";
        document.getElementById('filter_close').style.display = "none";
        document.getElementById('filter_content').style.display = "";
    }
}