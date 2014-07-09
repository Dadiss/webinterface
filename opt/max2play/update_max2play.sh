#!/bin/sh
#Update Max2Play Scripts and Webinterface
echo "Get Data"
wget http://shop.max2play.com/media/downloadable/currentversion/webinterface.zip -O /opt/max2play/cache/webinterface.zip
wget http://shop.max2play.com/media/downloadable/currentversion/scripts.zip -O /opt/max2play/cache/scripts.zip

echo "Install Webinterface"
unzip -o /opt/max2play/cache/webinterface.zip -d /var/www

echo "Install Scripts"
unzip -o /opt/max2play/cache/scripts.zip -d /

# Fix für usbmount Geschwindigkeit
sed -i 's/^MOUNTOPTIONS="sync,noexec,nodev,noatime,nodiratime"/MOUNTOPTIONS="noexec,nodev,noatime,nodiratime"/' /etc/usbmount/usbmount.conf