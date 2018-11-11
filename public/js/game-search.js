document.onscroll = (e) => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        const scrollUrl = document.querySelector('.results').dataset.url;
        fetch(scrollUrl, {
            headers: {"X-Requested-With": "XMLHttpRequest"}
        })
            .then(response => response.json())
            .then(json => console.log(JSON.parse(json)));
        //todo
    }
};
