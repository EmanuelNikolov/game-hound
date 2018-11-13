class UI {
    constructor() {
        this.searchBar = document.getElementById("search");
        this.cardDeck = document.querySelector(".card-deck");
    }

    showCards() {
        //todo
    }

    clearSearch() {
        this.searchBar.value = "";
    }
}

export default UI;