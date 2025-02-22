
searchHeader()
textTruncate()
categoryModal()
modal()
loginBtn()
popOver()
hamburgerMenu()
suggestedSwiper()
hoverImg()
// Seperate Number 3 Digits
$(document).ready(function(){
    $('.currency').each(function(){
        let number=$(this).text();
        let formattedNumber=number.replace(/\B(?=(\d{3})+(?!\d))/g , ',');
        $(this).text(formattedNumber);
    })
});
// Show Deatils When Clicked User Button On Header
function loginBtn(){
    let btn=$('.login-btn');
    btn.click(function(){
        let child=$(this).children('div');
        child.toggleClass('active');
        if(child.hasClass('active')){
            $(this).css({'border-bottom-left-radius': 'unset',
                'border-top-left-radius':'18px',
                'border-bottom-right-radius' : 'unset',
                'border-top-right-radius':'18px',
            });
        }else{
            $(this).css({'border-radius':'100px',
            });
        }
    })
}
function openOverlay(){
    $('.overlay').addClass('active');
    $('body').addClass('no-overflow');
}
function closeOverlay(){
    $('.overlay').removeClass('active');
    $('body').removeClass('no-overflow');
}
function textTruncate(){
    let titles=$('.explain-weblog');
    titles.each(function(){  
        let title=$(this).text();
      if(title.length> 161){        
        let truncate=title.slice(0,161)+' ...';
        $(this).text(truncate);
        
      }
    })
}
// Show Header After Scroll
$(document).ready(function(){
    $(window).scroll(function(){
        if($(this).scrollTop()>150){
            $('header').css({'position':'fixed',
                'top':'0',
                'width':'100%',
                'z-index':'20',
                'background-color':'white',
                'transition':'top 0.5s linear',
               
            });
        }else{
            $('header').css({'position':'unset',
                'top':'-100px',
                'background-color':'unset',
                'transition':'top 0.5s linear',
            });
        }
        
    })
});
function searchHeader(){
    let input = $('.header-search-input'),
        result = $('.header-search-result'),
        search = $('.search');
    let win = $(window);
    win.on("click", function (e) {
        if (input.is(e.target) ||
            result.is(e.target) ||
            result.has(e.target).length) {
            search.addClass("active");
            openOverlay();
        }
        else {
            search.removeClass("active");
            if ($('.header-overlay').hasClass('active')){
                closeOverlay();
            }
               
        }
    })


}
// Category Modal
function categoryModal(){
  let btn=$('.modal-mobile-category .category-btn'),
  btnChild=$('.modal-mobile-category .category-btn-child')
  btn.each(function(){
    $(this).click(function(){
       $('.menu-sublist').toggleClass('active');
       $(this).children($('.icon-angle-down')).toggleClass('deactive');
       $(this).children($('.icon-angle-up')).toggleClass('active');
    })
  })
  btnChild.each(function(){
    $(this).click(function(){
        $('.menu-sublist-child').toggleClass('active');
        $(this).children($('.icon-angle-down')).toggleClass('deactive');
        $(this).children($('.icon-angle-up')).toggleClass('active');
    })
  })
}
// Hamburger Menu Mobile
function hamburgerMenu(){
    let btn=$('.modal-mobile-menu .category-btn'),
    btnChild=$('.modal-mobile-menu .category-btn-child')
    btn.each(function(){
      $(this).click(function(){
         $('.menu-sublist').toggleClass('active');
         $(this).children($('.icon-angle-down')).toggleClass('deactive');
         $(this).children($('.icon-angle-up')).toggleClass('active');
      })
    })
    btnChild.each(function(){
      $(this).click(function(){
          $('.menu-sublist-child').toggleClass('active');
          $(this).children($('.icon-angle-down')).toggleClass('deactive');
          $(this).children($('.icon-angle-up')).toggleClass('active');
      })
    })
}
// Suggested Swiper In Search Modal
function suggestedSwiper(){
    new Swiper(".suggested-swiper",{
        slidesPerView:4,
        freeMode: true,
        spaceBetween:15,
        autoplay:true,
        breakpoints:{
            200: {
                slidesPerView: 1.5,
                spaceBetweenSlides: 10
            },
            360: {
                slidesPerView: 2,
                spaceBetweenSlides: 10
            },
            420: {
                slidesPerView: 2.2,
                spaceBetweenSlides: 10
            },
            640: {
                slidesPerView: 3,
                spaceBetweenSlides: 10
            },
            768: {
                slidesPerView:3.5,
                spaceBetweenSlides: 10
            },
            1024: {
                slidesPerView: 4,
                spaceBetweenSlides: 10
            },
    
        }
    });
}
// Open PopOver()
function popOver(){    
    $('[data-popover]').each(function(){
        $(this).click(function(event){  
            event.stopPropagation();
            let id = $(this).data('popover');
            $('[data-id]').removeClass('opened'); 
            $(this).closest($('.item')).find('[data-id=' + id + ']').addClass('opened');      
        });
        $(document).click(function(){
            $('[data-id]').removeClass('opened'); 
        });
        
    })
    
}
function modal(){
    $('[data-modal]').click(function () {
        let id = $(this).data('modal');        
        $('.modal[data-id=' + id + ']').addClass('active');
        $('.modal-overlay').addClass('active');
        $('body').addClass('no-overflow');
    });

    $('.modal-close').click(function () {
        $('.modal-overlay').removeClass('active');
        $('.modal').removeClass('active');
        $('body').removeClass('no-overflow');
    })


    $('.modal-overlay').click(function () {
        $('.modal-overlay').removeClass('active');
        $('.modal').removeClass('active');
        $('body').removeClass('no-overflow');
    });

}
// Scroll To Target Place
function scrollToTarget(){
    $('[data-scroll]').click(function () {
        let id = $(this).data('scroll');  
        let target=$('[data-id=' + id + ']');      
        $('html, body').stop().animate({
            scrollTop: target.offset().top -100
         }, 800);
    });
}

