#!/bin/sh
sudo killall ngrok
cd /var/www/library/scripts/
nohup ./ngrok start --all --config ./ngrok.yml &
echo 'ngrok started'
sleep 2
URLS=`python parse_ngrok.py | tail -n 1`
FRONT=`echo $URLS | cut -d ";" -f 1`
BACK=`echo $URLS | cut -d ";" -f 2`
export FRONTEND_URL=$FRONT
export BACKEND_URL=$BACK
cd /var/www/library/frontend/
#sudo yarn install
nohup yarn serve &
echo 'frontend started'
echo 'FRONTEND URL:' $FRONTEND_URL
echo 'BACKEND URL:' $BACKEND_URL


