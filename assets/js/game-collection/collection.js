import {ui} from '../ui/ui';

// Event Listeners
$("body").on("click", ".js-load-btn", loadMore);
ui.container.on("click", ".btn-game-remove", deleteGame);

function deleteGame(e) {
    if (confirm(`Are you sure you want to delete this collection?`)) {
        const delBtn = e.target;

        $.ajax({
            url: delBtn.href,
            type: "DELETE",
            data: `token=${delBtn.dataset.csrf}`,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        })
            .done(() => $(delBtn).parentsUntil(ui.container).remove())
            .fail(() => ui.showAlert("An error occurred while trying to delete the game."));
    }

    e.preventDefault();
}

function loadMore() {
    $.ajax({
        url: `${window.location.href}?offset=${ui.offset}`,
        dataType: "json",
        beforeSend: () => ui.showSpinner()
    })
        .done((games, status, xhr) => {
            if (games.length === ui.offset) {
                ui.showLoadButton(xhr, games.length);
            } else {
                ui.showLoadButton(xhr, 0);
            }

            ui.showGames(games);
        })
        .fail(xhr => ui.showLoadButton(xhr))
        .always(() => ui.clearSpinner());
}