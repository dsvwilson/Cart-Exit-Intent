## Cart Exit Intent

This plugin access cart data and passess it to a campaign. The campaign is designed
to target customers who may exit the website while they have a product in cart and
while they are on the Checkout page.

## Features

1. Fetches information about the first product in a user's cart. This information include name, price, cart link (for direct checkout), sign up fee (for subscription products with a sign up fee), and cart total.

2. Builds a cart link - eg https://example.com/checkout/?add-to-cart=000/ from the fetched data to send to the potential customer so they can access the product again directly.

3. Clears the cart of whatever product(s) it has when the built cart link is used to add a product to cart. This protects users from accidentaly buying more than one quantity of the product they intend to buy.

4. Passes the fetched data to a campaign form on the frontend.

5. Creates a diplay logic to conditionally show a user campaign text depending on the kind of product they have in their cart - if it has a sign up fee or not.

## Usage

Activating the plugin alone will not do the trick. A few things are required:

<br>
1. When the AWeber form is being created, ensure there are 5 custom fields:
<br><br>

        a. Product Name

        b. Product Price
        
        c. Cart Link
        
        d. Sign Up Fee
        
        e. Cart Total
<br>
There is currently no logic to handle situations where any or multiple of these fields are
not available.

<br>
2. Installation of the form is by HTML code and optionally CSS included. In the code, 
the AWeber form will have a class above each of the custom label like so:
<br>
<br>

```html
<div class="af-element">
<label class="previewLabel" for="awf_field-116958147">Product Name:</label>
```

A few things would need to happen for the plugin to find the relevant custom fields:

a. Add "hide-label" to the class alongside "af-element" as shown below. <br>

```html
<div class="af-element hide-label">
```
<br>

b. The "awf_field-116958147" portion shown below will be unique for each custom field. They will need to be copied and updated within the code to target them correctly. 

```html
<label class="previewLabel" for="awf_field-116958147">Product Name:</label>
```
<br>
3. The campaign text is defined within the plugin, so there will be no need to write it on the campaign builder itself. But a placeholder needs to be created on the campaign and given an ID so that the plugin knows where to send the campaign text to.

<br>

The ID to be given to the place holder is "campaign". 

<br>

This is how to define a place holder. Create a HTML element in the place of where the text should be in the campaign and add this:

<br>

```html
<div id="campaign"></div>
```

An empty "div" with the ID "campaign" provides the avenue. The plugin handles the rest including paragraph formatting.

## ShortComings

The scopt of this project makes for the output and handling of just the first product in the cart. The website it will be used for is suited for this as customers will only be buying one product. So if for a reason the customer buys more than one product, only the first product will be processed.

The plugin is not made for use on just any site. It is made with just a target site in mind that is built in a certain way.