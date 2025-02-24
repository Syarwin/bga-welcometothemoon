#!/bin/bash

# Extract datas from JSON
cd misc/
php8.3 extractDatas.php
cd ..

# Format files
prettier -w modules/php/Material/*.php

# Copy
SRC=~/bga/bga-welcometothemoon/
NAME=welcometothemoon

rsync $SRC/modules/php/Material/*.php ~/bga/studio/$NAME/modules/php/Material/
rsync $SRC/modules/js/data.js ~/bga/studio/$NAME/modules/js/
