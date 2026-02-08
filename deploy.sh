#!/bin/bash

# JOLERO SRL - Deployment Script
# This script builds and deploys the site to the live server via FTP
# Credentials are stored securely in ~/.netrc

set -e  # Exit on error

echo "🚀 Starting deployment process..."

# Step 1: Build the project
echo "📦 Building the project..."
npm run build

# Step 2: Deploy via FTP
echo "📤 Uploading to live server..."

# Load environment variables from .env file
if [ -f .env ]; then
    # More robust way to load .env, handling quotes and special characters
    while IFS= read -r line || [ -n "$line" ]; do
        # Remove carriage return if file has Windows endings
        line=$(echo "$line" | tr -d '\r')
        # Skip comments and empty lines
        if [[ ! "$line" =~ ^# ]] && [[ "$line" == *=* ]]; then
            export "${line%%=*}"="${line#*=}"
        fi
    done < .env
fi

# Check if required variables are set
if [ -z "$FTP_HOST" ] || [ -z "$FTP_USER" ] || [ -z "$FTP_PASS" ]; then
    echo "❌ Error: FTP_HOST, FTP_USER, or FTP_PASS is not set in .env"
    exit 1
fi

REMOTE_DIR=${REMOTE_DIR:-/public_html}

# Upload using lftp
lftp -c "
set ftp:ssl-allow no;
open $FTP_HOST;
user \"$FTP_USER\" \"$FTP_PASS\";
lcd dist;
cd \"$REMOTE_DIR\";
mirror --reverse --delete --verbose --exclude-glob .git* --exclude-glob .DS_Store;
bye;
"

echo "✅ Deployment complete!"
echo "🌐 Your site is now live at https://jole.ro"
