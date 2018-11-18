import {ui} from './game-search/search-ui';

$(".table").on("click", ".btn-game-remove", (e) => {
    if (confirm(`Are you sure you want to delete this collection?`)) {
        const delBtn = e.target;

        fetch(delBtn.href, {
            method: "DELETE",
            body: `token=${delBtn.dataset.csrf}`,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw Error();
                }

                delBtn.parentElement.parentElement.remove();
            })
            .catch((e) => {
                const alert = `
                    <span class="alert alert-danger d-block mb-0">
                        <span class="d-block">
                            <span class="form-error-icon badge badge-danger text-uppercase">ERROR</span>
                            <span class="form-error-message">An error occurred while trying to delete the game</span>
                        </span>
                    </span>
                `;

                $(alert).prependTo(ui.alertContainer);

                setTimeout(() => alert.remove(), 3000);
            });
    }

    e.preventDefault();
});