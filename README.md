#############################################################################
##  PROJECT:           NewLoGD
##  VERSION:           dev
##  AUTHOR:            Basilius Sauter <basilius.sauter@hispeed.ch>
##  LICENCE:           GNU Affero GPL 3 (see file LICENCE for details)
#############################################################################

    NewLoGD, a Browsergame based in PHP and MySQL
    Copyright (C) 2015  Basilius Sauter

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
#
#   PREFACE
#

BE CAREFULE - This code is under early development and is NOT meant for production runs.
There is so far no implemented user right management, meaning that every registered user 
can access the administrative modules!

#
#   REQUIREMENTS
#

For running this game, you will need:
	- A webserver with PHP support
	- PHP 5.5 or higher
	- A running MySQL server (MySQL 5.6.20 works, earlier versions probably as well)

#
#   INSTALLATION
#

1. Create a new table
2. Import the .sql file including the values
3. Edit the file dbconfig.php with the connection informations
4. Enjoy. Default login is Admin/CHANGEME
5. Tada - you have now successfully installed NewLoGD.
