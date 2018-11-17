import {ui} from './search-ui';

ui.box.on("keypress", (e) => {
    if (e.which == 13) {
        getGames();
    }
});

$(".js-search-btn").on("click", getGames);

$("body").on("click", ".js-load-btn", loadMore);

function getGames() {
    if (ui.box.val().length > 3) {
        $.ajax({
            url: `${ui.baseUrl}search/${ui.box.val()}`,
            dataType: "json",
            beforeSend: () => {
                ui.showSpinner();
                ui.container.hide();
            }
        })
            .done((games, status, xhr) => {
                if (games.length === 0) {
                    ui.showAlert("Nothing matches your search ┐(͠≖ ͜ʖ͠≖)┌");
                } else {
                    ui.changeMainHeading(ui.box.val());
                    ui.showGames(games, true);
                    ui.showLoadButton(xhr);
                }
            })
            .fail(xhr => ui.showLoadButton(xhr))
            .always(() => {
                ui.clearSpinner();
                ui.container.show();
            });
    } else {
        ui.showAlert("Your input must be at least 4 characters!");
    }
}

function loadMore() {
    $.ajax({
        url: `${ui.baseUrl}search/${ui.box.val()}?offset=${ui.offset}`,
        dataType: "json",
        beforeSend: () => ui.showSpinner()
    })
        .done((games, status, xhr) => {
            ui.showGames(games);
            ui.showLoadButton(xhr, games.length);
        })
        .fail(xhr => ui.showLoadButton(xhr))
        .always(() => ui.clearSpinner());
}