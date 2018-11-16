class UI {
    constructor() {
        this.container = $(".js-card-container");
        this.box = $(".js-search-box");
        this.searchContainer = $(".js-search-container");
        this.mainHeading = $("h1");
        this.loader = $(".loader");
        this.baseUrl = "http://127.0.0.1:8000/game/";
        this.offset = 0;
    }

    changeMainHeading(input) {
        this.mainHeading.text(`Results for ${input}`);
    }

    showAlert(message) {
        this.clearAlert();

        const alert = `
            <div class="alert alert-danger" role="alert">
                ${message}
            </div>
        `;

        $(alert).prependTo(this.searchContainer);

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
        const cards = games.map(game => {
            return (`
                <div class="col-sm-6 col-md-4 col-lg-2 px-lg-2 mx-auto mb-4">
                    <div class="card">
                        <a href="${this.baseUrl + game.slug}">
                            <img src="${this.generateImageUrl(game.cover)}" class="card-img-top">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title text-truncate">${game.name}</h5>
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

    showLoadButton(xhr, count = 1) {
        this.clearLoadButton();

        let load = ``;

        if (xhr.status === 200) {
            this.offset = xhr.getResponseHeader("Offset");

            if (count === 0) {
                load = `
                    <div class="alert alert-secondary js-load-btn" role="alert">
                        That's all folks ( ͡° < ͡°)炎炎炎炎
                    </div>
                `;
            } else {
                load = `
                    <button class="btn btn-block btn-lg btn-info mb-4 js-load-btn" 
                            type="button">
                            Load More Results
                    </button>
                `;
            }
        } else {
            load = `
                    <div class="alert alert-secondary js-load-btn" role="alert">
                        An error has occurred... ( ‾ ʖ̫ ‾)
                    </div>
            `;
        }

        $(load).insertAfter(this.loader.parent().parent());
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
        $("html, body").animate({ scrollTop: $(document).height() }, "slow");
    }

    clearSpinner() {
        this.loader.removeClass("m-5").hide();
    }

    generateImageUrl(cover) {
        let {cloudinary_id, url} = {...cover};

        if (cloudinary_id !== undefined) {
            let base = "https://images.igdb.com/igdb/image/upload/t_";

            return `${base}cover_uniform/${cloudinary_id}.png`;
        }

        return url;
    }
}

export const ui = new UI();