// Main Page
function mainPage(){
    categories()
    discounter()
    mostSaled()
    newest()
    function categories(){
        new Swiper(".categories-swiper",{
            slidesPerView: "5.3",
            freeMode:true,
            breakpoints:{
                200: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                360: {
                    slidesPerView: 2.5,
                    spaceBetweenSlides: 10
                },
                640: {
                    slidesPerView: 3.3,
                    spaceBetweenSlides: 10
                },
                768: {
                    slidesPerView: 4.3,
                    spaceBetweenSlides: 10
                },
                1024: {
                    slidesPerView: 5.3,
                    spaceBetweenSlides: 10
                },
        
            }
        });
    }
    function discounter(){
        new Swiper(".discounter-swiper",{
            slidesPerView:5,
            freeMode: true,
            spaceBetween:15,
            autoplay:true,
            breakpoints:{
                200: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                360: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                420: {
                    slidesPerView: 2.2,
                    spaceBetweenSlides: 10
                },
                640: {
                    slidesPerView: 3,
                    spaceBetweenSlides: 10
                },
                768: {
                    slidesPerView:4,
                    spaceBetweenSlides: 10
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetweenSlides: 10
                },
        
            }
        });
    }
    function mostSaled(){
        new Swiper(".mostSaled-swiper",{
            slidesPerView:5,
            freeMode: true,
            spaceBetween:15,
            autoplay:true,
            breakpoints:{
                200: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                360: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                420: {
                    slidesPerView: 2.2,
                    spaceBetweenSlides: 10
                },
                640: {
                    slidesPerView: 3,
                    spaceBetweenSlides: 10
                },
                768: {
                    slidesPerView:4,
                    spaceBetweenSlides: 10
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetweenSlides: 10
                },
        
            }
        });
    }
    function newest(){
        new Swiper(".newest-swiper",{
            slidesPerView:5,
            freeMode: true,
            spaceBetween:15,
            autoplay:true,
            breakpoints:{
                200: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                360: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                420: {
                    slidesPerView: 2.2,
                    spaceBetweenSlides: 10
                },
                640: {
                    slidesPerView: 3,
                    spaceBetweenSlides: 10
                },
                768: {
                    slidesPerView:4,
                    spaceBetweenSlides: 10
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetweenSlides: 10
                },
        
            }
        });
    }
   
}
 // view Another Photo After Hover In Product Cart
 function hoverImg(){

    $('article.product-cart').each(function(){
        $(this).hover(function(){
            $(this).children('a').find('figure .main-img').addClass('hidden');
            $(this).children('a').find('figure .hover-img').removeClass('hidden');
        },
        function(){
            $(this).children('a').find('figure .main-img').removeClass('hidden');
            $(this).children('a').find('figure .hover-img').addClass('hidden');
        }
    )
    })
}
// Products Page
function productsPage(){
  showSublist()
  priceRange()
  priceRange2()
  twoCloumn()
  threeCloumn()
  checkedAvailableInput()
   //  Show More Products Step By Step  
    $(document).ready(function(){
        let productShow=8;
        let totalPro=$('.product-item').length,        
        increment=6;

        $('.product-item:lt('+ productShow +')').addClass('show');
        $('.showMore').click(function(){
            productShow +=increment;
            $('.product-item:lt('+ productShow +')').addClass('show');
            if(productShow >= totalPro){
                $('.showMore').empty();
            }

        });
    })
    function showSublist(){
        let buttonShow=$('.show-subList'),
        btnChild=$('.show-subList-child');
        buttonShow.each(function(){
           $(this).click(function(){
               let plusIcon=$(this).find('.icon-plus');
               let crossIcon=$(this).find('.icon-minus');
               plusIcon.toggleClass('deactive');
               crossIcon.toggleClass('active');
               $(this).closest('li').find('.category-sublist').toggleClass('active');
            });
        });
        btnChild.each(function(){
            $(this).click(function(){
                let plusIcon=$(this).find('.icon-plus');
                let crossIcon=$(this).find('.icon-minus');
                plusIcon.toggleClass('deactive');
                crossIcon.toggleClass('active');
                $(this).closest('li').find('.category-sublist-child').toggleClass('active');
             });
         })
    }
    function priceRange(){
        let rangeInput = document.querySelectorAll('.range-input input'),        
            price = document.querySelectorAll('.price-text .currency'),
            range = document.querySelector('.slider .progress');
        let priceGap = 10000;
        rangeInput.forEach((input) => {
            input.addEventListener("input", (e) => {
                let minVal = parseInt(rangeInput[0].value),
                    maxVal = parseInt(rangeInput[1].value);
                if (maxVal - minVal < priceGap) {
                    if (e.target.className === "range-min") {
                        rangeInput[0].value = maxVal - priceGap;
                    } else {
                        rangeInput[1].value = minVal + priceGap;
                    }
                } else {
                    // Update the slider's visual representation
                    range.style.right = (minVal / rangeInput[0].max) * 100 + "%";
                    range.style.left = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
                    // Update the displayed prices after changing the range
                    let min = minVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    let max = maxVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');    
                    price[0].textContent = min;
                    price[1].textContent = max;
                }
            });
        });
    }
    // Modal
    function priceRange2(){
        let rangeInput = document.querySelectorAll('.range-input2 input'),        
            price = document.querySelectorAll('.price-text2 .currency'),
            range = document.querySelector('.slider2 .progress2');
        let priceGap = 10000;
        rangeInput.forEach((input) => {
            input.addEventListener("input", (e) => {
                let minVal = parseInt(rangeInput[0].value),
                    maxVal = parseInt(rangeInput[1].value);
                if (maxVal - minVal < priceGap) {
                    if (e.target.className === "range-min2") {
                        rangeInput[0].value = maxVal - priceGap;
                    } else {
                        rangeInput[1].value = minVal + priceGap;
                    }
                } else {
                    // Update the slider's visual representation
                    range.style.right = (minVal / rangeInput[0].max) * 100 + "%";
                    range.style.left = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
                    // Update the displayed prices after changing the range
                    let min = minVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    let max = maxVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');    
                    price[0].textContent = min;
                    price[1].textContent = max;
                }
            });
        });
    }
    // Show Products In 2 Cloumn
    function twoCloumn(){
        let btn=$('.two-cloumn');
        btn.click(function(){
            $('.products-carts div').removeClass('g-col-md-4');
            $('.products-carts div').addClass('g-col-md-6');
            $('.display-sort button').removeClass('select');
            $(this).addClass('select');
        })

    }
     // Show Products In 3 Cloumn
     function threeCloumn(){
        let btn=$('.three-cloumn');
        btn.click(function(){
            $('.products-carts div').removeClass('g-col-md-6');
            $('.products-carts div').addClass('g-col-md-4');
            $('.display-sort button').removeClass('select');
            $(this).addClass('select');
        })

    }
    // When Available Input Checked, Checked Another One
    function checkedAvailableInput(){
        let input=$('.available-btn'),
        input2=$('.available-btn2');
        input.click(function(){
            if($(this).hasClass('.available-btn[checked]')){
                input2.attr('checked',true);
            }else{
                input2.removeAttr('checked');
            }
        });
        input2.click(function(){
            if($(this).hasClass('.available-btn[checked]')){
                input.attr('checked',true);
            }else{
                input.removeAttr('checked');
            }
        })
    }


}

