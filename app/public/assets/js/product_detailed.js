document.addEventListener('DOMContentLoaded', function () {
    const cartForm = document.getElementById('addToCartForm');
    if (cartForm) {
        cartForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(cartForm);

            fetch('/cart/add', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = '/cart/';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

    const favForm = document.getElementById('addToFavouritesForm');
    if (favForm) {
        favForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(favForm);

            fetch('/favourites/add', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(err => {
                    console.error('Error:', err);
                });
        });
    }
});
