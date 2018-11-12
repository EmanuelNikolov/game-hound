document.querySelector('.btn-game-remove').addEventListener('click', e => {
    if (confirm(`Are you sure you want to delete this collection?`)) {
        const delBtn = e.target;

        fetch(delBtn.href, {
            method: "DELETE",
            body: `token=${delBtn.dataset.csrf}`,
            headers: {"Content-Type": "application/x-www-form-urlencoded"}
        })
            .then(response => {
                if (!response.ok) {
                    throw Error();
                }

                delBtn.parentElement.remove();
            })
            .catch(() => {
                const message = document.createElement('span');
                message.className = 'alert alert-danger d-block';
                message.appendChild(document.createTextNode("An error occured when trying to delete the game!"));
                delBtn.parentElement.insertBefore(message, delBtn.parentElement.firstChild);

                setTimeout(() => message.remove(), 3000);
            });
    }

    e.preventDefault();
});