// Product Detail Page
function productDetailPage(){
    productImageSwiper()
    similarProSwiper()
    showMoreSpecifications()
    comment_specific_descrip()
    comment()
    showCmBox()
    sizeSelect()
    ShowQuantities()
    showRedHeart()
    function productImageSwiper(){
        let thumbImg=$('.product-thumbImages-swiper .swiper-wrapper .swiper-slide');
        let thumb=new Swiper('.product-thumbImages-swiper', {
            slidesPerView:'auto',
            direction: 'vertical',
            freeMode: true,
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true,
                dragSize:'auto'
            },
            mousewheel: true,
        });
        let main=new Swiper('.product-main-swiper', {
            slidesPerView: '1',
            freeMode: true,
            thumbs:{
                swiper: thumb
            },
            pagination:{
                el:".swiper-pagination",
                dynamicBullets: true,
                dynamicMainBullets:2,
            },
        });
        thumbImg.each(function(){
            $(this).hover(function() {
              let index=$(this).index();
              main.slideTo(index);
            })
        });
        lightbox.option({
            resizeDuration: 500,
            wrapAround: true,
            showImageNumberLabel:true,
            disableScrolling:true,
            alwaysShowNavOnTouchDevices:true,
        });
    }
    // Select A Size
    function sizeSelect(){
        let btn=$('.size-btn');
        btn.each(function(){
            $(this).click(function(){
                if(!$(this).hasClass('disabled')){
                  btn.removeClass('active');
                  $(this).addClass('active');
                }
            })
        })
    }
    function similarProSwiper(){
        new Swiper('.similar-products-swiper', {
            slidesPerView:'5.2',
            freeMode: true,
            breakpoints:{
                200: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                360: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 10
                },
                420: {
                    slidesPerView: 2.2,
                    spaceBetweenSlides: 10
                },
                640: {
                    slidesPerView: 3,
                    spaceBetweenSlides: 10
                },
                768: {
                    slidesPerView:4,
                    spaceBetweenSlides: 10
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetweenSlides: 10
                },
        
            },
            pagination:{
                el:".swiper-pagination",
                dynamicBullets: true,
                dynamicMainBullets:2,
            },
        });
    }
    // Scroll To Specification
    function showMoreSpecifications(){
        $('.showMore-specifications').on('click',function(event){
            let target=$('.specification-table');
            event.preventDefault();
               $('html, body').stop().animate({
                  scrollTop: target.offset().top
               }, 500);
        });
    }
    // Show Specification Or Description Or Comment
    function  comment_specific_descrip(){
        let tabel= $('.specification-table'),
            commentTitle=$('.comments-title'),
            comment= $('.comments'),
            specificTitle=$('.specifications-title'),
            descripTitle=$('.description-title'),
            descrip=$('.description'),
            ul=$('.second-section-list').children(),
            content=$('.second-section-content').children();

        specificTitle.click(function(){
            ul.removeClass('active');
            content.removeClass('active');
            $(this).addClass('active');
            tabel.addClass('active');
        });
        commentTitle.click(function(){
            ul.removeClass('active');
            content.removeClass('active');
            $(this).addClass('active');
            comment.addClass('active');
        });
        descripTitle.click(function(){
            content.removeClass('active');
            ul.removeClass('active');
            $(this).addClass('active');
            descrip.addClass('active');

        });
    }
    // Stars In Comment
    function comment(){
        $('.star-fill').each(function(){
            $(this).click(function(){
              let index=$(this).index('.star-fill');
              $('.star-fill').eq(index).nextAll().addClass('deactive');
              $('.starSimple').eq(index).nextAll().addClass('active');
            });
        })
        $('.starSimple').each(function(){
            $(this).click(function(){
              let index=$(this).index('.starSimple');              
              $('.starSimple').eq(index).prevAll().removeClass('active');
              $('.star-fill').eq(index).prevAll().removeClass('deactive'); 
              if(index==4){
                $('.starSimple').eq(index).removeClass('active');
                $('.star-fill').eq(4).removeClass('deactive');
              }
            })
        })
    }
    // Show Comment Box
    function showCmBox(){
        let btn=$('.writeComment');
        btn.click(function(){
            $('.comments-form').toggleClass('show');
        })
    }
    // After Clicked Add To Shippcart Btn Show Qauntity In Store And In ShippingCart
    function ShowQuantities(){
         let btnDekstop=$('.add-toCart'),
         btnMobile=$('.add-toCart-mobile');
         btnDekstop.click(function(){
            $('.quantity').addClass('active');
            $('.product-in-shippcart').addClass('active');
         });
         btnMobile.click(function(){
            $('.quantity-mobile').addClass('active');
            $('.product-in-shippcart').addClass('active');
         })
    }
    // When Like Btn Clicked Show Red Red Heart
    function showRedHeart(){
       let btn=$('.like-btn');
       btn.click(function(){
          $(this).children('.heart').toggleClass('d-none');
          $(this).children('.heart-red').toggleClass('d-block');
       })
    }
}
// Order Page
function orderPage(){
    activeTopSvgs()
    continueProcess()
    deleteAllProductsInCart()
    deleteAproduct()
    deliveryBtnNextStep()
    selectDeliveryMethod()
    addressSelect()
    function activeTopSvgs(){
        let shippCart=$('.shipping-cart'),
            sendInfo=$('.send-information'),
            paymentInfo=$('.payment-information'),
            shippCartBtn=$('.shipping-cart-btn'),
            paymentInfoBtn=$('.payment-information-btn'),
            sendInfoBtn=$('.send-information-btn'),
            topCart=$('.top-cart');
        shippCartBtn.click(function(){
            topCart.children('span').removeClass("active");
            topCart.children('button').removeClass("active");
            sendInfo.removeClass('active');
            paymentInfo.removeClass('active');
            shippCartBtn.addClass('active');
            shippCart.addClass('active');
        });
        sendInfoBtn.click(function(){
            paymentInfo.removeClass('active');
            topCart.children('.shipp-info').addClass('active');
            topCart.children('.info-peyment').removeClass('active');
            shippCart.removeClass('active');
            paymentInfoBtn.removeClass('active');
            sendInfoBtn.addClass('active');
            sendInfo.addClass('active');
        })
        paymentInfoBtn.click(function(){
            topCart.children('.info-peyment').addClass('active');
            shippCart.removeClass('active');
            sendInfo.removeClass('active');
            paymentInfoBtn.addClass('active');
            paymentInfo.addClass('active');
        })
    }
    // When Next Step In Shipping Cart Clicked
    function continueProcess(){
       let btn=$('.continue-process-btn'),
          info=$('.send-information'),
          shippCart=$('.shipping-cart'),
          shippCartBtn=$('.shipping-cart-btn'),
          sendInfoBtn=$('.send-information-btn');
        btn.click(function(){
          shippCart.removeClass('active');
          shippCartBtn.removeClass('active');
          info.addClass('active');
          sendInfoBtn.addClass('active');
          $('html, body').stop().animate({
            scrollTop: $('.top-cart').offset().top
         }, 500);
        })
    }
    function deleteAllProductsInCart(){
        let btn=$('.delete-allProducts');
        btn.click(function(){
            let list=$('.product-list'),
                price=$('.price-details');
            list.empty();
            price.empty();
            $('.empty-cart').addClass('active');
        })
    }
    function deleteAproduct(){
        function showEmptyCart(){
            if($('.shippingCart-info').nextAll().text()==''){
                $('.product-list').text('');
                $('.price-details').empty();   
                $('.empty-cart').addClass('active');
            }
        }
        let product=$('.product-item'),
            btnDelete=$('.delete-product');
        btnDelete.each(function(){
            $(this).click(function(){
                $(this).closest(product).css('box-shadow', 'unset');
                $(this).closest(product).empty();
               
                showEmptyCart();
            })
            
        });
    }   
    // When A Delivery Methods Checked Close The Modal
    function selectDeliveryMethod(){
        let input=$('.delivery-method-form ').find('input');
        input.click(function(){
           setTimeout(() => {
            if(input.is(':checked')){
                $('.modal[data-id=delivery-method]').removeClass('active');
                $('.modal-overlay').removeClass('active');
                $('body').removeClass('no-overflow');
                $('.choose-method-btn').addClass('bg-black color-white');
            }
           }, 100);
        })
    }
    // When A address Selected Close The Modal
    function addressSelect(){
       let input=$('.available-address-form .item').find('input');
       input.click(function(){
        setTimeout(() => {
         if(input.is(':checked')){
             $('.modal[data-id=choose-address]').removeClass('active');
             $('.modal-overlay').removeClass('active');
             $('body').removeClass('no-overflow');
             $('.change-edit-btn').addClass('bg-primary-500 color-white');
         }
        }, 100);
     })
    }
    // When Next Step In Sending Informations Clicked
    function deliveryBtnNextStep(){
        let btn=$('.delivery-btn'),
        info=$('.send-information'),
        paymentInfo=$('.payment-information'),
        paymentInfoBtn=$('.payment-information-btn'),
        sendInfoBtn=$('.send-information-btn');
        btn.click(function(){
        if($('.delivery-method-form ').find('input').is(':checked')){
                info.removeClass('active');
                sendInfoBtn.removeClass('active');
                paymentInfo.addClass('active');
                paymentInfoBtn.addClass('active');
        }else{
            btn.attr('data-modal' , 'delivery-method');
            let id = $(this).data('modal');        
            $('.modal[data-id=' + id + ']').addClass('active');
            $('.modal-overlay').addClass('active');
            $('body').addClass('no-overflow');
        }
        $('html, body').stop().animate({
            scrollTop: $('.top-cart').offset().top
         }, 500);
        })
    }
}
function counter(){
    let add= $('.counter .add-btn'),
     remove= $('.counter .remove-btn');
    add.each(function(){
        $(this).click(function(){
            let span=$(this).closest($('.counter')).find('.count').text().trim();
            span=parseInt(span);
            console.log(span);
            $(this).closest($('.counter')).find('.count').text(span +1);
            $(this).closest($('.counter')).find('.remove-btn').attr('disabled',false);
            remove.css('opacity','1');
        })
    });
    remove.each(function(){
        $(this).click(function(){
            let span=parseInt($(this).closest($('.counter')).find('.count').text().trim());
            if(span >1){
               $(this).closest($('.counter')).find('.count').text(span -1);
            }else{
               remove.attr('disabled',true);
               remove.css('opacity','0.5');

            }
        })
    });
}
// Choose Portal In UserPanel & Order Page
function choosePortal(){
    let sepBtn=$('.portal-item-sep'),
        zarinpalBtn=$('.portal-item-zarinpal');
    sepBtn.click(function(){
        zarinpalBtn.removeClass('active');
        sepBtn.toggleClass('active');
    });
    zarinpalBtn.click(function(){
        sepBtn.removeClass('active');
        zarinpalBtn.toggleClass('active');
    });
}
// Login/Regiater Page
function loginRegisterPage(){
    let logRegForm=$('.login-register-form'),
        tokenForm=$('.token-form');
    loginReg();
    editNumber();
    goBack()
    function loginReg(){
        let btn=$('.login-btn'),
            input=$('.number-input');
        btn.click(function(){ 
           if(input.val().length==11){
            logRegForm.removeClass('active');
            tokenForm.addClass('active');
            countdown()
           }
        })
    }
    function editNumber(){
       let btn=$('.edit-btn');
       btn.click(function(){
        tokenForm.removeClass('active');
        logRegForm.addClass('active');
       })
    }
    function goBack(){
        let btn=$('.back-btn');
        btn.click(function(){
            tokenForm.removeClass('active');
            logRegForm.addClass('active');
        })
    }
    function countdown(){
        setInterval( function() {
            var timer = $('.timer').html();
            timer = timer.split(':');
            var minutes = timer[0];
            var seconds = timer[1];
            seconds -= 1;
            if (minutes < 0) return;
            else if (seconds < 0 && minutes != 0) {
                minutes -= 1;
                seconds = 59;
            }
            else if (seconds < 10 && length.seconds != 2) seconds = '0' + seconds;
      
            $('.timer').html(minutes + ':' + seconds);
      
            if (minutes == 0 && seconds == 0){
                $('.timer').addClass('d-none');
                $('.send-token-again').addClass('d-block');
            }
        }, 1000);
    }
}
// User Panel Page
function userPanelPage(){
    activeSections()
    likeButton()
    deleteAddress()
    showOrderHistory()
    backToOrdersInfo()
    rating()
    modalRating()
    proInfoModalCm()
    showOrdersList()
    showCustomerClubList()
    // Show Each Section When In A Right List Button Clicked
    function activeSections(){
        let listsBtn=$('.lists').children(':not(:last-child)');
        listsBtn.each(function(){
            $(this).click(function(){
                listsBtn.removeClass('select');
                $(this).addClass('select');
                let data=$(this).data('btn');
                $('.section').removeClass('active');
                $('[data-id=' + data + ']').addClass('active');
                $('html, body').stop().animate({
                    scrollTop: $('[data-id=' + data + ']').offset().top -100
                }, 700);
            })
            
        });
    }
    // Remove Product When in Heart Clicked
    function likeButton(){
        let btn=$('.heart-btn');
        btn.each(function(){
            $(this).click(function(e){
                e.preventDefault();
                $(this).closest('div').remove();
            });
        })
    }
    function deleteAddress(){
        let btn=$('.delete-address-btn'),
        address=$('.item');
        btn.each(function(){
            $(this).click(function(){
                $(this).closest(address).remove()
            })
        })
    }
    function showOrdersList(){
        let listsBtn=$('.orders-list button');
        listsBtn.each(function(){
            $(this).click(function(){
                listsBtn.removeClass('active');
                $(this).addClass('active');
                let data=$(this).data('btn');
                $('.div').removeClass('active');
                $('[data-id=' + data + ']').addClass('active');
            })
        });
    }
    function showCustomerClubList(){
        let listsBtn=$('.customer-club-list button');
        listsBtn.each(function(){
            $(this).click(function(){
                listsBtn.removeClass('active');
                $(this).addClass('active');
                let data=$(this).data('btn');
                $('.div').removeClass('active');
                $('[data-id=' + data + ']').addClass('active');
            })
        });
    }
    // Order Details
    function showOrderHistory(){
        let btn=$('.order-history-btn');
        btn.each(function(){
            $(this).click(function(){
                $('.section').removeClass('active');
                $('.section.orders-history').addClass('active');
            })
        })
    }
    function backToOrdersInfo(){
        let btn=$('.backTo-orders-info-btn');
            btn.click(function(){
                $('.section').removeClass('active');
                $('.section.orders-info').addClass('active');
            })
    }
    // Rating
    function rating(){
        let star=$('.starSimple');
        star.each(function(){
            $(this).click(function(){
                let index=$(this).index(); 
                let ratingText=`امتیاز ثبت شده: ${index +1}`;
                
                $(this).closest($('.product')).find('.rating-text').text(ratingText);
                $(this).closest($('.score')).removeClass('d-flex');
                $(this).closest($('.score')).addClass('d-none');
                $(this).closest($('.product')).find('.registered-rating').removeClass('d-none');
                $(this).closest($('.product')).find('.registered-rating').addClass('d-flex');   
            })
        })           
    }
    // Rating Modal
    function modalRating(){
           let empty=$('.simple'),
           full=$('.fill');
            empty.each(function(){
                $(this).click(function(){
                  let index=empty.index(this);
                  empty.eq(index).prevAll().addClass('deactive');
                  full.eq(index).prevAll().addClass('active');
                  if($(this).index() == 9){
                    console.log('xxxx');
                    $(this).addClass('deactive');
                    $(this).prev().addClass('active')
                  }
                });
            });
            full.each(function(){
                $(this).click(function(){
                  let index=full.index(this);
                  full.eq(index).nextAll().removeClass('active');
                  empty.eq(index).nextAll().removeClass('deactive');
                });
            })
    }
    // Fill The Product Info IN Modal Comment User Panel
    function proInfoModalCm(){
        let btn=$('.comment-rate-btn');
        btn.each(function(){
           $(this).click(function(){
              let product=$(this).closest($('.product'));
              let size=product.find('.product-size').text(),
              color=product.find('.product-color').text(),
              title=product.find('.product-title').text(),
              img=product.find('.product-img').attr('src');              
              let modal=$('.modal-comment-userPanel');
              modal.find('.product-img').attr('src' , img);
              modal.find('.product-size').text(size);
              modal.find('.product-color').text(color);
              modal.find('.product-title').text(title);
           })
        })
    } 
}
// Seperate Number 3 Digits In Inputs 
$(document).ready(function(){
    $('.priceinput').on('input',function(){
        let val=$(this).val();
        val=val.replace(/,/g,'');
        val=val.replace(/\D/g,'');
        let formattedNumber=val.replace(/\B(?=(\d{3})+(?!\d))/g , ',');
        $(this).val(formattedNumber);  
    });
});
// Edit Address In Order Page Or User Panel Page
function editAddress(){
    let editBtn=$('.edit-address-btn');
    editBtn.each(function(){
        $(this).click(function(){
            let item=$(this).closest($('.item')),
            address=item.find('.item-address-text').text(),
            city=item.find('.item-city').text(),
            number=item.find('.item-number').text(),
            postalCode=item.find('.item-postalCode').text(),
            name=item.find('.item-name').text(),
            lastName=item.find('.item-lastName').text();
            let addressForm=$('.newAddress-from');
            addressForm.find('#city').val(city);
            addressForm.find('#address-input').val(address);
            addressForm.find('#postal-code').val(postalCode);
            addressForm.find('#name').val(name);
            addressForm.find('#last-name').val(lastName);
            addressForm.find('#phone-number').val(number);
            // When RecipieNt Information Changed In Edit Address Modal
            let input=$('#own-recipient');
            input.click(function(){                    
                let addressForm=$('.newAddress-from');
                if($(this).prop('checked')){
                    addressForm.find('#name').val(name);
                    addressForm.find('#last-name').val(lastName);
                    addressForm.find('#phone-number').val(number);
                }else{
                    console.log('false');
                    
                    addressForm.find('#name').val('');
                    addressForm.find('#last-name').val('');
                    addressForm.find('#phone-number').val('');
                }
            })
        })
    })
}
// Post Tracking Page
function postTracking(){
    orderDetail()
    // Show Order Detail
    function orderDetail(){
        let table=$('.orderdetail-table'),
        form=$('.tracking-input input');
        form.click(function(){
            table.addClass('active');
        })
    }
}
// Weblog Detail Page
function weblogDetailPage(){
    showCommentBox()
    function showCommentBox(){
        let btn=$('.weblog-answer-btn');
        btn.click(function(){
            $('.weblog-answerForm').toggleClass('show');
        })
    }
}