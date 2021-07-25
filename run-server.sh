#!/bin/bash

# Stop LAMPP services on finish.
function finish {
  echo "Stopping LAMPP services."
  sudo /opt/lampp/lampp stop
}

trap finish EXIT

# Run MySQL
sudo /opt/lampp/lampp start

# Serve server
php artisan serve