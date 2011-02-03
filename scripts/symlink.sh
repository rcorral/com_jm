#!/bin/bash

curr_path=`pwd`
api_path=$curr_path/repositories/com_api
site_path=$curr_path/server

ln -s $api_path/components/com_api $site_path/components/
ln -s $api_path/administrator/components/com_api $site_path/administrator/components/
ln -s $api_path/administrator/language/en-GB/* $site_path/administrator/language/en-GB/
mkdir $site_path/plugins/api
ln -s $api_path/plugins/api/* $site_path/plugins/api/
ln -s $api_path/language/en-GB/* $site_path/language/en-GB/
