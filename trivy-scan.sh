#!/bin/bash


mkdir -p trivy-results

# This code will run a Trivy scan and save results in json format
docker run --rm \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -v $(pwd)/trivy-results:/data \
    aquasec/trivy:latest \
    image php:7.4-apache \
    --format json --output /data/php-vuln.json

echo "Vulnerability scan complete! Results saved to trivy-results/php-vuln.json"
