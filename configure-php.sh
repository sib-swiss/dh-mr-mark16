#!/bin/bash

# TODO: Implement ini file reader

# Config
INI_FILE=$(php --ini | grep -i loaded | awk '{print $4}')

# Set values
sudo cp -v $INI_FILE "${INI_FILE}.bak"
sudo sed -e 's/post_max_size = 8M/post_max_size = 80M/i' -i $INI_FILE
sudo sed -e 's/upload_max_filesize = 2M/upload_max_filesize = 80M/i' -i $INI_FILE
sudo sed -e 's/memory_limit = -1/memory_limit = 1024M/i' -i $INI_FILE
sudo sed -e 's/memory_limit = 128M/memory_limit = 1024M/i' -i $INI_FILE

# Check values
cat $INI_FILE | grep post_max_size
cat $INI_FILE | grep upload_max_filesize
cat $INI_FILE | grep memory_limit