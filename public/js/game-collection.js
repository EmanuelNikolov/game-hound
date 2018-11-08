document.querySelector('.collections-container').addEventListener('click', evt => {
    if (evt.target.matches('.collection-delete')) {
        const name = evt.target.previousElementSibling.textContent;
        if (confirm(`Are you sure you want to delete ${name.trim()}?`)) {
            console.log(name);
            fetch(evt.target.href, {
                method: "DELETE",
                body: `token=${evt.target.dataset.csrf}`,
                headers: {"Content-Type": "application/x-www-form-urlencoded"}
            })
                .then(response => {
                    if (!response.ok) {
                        throw Error(response.statusText);
                    }

                    evt.target.parentElement.remove();
                })
                .catch(e => evt.target.textContent = "An unexpected error occured!");
        }

        evt.preventDefault();
    }
});