import UI from "./game-search-ui";

let ui = new UI();

function getCards() {
    $.ajax();
}

let scrollLoad = true;

$(window).scroll(function () {
    if (scrollLoad && $(window).scrollTop() >= $(document).height() - $(window).height() - 300) {
        scrollLoad = false;
        let url = $(".results").data("url");
        $.ajax({
            type: "GET",
            dataType: "json",
            url: url,
            success: (data) => console.log(JSON.parse(data))
        })
    }
});