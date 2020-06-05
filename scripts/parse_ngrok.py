import os
import json
import requests

if __name__ == '__main__':
    response = requests.get('http://127.0.0.1:4040/api/tunnels')

    data = response.json()
    frontend_url = None

    backend_url = None
    for tunnel in data['tunnels']:
      if tunnel['name'] == 'frontend':
        frontend_url = tunnel['public_url'].replace('https', 'http')
      if tunnel['name'] == 'backend':
        backend_url = tunnel['public_url'].replace('https', 'http')

    print("{};{}".format(frontend_url,backend_url))
