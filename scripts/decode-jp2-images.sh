#!/bin/bash

# JP2/JPEG2000 Image decoder/converter to JPEG
#
# Made by Jonathan Barda / SIB - 2020
#
# Call it with find like that
# find . -iname "*.jp2" -exec ./decode-jp2-images.sh {} \;
#
# Require packages:
# - libopenjp2-tools
# - imagemagick-6.q16hdri

echo -e "\nJP2 Image decoder to JPEG / SIB - 2020\n"

[[ $# -eq 0 ]] && echo -e "\nUsage: $0 <source-image>" && exit 1

ENCODED_IMAGE="$1"
DECODED_IMAGE="$1.bmp"
CONVERTED_IMAGE="$DECODED_IMAGE.jpeg"

echo -e "Decoding image [${ENCODED_IMAGE}] to [${DECODED_IMAGE}]..."
opj_decompress -i $ENCODED_IMAGE -o $DECODED_IMAGE -threads $(nproc)

echo -e "\nConverting image from BMP to JPEG...\n"
convert $DECODED_IMAGE $CONVERTED_IMAGE

echo -e "\nDone.\n"