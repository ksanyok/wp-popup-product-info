jQuery(document).ready(function($) {
    // Код для десктопной версии
    $('.wp-popup-add-to-cart').click(function() {
        $('.single_add_to_cart_button').click();
    });
    
    // Код для мобильной версии
    $('.mobile-popup-add-to-cart').click(function() {
        $('.single_add_to_cart_button').click();
    });
});


jQuery(document).ready(function($) {
    // Отслеживаем скроллинг
    $(window).scroll(function() {
        // Получаем позицию блока с рейтингом
        var ratingPosition = $('.woocommerce-product-rating').offset().top + $('.woocommerce-product-rating').height();

        // Получаем текущую позицию скроллинга
        var scrollPosition = $(this).scrollTop();

        // Показываем попапы, если пользователь проскроллил за блок с рейтингом
        if (scrollPosition > ratingPosition) {
            $('.popup-container').fadeIn();
            $('.mobile-popup-container').fadeIn();
        } else {
            $('.popup-container').fadeOut();
            $('.mobile-popup-container').fadeOut();
        }
    });
});




/* jQuery(document).ready(function($) {
    // Скрываем всплывающее окно при загрузке страницы
    $('.popup-container').hide();

    // Отслеживаем скроллинг
    $(window).scroll(function() {
        // Получаем текущую позицию скроллинга
        var scrollPosition = $(this).scrollTop();

        // Показываем всплывающее окно, если пользователь проскроллил больше чем 200px
        if (scrollPosition > 200) {
            $('.popup-container').fadeIn();
        } else {
            $('.popup-container').fadeOut();
        }
    });
});
 */