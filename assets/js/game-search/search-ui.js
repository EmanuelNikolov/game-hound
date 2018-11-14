class UI {
    constructor() {
        this.container = $(".js-card-container");
        this.mainHeading = $("h1");
        this.baseUrl = "http://127.0.0.1:8000/game/";
        this.offset = 0;
        this.alert = $(".alert");
    }

    changeMainHeading(input) {
        this.mainHeading.text(`Results for ${input}`);
    }

    showAlert() {
        this.clearAlert();

        this.alert.text("Your input must be at least 4 characters!").show();

        setTimeout(() => {
            this.alert.hide();
        }, 3000);
    }

    clearAlert() {
        if (this.alert) {
            alert().remove();
        }
    }

    showGames(games, firstCall = false) {
        const cards = games.map(game => {
            return (`
                <div class="col-sm-6 col-md-5 col-lg-3 mx-auto mb-4">
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

    showLoadButton(xhr) {
        this.clearLoadButton();

        let load = ``;

        if (xhr.status === 200) {
            this.offset = xhr.getResponseHeader("Offset");

            load = `
                    <button class="btn btn-block btn-lg btn-info mb-4 js-load-btn" 
                            type="button">
                            Load More Results
                    </button>
            `;
        } else {
            load = `
                    <div class="alert alert-secondary" role="alert">
                        No more results... ( ‾ ʖ̫ ‾)
                    </div>
            `;
        }

        $(load).insertAfter(this.container);
    }

    clearLoadButton() {
        const loadBtn = $(".js-load-btn");

        if (loadBtn) {
            loadBtn.remove();
        }
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