<?php
session_start();
require 'method.php';

if (!isset($_SESSION['poker'])) {
    $_SESSION['poker'] = array();
    $amount = 0;
    $_SESSION['poker']['max-win']=0;
} else {
    //get the how many times that the player buying chips
    $_SESSION['poker']['buying_chips'] += $_GET['buying'];
    //get the amount of the player cache
    if (isset($_GET['amount'])) {
        $_SESSION['poker']['amount'] = $_GET['amount'];
        $amount = $_SESSION['poker']['amount'];
    } else {
        $amount = $_SESSION['poker']['amount'];
    }
    //display history 

}

//get the amount of bet (chip) that player bet 
if (isset($_GET['chip'])) {
    $_SESSION['poker']['chip'] = $_GET['chip'];
}
//show/hide the bet chips 
if (isset($_GET['bool'])) {
    if ($_GET['bool'] == 'false') {
        $display_chips_on_screen = 'none';
        $display_button_cash_on_screen = 'none';
        $display_button_on_screen = 'block';
    } else {
        $display_button_cash_on_screen = 'block';
        $display_button_on_screen = 'none';
        $display_chips_on_screen = 'block';
    }
}
//if player bet then start the game
if (isset($_GET['start_game'])) {
    if ($_GET['start_game'] == 'y') {
        $btn_draw='block';
        $e = new Card_deck();
        $e->start_the_game();
        $e->shuffle_the_deck();
        $e->new_game();
    }
    if (isset($_GET['click_button']) && ($_GET['click_button'] == 'Show down')) {
        $_SESSION['poker']['check_win'] = Card_deck::check_for_win($_SESSION['card_hand']['first_card'], $_SESSION['card_hand']['second_card'], $_SESSION['card_hand']['third_card'], $_SESSION['card_hand']['fourth_card'], $_SESSION['card_hand']['fifth_card']);
    }
}

