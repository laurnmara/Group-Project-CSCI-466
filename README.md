# Group-Project-CSCI-466

Hi everyone! This is the official workspace for our group project. There are a couple main components to the PHP/PDO portion of the application. They include the following. Please make whatever changes required and this is just a rough idea of what we need to do based on the requirements. Feel free to clone the repository from this GitHub page so you can get all the files. Anastasia and Lauren will work on the PHP/PDO but we might need more people since there is a lot of things to do. 

* Important note: Try to work on ONLY the files you're set to work on. We don't want a lot of people messing with the same code. If we stick to working on our specified files, all you need to do is use the command `git pull` and `git push` so we can get and send updates files on here. 

# Components to Create and Reuse
* Nav bar - navigation bar that includes links to Products page (1), Cart (4), User Orders (5)
* Footer bar - navigation bar on bottom of page for OWNER Links. Links to pages: Owner Outstanding Order Tracker (7), Product Inventory (6), Order Fufillment page (8)
* Product display - Shows image, product name, price, and other buttons (like description/more details button, and add to cart button). We can reuse this on Home page (1), and several others

# User Pages
1. Home / Product Listing page - 
   This page will show all the products currently IN STOCK (not the products that have <1 in quantity). This will be the first page that the user is directed to.
     * *EX: (SELECT * FROM Product WHERE QTY >= 1)*

3. Product Detail page - 
   This page will allows users to see the product details / descriptions / prices/ and all info on the product
     * *EX: (SELECT * FROM Product where productid= ...)* 

4. Cart - 
   This page will show all the items in CartProduct, then at the bottom will have a checkout form that makes users enter their shipping address and billing address, then calculates the total price
   There will also be a submit button that allows the user to finalize the order taking them to the Order Placed page
     * *EX: (SELECT * FROM CartProduct). Display results in table or product component.*
       *SUM all product costs (based on individual price AND QTY: price * qty), then display on bottom of cart.*

5. Order Placed page - 
   This shows the user the order number and all the items in the current order they just placed. Allow the user to come back to this page on the Nav bar
   * *EX: (SELECT * FROM Order where ordernum=...)*
     *Also look up associated products in order (JOIN)*

# Owner Pages

6. Product Inventory page - 
   Will show the owner ALL the products regardless in they are in stock or not.
   * *EX: (SELECT * from Product). Show results in table or product component (working on that - lauren)*

(7 & 8 can be combined on one page -> the user can then be redirected to each individual order's detail pg)
7. Outstanding Orders Tracker page - 
   Shows the owner all the orders not shipped yet. (Processing)
    * *EX: (SELECT * from Order WHERE status!='Shipped')*

8. Order Fufillment page -
   Shows all the orders and allows the user to mark them as Processed, Delivered, or Shipped, AND allows the owner to view more details about the specific order
    * *EX: Create table where all order info is displayed. A dropdown with processed, delivered, and shipped. Each order has a corresponding button that redirects them to order detail page.* 

9. Order Detail page - 
   If the owner clicks the button associated with a specific order on the order fufillment page, he is redirected to this page where he can see all the details on that specific order.
    * *EX: (SELECT * from Order WHERE Ordernum = ...)*
