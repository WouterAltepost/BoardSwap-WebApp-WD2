document.addEventListener("DOMContentLoaded", function () {
    const addToCartButtons = document.querySelectorAll(".add-to-cart-btn");

    addToCartButtons.forEach(button => {
        button.addEventListener("click", function () {
            const productId = this.getAttribute("data-id");

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `product_id=${productId}&quantity=1`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                    } else {
                        alert("Failed to add to cart: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Add to cart failed:", error);
                });
        });
    });
});
