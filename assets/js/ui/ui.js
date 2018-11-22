class UI {
    constructor() {
        this.container = $(".js-card-container");
        this.box = $(".js-search-box");
        this.alertContainer = $(".js-alert-container");
        this.mainHeading = $("h1");
        this.loader = $(".loader");
        this.baseUrl = "/game/";
        this.offset = 0;
    }

    changeMainHeading(input) {
        this.mainHeading.text(`Results for ${input}`);
    }

    showAlert(message) {
        this.clearAlert();

        const alert = `
            <span class="alert alert-danger d-block mb-0">
                <span class="d-block">
                    <span class="form-error-icon badge badge-danger text-uppercase">ERROR</span>
                    <span class="form-error-message">${message}</span>
                </span>
            </span>
        `;

        $(alert).prependTo(this.alertContainer);

        setTimeout(() => {
            this.clearAlert();
        }, 3000);
    }

    clearAlert() {
        const alert = $(".alert");

        if (alert) {
            alert.remove();
        }
    }

    showGames(games, firstCall = false) {
        const deleteBtn = document.querySelector(".btn-game-remove");

        const cards = games.map(game => {
            let imageUrl,
                {cloudinary_id, url} = {...game.cover}
            ;

            if (cloudinary_id !== undefined) {
                imageUrl = this.generateImageUrl(cloudinary_id);
            } else {
                imageUrl = this.generateImageUrl(url);
            }

            return (`
                <div class="col-sm-6 col-md-6 col-xl-3 mx-auto mb-4">
                    <div class="card">
                        <a href="${this.baseUrl + game.slug}">
                            <img src="${imageUrl}" class="card-img-top">
                        </a>
                        <div class="card-body">
                            <p class="card-title text-truncate">${game.name}</p>
                            ${deleteBtn ? deleteBtn.outerHTML : ""}
                        </div>
                    </div>
                </div>
            `);
        });

        if (firstCall) {
            this.container.children().remove();
        }

        this.container.append(cards);
    }

    showLoadButton(xhr = {status: 200}, count = 4) {
        this.clearLoadButton();

        let message, type, attr;

        if (xhr.status === 200) {
            this.offset += count;

            if (count === 0) {
                message = "That's all folks";
                type = "btn-default disabled";
                attr = "disabled";
            } else {
                message = "Load More Games";
                type = "btn-info";
            }
        } else {
            message = "An error has occurred";
            type = "btn-default";
        }

        const loadBtn = `
            <button class="btn btn-block btn-lg ${type} mb-4 js-load-btn" 
                     ${attr}>
                    ${message}
            </button>
        `;

        $(loadBtn).insertAfter(this.loader.parent());
    }

    clearLoadButton() {
        const loadBtn = $(".js-load-btn");

        if (loadBtn) {
            loadBtn.remove();
        }
    }

    showSpinner() {
        this.clearLoadButton();
        this.loader.addClass("m-5").show();
        $("html, body").animate({scrollTop: $(document).height()}, "slow");
    }

    clearSpinner() {
        this.loader.removeClass("m-5").hide();
    }

    generateImageUrl(url) {
        return `https://images.igdb.com/igdb/image/upload/t_cover_uniform/${url}.png`;
    }
}

export const ui = new UI();