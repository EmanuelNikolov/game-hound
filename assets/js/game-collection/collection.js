import {ui} from '../ui/ui';

// Event Listeners
$("body").on("click", ".js-load-btn", loadMore);
$("#deleteGameModal").on("show.bs.modal", handleModal);
$("#confirmDelete").on("click", deleteGame);

function handleModal(e) {
    const removeBtn = $(e.relatedTarget),
        modal = $(this),
        gameName = removeBtn.prev().text();

    ui.removeBtn = removeBtn;

    modal.find(".modal-title").text(`Delete ${gameName}?`);
    modal.find(".modal-body-p").text(`Are you sure you want to delete ${gameName}?`);
    $("#confirmDelete").attr("href", removeBtn.attr("href"));
}

function deleteGame(e) {
    const confirmBtn = e.target;

    $.ajax({
        url: confirmBtn.href,
        type: "DELETE"
    })
        .done(() => {
            $(ui.removeBtn).parentsUntil(ui.container).remove();
            ui.offset--;
        })
        .fail(() => ui.showAlert("An error occurred while trying to delete the game."))
        .always(() => $("#deleteGameModal").modal("hide"));

    e.preventDefault();
}

function loadMore() {
    $.ajax({
        url: `${window.location.href}?offset=${ui.offset}`,
        dataType: "json",
        beforeSend: () => ui.showSpinner()
    })
        .done((games, status, xhr) => {
            console.log(games, ui.offset);
            if (games.length > 0) {
                ui.showLoadButton(xhr, games.length);
            } else {
                ui.showLoadButton(xhr, 0);
            }

            ui.showGames(games);
        })
        .fail(xhr => ui.showLoadButton(xhr))
        .always(() => ui.clearSpinner());
    console.log(ui.offset);
}