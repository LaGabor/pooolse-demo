$(document).ready(function() {
    
    let shoppingCart = JSON.parse(sessionStorage.getItem('shoppingCart')) || {};
    let temporaryShoppingCart = JSON.parse(sessionStorage.getItem('temporaryShoppingCart')) || {};
    let products = null;

    if (Object.keys(shoppingCart).length > 0) {
        temporaryShoppingCart = { ...shoppingCart };
        sessionStorage.setItem('temporaryShoppingCart', JSON.stringify(temporaryShoppingCart));
    }

    if (Object.keys(shoppingCart).length > 0) {
       
        $.ajax({
            url: shoppingCartDataUrl,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ cart: shoppingCart }),
            success: function(response) {
                console.log(response['data']);
                products = response['data'];
                updateCartUI(products);
            },
            error: function() {
                console.error('Error fetching cart data.');
            }
        });
    } else {
        
        $('#cartContainer').html(`
            <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
                <a href="${homepageUrl}" class="btn btn-secondary">Vissza a vásárláshoz</a>
            </div>
        `);
        
    }

    function updateCartUI(products) {
        if (products) {
            let cartItemsHtml = '';
    
            products.forEach(product => {
                
                let quantity = product.piece || 0; 
               
                cartItemsHtml += `
                    <div class="cartCard mt-5 moveTransition">
                        <div class="card-body d-flex align-items-center position-relative">
                            <div class="position-relative d-flex justify-content-center cursor-pointer cartImgBg">
                                <div class="d-flex justify-content-center position-relative">
                                    <img class="cartImg" src="${product.pic_route}">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="cartBodyDivider">
                                    <h5 class="cartCardTitle me-4 mb-3">${product.name}</h5>
                                    <div class="d-flex align-items-center justify-content-center cartQuantityWrapper mb-3">
                                        <button type="button" class="cartNrOfProductBtn minusBtn cart" data-id="${product.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#4D4F53">
                                                <path fill="none" d="M0 0h24v24H0z"></path>
                                                <path d="M5 11h14v2H5z"></path>
                                            </svg>
                                        </button>
                                        <div class="d-flex flex-column align-items-center mx-3">
                                            <span class="cartCardQuantityText">Darabszám:</span>
                                            <div class="cartQuantityToBuy buyCounter" data-id="${product.id}" data-price="${product.discount_price}" data-originalPrice="${product.price}" data-quantity="${quantity}">
                                                ${quantity} db
                                            </div>
                                        </div>
                                        <button type="button" class="cartNrOfProductBtn plusBtn cart" data-id="${product.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#4D4F53">
                                                <path fill="none" d="M0 0h24v24H0z"></path>
                                                <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mt-3 aggregatedProductPrices" data-aggregated-price="${product.discount_price_all}">
                                        ${product.price === product.discount_price ? 
                                            `<span class="cardPrice">${product.discount_price.toLocaleString('hu-HU')} Ft</span>` :
                                            `<span class="cardOriginalPrice me-4"><s>${product.price_all.toLocaleString('hu-HU')} Ft</s></span>
                                             <span class="cardPrice" style="color: red;">${product.discount_price_all.toLocaleString('hu-HU')} Ft</span>`
                                        }
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close custom-close-btn position-absolute" aria-label="Close" data-id="${product.id}"></button>
                        </div>
                    </div>
                `;
            });
            
            $('#cartContainer').html(cartItemsHtml);
        }
    }
    
});
