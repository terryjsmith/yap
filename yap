#!/bin/bash

if [ $1 == "create" ]; then
	if [ $# -le 2 ]; then
		echo "Please specify what we will be creating."
		exit
	else
		if [ $2 == "controller" ]; then
			if [ $# -ne 3 ]; then
				echo "Please specify a controller name."
				exit
			else
				mkdir views/$3
				touch views/$3/index.php
				sed "s/default/$3/g" controllers/defaultController.php > controllers/$3Controller.php
			fi
		fi

		if [ $2 == "model" ]; then
			if [ $# -ne 3 ]; then
				echo "Please specify a model name."
				exit
			else
				echo -n "Please enter a primary key: "
				read pkey
				name=`echo $3 | tr "[:upper:]" "[:lower:]"`
				sed "s/Example/$3/g" models/example.php > models/$name.php
				sed -i "s/example_id/$pkey/g" models/$name.php
			fi
		fi
	fi
fi

if [ $1 == "remove" ]; then
        if [ $# -le 2 ]; then
                echo "Please specify what we will be removing."
                exit
        else
                if [ $2 == "controller" ]; then
                        if [ $# -ne 3 ]; then
                                echo "Please specify a controller name."
                                exit
                        else
                                rm -rf views/$3
                                rm -rf controllers/$3Controller.php
                        fi
                fi
        fi
fi
