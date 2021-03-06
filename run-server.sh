#!/bin/bash

domain=bike.test
port=8000

# Stop LAMPP services on finish.
function finish {
  echo "Stopping LAMPP services."
  sudo /opt/lampp/lampp stop
}

trap finish EXIT

# Run MySQL
sudo /opt/lampp/lampp start

# Serve server (with domain bike.test, read README)
php artisan serve --host=$domain --port=$port

# Normally start server on http://localhost:8000
# php artisan serve