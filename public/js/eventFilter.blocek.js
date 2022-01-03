function applyFilter(userIndex, url) {
    let filter = saveFilterToLocalStorage(userIndex);
    renderEventsFromURL(JSON.parse(filter), url);
}

function saveFilterToLocalStorage(userIndex) {
    let filter = {};
    document.querySelectorAll('[data-selector]').forEach(element => {
        if (!element.dataset.selector.includes('sort') && element.checked){
           filter[element.dataset.selector] = "1";
        } else if (element.dataset.selector.includes('sort') && element.value !== "none") {
            filter[element.dataset.selector] = element.value;
        }
    });
    localStorage.setItem(userIndex, JSON.stringify(filter));
    return JSON.stringify(filter);
}

function updateFilter(loadedFilter) {
    if (!loadedFilter) return;
    document.querySelectorAll('[data-selector]').forEach(element => {
        if (!element.dataset.selector.includes('sort') && loadedFilter[element.dataset.selector] !== undefined) {
            element.checked = loadedFilter[element.dataset.selector];
        } else if (element.dataset.selector.includes('sort') && loadedFilter[element.dataset.selector] !== undefined) {
            element.value = loadedFilter[element.dataset.selector];
        }
    });
}

function loadFilterFromLocalStorage(userIndex){
    if (localStorage.getItem(userIndex) !== null) {
        return JSON.parse(localStorage.getItem(userIndex));
    }
    return null;
}

function renderEventsFromURL(loadedFilter, url) {
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState === XMLHttpRequest.DONE) {
            if (xmlhttp.status === 200) {
                let response = JSON.parse(xmlhttp.responseText);
                let container = document.getElementById('events_container');
                container.innerHTML = "";
                if (typeof response !== 'undefined' && response.length > 0){
                    let html = "<div class=\"row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3\">";
                    response.forEach(event => {
                        html += eventTemplate(event);
                    });
                    html += "</div>";
                    container.innerHTML = html;
                } else {
                    container.innerHTML = nothingTemplate();
                }
            } else {
            }
        }
    };

    if (loadedFilter !== null) {
        url += "?";
        Object.keys(loadedFilter).forEach(function (key) {
            url += key + "=" + loadedFilter[key] + "&";
        });
        url = url.substring(0, url.length - 1);
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function nothingTemplate() {
    return "<div class=\"px-4 py-5 my-5 text-center\"><h1 class=\"display-5 fw-bold text-danger\">Nenalezeny žádné události</h1></div>"
}

function eventTemplate(event) {
    let eventElement = "<div class=\"col\">" +
        "<div class=\"card shadow-sm\">";
        if (event.visibility === "private"){
            eventElement += "<img style=\"position: absolute; top: 10px; left: 10px\" src=\"images/lock.png\" height=\"10\" width=\"10\" alt=\"\">";
        }
        if (event.section === "main_page") {
            eventElement += "<img style=\"position: absolute; top: 30px; left: 10px\" src=\"images/page.png\" height=\"10\" width=\"10\" alt=\"\">";
        }
        eventElement += "<svg class=\"bd-placeholder-img card-img-top\" width=\"100%\" height=\"125\" preserveAspectRatio=\"xMidYMid slice\"" +
        " focusable=\"false\">" +
        "<title>" + event.title + "</title>" +
        "<rect width=\"100%\" height=\"100%\" fill=\"";
        if (event.status === "completed") {
            eventElement += "#198754";
        } else if (event.actual_datetime >= event.date_to) {
            eventElement += "#dc3545";
        } else {
            eventElement += "#55595c";
        }
        eventElement +=
        "\"></rect>" +
        "<text x=\"50%\" y=\"50%\" fill=\"#eceeef\" dy=\".3em\">" + event.title + "</text>" +
        "<text x=\"50%\" y=\"80%\" fill=\"#eceeef\" dy=\".3em\" style=\"font-size: 0.825rem;\">" +
        "Vytovřeno: " + event.date_created + "</text>" +
        "</svg>" +
        "<div class=\"card-body\">" +
        "<p class=\"card-text\">" + event.body + "</p>" +
        "<div class=\"d-flex justify-content-between align-items-center\">" +
        "<div class=\"btn-group\">" +
        "<button onclick=\"completeEvent('/event/finish/" + event.id + "')\" type=\"button\"" +
        " class=\"btn btn-sm btn-outline-secondary btn-outline-success\"";
        if (event.status === "completed"){
            eventElement += "disabled";
        }
        eventElement += ">Dokončit" +
        "</button>" +
        "<button onclick=\"editEvent('/event/edit/"+event.id+"')\" type=\"button\" class=\"btn btn-sm btn-outline-secondary\">Upravit</button>" +
        "<button onclick=\"askToDelete('/event/delete/"+event.id+"')\" type=\"button\"" +
        " class=\"btn btn-sm btn-outline-secondary btn-outline-danger\">Smazat" +
        "</button>" +
        "</div>" +
        "<small class=\"text-muted\">" + event.date_to + "</small>" +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>";
    return eventElement;
}