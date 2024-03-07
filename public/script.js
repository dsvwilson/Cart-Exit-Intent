/*  
    Fetching the JavaScript Objects from 'cart-exit-intent.php' which holds cart data and 
    assigning them to JavaScript variables for easier use.

    'cartLink' uses concatenation to build a cart link by appending the product ID to the 
    '/checkout/?add-to-cart=' string and then to the website's URL which is fetched using
    'window.location.origin'.
*/

const productName = connectJSFile.getProductName;
const productPrice = connectJSFile.getProductPrice;
const cartLink = window.location.origin + '/checkout/?add-to-cart=' + connectJSFile.getProductID;
let signUpFee = connectJSFile.getSignUpFee; // using 'let' as 'signUpFee' will need to be reassigned
const cartTotal = connectJSFile.getCartTotal;



/* 
    The 'signUpFee' originally returns the sign up fee as a string. This block of code
    converts it to a number and re-appends the currency symbol as well as decimal places 
    dynamically.

    Appending the currency symbol makes it impossible to use the data
    as a number which will later on be useful in the code. So the point
    at which the data is a pure number is 'strToNumSignUpFee' and will be
    noted and used later in a conditional statement. 
*/

let nonNumSignUpFee = signUpFee.replace( /[^0-9.]/g, '' ); 
let strToNumSignUpFee = parseFloat( nonNumSignUpFee ); // pure number at this point
signUpFee = signUpFee[ 0 ] + strToNumSignUpFee.toFixed( 2 );



/*
    Checks if the user is on the Checkout page and inserts cart data into the
    AWeber field.
*/

const checkURLPath = window.location.pathname; // holds the URL path

// checks if the URL path is 'checkout' which means the user is on the Checkout
// page and also checks if the cart has a product in it. Should both conditions
// be true, the data is inputed into the AWeber form.

if ( checkURLPath === '/checkout/' && productName !== null ) { 

    document.addEventListener( "DOMContentLoaded", () => { // ensures the DOM is fully loaded
        
        document.getElementById( 'awf_field-116958147' ).value = productName; // inputs product name

        document.getElementById( 'awf_field-116958148' ).value = productPrice; // inputs product price

        document.getElementById( 'awf_field-116958149' ).value = cartLink; // inputs cart link

        // not all products have a sign up fee. And this is where our sign up fee number
        // is needed. If the sign up fee is present, the sign up fee will be inputed. If not, it inputs 
        // a report - 'No sign up fee'.

        if ( ( strToNumSignUpFee > 0 ) ) {
            document.getElementById( 'awf_field-116958150' ).value = signUpFee;
        } else {
            document.getElementById( 'awf_field-116958150' ).value = 'No sign up fee';
        }

        document.getElementById( 'awf_field-116958151' ).value = cartTotal; // inputs cart total

     });

}



/*
    'condSignUpFee()' is a function used to dynamically include text if the product has a sign up fee.
    This helps us to be flexible in the campaign output. If there is no sign up fee, it will include a 
    defined sign up fee text to the text that will be sent to the campaign. If there is no sign up fee,
    then nothing is returned. 
*/

function condSignUpFee() {

    if ( strToNumSignUpFee > 0 ) {

        const signUpFeeText = ' with a sign up fee of ' + signUpFee; // appendable sign up fee text

        return signUpFeeText;

    } else {

        return '';

    }
    
}



/*
    Includes the logic of appending a sign up fee text and the logic of the text that will be passed on 
    to the campaign for display on the frontend to the user.
*/

// calls the appendable sign up fee text function and saves it to a variable.

const signUpFeeData = condSignUpFee();

// builds the text that will be shown to the user in the campaign and inputs it to the target field.

document.addEventListener( 'DOMContentLoaded', () => { // ensures the DOM is fully loaded

    // inputs the text to the campaign on the frontend

    document.getElementById( 'test-target' ).innerHTML = '<p>You opted to buy ' + connectJSFile.getProductName + 
    ' for ' + connectJSFile.getProductPrice + signUpFeeData + ' and will be spending a total of ' + 
    connectJSFile.getCartTotal + ' today. We are excited to see you get started.</p>' + 
    '<p>Have any questions about what your purchase might mean to you? Let us know!</p>';
    
});
