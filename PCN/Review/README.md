# Product Review Media - Magento2

# Overview

The Product Review Media for Magento 2 extension has been developed by myself. This extension will allow your customers to upload images within default product reviews.

Here are some of the salient features for the extension:

```
1. Allow your customers to upload images in Magento 2 default product reviews
2. Admin approval required to ensure relevant and appropriate images only to be viewed at frontend
3. Allows uploading of up to 10 product images
4. Allows potential customers to view the review images uploaded by other customers
5. Original image size results in good user experience
```

## Installation
 
1. Download code from this repo under Magento 2 following directory:

   ```
   app/code/PCN/Review
   ```

2. Enter following commands to enable the module:

   ```
   php bin/magento module:enable PCN_Review
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   php bin/magento cache:flush
   ```

3. If Magento is running in production mode, deploy static content: 

   ```
   php bin/magento setup:static-content:deploy
   ```

_Please feel free to contact me at phamcongnhat1415@gmail.com if you have any questions. Thank you!_
