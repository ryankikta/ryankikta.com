;( function () {
    
    var pageSettings = {
        desktop         : false,
    }
    
    var expApp = {
        
        settings : pageSettings,

        isDesktop: function () {
            return this.settings.desktop
        },
        mobileNav: function(){
            // mobile nav trigger
			var mobileNav = $('.mobile-nav');
			// mobile nav open
			$('.navbar-toggler').on('click', function(){
				mobileNav.show();
				mobileNav.animate({right: 0},250);
				return false;
			});
			// mobile nav close
			$('.mobile-nav a.mobile-close').on('click', function(){
				mobileNav.animate({right: '-100%'},250);
				return false;
			});

            $('.mobile-nav .menu > li a').on('click', function(){
                $(this).next().slideToggle();
                return false;
            })
        },
        sidebarDropdown: function(){
            $('.sidebar_menu_link_dropdown').on('click', function(){
                var $this = $(this)

                if (!$this.hasClass('active')) {
                    $('.sidebar_menu_link_dropdown').removeClass('active');
                    $('.sidebar_submenu').slideUp();
                    //setTimeout(function(){
                        $this.addClass('active');
                        $this.next().slideDown();
                    //}, 300)
                } else {
                    $this.removeClass('active');
                    $this.next().slideUp();
                }

                return false;
            })
        },
        sidebarMobile: function(){
            $('.sidebar_name').on('click', function(){
                var $this = $(this)
                if (!expApp.isDesktop()) {
                    $this.next().slideToggle()
                }

                return false;
            })
        },
        mobileFooter: function(){
            $('.footer_mobile_trigger').on('click', function(){
                var $this = $(this)
                if ($(window).innerWidth() < 576) {
                    $this.next().slideToggle();
                }
                return false;
            })
        },
        /*testimonialSlider: function(){
            $('.home_testimonials_slider').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
            });
        },
	*/
        membershipTable: function(){
        	$('.membership_table_icons_button').on('click', function(){
				var mobileShow = $(this).data('td');
				if (!expApp.isDesktop()) {
					$('.membership_table td:not(:first-child), .membership_table th:not(:first-child)').hide()
					$('.membership_table td:nth-child(' + mobileShow + '), .membership_table th:nth-child(' + mobileShow + ')').show()

                    $('.membership_table_desc_item').hide()
                    $('.membership_table_desc_item:nth-child(' + parseInt(mobileShow - 1) + ')').show()
				}
			})
        },
        iconTabs: function(){
            $('.tab_trigger').on('click', function(){
                $this = $(this);
                let tabIndex = $this.index()
                if (!$this.hasClass('active')) {
                    $('.tab_trigger').removeClass('active');
                    $('.tab_content').hide();

                    $this.addClass('active');
                    $('.tab_content').eq(tabIndex).show();
                }
            }) 
        },
        importOrderHandling: function(){
            $('#importOrderStepTwo').hide();
            $('#importOrderContinue').click(function(){
                $('#importOrderStepOne').hide();
                $('#importOrderStepTwo').show();
            });
        },
        payments: function(){
            $('body').on('keyup', '.dashboard_payments #amount', function () {
                inputControl($(this), 'float');
            });
            $('body').on('keyup', '.dashboard_payments #amount_p', function () {
                inputControl($(this), 'float');
            });

            function inputControl(input, format) {
                var value = input.val();
                var values = value.split("");
                var update = "";
                var transition = "";
                if (format == 'int') {
                    expression = /^([0-9])$/;
                    finalExpression = /^([1-9][0-9]*)$/;
                } else if (format == 'float') {
                    var expression = /(^\d+$)|(^\d+\.\d+$)|[,\.]/;
                    var finalExpression = /^([1-9][0-9]*[,\.]?\d{0,2})$/;
                }
                for (id in values) {
                    if (expression.test(values[id]) == true && values[id] != '') {
                        transition += '' + values[id].replace(',', '.');
                        if (finalExpression.test(transition) == true) {
                            update += '' + values[id].replace(',', '.');
                        }
                    }
                }
                input.val(update);
            }

            $('.dashboard_payments #form').submit(function () {
                var amount = parseInt($(".dashboard_payments #amount").val());
            });

            $('.dashboard_payments #amount').keyup(function () {

                var amount = parseFloat($(".dashboard_payments #amount").val());
                if (isNaN(amount)) {
                    var amount = 0;
                }
                
                //var currentbalance = parseFloat("<?php echo $balance; ?>", 0.01);
                var currentbalance = parseFloat($('.dashboard_payments #current_balance').text(), 0.01);
                var newbalance = amount + currentbalance;

                newbalance = Math.round(newbalance * 100) / 100;
                newbalance = newbalance.toFixed(2);

                amount = Math.round(amount * 100) / 100;
                amount = amount.toFixed(2);

                jQuery('.dashboard_payments .amounttosend').html(amount);
                jQuery('.dashboard_payments .balanceafter').html(newbalance);
            });
            
            $('.dashboard_payments #amount_p').keyup(function () {

                var amount = parseFloat(jQuery(".dashboard_payments #amount_p").val());
                if (isNaN(amount)) {
                    var amount = 0;
                }
                var currentbalance = parseFloat($('.dashboard_payments #current_balance').text(), 0.01);
                var newbalance = amount + currentbalance;

                newbalance = Math.round(newbalance * 100) / 100;
                newbalance = newbalance.toFixed(2);

                amount = Math.round(amount * 100) / 100;
                amount = amount.toFixed(2);

                $('.dashboard_payments .amounttosend').html(amount);
                $('.dashboard_payments #amount').val(amount);
                $('.dashboard_payments .balanceafter').html(newbalance);
            });

            $('body').on('click', '.dashboard_payments #cvc_help_link', function () {
                if ($(".dashboard_payments #cvc_help").is(":visible"))
                    $(".dashboard_payments #cvc_help").hide("slow");
                else
                    $(".dashboard_payments #cvc_help").show("slow");
            });
        },

        iconTabsBlock: function (){
            $('.icon-tabs-target').on('click', function() {
                let dataAttribute = $(this).attr('data-icon-tab-target');
                let target = '#' + String(dataAttribute);
                $('.icon-tabs-content').removeClass('icon-tabs-active');
                $('.icon-tabs-target').removeClass('icon-tabs-active')
                $(target).addClass('icon-tabs-active');
                $(this).addClass('icon-tabs-active');
            })
        },

        accordions: function (){
            $('.accordion-target').on('click', function() {
                if ( $(this).hasClass('accordion-active') ) {
                    $(this).removeClass('accordion-active');
                } else {
                    $('accordion-target').removeClass('accordion-active');
                    $(this).addClass('accordion-active');
                }
            })
        },

        screenResizeLive: function (){
            var resizeTimer;
            $(window).on('resize', function(){
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    // functions here on resize

                }, 250);
            })
        },
        screenResizeCheck: function (){
            if ($(window).innerWidth() > 991) expApp.settings.desktop = true;

            $(window).on('resize', function(){  
                if($(window).innerWidth() <= 991 && expApp.settings.desktop == true) {
                    //  Mobile 
                    $('.exp').removeClass('desktop')
                    expApp.settings.desktop = false;
                    // add functions below

                } else if ($(window).innerWidth() > 991 && expApp.settings.desktop == false) {
                    //  Desktop
                   $('.exp').addClass('desktop')
                   expApp.settings.desktop = true;
                   // add functions below

                }
            })
        },
        init: function () {
            //ON LOAD
            // Desktop and mobile
            this.screenResizeCheck()
            this.screenResizeLive()
            this.mobileNav()
            this.mobileFooter()
            this.sidebarDropdown()
            this.sidebarMobile()

            //this.testimonialSlider()
            this.membershipTable()
            this.iconTabs()
            this.importOrderHandling()

            // dashboard, payments page
            this.payments()

            if ( this.isDesktop() ) { 
                // Desktop only 

            } else {
                // Mobile Only

            }

            if ($('.icon-tabs').length > 0) {
                this.iconTabsBlock();
            }

            if ($('.accordions').length > 0) {
                this.accordions();
            }

            return this;
        }
    }
    window.expApp = expApp
})();

$(window).on('load', function(){        
    
    expApp.init()

}) /* window load */  

