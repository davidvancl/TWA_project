function action(href){
    document.location.href = href
}

function confirmAction(href){
    if (confirm('Opravdu chceš smazat uživatele?')) {
        document.location.href = href
    }
}