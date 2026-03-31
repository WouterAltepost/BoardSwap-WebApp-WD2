document.addEventListener("DOMContentLoaded", function () {
    fetch('/favourites/get')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('favouritesContainer');
            if (data.status === 'success' && data.favourites.length > 0) {
                container.innerHTML = '';
                data.favourites.forEach(product => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    col.innerHTML = `
                        <div class="card product-card">
                            <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-price">$${parseFloat(product.price).toFixed(2)}</p>
                                <div class="button-group">
                                    <a href="/product/${product.id}" class="btn btn-outline">View Details</a>
                                    <button class="btn btn-outline remove-fav" data-id="${product.id}">Remove</button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

                document.querySelectorAll('.remove-fav').forEach(button => {
                    button.addEventListener('click', function () {
                        const productId = this.dataset.id;

                        fetch(`/favourites/remove/${productId}`, {
                            method: 'POST',
                            headers: {
                                'X-HTTP-Method-Override': 'DELETE'
                            }
                        })
                            .then(response => response.json())
                            .then(result => {
                                if (result.status === 'success') {
                                    alert(result.message);
                                    window.location.reload();
                                } else {
                                    alert('Failed to remove product from favourites.');
                                }
                            })
                            .catch(error => {
                                console.error('Error removing from favourites:', error);
                            });
                    });
                });

            } else {
                container.innerHTML = '<p>You have no favourites yet.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading favourites:', error);
            document.getElementById('favouritesContainer').innerHTML = '<p>Failed to load favourites.</p>';
        });
});
