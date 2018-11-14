import {ui} from './search-ui';

$(".js-search-btn").on("click", getGames);

$("body").on("click", ".js-load-btn", loadMore);

function getGames() {
    const box = $(".js-search-box");

    if (box.val().length > 3) {
        $.ajax({
            url: `${ui.baseUrl}search/${box.val()}`,
            dataType: "json"
        }).done((games, status, xhr) => {
                ui.changeMainHeading(box.val());
                ui.showGames(games, true);
                ui.showLoadButton(xhr, games.length);
            }
        ).fail(xhr => ui.showLoadButton(xhr));
    } else {
        ui.showAlert();
    }
}

function loadMore() {
    const box = $(".js-search-box");
console.log(ui.nextPageHeader);
    $.ajax({
        url: `${ui.baseUrl}search/${box.val()}`,
        dataType: "json",
        headers: {"X-Next-Page": ui.nextPageHeader}
    }).done((games, status, xhr) => {
        ui.showGames(games);
        ui.showLoadButton(xhr, games.length);
    }).fail(xhr => ui.showLoadButton(xhr));
}