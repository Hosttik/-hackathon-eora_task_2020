#!/bin/sh
sudo killall ngrok
sudo killall node
export FRONTEND_URL=''
export BACKEND_URL=''
echo 'Server stopped'
