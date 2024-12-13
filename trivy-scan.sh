#!/bin/bash

# Define the absolute path
PROJECT_DIR="/mnt/c/Users/seree/Desktop/Final year project/Docker-Project"
IGNORE_FILE="$PROJECT_DIR/.trivyignore"
RESULTS_DIR="$PROJECT_DIR/trivy-results"

# check if .trivyignore exists
if [ ! -f "$IGNORE_FILE" ]; then
  echo "Error: .trivyignore file not found at $IGNORE_FILE"
  exit 1
fi


mkdir -p "$RESULTS_DIR"

# This code runs the Trivy scan on the entire Application directory - this allows the scan to only scan Application folder and not the other containers
docker run --rm \
    -v "$PROJECT_DIR:/project" \
    -v "$RESULTS_DIR:/data" \
    -v "$IGNORE_FILE:/project/.trivyignore" \
    aquasec/trivy:latest \
    fs --ignorefile /project/.trivyignore --severity CRITICAL,HIGH \
    --format json --output /data/php-vuln.json /project/html

echo "Filesystem vulnerability scan complete! Results saved to trivy-results/php-vuln.json"