if (isset($_GET['card_selected'])) {
    if (($_SESSION['card_hand']['changed']) != 'y') {
        if ($_GET['card_selected'] != 'null') {
            Card_deck::draw_cards($_GET['card_selected']);
           // if the player change card
            $_SESSION['card_hand']['changed'] = 'y';
            $btn_draw='none';
            $_SESSION['poker']['check_win'] = Card_deck::check_for_win($_SESSION['card_hand']['first_card'], $_SESSION['card_hand']['second_card'], $_SESSION['card_hand']['third_card'], $_SESSION['card_hand']['fourth_card'], $_SESSION['card_hand']['fifth_card']);
        }
    }
}
if (isset($_GET['game_finish'])) {
    if ($_GET['game_finish'] == 'true') {

        $_SESSION['poker']['cash_win'] = (Card_deck::win_chips($_SESSION['poker']['check_win']) *  $_SESSION['poker']['chip']);
        $amount = $_GET['amount'] + (Card_deck::win_chips($_SESSION['poker']['check_win']) *  $_SESSION['poker']['chip']);
        $his = "You got: {$_SESSION['poker']['check_win']} -> Bet: {$_SESSION['poker']['chip']} -> Win: {$_SESSION['poker']['cash_win']}";
        $_SESSION['history'][] = $his;
        $_SESSION['poker']['max-win']=max($_SESSION['poker']['max-win'],$_SESSION['poker']['cash_win']);
        $_SESSION['card_hand']['changed'] = 'n';
        unset($_SESSION['poker']['chip']);
        $btn_draw='none';
    }
}
if (isset($_SESSION['history'])) {
    $hand_history = $_SESSION['history'];
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>5 Card Poker</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <!-- font awesome library -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="script.js"></script>
</head>

<body>
    <section class="game-body-cover">
        <!-- //information button on the top right -->
        <div class="info">
            <a href="#" id="myBtn"><i class="fas fa-info-circle" style="color: blue; font-size: 4rem "></i></a>
        </div>
        <!-- deck of card on table -->
        <div class="deck_of_card">
            <img src="images/deck_of_card.png" alt="deck_of_card">
        </div>

        <div class="chip_on_table">
            <?php if (isset($_SESSION['poker']['chip'])) {
                $print = <<<msg
            <img src="images/{$_GET['chip']}.png" alt=".">
msg;
                echo $print;
            } elseif (isset($_GET['game_finish'])) {
                if ($_GET['game_finish'] == 'true') {
                    echo "<p class='white_color_20'> {$_SESSION['poker']['check_win']}. <br/>You win: {$_SESSION['poker']['cash_win']}</p>";
                }
            }
            ?>
        </div>

        <!-- Button Fast Cash . on click add 100 chip on total amount of player chip -->
        <div id="frm-fast-cash" style="display: <?php echo $display_button_cash_on_screen; ?>">
            <div class="submit">
                <a class="fast-cash">Fast Cash</a>
            </div>
        </div>
        <!-- display the amount of the player -->
        <p class="player-amount"><i class="fas fa-euro-sign"></i><span id="amount"><?php echo $amount; ?></span></p>

        <!-- close the game and print the result on a record on homepage -->
        <form action="display_results.php" method="GET" id="frm-cash-out">
            <div class="submit">
                <input type="submit" value="Cash out" name="cash_out" class="cash-out">
            </div>
        </form>


        <div class="game-inner">
            <!-- here is draw casino table -->
            <div class="poker-table"></div>
            <!-- here is all casino chips from 5 to 100  -->
            <div class="poker-chips-box" style="display: <?php echo $display_chips_on_screen; ?>">
                <!-- casino chip rate of 100$ -->
                <a class="select-chip" data-value="100"> <img class="chip" src="images/100.png" alt="chip 100"></a>
                <!-- casino chip rate of 50$ -->
                <a class="select-chip" data-value="50"><img class="chip" src="images/50.png" alt="chips 50"></a>
                <!-- casino chip rate of 20$ -->
                <a class="select-chip" data-value="20"><img class="chip" src="images/20.png" alt="chip 20"></a>
                <!-- casino chip rate of 10$ -->
                <a class="select-chip" data-value="10"><img class="chip" src="images/10.png" alt="chip 10"></a>
                <!-- casino chip rate of 5$ -->
                <a class="select-chip" data-value="5"><img class="chip" src="images/5.png" alt="chip 5"></a>
            </div>

            <!-- button of deal and showdown -->
            <form action="display_game.php" method="GET" class="frm-deal" style="display: <?php echo $display_button_on_screen; ?>">
                <div class="form-cotainer-submit">
                    <div class="submit">
                        <a class="showdown"><?php if (isset($_GET['click_button'])) echo  $_GET['click_button']; ?></a>
                    </div>
                    <div style="display: <?php echo $btn_draw ?>" class="submit">
                        <a class="deal" >Draw</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="history">
            <h3>Game history</h3>
            <p class="game-hand" style="padding: 25px">
                <?php
                foreach ($_SESSION['history'] as $e) {
                    echo $e . '<br>';
                }
                ?>
            </p>
        </div>



        <!-- The Modal -->
        <div id="myModal" class="modal">

            <!-- Modal content -->
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h2>HAND RANKINGS</h2>
                </div>
                <div class="modal-body">
                    <p>
                        <table style="font-size: 2rem">
                            <tr>
                                <td>Royal Flush</td>
                                <td><span>10 <i class='fa fa-heart color red'></i></span>
                                    <span>J <i class='fa fa-heart color red'></i></span>
                                    <span>Q <i class='fa fa-heart color red'></i></span>
                                    <span>K <i class='fa fa-heart color red'></i></span>
                                    <span>A <i class='fa fa-heart color red'></i></span>
                                </td>
                                <td><span class="red-dark">250/1</span></td>
                            </tr>
                            <tr>
                                <td>Straight Flush</td>
                                <td><span>A <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>2 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>3 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>4 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>5 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                </td>
                                <td><span class="red-dark">100/1</span></td>
                            </tr>
                            <tr>
                                <td>Four of a King</td>
                                <td><span>K <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>K <i class='fa fa-heart color red'></i></span>
                                    <span>K <img src="https://img.icons8.com/metro/32/000000/spades.png" /></span>
                                    <span>K <img src="https://img.icons8.com/color/32/000000/kite-shape.png" /></span>

                                </td>
                                <td><span class="red-dark">50/1</span></td>
                            </tr>
                            <tr style="width: 100%">
                                <td>Full House</td>

                                <td><span>A <i class='fa fa-heart color red'></i></span>
                                    <span>A <i class='fa fa-heart color red'></i></span>
                                    <span>K <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>K <i class='fa fa-heart color red'></i></span>
                                    <span>K <img src="https://img.icons8.com/metro/32/000000/spades.png" /></span>


                                </td>
                                <td><span class="red-dark">25/1</span></td>
                            </tr>
                            <tr>
                                <td>Flush</td>
                                <td><span>A <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>K <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>10 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>9 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>7 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                </td>
                                <td><span class="red-dark">15/1</span></td>
                            </tr>
                            <tr style="width: 100%">
                                <td>Straight</td>
                                <td><span>7 <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>8 <img src="https://img.icons8.com/ios-filled/32/000000/spades.png" /></span>
                                    <span>9 <img src="https://img.icons8.com/ios-filled/32/000000/spades.png" /></span>
                                    <span>10 <i class='fa fa-heart color red'></i></span>
                                    <span>J <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                </td>
                                <td><span class="red-dark">9/1</span></td>
                            </tr>
                            <tr>
                                <td>Three of a King</td>
                                <td><span>K <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>K <i class='fa fa-heart color red'></i></span>
                                    <span>K <img src="https://img.icons8.com/metro/32/000000/spades.png" /></span>
                                </td>
                                <td><span class="red-dark">5/1</span></td>
                            </tr>
                            <tr>
                                <td>2 Pairs</td>
                                <td><span>K <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>K <img src="https://img.icons8.com/metro/32/000000/spades.png" /></span>
                                    <span>Q <i class='fa fa-heart color red'></i></span>
                                    <span>Q <img src="https://img.icons8.com/metro/32/000000/spades.png" /></span>
                                </td>
                                <td><span class="red-dark">2/1</span></td>
                            </tr>
                            <tr>
                                <td>1 Pair</td>
                                <td><span>K <img src="https://img.icons8.com/ios-filled/32/000000/clubs.png" /></span>
                                    <span>K <img src="https://img.icons8.com/metro/32/000000/spades.png" /></span>
                                </td>
                                <td><span class="red-dark">1/1</span></td>
                            </tr>
                        </table>

                    </p>
                </div>
                <div class="modal-footer">
                    <h3>Asso Trantana Casino</h3>
                </div>
            </div>
            <script>
                // Get the modal
                var modal = document.getElementById("myModal");

                // Get the button that opens the modal
                var btn = document.getElementById("myBtn");

                // Get the <span> element that closes the modal
                var span = document.getElementsByClassName("close")[0];

                // When the user clicks the button, open the modal 
                btn.onclick = function() {
                    modal.style.display = "block";
                }
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() {
                    modal.style.display = "none";
                }
                // When the user clicks anywhere outside of the modal, close it
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            </script>
        </div>
        <!-- show the table card only the guest atart thr game -->
        <?php if ($_GET['bool'] == "false") : ?>


            <div class="game-start-shown-card">
                <div class="hover panel">
                    <div class="front">
                        <div class="pad">
                            <img src="images/faces/back.png" alt="logo back" />
                        </div>
                    </div>
                    <div class="back">
                        <div class="pad">
                            <img data-card-position="first_card" src="images/faces/<?php echo $_SESSION['card_hand']['first_card']; ?>.png" alt="logo front" />
                        </div>
                    </div>
                </div>

                <div class="hover panel">
                    <div class="front">
                        <div class="pad">
                            <img src="images/faces/back.png" alt="logo back" />
                        </div>
                    </div>
                    <div class="back">
                        <div class="pad">

                            <img data-card-position="second_card" src="images/faces/<?php echo  $_SESSION['card_hand']['second_card']; ?>.png" alt="logo front" />
                        </div>
                    </div>
                </div>

                <div class="hover panel">
                    <div class="front">
                        <div class="pad">
                            <img src="images/faces/back.png" alt="logo back" />
                        </div>
                    </div>
                    <div class="back">
                        <div class="pad">
                            <img data-card-position="third_card" src="images/faces/<?php echo  $_SESSION['card_hand']['third_card']; ?>.png" alt="logo front" />
                        </div>
                    </div>
                </div>

                <div class="hover panel">
                    <div class="front">
                        <div class="pad">
                            <img src="images/faces/back.png" alt="logo back" />
                        </div>
                    </div>
                    <div class="back">
                        <div class="pad">
                            <img data-card-position="fourth_card" src="images/faces/<?php echo  $_SESSION['card_hand']['fourth_card']; ?>.png" alt="logo front" />
                        </div>
                    </div>
                </div>

                <div class="hover panel">
                    <div class="front">
                        <div class="pad">
                            <img src="images/faces/back.png" alt="logo back" />
                        </div>
                    </div>
                    <div class="back">
                        <div class="pad">

                            <img data-card-position="fifth_card" src="images/faces/<?php echo  $_SESSION['card_hand']['fifth_card']; ?>.png" alt="logo front" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- <?php endif; ?> -->
    </section>
</body>

</html>