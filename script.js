var flip = (function () {
    $('.hover').addClass('flip');
});


$(document).ready(function () {
    var count_buying_chips = 0;
    var value = 0;
    var card_selected = [];
    //on every click Add 100 chips to amount
    $("#frm-fast-cash").click(function () {
        var amount = 100 + parseInt($("#amount").text());
        count_buying_chips++;
        $({
            countNum: $('#amount').html() //starting point of existing cache
        }).animate({
            countNum: amount //ending
        }, {
            duration: 400,
            easing: 'swing',
            step: function () {
                $('#amount').html(Math.ceil(this.countNum));
            },
            complete: function () {
                $('#amount').html(amount);
                //alert('finished');
            }
        });
        //saveAsNewName(amount)
    });
    //remove the chip amount from the total amount
    $(".select-chip").click(function () {
        value = $(this).attr("data-value");
        var amount = parseInt($("#amount").text()) - value;


        if (amount < 0) {
            alert('Not enough Chips! Please Buy more chip for this Bet!!')
        } else {

            $({
                countNum: $('#amount').html() //starting point of existing cache
            }).animate({
                countNum: amount //ending
            }, {
                duration: 400,
                easing: 'swing',
                step: function () {
                    $('#amount').html(Math.floor(this.countNum));
                },
                complete: function () {
                    $('#amount').html(amount);
                    var hrefAttr = "display_game.php?chip=" + value + '&buying=' + count_buying_chips + "&bool=false" + "&start_game=y" + "&amount=" + $('#amount').text() + "&click_button=" + $(".showdown").text("Show down").text();
                    window.location = hrefAttr;
                    //alert('finished');
                }
            });
        }
    });
    $('.showdown').click(function () {
        count_flip = 0;

        if ($('.showdown').text() == "Show down") {
            var hrefAttr = "display_game.php?&buying=" + count_buying_chips + "&bool=false" + '&game_finish=true' + "&start_game=n" + "&amount=" + $('#amount').text() + "&click_button=" + $(".showdown").text("New game").text();
            window.location = hrefAttr;
        } else {
            var hrefAttr = "display_game.php?&buying=" + count_buying_chips + "&bool=true" + '&game_finish=false' + "&amount=" + $('#amount').text() + "&click_button=" + $(".showdown").text("Show down").text();
            window.location = hrefAttr;

        }
    });

//select player cards to discard 
    $(".back .pad img").click(function () {
        if (!$(this).hasClass('selected_card')) {
            card_selected.push($(this).attr('data-card-position'));
            $(this).toggleClass('selected_card');
        } else {
            // card_selected = $.grep(card_selected, function (value) {
            //     return value != $(this).attr('data-card-position');
            // });
            $(this).toggleClass('selected_card');
            card_selected = [];
            $(".back .pad img").each(function (i) {
                if ($(this).hasClass('selected_card')) {
                    card_selected.push($(this).attr('data-card-position'));
                }
            });
        }

    });
//player discart cards with new one
    $('.deal').click(function () {
        if (card_selected != null) {
            var hrefAttr = "display_game.php?&card_selected=" + card_selected +
                '&buying=' + count_buying_chips + "&bool=false" +
                "&amount=" + $('#amount').text() + "&click_button=" + $(".showdown").text("New game").text()+ '&game_finish=true';
            window.location = hrefAttr;

        }

    });
    setInterval(() => {
        flip();
    }, 800);
});