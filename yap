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
