document.addEventListener("DOMContentLoaded", function () {
    const cartBody = document.getElementById("cartBody");
    const cartTotal = document.getElementById("cartTotal");

    function loadCart() {
        fetch('/cart/get')
            .then(response => response.json())
            .then(data => {
                cartBody.innerHTML = "";
                let totalSum = 0;

                if (data.status === 'success' && data.cart.length > 0) {
                    data.cart.forEach(product => {
                        const total = product.price * product.quantity;
                        totalSum += total;

                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${product.name}</td>
                            <td>$${parseFloat(product.price).toFixed(2)}</td>
                            <td>
                                <input type="number" value="${product.quantity}" min="1" data-id="${product.id}" class="qty-input">
                            </td>
                            <td>$${parseFloat(total).toFixed(2)}</td>
                            <td>
                                <button class="btn btn-danger remove-btn" data-id="${product.id}">Remove</button>
                            </td>
                        `;
                        cartBody.appendChild(row);
                    });

                    cartTotal.textContent = totalSum.toFixed(2);
                } else {
                    cartBody.innerHTML = `<tr><td colspan="5">Your cart is empty.</td></tr>`;
                    cartTotal.textContent = "0.00";
                }
            })
            .catch(err => {
                cartBody.innerHTML = `<tr><td colspan="5">Error loading cart.</td></tr>`;
                console.error("Fetch error:", err);
            });
    }

    // Listen for changes to quantity inputs
    cartBody.addEventListener('change', function (e) {
        if (e.target.classList.contains('qty-input')) {
            const productId = e.target.getAttribute('data-id');
            const newQty = e.target.value;

            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `product_id=${productId}&quantity=${newQty}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadCart();
                    } else {
                        alert(data.message);
                    }
                });
        }
    });

    // Listen for click on remove buttons
    cartBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-btn')) {
            const productId = e.target.getAttribute('data-id');

            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `product_id=${productId}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadCart();
                    } else {
                        alert(data.message);
                    }
                });
        }
    });

    // Initial load
    loadCart();
});
