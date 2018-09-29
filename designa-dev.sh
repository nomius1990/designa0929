#! /bin/sh
rm -rf pub/static/frontend/Silk/designa/ && php bin/magento cache:clean && php bin/magento setup:static-content:deploy -f && sudo chmod -R 777 ./ && npm run dev