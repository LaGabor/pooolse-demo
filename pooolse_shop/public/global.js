$(document).ready(async function() {

    
    async function getShoppingCartPrice(shoppingCart) {
        try {
            let response = await $.ajax({
                url: shoppingCartPriceUrl,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ cart: shoppingCart })
            });
            return response.totalPrices;  
        } catch (error) {
            console.error('Error in getShoppingCartPrice:', error);
            return null;
        }
    }

    let shoppingCart = JSON.parse(sessionStorage.getItem('shoppingCart')) || {};
    let temporaryShoppingCart = JSON.parse(sessionStorage.getItem('temporaryShoppingCart')) || {};

    if (Object.keys(shoppingCart).length > 0 && Object.keys(temporaryShoppingCart).length === 0) {
        temporaryShoppingCart = { ...shoppingCart };
        sessionStorage.setItem('temporaryShoppingCart', JSON.stringify(temporaryShoppingCart));
    }


    if (Object.keys(shoppingCart).length > 0) {
        await updateCartInfo(shoppingCart);
    }

    async function updateCartInfo(shoppingCart) {
        try {
            let prices = await getShoppingCartPrice(shoppingCart);
            
            if (prices) {
                let cartCount = Object.values(shoppingCart).reduce((total, item) => total + item.quantity, 0);
                $('#cartProductCount').text(cartCount);
                
                let formattedDiscountPrice = prices.discountPrice.toLocaleString('hu-HU');
                $('#priceSummary').text(`${formattedDiscountPrice} HUF`);

                let formattedOriginalPrice = prices.originalPrice.toLocaleString('hu-HU');
                $('#originalPrice').html(`<s>${formattedOriginalPrice} Ft</s>`);

                let formattedDiscountedPrice = prices.discountPrice.toLocaleString('hu-HU');
                $('#discountedPrice').text(`${formattedDiscountedPrice} Ft`);
                
                }
        } catch (error) {
            console.error('Error updating cart info:', error);
        }
    }

    let plus = 1;
    let minus = -1;
    
    $('.minusBtn').click(function(event) {
        let [$quantityElement, originalQuantity, currentQuantity] = changeProductCount(event, minus);
        let productId = $(this).data('id');
        
        if ($(this).hasClass('cart') && temporaryShoppingCart[productId].quantity > 1) {
            temporaryShoppingCart[productId].quantity -= 1;
            sessionStorage.setItem('temporaryShoppingCart', JSON.stringify(temporaryShoppingCart));
        }
        
        updatePrice($quantityElement, event, minus, originalQuantity, currentQuantity);
    });

    $('.plusBtn').click(function(event) {
        let [$quantityElement, originalQuantity, currentQuantity] = changeProductCount(event, plus);
        let productId = $(this).data('id');
        
        if($(this).hasClass('cart')){
            temporaryShoppingCart[productId].quantity += 1;
             sessionStorage.setItem('temporaryShoppingCart', JSON.stringify(temporaryShoppingCart));

        }
        
        updatePrice($quantityElement, event, plus, originalQuantity, currentQuantity );
    });

    function changeProductCount(event, eventType) {
        let $button = $(event.currentTarget);
        let $quantityElement = $button.siblings('.mx-3').find('.buyCounter');
        let currentQuantity = parseInt($quantityElement.attr('data-quantity'));
        let originalQuantity = currentQuantity;
        console.log(currentQuantity, originalQuantity);
        
        if (currentQuantity > 1 || eventType > 0) {
            currentQuantity += eventType;
            $quantityElement.attr('data-quantity', currentQuantity);
            $quantityElement.text(currentQuantity + ' db');
        }

        if (originalQuantity == 2 && eventType < 0) {
            $button.removeClass('moveTransition').addClass('moveTransitionMinMinus');
        }
        else if (originalQuantity == 1 && eventType > 0) {
            $button.siblings('.minusBtn').removeClass('moveTransitionMinMinus').addClass('moveTransition');
        }

        return [$quantityElement, originalQuantity, currentQuantity];
    }

    function updatePrice($quantityElement, event, eventType, originalQuantity, currentQuantity) {
        let pricePerItem = parseInt($quantityElement.attr('data-price'));
        let totalPrice = pricePerItem * currentQuantity;

        if ($quantityElement.attr('data-originalprice') !== undefined) {
            console.log('Kacsa');
            let originalPricePerItem = parseInt($quantityElement.attr('data-originalprice'));
            let $cartCard =  $(event.currentTarget).closest('.cartCard');
            let $cardPrice = $cartCard.find('.cardPrice');
            let formattedTotalPrice = totalPrice.toLocaleString('hu-HU', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    
            $cardPrice.text(formattedTotalPrice  + ' Ft');

            if(originalPricePerItem !== pricePerItem){
                let originalTotalPrice = originalPricePerItem * currentQuantity;
                let $originalCardPrice = $cartCard.find('.cardOriginalPrice');
                let formattedOriginalTotalPrice = originalTotalPrice.toLocaleString('hu-HU', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

                $originalCardPrice.text(formattedOriginalTotalPrice  + ' Ft');

                updateCartTotals(eventType, pricePerItem, originalPricePerItem, originalQuantity, currentQuantity)
            }else{
                updateCartTotals(eventType, pricePerItem, pricePerItem, originalQuantity, currentQuantity)
            }


        }else{
            
            let $aggregatedPriceElement = $quantityElement.closest('.quantityContainer').find('.aggregatedProductPrice');
            let formattedTotalPrice = totalPrice.toLocaleString('hu-HU', {minimumFractionDigits: 0, maximumFractionDigits: 0 });
            
            $aggregatedPriceElement.text(formattedTotalPrice + ' Ft');
        }
    }

    $(".dropdown-item:contains('Név [A-Z]')").click(function(e) {
        e.preventDefault();

        let $productsList = $("#productsList");

        let $products = $productsList.children(".col");

        $products.sort(function(a, b) {
            let aName = $(a).find(".cardTitle").text().toLowerCase(),
                bName = $(b).find(".cardTitle").text().toLowerCase();

            return aName > bName ? 1 : aName < bName ? -1 : 0;
        });

        $products.detach().appendTo($productsList);
    });

    $(".dropdown-item:contains('Ár Növekvő')").click(function(e) {
        e.preventDefault();

        let $productsList = $("#productsList");

        let $products = $productsList.children(".col");

        $products.sort(function(a, b) {
            let aPrice = parseInt($(a).find(".cardPrice").text().replace(' Ft', '').replace(/\s/g, '')),
                bPrice = parseInt($(b).find(".cardPrice").text().replace(' Ft', '').replace(/\s/g, ''));

            return aPrice - bPrice;
        });

        $products.detach().appendTo($productsList);
    });

    function updateCartTotals(eventType, discountValue, originalValue, originalQuantity, currentQuantity){

        let currentOriginalPrice = parseInt($('#originalPrice').text().replace(/\s/g, '').replace('Ft', ''));
        let currentDiscountedPrice = parseInt($('#discountedPrice').text().replace(/\s/g, '').replace('Ft', ''));

        let totalOriginalPrice = eventType === 1 
            ? currentOriginalPrice + parseInt(originalValue)
            : (originalQuantity === currentQuantity 
                ? currentOriginalPrice 
                : currentOriginalPrice - parseInt(originalValue));
        
        let totalPrice = eventType === 1 
            ? currentDiscountedPrice + parseInt(discountValue) 
            :(originalQuantity === currentQuantity 
                ? currentDiscountedPrice 
                : currentDiscountedPrice - parseInt(discountValue));
        
        $('#originalPrice').text(totalOriginalPrice.toLocaleString('hu-HU', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Ft');
        $('#discountedPrice').text(totalPrice.toLocaleString('hu-HU', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Ft');
    }

    $('.btn-close').click(function(event) {
        
        let $cartCard = $(event.currentTarget).closest('.cartCard');
        let quantity = parseInt($cartCard.find('.buyCounter').attr('data-quantity'));
        let price = parseFloat($cartCard.find('.buyCounter').attr('data-price'));
        let originalPrice = parseFloat($cartCard.find('.buyCounter').attr('data-originalPrice'));

        let productId = $(this).data('id');
        delete temporaryShoppingCart[productId];
        sessionStorage.setItem('temporaryShoppingCart', JSON.stringify(temporaryShoppingCart));
        
        let currentDiscountedPrice = parseInt($('#discountedPrice').text().replace(/\s/g, '').replace('Ft', ''));
        let currentOriginalPrice = parseInt($('#originalPrice').text().replace(/\s/g, '').replace('Ft', ''));

        let newDiscountedPrice = currentDiscountedPrice - (price * quantity);
        let newOriginalPrice = currentOriginalPrice - (originalPrice * quantity);

        $('#discountedPrice').text(newDiscountedPrice.toLocaleString('hu-HU', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Ft');
        $('#originalPrice').text(newOriginalPrice.toLocaleString('hu-HU', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' Ft');

        $cartCard.remove();
    });

    $('.btn-danger').on('click', async function() {
        let productId = $(this).closest('.card').find('.quantityToBuy').data('id');
        let quantityText = $(this).closest('.card').find('.buyCounter').text(); 
        let quantity = parseInt(quantityText.replace(' db', ''));
        let shoppingCart = JSON.parse(sessionStorage.getItem('shoppingCart')) || {};
        
        console.log(quantity)
        if ($(this).text().trim() === 'Kosár mentése') {
           
            sessionStorage.setItem('shoppingCart', JSON.stringify(temporaryShoppingCart));
            updateCartProductCount();
            
            let prices = await getShoppingCartPrice(temporaryShoppingCart);
            if (prices) {
                
                let formattedDiscountPrice = prices.discountPrice.toLocaleString('hu-HU');
                let formattedOriginalPrice = prices.originalPrice.toLocaleString('hu-HU');

                $('#discountedPrice').text(`${formattedDiscountPrice} Ft`);
                $('#originalPrice').text(`${formattedOriginalPrice} Ft`); 
                $('#priceSummary').text(`${formattedDiscountPrice} HUF`);
            }
        }else{
            
            if (shoppingCart[productId]) {
                shoppingCart[productId].quantity += quantity;
            } else {
                shoppingCart[productId] = {
                    id: productId,
                    quantity: quantity
                };
            }
        
            sessionStorage.setItem('shoppingCart', JSON.stringify(shoppingCart));
        
            let prices = await getShoppingCartPrice(shoppingCart);
            
            if (prices) {
                let cartCount = Object.values(shoppingCart).reduce((total, item) => total + item.quantity, 0);
                $('#cartProductCount').text(cartCount);
                let formattedDiscountPrice = prices.discountPrice.toLocaleString('hu-HU');
                $('#priceSummary').text(`${formattedDiscountPrice} HUF`);    
            }
        }
    
    });

    function updateCartProductCount() {
        
        let shoppingCart = JSON.parse(sessionStorage.getItem('shoppingCart')) || {};
        let totalCount = 0;
        for (let productId in shoppingCart) {
            totalCount += shoppingCart[productId].quantity;
        }

        $('#cartProductCount').text(totalCount);
    }

});
