# fundraising-event
Thermometer and Name Ticker for live fundraising event, using PayPal API


This is specific to a live event, where a fundraising thermometer is projected on the screen, along with names of donors who have opted in.

WHAT IT DOES:

1) Gets a balance from one or two PayPal accounts and displays a fundraising thermometer

2) Gets names of donors who have opted in and shows them on a scrolling list

SCREENSHOT ('wow' graphic appears above thermometer when goal is reached):

![screenshot of final product](http://www.jrrb.com/cdm/screenshot.png)

FILES:


1) index.html --app home

2) input.html --add $ manually, if someone doesn't use PayPal. Also good for testing thermometer


3) autoload.php --needed for angeleye's PayPal PHP Library

4) config.php --initialize variables

5) getBalances.php --gets balances from (1 or 2) paypal accounts, and a manual balances added from input.html and passes result to fundraising-thermometer.js

6) transactions.php --checks recent PayPal transactions using web form or PayPal Here, checks whether the donor has opted-in, and writes list of names to a text file

7) paypal.php and files in src folder -- as-is from angeleye's PayPal PHP Library

8) input.php  --works with input.html to add value manually to the balance


9) fundaising-thermometer.js -- jQuery renders the thermometer, then shows a "wow" graphic when over goal

10) update-names.js --jQuery runs transactions.php for each PayPal account every 20 seconds

11) scroll-names.js --jQuery that adds a graphic to each name, and scrolls it. Requires JQuery Advanced News Ticker from risq


12) addvalue.txt --input.php writes added value to this file

13) ticker.txt --transactions.php writes list of names to this file, and scroll-names reads it and renders on screen


14) 1.png through 4.png -- graphics that are added to each name when it scrolls, randomly assigned graphic and side of name

15) wow.png --shows when goal is met via fundraising-thermometer.js

16) therm1.png -- thermometer mask

17) therm2.png -- corresponding thermometer "inside" image that is revealed as the balance increases


18) styles.css --stylesheet


SETTING UP PAYPAL

Use a classic PayPal form: https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/

The only requirement for the "opt-in" check box (opts-in to putting name on the screen at the event) is 

```<input type="checkbox" id="os0" name="os0" value="Yes">
<input type="hidden" name="on0" value="Opt In">```

For PayPal Here (the PayPal credit card swipe device for iOS or Android app), individual giving levels (in "items") have to be set up with an opt-in of "No" and "Yes" (in "item options"). If a keypad amount is donated (not an "item"), there is no opt-in and the name won't be put on the screen.
