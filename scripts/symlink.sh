#!/bin/bash

SITE_PATH=$1
CODE_PATH=$2

if [ -z $SITE_PATH ]; then
	echo "Please enter the path to the root of your Joomla! install: "
	read SITE_PATH
fi

if [ ! -d $SITE_PATH ]; then
	echo "Invalid directory. Exiting..."
	exit
fi

if [ -z $CODE_PATH ]; then
	CWD=$(pwd)

	CODE_PATH=$CWD/code
	if [ ! -d $CODE_PATH ]; then
		PARENT=$(dirname $CWD)
		CODE_PATH=$PARENT/code
		if [ ! -d $CODE_PATH ]; then
			echo "Could not find code path.  Please enter path to the code directory of the com_api repository:"
			read CODE_PATH
			if [ ! -d $CODE_PATH ]; then
				echo "Path to code not found"
				exit
			fi
		fi
	fi
fi

# Delete old links and create new symlinks

if [ -L $SITE_PATH/components/com_api ]; then
	echo "Deleting old site component directory"
	rm -rf $SITE_PATH/components/com_api
fi

if [ -L $SITE_PATH/administrator/components/com_api ]; then
	echo "Deleting old administrator component directory"
	rm -rf $SITE_PATH/administrator/components/com_api
fi

if [ -L $SITE_PATH/plugins/api ]; then
	echo "Deleting old plugins api directory"
	rm -rf $SITE_PATH/plugins/api
fi

# Admin language files
adminlangs=( com_api plg_api_content plg_api_language plg_api_users )
for lang in ${adminlangs[@]}
	do
	if [ -L $SITE_PATH/administrator/language/en-GB/en-GB.$lang.ini ]; then
		echo "Deleting old $lang admin language file"
		rm -rf $SITE_PATH/administrator/language/en-GB/en-GB.$lang.ini
	fi
	if [ -L $SITE_PATH/administrator/language/en-GB/en-GB.$lang.sys.ini ]; then
		echo "Deleting old $lang.sys admin language file"
		rm -rf $SITE_PATH/administrator/language/en-GB/en-GB.$lang.sys.ini
	fi
done

# Site language files
sitelangs=( com_api )
for lang in ${sitelangs[@]}
	do
	if [ -L $SITE_PATH/language/en-GB/en-GB.$lang.ini ]; then
		echo "Deleting old $lang site language file"
		rm -rf $SITE_PATH/language/en-GB/en-GB.$lang.ini
	fi
done

ln -s $CODE_PATH/components/com_api $SITE_PATH/components/
ln -s $CODE_PATH/administrator/components/com_api $SITE_PATH/administrator/components/
ln -s $CODE_PATH/language/en-GB/* $SITE_PATH/language/en-GB/
ln -s $CODE_PATH/administrator/language/en-GB/* $SITE_PATH/administrator/language/en-GB/
ln -s $CODE_PATH/plugins/api $SITE_PATH/plugins/

echo "Links created successfully"
exit

#mkdir $SITE_PATH/plugins/api
#ln -s $CODE_PATH/plugins/api/* $SITE_PATH/plugins/api/
#ln -s $CODE_PATH/language/en-GB/* $SITE_PATH/language/en-GB/
