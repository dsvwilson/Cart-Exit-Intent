const checkURLPath = window.location.pathname;

if ( checkURLPath == '/cart/' && connectJSFile.getProductName !== null ) {

    document.addEventListener( "DOMContentLoaded", (event) => {
        document.getElementById( 'awf_field-116943292' ).value = connectJSFile.getProductName;

        document.getElementById( 'awf_field-116943293' ).value = connectJSFile.getProductPrice;

        document.getElementById( 'awf_field-116943294' ).value = window.location.origin + '/checkout/?add-to-cart=' + connectJSFile.getProductID;

        document.getElementById( 'awf_field-116943295' ).value = connectJSFile.getCartTotal;

     });

}