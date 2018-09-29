# SocialShare-magento2
Free magento2 plugin to place share and like buttons on product pages. Social networks implemented:
- Facebook
- Twitter
- Pinterest
- Google+

Tested and working on magento 2.1.6 & Luma (other themes can and will vary).

# Installation
- composer require app/socialshare
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy

# Usage
All of the buttons are disabled initially. Go to Stores/Configuration/App/Social Buttons to enable each button individually. If you want facebook stats create and enter an app id (optional).

If you wish to move the buttons override App/SocialShare/view/frontend/layout/catalog_product_view.xml in your theme (particularly the move command). You may also need to override the rule in App/SocialShare/view/frontend/web/css/social.css to get the facebook buttons aligned.

Should you decide that the network provided widgets are too slow there are local versions which you can enable. You do need to have loaded http://fontawesome.io/ into your theme (the module doesn't include it).
